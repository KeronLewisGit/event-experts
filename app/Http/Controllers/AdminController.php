<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\BookedDate;
use App\Models\Enquiry;
use App\Models\Project;
use App\Models\Vacancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        return Auth::check() ? redirect()->route('admin.dashboard') : view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Those details do not match our records.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard(): View
    {
        $counts = Enquiry::selectRaw('type, status, count(*) as total')->groupBy('type', 'status')->get();

        return view('admin.dashboard', [
            'byType' => collect(Enquiry::TYPES)->map(fn ($meta, $type) => [
                'meta' => $meta,
                'total' => $counts->where('type', $type)->sum('total'),
                'new' => $counts->where('type', $type)->where('status', 'new')->sum('total'),
            ]),
            'open' => $counts->whereIn('status', ['new', 'in_progress'])->sum('total'),
            'newCount' => $counts->where('status', 'new')->sum('total'),
            'thisWeek' => Enquiry::where('created_at', '>=', now()->subDays(7))->count(),
            'recent' => Enquiry::latest()->take(8)->get(),
            'dates' => BookedDate::upcoming()->take(5)->get(),
            'projectCount' => Project::where('is_published', true)->count(),
        ]);
    }

    public function enquiries(Request $request): View
    {
        $enquiries = Enquiry::query()
            ->when($request->query('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('reference', 'like', "%{$term}%")
                ->orWhere('summary', 'like', "%{$term}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.enquiries', ['enquiries' => $enquiries]);
    }

    public function enquiry(Enquiry $enquiry): View
    {
        $eventDate = $enquiry->eventDate();

        return view('admin.enquiry', [
            'enquiry' => $enquiry,
            'eventDate' => $eventDate,
            'bookedDate' => $eventDate ? BookedDate::whereDate('date', $eventDate)->first() : null,
        ]);
    }

    public function updateEnquiry(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $enquiry->update($request->validate([
            'status' => ['required', Rule::in(array_keys(Enquiry::STATUSES))],
            'notes' => ['nullable', 'string', 'max:4000'],
        ]));

        return back()->with('status', 'Enquiry updated.');
    }

    public function destroyEnquiry(Enquiry $enquiry): RedirectResponse
    {
        if ($enquiry->attachment_path) {
            Storage::delete($enquiry->attachment_path);
        }

        $enquiry->delete();

        return redirect()->route('admin.enquiries')->with('status', "Enquiry {$enquiry->reference} deleted.");
    }

    public function attachment(Enquiry $enquiry): StreamedResponse
    {
        abort_unless($enquiry->attachment_path && Storage::exists($enquiry->attachment_path), 404);

        return Storage::download($enquiry->attachment_path, $enquiry->attachment_name);
    }

    public function projects(): View
    {
        return view('admin.projects', ['projects' => Project::orderByDesc('event_date')->orderByDesc('id')->get()]);
    }

    public function createProject(): View
    {
        return view('admin.project', ['project' => new Project(['is_published' => true])]);
    }

    public function storeProject(Request $request): RedirectResponse
    {
        $project = new Project;
        $this->saveProject($request, $project);

        return redirect()->route('admin.projects.edit', $project)->with('status', 'Project added to the portfolio.');
    }

    public function editProject(Project $project): View
    {
        return view('admin.project', ['project' => $project]);
    }

    public function updateProject(Request $request, Project $project): RedirectResponse
    {
        $this->saveProject($request, $project);

        return redirect()->route('admin.projects.edit', $project)->with('status', 'Project saved.');
    }

    public function destroyProject(Project $project): RedirectResponse
    {
        Storage::disk('uploads')->delete(array_map(fn (string $path) => Str::after($path, 'uploads/'), $project->uploadedImages()));
        $project->delete();

        return redirect()->route('admin.projects')->with('status', "“{$project->title}” removed from the portfolio.");
    }

    public function dates(): View
    {
        return view('admin.dates', [
            'upcoming' => BookedDate::upcoming()->get(),
            'past' => BookedDate::whereDate('date', '<', now(config('site.timezone'))->toDateString())->orderByDesc('date')->take(10)->get(),
        ]);
    }

    public function storeDate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(array_keys(BookedDate::STATUSES))],
            'note' => ['nullable', 'string', 'max:160'],
        ]);

        // Matched with whereDate because SQLite stores the date with a time component.
        $day = BookedDate::whereDate('date', $data['date'])->first() ?? new BookedDate;
        $day->fill(['date' => $data['date'], 'status' => $data['status'], 'note' => $data['note'] ?? null])->save();

        return back()->with('status', 'Calendar updated. The date checker on the website now reflects it.');
    }

    public function destroyDate(BookedDate $date): RedirectResponse
    {
        $date->delete();

        return back()->with('status', 'Date opened up again.');
    }

    public function vacancies(): View
    {
        return view('admin.vacancies', ['vacancies' => Vacancy::latest('id')->get()]);
    }

    public function storeVacancy(Request $request): RedirectResponse
    {
        Vacancy::create($request->validate([
            'title' => ['required', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:255'],
            'requirements' => ['nullable', 'string', 'max:3000'],
        ]));

        return back()->with('status', 'Vacancy published on the Careers page.');
    }

    public function toggleVacancy(Vacancy $vacancy): RedirectResponse
    {
        $vacancy->update(['is_open' => ! $vacancy->is_open]);

        return back()->with('status', $vacancy->is_open ? 'Vacancy re-opened.' : 'Vacancy closed.');
    }

    public function destroyVacancy(Vacancy $vacancy): RedirectResponse
    {
        $vacancy->delete();

        return back()->with('status', 'Vacancy removed.');
    }

    public function announcements(): View
    {
        return view('admin.announcements', [
            'announcements' => Announcement::latest('id')->get(),
            'live' => Announcement::current(),
        ]);
    }

    public function storeAnnouncement(Request $request): RedirectResponse
    {
        Announcement::create($request->validate([
            'message' => ['required', 'string', 'max:140'],
            'link_text' => ['nullable', 'string', 'max:40', 'required_with:link_url'],
            'link_url' => ['nullable', 'string', 'max:255', 'regex:/^(\/|https:\/\/)/'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
        ], ['link_url.regex' => 'Links must start with / (a page on this site) or https://.']));

        return back()->with('status', 'Announcement published.');
    }

    public function toggleAnnouncement(Announcement $announcement): RedirectResponse
    {
        $announcement->update(['is_active' => ! $announcement->is_active]);

        return back()->with('status', $announcement->is_active ? 'Announcement switched on.' : 'Announcement switched off.');
    }

    public function destroyAnnouncement(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return back()->with('status', 'Announcement removed.');
    }

    /** Validate the portfolio form, store any new photos and save the project. */
    protected function saveProject(Request $request, Project $project): void
    {
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'];

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'category' => ['required', Rule::in(array_keys(config('site.categories')))],
            'event_date' => ['nullable', 'date'],
            'venue' => ['nullable', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:6000'],
            'credits' => ['nullable', 'string', 'max:3000'],
            'instagram_url' => ['nullable', 'url', 'starts_with:https://', 'max:255'],
            'cover_file' => ['nullable', ...$image],
            'photos' => ['nullable', 'array', 'max:12'],
            'photos.*' => $image,
            'remove' => ['nullable', 'array'],
            'remove.*' => ['string'],
        ]);

        $existing = $project->images();
        $remove = array_intersect($data['remove'] ?? [], $existing);
        $gallery = array_values(array_diff($project->gallery ?? [], $remove));
        $cover = in_array($project->cover, $remove, true) ? null : $project->cover;

        foreach ($request->file('photos', []) as $photo) {
            $gallery[] = 'uploads/'.$photo->store('portfolio', 'uploads');
        }

        if ($file = $request->file('cover_file')) {
            // The old cover stays in the gallery rather than being lost.
            if ($cover) {
                array_unshift($gallery, $cover);
            }
            $cover = 'uploads/'.$file->store('portfolio', 'uploads');
        }

        $project->fill([
            ...collect($data)->only(['title', 'category', 'event_date', 'venue', 'summary', 'body', 'instagram_url'])->all(),
            'slug' => $project->slug ?: $this->uniqueSlug($data['title']),
            'cover' => $cover ?? array_shift($gallery),
            'gallery' => array_values(array_unique($gallery)),
            'credits' => Project::parseCredits($data['credits'] ?? null),
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $request->boolean('is_published'),
        ])->save();

        Storage::disk('uploads')->delete(array_map(
            fn (string $path) => Str::after($path, 'uploads/'),
            array_filter($remove, fn (string $path) => str_starts_with($path, 'uploads/')),
        ));
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'project';
        $slug = $base;

        for ($i = 2; Project::where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BookedDate;
use App\Models\Project;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    /** Static pages searchable from the header: [route, title, description, keywords]. */
    protected const PAGES = [
        ['services', 'Services', 'Luxury weddings, micro weddings, corporate events, parties and event rentals.', 'services decor design management rentals'],
        ['portfolio', 'Portfolio', 'Weddings and events we have designed and built.', 'portfolio gallery photos weddings events real'],
        ['plan', 'Plan your event', 'Check your date and tell us about your event to get a quote.', 'quote price plan book booking date availability enquiry'],
        ['about', 'About us', 'Twenty years of event decor, management and rentals in Trinidad and Tobago.', 'about team story experience warehouse chaguanas'],
        ['careers', 'Careers', 'Join the Event Experts team.', 'careers jobs hiring vacancy work warehouse staff resume'],
        ['contact', 'Contact', 'Call, WhatsApp or message the team.', 'contact phone whatsapp email location chaguanas'],
    ];

    public function home(): View
    {
        return view('pages.home', [
            'featured' => Project::published()->where('is_featured', true)->take(6)->get(),
            'recent' => Project::published()->take(10)->get(),
            'booked' => BookedDate::publicMap(),
        ]);
    }

    public function services(): View
    {
        return view('pages.services');
    }

    public function service(string $service): View
    {
        abort_unless($meta = config("site.services.{$service}"), 404);

        return view('pages.service', [
            'slug' => $service,
            'service' => $meta,
            'projects' => $meta['categories']
                ? Project::published()->whereIn('category', $meta['categories'])->take(3)->get()
                : collect(),
        ]);
    }

    public function plan(): View
    {
        return view('pages.plan', ['booked' => BookedDate::publicMap()]);
    }

    public function about(): View
    {
        return view('pages.about', ['projects' => Project::published()->take(4)->get()]);
    }

    public function careers(): View
    {
        return view('pages.careers', ['vacancies' => Vacancy::open()->get()]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function search(Request $request): View
    {
        $query = trim((string) $request->query('q'));
        $terms = collect(preg_split('/\s+/', Str::lower($query)))->filter(fn ($t) => strlen($t) > 1);
        $results = collect();

        if ($terms->isNotEmpty()) {
            $matches = fn (string $haystack) => $terms->every(fn ($t) => str_contains(Str::lower($haystack), $t));

            foreach (self::PAGES as [$route, $title, $description, $keywords]) {
                if ($matches("$title $description $keywords")) {
                    $results->push(['type' => 'Page', 'title' => $title, 'text' => $description, 'url' => route($route)]);
                }
            }

            foreach (config('site.services') as $slug => $service) {
                if ($matches($service['name'].' '.$service['summary'].' '.$service['intro'].' '.collect($service['points'])->flatten()->implode(' '))) {
                    $results->push(['type' => 'Service', 'title' => $service['name'], 'text' => $service['summary'], 'url' => route('service', $slug)]);
                }
            }

            foreach (Project::published()->get() as $project) {
                if ($matches($project->title.' '.$project->venue.' '.$project->summary.' '.$project->body.' '.$project->categoryLabel())) {
                    $results->push(['type' => 'Portfolio', 'title' => $project->title, 'text' => $project->summary, 'url' => route('portfolio.show', $project)]);
                }
            }
        }

        return view('pages.search', ['query' => $query, 'results' => $results]);
    }
}

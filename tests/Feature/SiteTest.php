<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\BookedDate;
use App\Models\Enquiry;
use App\Models\Project;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public static function publicPages(): array
    {
        return [
            'home' => ['/', 'We make it happen.'],
            'services' => ['/services', 'Corporate & State Events'],
            'weddings' => ['/services/weddings', 'Floral overheads'],
            'micro weddings' => ['/services/micro-weddings', 'Home and backyard transformations'],
            'corporate' => ['/services/corporate', 'State and ceremonial occasions'],
            'parties' => ['/services/parties', 'Milestone birthdays'],
            'rentals' => ['/services/rentals', 'Send rental enquiry'],
            'portfolio' => ['/portfolio', 'Dillon & Kristal'],
            'portfolio project' => ['/portfolio/sime-christian', 'The biggest floral overhead we have ever done'],
            'plan' => ['/plan-your-event', 'What are we celebrating?'],
            'about' => ['/about', 'Twenty years of making it happen.'],
            'careers' => ['/careers', 'Warehouse Supervisor'],
            'contact' => ['/contact', 'eventexpertsltd@gmail.com'],
            'search' => ['/search?q=hyatt', 'Hyatt Lime'],
        ];
    }

    #[DataProvider('publicPages')]
    public function test_public_page_renders(string $url, string $expected): void
    {
        $this->get($url)->assertOk()->assertSee($expected);
    }

    public function test_unknown_page_returns_not_found(): void
    {
        $this->get('/no-such-page')->assertNotFound()->assertSee('This page has left the party.');
    }

    public function test_unknown_service_returns_not_found(): void
    {
        $this->get('/services/catering')->assertNotFound();
    }

    public function test_unpublished_project_is_hidden_from_visitors_but_previewable_by_staff(): void
    {
        $draft = Project::factory()->unpublished()->create(['title' => 'Unreleased Gala']);

        $this->get('/portfolio')->assertDontSee('Unreleased Gala');
        $this->get("/portfolio/{$draft->slug}")->assertNotFound();

        $this->actingAs(User::factory()->create())
            ->get("/portfolio/{$draft->slug}")
            ->assertOk()
            ->assertSee('Draft, visible to staff only');
    }

    public function test_date_checker_receives_upcoming_dates_without_staff_notes(): void
    {
        BookedDate::query()->delete();
        $date = now()->addMonths(3)->toDateString();
        BookedDate::create(['date' => $date, 'status' => 'booked', 'note' => 'Private client name']);
        BookedDate::create(['date' => now()->subMonth()->toDateString(), 'status' => 'booked']);

        $this->assertSame([$date => 'booked'], BookedDate::publicMap());
        $this->get('/plan-your-event')->assertSee($date)->assertDontSee('Private client name');
    }

    public function test_quote_request_is_stored_with_an_event_summary(): void
    {
        $date = now()->addMonths(8)->toDateString();

        $response = $this->from('/plan-your-event')->post('/enquiries/quote', $this->quote(['event_date' => $date]));

        $enquiry = Enquiry::where('email', 'bride@example.com')->sole();

        $response->assertRedirect('/plan-your-event#form-quote');
        $this->assertSame('Wedding · '.now()->addMonths(8)->format('j M Y'), $enquiry->summary);
        $this->assertMatchesRegularExpression('/^EVT-\d{5}$/', $enquiry->reference);
        $this->assertSame(['Full decor and design', 'Floral overhead'], $enquiry->data['needs']);
        $this->assertTrue($enquiry->eventDate()->isSameDay($date));
        $this->get('/plan-your-event')->assertSee($enquiry->reference);
    }

    public function test_quote_request_without_a_date_is_marked_to_be_confirmed(): void
    {
        $this->post('/enquiries/quote', $this->quote(['event_date' => '', 'date_flexible' => '1']))
            ->assertSessionHas('enquiry_success');

        $enquiry = Enquiry::where('email', 'bride@example.com')->sole();

        $this->assertSame('Wedding · Date to be confirmed', $enquiry->summary);
        $this->assertSame('Yes', $enquiry->data['date_flexible']);
        $this->assertNull($enquiry->eventDate());
    }

    public function test_quote_request_requires_services_a_phone_number_and_a_future_date(): void
    {
        $this->from('/plan-your-event')
            ->post('/enquiries/quote', $this->quote(['needs' => [], 'phone' => '', 'event_date' => now()->subDay()->toDateString()]))
            ->assertRedirect('/plan-your-event#form-quote')
            ->assertSessionHasErrorsIn('quote', ['needs', 'phone', 'event_date']);

        $this->assertSame(0, Enquiry::where('is_sample', false)->count());
    }

    public function test_quote_request_keeps_an_inspiration_photo(): void
    {
        Storage::fake();

        $this->post('/enquiries/quote', $this->quote(['attachment' => UploadedFile::fake()->image('moodboard.jpg')]))
            ->assertSessionHas('enquiry_success');

        $enquiry = Enquiry::where('email', 'bride@example.com')->sole();

        $this->assertSame('moodboard.jpg', $enquiry->attachment_name);
        Storage::assertExists($enquiry->attachment_path);
    }

    public function test_contact_form_stores_a_message(): void
    {
        $this->from('/contact')->post('/enquiries/contact', [
            'name' => 'Test Customer', 'email' => 'test@example.com', 'topic' => 'Micro wedding', 'message' => 'Do you decorate backyards?',
        ])->assertRedirect('/contact#form-contact');

        $enquiry = Enquiry::where('email', 'test@example.com')->sole();

        $this->assertSame('Micro wedding', $enquiry->summary);
        $this->assertMatchesRegularExpression('/^MSG-\d{5}$/', $enquiry->reference);
    }

    public function test_honeypot_submissions_are_silently_dropped(): void
    {
        $this->post('/enquiries/contact', [
            'name' => 'Bot', 'email' => 'bot@example.com', 'message' => 'Buy now', 'website' => 'https://spam.example',
        ])->assertSessionHas('enquiry_success');

        $this->assertDatabaseMissing('enquiries', ['email' => 'bot@example.com']);
    }

    public function test_rental_enquiry_is_stored(): void
    {
        $this->post('/enquiries/rental', [
            'name' => 'Host', 'email' => 'host@example.com', 'phone' => '868-555-0100', 'items' => 'Grass wall',
        ])->assertSessionHas('enquiry_success');

        $this->assertSame('Rental · Grass wall', Enquiry::where('email', 'host@example.com')->sole()->summary);
    }

    public function test_job_application_requires_a_resume(): void
    {
        $this->post('/enquiries/career', [
            'name' => 'Applicant', 'email' => 'applicant@example.com', 'phone' => '868-555-0100', 'role' => 'Warehouse Supervisor',
        ])->assertSessionHasErrorsIn('career', ['attachment']);
    }

    public function test_job_application_is_stored_with_the_resume(): void
    {
        Storage::fake();

        $this->post('/enquiries/career', [
            'name' => 'Applicant', 'email' => 'applicant@example.com', 'phone' => '868-555-0100', 'role' => 'Warehouse Supervisor',
            'attachment' => UploadedFile::fake()->create('resume.pdf', 120, 'application/pdf'),
        ])->assertSessionHas('enquiry_success');

        $enquiry = Enquiry::where('email', 'applicant@example.com')->sole();

        $this->assertSame('Warehouse Supervisor', $enquiry->summary);
        $this->assertMatchesRegularExpression('/^JOB-\d{5}$/', $enquiry->reference);
        Storage::assertExists($enquiry->attachment_path);
    }

    public function test_unknown_enquiry_type_returns_not_found(): void
    {
        $this->post('/enquiries/mailbox', ['name' => 'Test'])->assertNotFound();
    }

    public static function adminPages(): array
    {
        return [
            'dashboard' => ['/admin'],
            'enquiries' => ['/admin/enquiries'],
            'portfolio' => ['/admin/portfolio'],
            'new project' => ['/admin/portfolio/new'],
            'calendar' => ['/admin/calendar'],
            'careers' => ['/admin/careers'],
            'announcements' => ['/admin/announcements'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_admin_page_requires_sign_in_and_renders_for_staff(string $url): void
    {
        $this->get($url)->assertRedirect('/admin/login');

        $this->actingAs(User::factory()->create())->get($url)->assertOk();
    }

    public function test_staff_can_sign_in_with_the_seeded_account_and_sign_out(): void
    {
        $this->post('/admin/login', ['email' => 'admin@eventexpertstt.com', 'password' => 'events-demo-2026'])
            ->assertRedirect('/admin');
        $this->assertAuthenticated();

        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_sign_in_rejects_a_wrong_password(): void
    {
        $this->post('/admin/login', ['email' => 'admin@eventexpertstt.com', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_staff_can_filter_update_and_delete_enquiries(): void
    {
        $staff = User::factory()->create();
        $enquiry = Enquiry::factory()->create(['name' => 'Findable Person']);

        $this->actingAs($staff)->get('/admin/enquiries?q=Findable')->assertSee('Findable Person')->assertDontSee('Sample Client A');
        $this->actingAs($staff)->get("/admin/enquiries/{$enquiry->id}")->assertSee($enquiry->reference);

        $this->actingAs($staff)->patch("/admin/enquiries/{$enquiry->id}", ['status' => 'booked', 'notes' => 'Deposit received'])
            ->assertSessionHas('status');
        $this->assertSame(['booked', 'Deposit received'], [$enquiry->refresh()->status, $enquiry->notes]);

        $this->actingAs($staff)->delete("/admin/enquiries/{$enquiry->id}")->assertRedirect('/admin/enquiries');
        $this->assertModelMissing($enquiry);
    }

    public function test_staff_can_mark_a_quote_date_as_booked_and_open_it_again(): void
    {
        $staff = User::factory()->create();
        $date = now()->addMonths(5)->toDateString();
        $enquiry = Enquiry::factory()->quote($date)->create();

        $this->actingAs($staff)->get("/admin/enquiries/{$enquiry->id}")->assertSee('Mark this date as booked');

        $this->actingAs($staff)->post('/admin/calendar', ['date' => $date, 'status' => 'booked', 'note' => $enquiry->reference])
            ->assertSessionHas('status');
        $this->assertSame('booked', BookedDate::publicMap()[$date]);

        $this->actingAs($staff)->post('/admin/calendar', ['date' => $date, 'status' => 'limited'])->assertSessionHas('status');
        $this->assertSame(1, BookedDate::whereDate('date', $date)->count());
        $this->assertSame('limited', BookedDate::publicMap()[$date]);

        $this->actingAs($staff)->delete('/admin/calendar/'.BookedDate::whereDate('date', $date)->sole()->id);
        $this->assertArrayNotHasKey($date, BookedDate::publicMap());
    }

    public function test_staff_can_add_edit_and_delete_a_portfolio_project_with_photos(): void
    {
        Storage::fake('uploads');
        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/admin/portfolio', [
            'title' => 'Asha & Ravi', 'category' => 'wedding', 'summary' => 'A garden wedding.', 'is_published' => '1',
            'credits' => "Planning: Events by Tricia\nnot a credit line",
            'cover_file' => UploadedFile::fake()->image('cover.jpg'),
            'photos' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.jpg')],
        ])->assertRedirect('/admin/portfolio/asha-ravi');

        $project = Project::where('slug', 'asha-ravi')->sole();

        $this->assertCount(3, $project->images());
        $this->assertSame([['Planning', 'Events by Tricia']], $project->credits);
        Storage::disk('uploads')->assertExists(str_replace('uploads/', '', $project->cover));
        $this->get('/portfolio')->assertSee('Asha & Ravi');

        $removed = $project->gallery[0];
        $this->actingAs($staff)->put('/admin/portfolio/asha-ravi', [
            'title' => 'Asha & Ravi', 'category' => 'wedding', 'summary' => 'A garden wedding, updated.', 'remove' => [$removed],
        ])->assertRedirect('/admin/portfolio/asha-ravi');

        $project->refresh();
        $this->assertCount(2, $project->images());
        $this->assertFalse($project->is_published);
        Storage::disk('uploads')->assertMissing(str_replace('uploads/', '', $removed));

        $cover = $project->cover;
        $this->actingAs($staff)->delete('/admin/portfolio/asha-ravi')->assertRedirect('/admin/portfolio');
        $this->assertModelMissing($project);
        Storage::disk('uploads')->assertMissing(str_replace('uploads/', '', $cover));
    }

    public function test_portfolio_project_rejects_non_image_uploads(): void
    {
        $this->actingAs(User::factory()->create())->post('/admin/portfolio', [
            'title' => 'Bad Upload', 'category' => 'wedding', 'summary' => 'Test.',
            'cover_file' => UploadedFile::fake()->create('script.php', 10, 'text/x-php'),
        ])->assertSessionHasErrors('cover_file');

        $this->assertDatabaseMissing('projects', ['title' => 'Bad Upload']);
    }

    public function test_staff_can_publish_close_and_remove_a_vacancy(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/admin/careers', ['title' => 'Event Florist', 'requirements' => "Floral experience\nWeekend availability"]);
        $vacancy = Vacancy::where('title', 'Event Florist')->sole();
        $this->get('/careers')->assertSee('Event Florist')->assertSee('Weekend availability');

        $this->actingAs($staff)->patch("/admin/careers/{$vacancy->id}");
        $this->get('/careers')->assertDontSee('Weekend availability');

        $this->actingAs($staff)->delete("/admin/careers/{$vacancy->id}");
        $this->assertModelMissing($vacancy);
    }

    public function test_time_limited_announcement_takes_priority_and_can_be_switched_off(): void
    {
        $staff = User::factory()->create();

        $this->get('/')->assertSee('hiring a Warehouse Supervisor');

        $this->actingAs($staff)->post('/admin/announcements', [
            'message' => 'Now booking 2027 wedding dates.', 'link_text' => 'Plan your event', 'link_url' => '/plan-your-event',
            'ends_on' => now()->addWeek()->toDateString(),
        ])->assertSessionHas('status');

        $this->get('/')->assertSee('Now booking 2027 wedding dates.')->assertDontSee('hiring a Warehouse Supervisor');

        $this->actingAs($staff)->patch('/admin/announcements/'.Announcement::where('message', 'Now booking 2027 wedding dates.')->sole()->id);
        $this->get('/')->assertSee('hiring a Warehouse Supervisor');
    }

    public function test_announcement_link_must_be_a_site_path_or_https(): void
    {
        $this->actingAs(User::factory()->create())->post('/admin/announcements', [
            'message' => 'Bad link', 'link_text' => 'Click', 'link_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors('link_url');
    }

    /**
     * A valid event planner submission.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function quote(array $overrides = []): array
    {
        return [
            'event_type' => 'Wedding',
            'event_date' => now()->addMonths(6)->toDateString(),
            'venue' => 'Hyatt Regency Trinidad',
            'guests' => '150 – 300',
            'needs' => ['Full decor and design', 'Floral overhead'],
            'vision' => 'White florals and candlelight.',
            'name' => 'Test Bride',
            'email' => 'bride@example.com',
            'phone' => '868-555-0100',
            'contact_method' => 'WhatsApp',
            ...$overrides,
        ];
    }
}

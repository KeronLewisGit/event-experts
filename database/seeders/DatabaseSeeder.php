<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\BookedDate;
use App\Models\Enquiry;
use App\Models\Project;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@eventexpertstt.com'],
            ['name' => 'Event Experts Admin', 'password' => 'events-demo-2026'],
        );

        $this->projects();
        $this->vacancies();
        $this->announcements();
        $this->sampleDates();
        $this->sampleEnquiries();
    }

    /** Portfolio carried over from the company's Instagram posts. */
    protected function projects(): void
    {
        foreach (json_decode(file_get_contents(database_path('data/projects.json')), true) as $project) {
            $images = array_map(fn (int $n) => "images/portfolio/{$project['slug']}-{$n}.jpg", range(1, $project['images']));

            Project::updateOrCreate(['slug' => $project['slug']], [
                'title' => $project['title'],
                'category' => $project['category'],
                'event_date' => $project['event_date'],
                'venue' => $project['venue'],
                'summary' => $project['summary'],
                'body' => $project['body'],
                'cover' => array_shift($images),
                'gallery' => $images,
                'credits' => $project['credits'],
                'instagram_url' => $project['instagram_url'],
                'is_featured' => $project['is_featured'],
            ]);
        }
    }

    /** The warehouse supervisor role advertised on Instagram and Facebook. */
    protected function vacancies(): void
    {
        Vacancy::updateOrCreate(['title' => 'Warehouse Supervisor'], [
            'location' => 'Bejucal, Chaguanas',
            'summary' => 'Great events start behind the scenes. Supervise the team, manage inventory, coordinate operations and support amazing events.',
            'requirements' => implode("\n", [
                "Valid driver's licence (Heavy T an asset)",
                'Experience in warehouse operations',
                'Supervisory or team leadership experience (an asset)',
                'Knowledge of inventory management',
                'Ability to coordinate loading, unloading and dispatch',
                'Strong organisational and communication skills',
                'Able to work flexible hours, including weekends and event days',
                'Reliable and punctual',
                'Able-bodied and able to lift and move heavy items',
                'Based in or near Chaguanas or surrounding areas',
            ]),
        ]);
    }

    protected function announcements(): void
    {
        Announcement::updateOrCreate(['message' => 'We’re hiring a Warehouse Supervisor in Bejucal, Chaguanas.'], [
            'link_text' => 'See the role', 'link_url' => '/careers',
        ]);
    }

    /** Example dates so the date checker has something to show in a demo. */
    protected function sampleDates(): void
    {
        if (BookedDate::exists()) {
            return;
        }

        $saturday = now(config('site.timezone'))->next('Saturday');

        foreach ([[2, 'booked'], [3, 'limited'], [5, 'booked'], [8, 'booked'], [9, 'limited']] as [$weeks, $status]) {
            BookedDate::create([
                'date' => $saturday->copy()->addWeeks($weeks)->toDateString(),
                'status' => $status,
                'note' => 'Sample date for the demo',
            ]);
        }
    }

    /** Clearly-labelled sample submissions so the admin inbox is not empty in a demo. */
    protected function sampleEnquiries(): void
    {
        if (Enquiry::where('is_sample', true)->exists()) {
            return;
        }

        $eventDate = now(config('site.timezone'))->next('Saturday')->addWeeks(20)->toDateString();

        $samples = [
            ['quote', 'Sample Client A', 'sample.a@example.com', '868-555-0101', 'Wedding · '.date('j M Y', strtotime($eventDate)), 'new', 2,
                ['event_type' => 'Wedding', 'event_date' => $eventDate, 'venue' => 'Hotel ballroom, Port of Spain', 'guests' => '150 – 300', 'needs' => ['Full decor and design', 'Floral overhead', 'Stage or backdrop'], 'vision' => 'White and blush florals with a suspended overhead above the dance floor.', 'contact_method' => 'WhatsApp', 'heard' => 'Instagram']],
            ['quote', 'Sample Client B', 'sample.b@example.com', '868-555-0102', 'Corporate event · Date to be confirmed', 'in_progress', 22,
                ['event_type' => 'Corporate event', 'date_flexible' => 'Yes', 'venue' => 'To be confirmed', 'guests' => '300 – 500', 'needs' => ['Full decor and design', 'Event management'], 'vision' => 'Staff awards dinner. We would like a stage, entrance feature and table centrepieces.', 'contact_method' => 'Phone call', 'heard' => 'A planner or venue']],
            ['rental', 'Sample Client C', 'sample.c@example.com', '868-555-0103', 'Rental · Grass wall and two glass tables', 'new', 30,
                ['items' => 'Grass wall and two glass tables', 'message' => 'For a haldi night at home in Chaguanas.']],
            ['contact', 'Sample Client D', 'sample.d@example.com', null, 'Micro wedding', 'new', 50,
                ['topic' => 'Micro wedding', 'message' => 'Do you decorate backyard weddings for about 40 guests?']],
            ['career', 'Sample Applicant E', 'sample.e@example.com', '868-555-0105', 'Warehouse Supervisor', 'new', 75,
                ['role' => 'Warehouse Supervisor', 'message' => 'Five years of warehouse experience, Heavy T licence.']],
            ['quote', 'Sample Client F', 'sample.f@example.com', '868-555-0106', 'Birthday or anniversary · Date to be confirmed', 'booked', 140,
                ['event_type' => 'Birthday or anniversary', 'venue' => 'At home', 'guests' => '50 – 150', 'needs' => ['Tables and tablescapes', 'Stage or backdrop'], 'contact_method' => 'WhatsApp']],
        ];

        foreach ($samples as [$type, $name, $email, $phone, $summary, $status, $hoursAgo, $data]) {
            $enquiry = Enquiry::create([
                'type' => $type, 'name' => $name, 'email' => $email, 'phone' => $phone,
                'summary' => $summary, 'status' => $status, 'data' => $data, 'is_sample' => true,
            ]);
            $enquiry->forceFill(['created_at' => now()->subHours($hoursAgo), 'updated_at' => now()->subHours($hoursAgo)])->saveQuietly();
        }
    }
}

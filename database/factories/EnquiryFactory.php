<?php

namespace Database\Factories;

use App\Models\Enquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enquiry>
 */
class EnquiryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => 'contact',
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'summary' => 'General enquiry',
            'data' => ['topic' => 'General enquiry', 'message' => fake()->sentence()],
            'status' => 'new',
        ];
    }

    /** An event quote request for a specific date. */
    public function quote(?string $eventDate = null): static
    {
        return $this->state(fn () => [
            'type' => 'quote',
            'summary' => 'Wedding',
            'data' => ['event_type' => 'Wedding', 'event_date' => $eventDate ?? now()->addMonths(6)->toDateString(), 'guests' => '50 – 150'],
        ]);
    }
}

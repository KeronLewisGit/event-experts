<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title),
            'title' => Str::title($title),
            'category' => 'wedding',
            'event_date' => fake()->dateTimeBetween('-2 years')->format('Y-m-d'),
            'summary' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'cover' => 'images/portfolio/dillon-kristal-1.jpg',
            'gallery' => [],
            'credits' => [],
            'is_featured' => false,
            'is_published' => true,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}

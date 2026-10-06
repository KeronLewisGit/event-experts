<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'slug', 'title', 'category', 'event_date', 'venue', 'summary', 'body',
        'cover', 'gallery', 'credits', 'instagram_url', 'is_featured', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'gallery' => 'array',
            'credits' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Projects visible on the website, newest event first. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderByDesc('event_date')->orderByDesc('id');
    }

    public function categoryLabel(): string
    {
        return config("site.categories.{$this->category}", Str::headline($this->category));
    }

    /** Cover first, then the rest of the gallery, as public paths. */
    public function images(): array
    {
        return array_values(array_unique(array_filter([$this->cover, ...($this->gallery ?? [])])));
    }

    /** Images uploaded through the admin, as opposed to the ones shipped with the site. */
    public function uploadedImages(): array
    {
        return array_values(array_filter($this->images(), fn (string $path) => str_starts_with($path, 'uploads/')));
    }

    /** Only the year is shown publicly; the exact day is for ordering. */
    public function dateLabel(): ?string
    {
        return $this->event_date?->format('Y');
    }

    /** Credits as "Role: Name" lines, the format the admin form edits. */
    public function creditsText(): string
    {
        return collect($this->credits ?? [])->map(fn (array $credit) => $credit[0].': '.$credit[1])->implode("\n");
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    public static function parseCredits(?string $text): array
    {
        return collect(preg_split('/\R/', (string) $text))
            ->map(fn (string $line) => array_map('trim', explode(':', $line, 2)))
            ->filter(fn (array $parts) => count($parts) === 2 && $parts[0] !== '' && $parts[1] !== '')
            ->values()
            ->all();
    }
}

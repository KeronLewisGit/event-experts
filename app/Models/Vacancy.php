<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $fillable = ['title', 'location', 'summary', 'requirements', 'is_open'];

    protected function casts(): array
    {
        return ['is_open' => 'boolean'];
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_open', true)->latest('id');
    }

    /**
     * Requirements are stored one per line.
     *
     * @return list<string>
     */
    public function requirementList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->requirements))));
    }
}

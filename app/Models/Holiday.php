<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'from_date',
        'to_date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'date',
            'to_date' => 'date',
        ];
    }

    public function scopeOverlappingRange(Builder $query, string $fromDate, string $toDate): Builder
    {
        return $query
            ->whereDate('from_date', '<=', $toDate)
            ->whereDate('to_date', '>=', $fromDate);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SalaryAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_type',
        'staff_id',
        'amount',
        'date',
        'note',
        'approved_by_type',
        'approved_by_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function staff(): MorphTo
    {
        return $this->morphTo();
    }

    public function approver(): MorphTo
    {
        return $this->morphTo('approved_by');
    }

    public function user(): MorphTo
    {
        return $this->staff();
    }
}

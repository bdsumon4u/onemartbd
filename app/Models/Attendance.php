<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_type',
        'staff_id',
        'date',
        'check_in',
        'check_out',
        'is_off_day',
        'overtime_minutes',
        'extra_overtime_minutes',
        'late_minutes',
        'penalty_amount',
        'auto_checkout',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in' => 'datetime',
            'check_out' => 'datetime',
            'is_off_day' => 'boolean',
            'auto_checkout' => 'boolean',
            'penalty_amount' => 'decimal:2',
        ];
    }

    public function staff(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): MorphTo
    {
        return $this->staff();
    }
}

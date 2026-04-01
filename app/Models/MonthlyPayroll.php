<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'total_days',
        'working_days',
        'present_days',
        'absent_days',
        'off_day_presents',
        'daily_salary',
        'base_salary',
        'off_day_bonus',
        'overtime_amount',
        'late_deduction',
        'penalty_amount',
        'hazira_bonus_amount',
        'occasional_bonus_amount',
        'xsell_bonus_amount',
        'advance_deduction',
        'net_salary',
        'status',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'daily_salary' => 'decimal:2',
            'base_salary' => 'decimal:2',
            'off_day_bonus' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'late_deduction' => 'decimal:2',
            'penalty_amount' => 'decimal:2',
            'hazira_bonus_amount' => 'decimal:2',
            'occasional_bonus_amount' => 'decimal:2',
            'xsell_bonus_amount' => 'decimal:2',
            'advance_deduction' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}

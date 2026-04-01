<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MonthlyPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_type',
        'staff_id',
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
        'generated_by_type',
        'generated_by_id',
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

    public function staff(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): MorphTo
    {
        return $this->staff();
    }

    public function generator(): MorphTo
    {
        return $this->morphTo('generated_by');
    }
}

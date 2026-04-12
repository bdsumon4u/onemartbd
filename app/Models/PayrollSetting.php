<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'overtime_rate',
        'overtime_unit_minutes',
        'latetime_rate',
        'latetime_unit_minutes',
        'off_day_salary_boost',
        'forgot_checkout_penalty',
        'allow_self_checkout',
        'hazira_bonus',
        'xsell_bonus_rate',
    ];

    protected function casts(): array
    {
        return [
            'overtime_rate' => 'decimal:2',
            'latetime_rate' => 'decimal:2',
            'off_day_salary_boost' => 'decimal:2',
            'forgot_checkout_penalty' => 'decimal:2',
            'hazira_bonus' => 'decimal:2',
            'xsell_bonus_rate' => 'decimal:2',
            'allow_self_checkout' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'overtime_rate' => 50,
            'overtime_unit_minutes' => 60,
            'latetime_rate' => 0,
            'latetime_unit_minutes' => 60,
            'off_day_salary_boost' => 1.5,
            'forgot_checkout_penalty' => 100,
            'allow_self_checkout' => true,
            'hazira_bonus' => 500,
            'xsell_bonus_rate' => 5,
        ]);
    }
}

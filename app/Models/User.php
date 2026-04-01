<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const DEFAULT_START_TIME = '10:00:00';

    public const DEFAULT_END_TIME = '20:00:00';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'status',
        'role',
        'start_time',
        'end_time',
        'panel_start',
        'panel_end',
        'order_start',
        'order_end',
        'monthly_salary',
        'off_days',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'monthly_salary' => 'decimal:2',
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function salaryAdvances(): HasMany
    {
        return $this->hasMany(SalaryAdvance::class);
    }

    public function monthlyPayrolls(): HasMany
    {
        return $this->hasMany(MonthlyPayroll::class);
    }

    public function userBonuses(): HasMany
    {
        return $this->hasMany(UserBonus::class);
    }

    public function getOffDaysArray(): array
    {
        if (! $this->off_days) {
            return [];
        }

        return collect(explode(',', $this->off_days))
            ->map(fn (string $day): string => trim($day))
            ->filter()
            ->values()
            ->all();
    }

    public function isOffDay(CarbonInterface|string|null $date = null): bool
    {
        $workingDate = $date ? Carbon::parse($date) : now();
        $dayName = $workingDate->format('l');

        return in_array($dayName, $this->getOffDaysArray(), true);
    }

    public function todayAttendance(): ?Attendance
    {
        return $this->attendances()->whereDate('date', now()->toDateString())->first();
    }

    public function isCheckedInToday(): bool
    {
        $attendance = $this->todayAttendance();

        return (bool) ($attendance && $attendance->check_in && ! $attendance->check_out);
    }

    public function getScheduleStartTime(): string
    {
        return $this->start_time ?: self::DEFAULT_START_TIME;
    }

    public function getScheduleEndTime(): string
    {
        return $this->end_time ?: self::DEFAULT_END_TIME;
    }

    // Backward-compatible accessor
    public function get_orders(): HasMany
    {
        return $this->orders();
    }
}

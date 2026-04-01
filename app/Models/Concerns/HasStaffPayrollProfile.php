<?php

namespace App\Models\Concerns;

use App\Models\Attendance;
use App\Models\MonthlyPayroll;
use App\Models\SalaryAdvance;
use App\Models\UserBonus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

trait HasStaffPayrollProfile
{
    public function attendances(): MorphMany
    {
        return $this->morphMany(Attendance::class, 'staff');
    }

    public function salaryAdvances(): MorphMany
    {
        return $this->morphMany(SalaryAdvance::class, 'staff');
    }

    public function monthlyPayrolls(): MorphMany
    {
        return $this->morphMany(MonthlyPayroll::class, 'staff');
    }

    public function userBonuses(): MorphMany
    {
        return $this->morphMany(UserBonus::class, 'staff');
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
        return $this->start_time ?: config('attendance.default_start_time');
    }

    public function getScheduleEndTime(): string
    {
        return $this->end_time ?: config('attendance.default_end_time');
    }
}

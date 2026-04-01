<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\PayrollSetting;
use App\Services\AttendanceCalculationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AttendanceAutoCheckoutCommand extends Command
{
    protected $signature = 'attendance:auto-checkout';

    protected $description = 'Auto checkout open attendances at scheduled end time with penalty';

    public function __construct(private AttendanceCalculationService $attendanceCalculationService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $settings = PayrollSetting::current();

        $attendances = Attendance::query()
            ->with('user')
            ->whereDate('date', now()->toDateString())
            ->where('status', 'present')
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->get();

        foreach ($attendances as $attendance) {
            if (! $attendance->user) {
                continue;
            }

            $date = Carbon::parse($attendance->date);
            [$startDateTime, $endDateTime] = $this->attendanceCalculationService->scheduledDateTimes($attendance->user, $date);

            if (now()->lt($endDateTime)) {
                continue;
            }

            $checkIn = Carbon::parse($attendance->check_in);
            $attendance->check_out = $endDateTime;
            $attendance->auto_checkout = true;
            $attendance->penalty_amount = $settings->forgot_checkout_penalty;
            $attendance->overtime_minutes = 0;
            $attendance->late_minutes = 0;

            if ($checkIn->lt($startDateTime)) {
                $attendance->overtime_minutes += $checkIn->diffInMinutes($startDateTime);
            }

            if ($checkIn->gt($startDateTime)) {
                $attendance->late_minutes += $startDateTime->diffInMinutes($checkIn);
            }

            $attendance->save();
        }

        $this->info('Auto-checkout run completed.');

        return self::SUCCESS;
    }
}

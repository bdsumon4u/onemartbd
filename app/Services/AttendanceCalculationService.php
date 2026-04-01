<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceCalculationService
{
    /**
     * @return array{overtime_minutes:int,late_minutes:int,worked_minutes:int,scheduled_minutes:int}
     */
    public function calculateOffset(User $user, Carbon $checkIn, Carbon $checkOut): array
    {
        $workMinutes = max(0, $checkIn->diffInMinutes($checkOut, false));

        [$startDateTime, $endDateTime] = $this->scheduledDateTimes($user, $checkIn);
        $scheduledMinutes = max(0, $startDateTime->diffInMinutes($endDateTime, false));

        $offset = $workMinutes - $scheduledMinutes;

        if ($offset > 0) {
            return [
                'overtime_minutes' => $offset,
                'late_minutes' => 0,
                'worked_minutes' => $workMinutes,
                'scheduled_minutes' => $scheduledMinutes,
            ];
        }

        if ($offset < 0) {
            return [
                'overtime_minutes' => 0,
                'late_minutes' => abs($offset),
                'worked_minutes' => $workMinutes,
                'scheduled_minutes' => $scheduledMinutes,
            ];
        }

        return [
            'overtime_minutes' => 0,
            'late_minutes' => 0,
            'worked_minutes' => $workMinutes,
            'scheduled_minutes' => $scheduledMinutes,
        ];
    }

    public function applyOffsetToAttendance(Attendance $attendance, User $user, Carbon $checkIn, Carbon $checkOut): void
    {
        $calculation = $this->calculateOffset($user, $checkIn, $checkOut);

        $attendance->overtime_minutes = $calculation['overtime_minutes'];
        $attendance->late_minutes = $calculation['late_minutes'];
    }

    /**
     * @return array{0:Carbon,1:Carbon}
     */
    public function scheduledDateTimes(User $user, Carbon $referenceDate): array
    {
        $startDateTime = Carbon::parse($referenceDate->toDateString().' '.$user->getScheduleStartTime());
        $endDateTime = Carbon::parse($referenceDate->toDateString().' '.$user->getScheduleEndTime());

        if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
            $endDateTime->addDay();
        }

        return [$startDateTime, $endDateTime];
    }
}

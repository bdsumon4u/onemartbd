<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Manager;
use Carbon\Carbon;

class AttendanceCalculationService
{
    /**
     * @return array{overtime_minutes:int,late_minutes:int,worked_minutes:int,scheduled_minutes:int}
     */
    public function calculateOffset(Admin|Manager|Employee $staff, Carbon $checkIn, Carbon $checkOut): array
    {
        $workMinutes = max(0, $checkIn->diffInMinutes($checkOut, false));

        [$startDateTime, $endDateTime] = $this->scheduledDateTimes($staff, $checkIn);
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

    public function applyOffsetToAttendance(Attendance $attendance, Admin|Manager|Employee $staff, Carbon $checkIn, Carbon $checkOut): void
    {
        $calculation = $this->calculateOffset($staff, $checkIn, $checkOut);

        $attendance->overtime_minutes = $calculation['overtime_minutes'];
        $attendance->late_minutes = $calculation['late_minutes'];
    }

    /**
     * @return array{0:Carbon,1:Carbon}
     */
    public function scheduledDateTimes(Admin|Manager|Employee $staff, Carbon $referenceDate): array
    {
        $startDateTime = Carbon::parse($referenceDate->toDateString().' '.$staff->getScheduleStartTime());
        $endDateTime = Carbon::parse($referenceDate->toDateString().' '.$staff->getScheduleEndTime());

        if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
            $endDateTime->addDay();
        }

        return [$startDateTime, $endDateTime];
    }
}

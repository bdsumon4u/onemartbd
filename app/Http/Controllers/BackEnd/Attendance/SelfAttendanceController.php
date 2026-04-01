<?php

namespace App\Http\Controllers\BackEnd\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PayrollSetting;
use App\Services\AttendanceCalculationService;
use App\Services\StaffUserResolver;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SelfAttendanceController extends Controller
{
    public function __construct(
        private StaffUserResolver $staffUserResolver,
        private AttendanceCalculationService $attendanceCalculationService,
    ) {}

    public function toggle(): JsonResponse
    {
        $user = $this->staffUserResolver->resolveAuthenticatedStaffUser();

        if (! $user) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        $attendance = Attendance::query()->firstOrCreate(
            [
                'staff_type' => $user->getMorphClass(),
                'staff_id' => (int) $user->getAuthIdentifier(),
                'date' => now()->toDateString(),
            ],
            [
                'check_in' => now(),
                'status' => 'present',
                'is_off_day' => $user->isOffDay(),
            ]
        );

        if ($attendance->wasRecentlyCreated) {
            $settings = PayrollSetting::current();

            return response()->json([
                'status' => 'checked_in',
                ...$this->buildAttendanceStatePayload($attendance, $settings),
            ]);
        }

        if ($attendance->check_in && ! $attendance->check_out) {
            $settings = PayrollSetting::current();

            if (! $settings->allow_self_checkout) {
                return response()->json([
                    'status' => 'checkout_disabled',
                    ...$this->buildAttendanceStatePayload($attendance, $settings),
                ], 422);
            }

            $attendance->check_out = now();
            $this->attendanceCalculationService->applyOffsetToAttendance(
                $attendance,
                $user,
                Carbon::parse($attendance->check_in),
                Carbon::parse($attendance->check_out),
            );
            $attendance->save();

            return response()->json([
                'status' => 'checked_out',
                ...$this->buildAttendanceStatePayload($attendance, $settings),
            ]);
        }

        $settings = PayrollSetting::current();

        return response()->json([
            'status' => 'already_done',
            ...$this->buildAttendanceStatePayload($attendance, $settings),
        ]);
    }

    public function status(): JsonResponse
    {
        $user = $this->staffUserResolver->resolveAuthenticatedStaffUser();

        if (! $user) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        $attendance = $user->todayAttendance();
        $settings = PayrollSetting::current();

        return response()->json($this->buildAttendanceStatePayload($attendance, $settings));
    }

    public function myAttendance(Request $request): View
    {
        $user = $this->staffUserResolver->resolveAuthenticatedStaffUser();
        abort_unless($user, 403);

        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);

        $attendances = Attendance::query()
            ->where('staff_type', $user->getMorphClass())
            ->where('staff_id', (int) $user->getAuthIdentifier())
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->latest('date')
            ->paginate(25)
            ->withQueryString();

        $panel = $this->panelSlug();

        return view("backEnd.{$panel}.attendance.my", compact('user', 'attendances', 'month', 'year'));
    }

    private function panelSlug(): string
    {
        if (auth('admin')->check()) {
            return 'admin';
        }

        if (auth('manager')->check()) {
            return 'manager';
        }

        return 'employee';
    }

    private function buildAttendanceStatePayload(?Attendance $attendance, PayrollSetting $settings): array
    {
        return [
            'has_attendance' => (bool) $attendance,
            'is_checked_in' => (bool) ($attendance?->check_in),
            'is_checked_out' => (bool) ($attendance?->check_out),
            'check_in' => $attendance?->check_in ? Carbon::parse($attendance->check_in)->format('h:i A') : null,
            'check_out' => $attendance?->check_out ? Carbon::parse($attendance->check_out)->format('h:i A') : null,
            'allow_self_checkout' => (bool) $settings->allow_self_checkout,
        ];
    }
}

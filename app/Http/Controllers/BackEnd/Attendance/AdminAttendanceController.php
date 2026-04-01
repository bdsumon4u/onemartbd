<?php

namespace App\Http\Controllers\BackEnd\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Manager;
use App\Services\AttendanceCalculationService;
use App\Services\StaffUserResolver;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminAttendanceController extends Controller
{
    public function __construct(
        private AttendanceCalculationService $attendanceCalculationService,
        private StaffUserResolver $staffUserResolver,
    ) {}

    public function index(Request $request): View
    {
        $date = $request->string('date')->toString() ?: now()->toDateString();

        $users = $this->staffUsersCollection();
        $attendances = Attendance::query()
            ->whereDate('date', $date)
            ->get()
            ->keyBy(fn (Attendance $attendance): string => $attendance->staff_type.':'.$attendance->staff_id);

        return view('backEnd.admin.attendance.index', compact('users', 'attendances', 'date'));
    }

    public function history(Request $request): View
    {
        $users = $this->staffUsersCollection();

        $query = Attendance::query()->with('staff')->latest('date');

        if ($request->filled('staff_key')) {
            $staff = $this->resolveStaffFromRequest($request, 'staff_key');

            if ($staff) {
                $query
                    ->where('staff_type', $staff->getMorphClass())
                    ->where('staff_id', (int) $staff->getAuthIdentifier());
            }
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', (int) $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', (int) $request->year);
        }

        $attendances = $query->paginate(25)->withQueryString();

        return view('backEnd.admin.attendance.history', compact('attendances', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_key' => ['required', 'string'],
            'date' => ['required', 'date'],
            'check_in' => ['required', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'note' => ['nullable', 'string'],
        ]);

        $user = $this->resolveStaffByKeyOrFail($validated['staff_key']);

        $exists = Attendance::query()
            ->where('staff_type', $user->getMorphClass())
            ->where('staff_id', (int) $user->getAuthIdentifier())
            ->whereDate('date', $validated['date'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Attendance already exists for that date.');
        }

        $checkIn = Carbon::parse($validated['date'].' '.$validated['check_in']);
        $checkOut = ! empty($validated['check_out'])
            ? Carbon::parse($validated['date'].' '.$validated['check_out'])
            : null;

        $attendance = Attendance::query()->create([
            'staff_type' => $user->getMorphClass(),
            'staff_id' => (int) $user->getAuthIdentifier(),
            'date' => $validated['date'],
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => 'present',
            'is_off_day' => $user->isOffDay(Carbon::parse($validated['date'])),
            'note' => $validated['note'] ?? null,
        ]);

        if ($checkOut) {
            $this->attendanceCalculationService->applyOffsetToAttendance($attendance, $user, $checkIn, $checkOut);
            $attendance->save();
        }

        return back()->with('success', 'Attendance added successfully.');
    }

    public function manualCheckIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_key' => ['required', 'string'],
            'date' => ['required', 'date'],
            'check_in' => ['nullable', 'date_format:H:i'],
        ]);

        $user = $this->resolveStaffByKeyOrFail($validated['staff_key']);
        $checkIn = Carbon::parse($validated['date'].' '.($validated['check_in'] ?? now()->format('H:i')));

        Attendance::query()->updateOrCreate(
            [
                'staff_type' => $user->getMorphClass(),
                'staff_id' => (int) $user->getAuthIdentifier(),
                'date' => $validated['date'],
            ],
            [
                'check_in' => $checkIn,
                'status' => 'present',
                'is_off_day' => $user->isOffDay(Carbon::parse($validated['date'])),
            ]
        );

        return back()->with('success', 'Manual check-in completed.');
    }

    public function manualCheckOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_key' => ['required', 'string'],
            'date' => ['required', 'date'],
            'check_out' => ['nullable', 'date_format:H:i'],
        ]);

        $user = $this->resolveStaffByKeyOrFail($validated['staff_key']);

        $attendance = Attendance::query()
            ->where('staff_type', $user->getMorphClass())
            ->where('staff_id', (int) $user->getAuthIdentifier())
            ->whereDate('date', $validated['date'])
            ->first();

        if (! $attendance || ! $attendance->check_in) {
            return back()->with('error', 'Open attendance with check-in is required for manual check-out.');
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = ! empty($validated['check_out'])
            ? Carbon::parse($validated['date'].' '.$validated['check_out'])
            : now();

        $attendance->check_out = $checkOut;
        $this->attendanceCalculationService->applyOffsetToAttendance($attendance, $user, $checkIn, $checkOut);
        $attendance->save();

        return back()->with('success', 'Manual check-out completed.');
    }

    public function markAbsent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_key' => ['required', 'string'],
            'date' => ['required', 'date'],
        ]);

        $user = $this->resolveStaffByKeyOrFail($validated['staff_key']);

        Attendance::query()->updateOrCreate(
            [
                'staff_type' => $user->getMorphClass(),
                'staff_id' => (int) $user->getAuthIdentifier(),
                'date' => $validated['date'],
            ],
            [
                'status' => 'absent',
                'check_in' => null,
                'check_out' => null,
                'overtime_minutes' => 0,
                'late_minutes' => 0,
            ]
        );

        return back()->with('success', 'Attendance marked as absent.');
    }

    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'penalty_amount' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'extra_overtime_minutes' => ['nullable', 'integer', 'min:0'],
            'auto_checkout' => ['nullable', 'boolean'],
        ]);

        $date = Carbon::parse($attendance->date)->toDateString();

        $attendance->check_in = array_key_exists('check_in', $validated) && $validated['check_in']
            ? Carbon::parse($date.' '.$validated['check_in'])
            : null;

        $attendance->check_out = array_key_exists('check_out', $validated) && $validated['check_out']
            ? Carbon::parse($date.' '.$validated['check_out'])
            : null;

        if (array_key_exists('penalty_amount', $validated)) {
            $attendance->penalty_amount = $validated['penalty_amount'] ?? 0;
        }

        if (array_key_exists('extra_overtime_minutes', $validated)) {
            $attendance->extra_overtime_minutes = $validated['extra_overtime_minutes'] ?? 0;
        }

        if (array_key_exists('auto_checkout', $validated)) {
            $attendance->auto_checkout = (bool) $validated['auto_checkout'];
        }

        $attendance->note = $validated['note'] ?? null;

        if ($attendance->check_in && $attendance->check_out && $attendance->staff) {
            $this->attendanceCalculationService->applyOffsetToAttendance(
                $attendance,
                $attendance->staff,
                Carbon::parse($attendance->check_in),
                Carbon::parse($attendance->check_out),
            );
        } else {
            $attendance->overtime_minutes = 0;
            $attendance->late_minutes = 0;
        }

        $attendance->save();

        return back()->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $attendance->delete();

        return back()->with('success', 'Attendance deleted successfully.');
    }

    public function printDaily(Request $request): View
    {
        $date = $request->string('date')->toString() ?: now()->toDateString();
        $users = $this->staffUsersCollection();
        $attendances = Attendance::query()
            ->whereDate('date', $date)
            ->get()
            ->keyBy(fn (Attendance $attendance): string => $attendance->staff_type.':'.$attendance->staff_id);

        return view('backEnd.admin.attendance.print-daily', compact('users', 'attendances', 'date'));
    }

    public function printMonthly(Request $request): View
    {
        $validated = $request->validate([
            'staff_key' => ['required', 'string'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2020,2100'],
        ]);

        $user = $this->resolveStaffByKeyOrFail($validated['staff_key']);
        $attendances = Attendance::query()
            ->where('staff_type', $user->getMorphClass())
            ->where('staff_id', (int) $user->getAuthIdentifier())
            ->whereMonth('date', (int) $validated['month'])
            ->whereYear('date', (int) $validated['year'])
            ->orderBy('date')
            ->get();

        $summary = [
            'present_days' => $attendances->where('status', 'present')->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'off_day_work' => $attendances->where('is_off_day', true)->where('status', 'present')->count(),
            'overtime_minutes' => (int) $attendances->sum('overtime_minutes'),
            'extra_overtime_minutes' => (int) $attendances->sum('extra_overtime_minutes'),
            'late_minutes' => (int) $attendances->sum('late_minutes'),
            'penalties' => (float) $attendances->sum('penalty_amount'),
        ];

        return view('backEnd.admin.attendance.print-monthly', [
            'user' => $user,
            'attendances' => $attendances,
            'month' => (int) $validated['month'],
            'year' => (int) $validated['year'],
            'summary' => $summary,
        ]);
    }

    /**
     * @return Collection<int,Admin|Manager|Employee>
     */
    private function staffUsersCollection(): Collection
    {
        return $this->staffUserResolver->allActiveStaff();
    }

    private function resolveStaffFromRequest(Request $request, string $key): Admin|Manager|Employee|null
    {
        $staffKey = (string) $request->input($key, '');

        if ($staffKey === '') {
            return null;
        }

        return $this->staffUserResolver->resolveByStaffKey($staffKey);
    }

    private function resolveStaffByKeyOrFail(string $staffKey): Admin|Manager|Employee
    {
        $staff = $this->staffUserResolver->resolveByStaffKey($staffKey);
        abort_unless($staff, 422, 'Invalid staff selected.');

        return $staff;
    }
}

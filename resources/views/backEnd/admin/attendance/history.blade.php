@extends('backEnd.admin.layouts.master')

@section('title', 'Attendance History')

@section('body')
    <div class="dashboard-wrapper">
        <div class="container-fluid dashboard-content">
            <div class="page-header">
                <h2 class="pageheader-title">Attendance History</h2>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="form-row align-items-end">
                        <div class="col-md-4 mb-2">
                            <label>User</label>
                            <select name="staff_key" class="form-control">
                                <option value="">All Staff</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->staff_key }}" @selected((string) request('staff_key') === (string) $user->staff_key)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Month</label>
                            <select name="month" class="form-control">
                                <option value="">All Months</option>
                                @foreach (range(1, 12) as $monthNumber)
                                    <option value="{{ $monthNumber }}" @selected((int) request('month') === $monthNumber)>
                                        {{ \Carbon\Carbon::create()->month($monthNumber)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Year</label>
                            <input type="number" class="form-control" name="year" min="2020" max="2100"
                                value="{{ request('year') }}" placeholder="{{ now()->year }}">
                        </div>
                        <div class="col-md-2 mb-2 d-flex" style="gap:6px;">
                            <button class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.attendance.history') }}"
                                class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>OT</th>
                                    <th>Extra OT</th>
                                    <th>Late</th>
                                    <th>Penalty</th>
                                    <th>Off Day</th>
                                    <th>Auto Out</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendances as $index => $attendance)
                                    <tr>
                                        <td>{{ $attendances->firstItem() + $index }}</td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                                        <td>{{ $attendance->staff?->name ?? '-' }}</td>
                                        <td>{{ $attendance->status }}</td>
                                        <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}
                                        </td>
                                        <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}
                                        </td>
                                        <td>{{ (int) ($attendance->overtime_minutes ?? 0) }}</td>
                                        <td>{{ (int) ($attendance->extra_overtime_minutes ?? 0) }}</td>
                                        <td>{{ (int) ($attendance->late_minutes ?? 0) }}</td>
                                        <td>{{ number_format((float) ($attendance->penalty_amount ?? 0), 2) }}</td>
                                        <td>{{ $attendance->is_off_day ? 'Yes' : 'No' }}</td>
                                        <td>{{ $attendance->auto_checkout ? 'Yes' : 'No' }}</td>
                                        <td>{{ $attendance->note ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center">No attendance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $attendances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

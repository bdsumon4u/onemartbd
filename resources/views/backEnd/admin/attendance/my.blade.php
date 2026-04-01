@extends('backEnd.admin.layouts.master')
@section('title', 'My Attendance')
@section('body')
    <div class="dashboard-wrapper">
        <div class="container-fluid dashboard-content">
            <div class="page-header">
                <h2 class="pageheader-title">My Attendance</h2>
            </div>

            <div class="card mb-3">
                <div class="card-body d-flex flex-wrap align-items-center" style="gap: 10px;">
                    <button class="btn btn-primary btn-sm" id="attendanceToggle">Toggle Check In/Out</button>
                    <div id="attendanceStatus" class="text-muted"></div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="form-inline" style="gap: 8px;">
                        <select name="month" class="form-control">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @selected((int) $month === $m)>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                        <input type="number" name="year" min="2020" max="2100" class="form-control"
                            value="{{ $year }}">
                        <button class="btn btn-secondary">Filter</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Overtime (min)</th>
                                <th>Late (min)</th>
                                <th>Penalty</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('Y-m-d') }}</td>
                                    <td>
                                        {{ ucfirst($attendance->status) }}
                                        @if ($attendance->auto_checkout)
                                            <span class="badge badge-warning">Auto-out</span>
                                        @endif
                                        @if ($attendance->is_off_day)
                                            <span class="badge badge-info">Off-day</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}
                                    </td>
                                    <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}
                                    </td>
                                    <td>{{ (int) $attendance->overtime_minutes + (int) $attendance->extra_overtime_minutes }}
                                    </td>
                                    <td>{{ (int) $attendance->late_minutes }}</td>
                                    <td>{{ number_format((float) $attendance->penalty_amount, 2) }}</td>
                                    <td>{{ $attendance->note ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No attendance found for selected
                                        month.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        (function() {
            const statusBox = document.getElementById('attendanceStatus');
            const toggleBtn = document.getElementById('attendanceToggle');

            if (!statusBox || !toggleBtn) {
                return;
            }

            const statusUrl = @json(route('admin.my_attendance.status'));
            const toggleUrl = @json(route('admin.my_attendance.toggle'));

            const renderStatus = (data) => {
                statusBox.innerHTML =
                    `Checked In: ${data.is_checked_in ? 'Yes' : 'No'} | Checked Out: ${data.is_checked_out ? 'Yes' : 'No'} | In: ${data.check_in ?? '-'} | Out: ${data.check_out ?? '-'}`;
            };

            const refresh = async () => {
                try {
                    const response = await fetch(statusUrl, {
                        headers: {
                            Accept: 'application/json',
                        },
                    });
                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();
                    renderStatus(data);
                } catch (error) {
                    console.error(error);
                }
            };

            toggleBtn.addEventListener('click', async () => {
                try {
                    await fetch(toggleUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            Accept: 'application/json',
                        },
                    });

                    await refresh();
                } catch (error) {
                    console.error(error);
                }
            });

            refresh();
        })();
    </script>
@endsection

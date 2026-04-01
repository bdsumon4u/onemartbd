@extends('backEnd.admin.layouts.master')

@section('title', 'Attendance History')

@section('body')
    <div class="dashboard-wrapper"><div class="container-fluid dashboard-content">
        <div class="page-header"><h2 class="pageheader-title">Attendance History</h2></div>
        <div class="card"><div class="card-body">
            <form class="form-inline mb-3" method="GET">
                <select class="form-control mr-2" name="user_id"><option value="">All Users</option>@foreach ($users as $user)<option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>@endforeach</select>
                <input type="number" min="1" max="12" name="month" class="form-control mr-2" placeholder="Month" value="{{ request('month') }}">
                <input type="number" min="2020" max="2100" name="year" class="form-control mr-2" placeholder="Year" value="{{ request('year') }}">
                <button class="btn btn-primary">Filter</button>
            </form>
            <div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Date</th><th>User</th><th>Status</th><th>In</th><th>Out</th><th>OT</th><th>Extra OT</th><th>Late</th><th>Penalty</th><th>Off Day</th><th>Auto</th></tr></thead><tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->date->format('Y-m-d') }}</td>
                    <td>{{ $attendance->user?->name }}</td>
                    <td>{{ $attendance->status }}</td>
                    <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}</td>
                    <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}</td>
                    <td>{{ $attendance->overtime_minutes }}</td>
                    <td>{{ $attendance->extra_overtime_minutes }}</td>
                    <td>{{ $attendance->late_minutes }}</td>
                    <td>{{ number_format((float) $attendance->penalty_amount, 2) }}</td>
                    <td>{{ $attendance->is_off_day ? 'Yes' : 'No' }}</td>
                    <td>{{ $attendance->auto_checkout ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
            </tbody></table></div>
            {{ $attendances->links() }}
        </div></div>
    </div></div>
@endsection

@extends('backEnd.admin.layouts.master')

@section('title', 'Attendance Management')

@section('body')
    <div class="dashboard-wrapper">
        <div class="container-fluid dashboard-content">
            <div class="page-header">
                <h2 class="pageheader-title">Attendance Daily Management</h2>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-2">
                        <div class="card-body">
                            <form method="GET" class="form-inline mb-3">
                                <input type="date" name="date" value="{{ $date }}" class="form-control mr-2" />
                                <button class="btn btn-primary">Filter</button>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Check In</th>
                                            <th>Check Out</th>
                                            <th>OT</th>
                                            <th>Extra OT</th>
                                            <th>Late</th>
                                            <th>Penalty</th>
                                            <th>Off Day</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            @php($row = $attendances[$user->id] ?? null)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $row?->status ?? 'absent' }}</td>
                                                <td>{{ $row?->check_in ? \Carbon\Carbon::parse($row->check_in)->format('h:i A') : '-' }}</td>
                                                <td>{{ $row?->check_out ? \Carbon\Carbon::parse($row->check_out)->format('h:i A') : '-' }}</td>
                                                <td>{{ $row?->overtime_minutes ?? 0 }}</td>
                                                <td>{{ $row?->extra_overtime_minutes ?? 0 }}</td>
                                                <td>{{ $row?->late_minutes ?? 0 }}</td>
                                                <td>{{ number_format((float) ($row?->penalty_amount ?? 0), 2) }}</td>
                                                <td>{{ $row?->is_off_day ? 'Yes' : 'No' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card mb-2">
                        <div class="card-header">Manual Attendance Add</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.attendance.store') }}">
                                @csrf
                                <div class="form-group"><label>User</label><select class="form-control" name="user_id">@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                                <div class="form-group"><label>Date</label><input type="date" class="form-control" name="date" required></div>
                                <div class="form-group"><label>Check In</label><input type="time" class="form-control" name="check_in" required></div>
                                <div class="form-group"><label>Check Out</label><input type="time" class="form-control" name="check_out"></div>
                                <div class="form-group"><label>Note</label><textarea class="form-control" name="note"></textarea></div>
                                <button class="btn btn-success">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card mb-2">
                        <div class="card-header">Quick Actions</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.attendance.manual_checkin') }}" class="mb-2">@csrf
                                <div class="form-row align-items-center">
                                    <div class="col"><select class="form-control" name="user_id">@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                                    <div class="col"><input type="date" class="form-control" name="date" required></div>
                                    <div class="col"><input type="time" class="form-control" name="check_in"></div>
                                    <div class="col"><button class="btn btn-primary btn-block">Manual Check-in</button></div>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('admin.attendance.manual_checkout') }}" class="mb-2">@csrf
                                <div class="form-row align-items-center">
                                    <div class="col"><select class="form-control" name="user_id">@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                                    <div class="col"><input type="date" class="form-control" name="date" required></div>
                                    <div class="col"><input type="time" class="form-control" name="check_out"></div>
                                    <div class="col"><button class="btn btn-info btn-block">Manual Check-out</button></div>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('admin.attendance.mark_absent') }}">@csrf
                                <div class="form-row align-items-center">
                                    <div class="col"><select class="form-control" name="user_id">@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                                    <div class="col"><input type="date" class="form-control" name="date" required></div>
                                    <div class="col"><button class="btn btn-danger btn-block">Mark Absent</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

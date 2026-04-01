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
                                <input type="date" name="date" value="{{ $date }}"
                                    class="form-control mr-2" />
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
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            @php($row = $attendances[$user->id] ?? null)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $row?->status ?? 'absent' }}</td>
                                                <td>{{ $row?->check_in ? \Carbon\Carbon::parse($row->check_in)->format('h:i A') : '-' }}
                                                </td>
                                                <td>{{ $row?->check_out ? \Carbon\Carbon::parse($row->check_out)->format('h:i A') : '-' }}
                                                </td>
                                                <td>{{ $row?->overtime_minutes ?? 0 }}</td>
                                                <td>{{ $row?->extra_overtime_minutes ?? 0 }}</td>
                                                <td>{{ $row?->late_minutes ?? 0 }}</td>
                                                <td>{{ number_format((float) ($row?->penalty_amount ?? 0), 2) }}</td>
                                                <td>{{ $row?->is_off_day ? 'Yes' : 'No' }}</td>
                                                <td>
                                                    @if ($row)
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary js-edit-attendance"
                                                            data-toggle="modal" data-target="#edit-attendance-modal"
                                                            data-update-url="{{ route('admin.attendance.update', $row) }}"
                                                            data-user-name="{{ $user->name }}"
                                                            data-date="{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}"
                                                            data-check-in="{{ $row->check_in ? \Carbon\Carbon::parse($row->check_in)->format('H:i') : '' }}"
                                                            data-check-out="{{ $row->check_out ? \Carbon\Carbon::parse($row->check_out)->format('H:i') : '' }}"
                                                            data-extra-overtime="{{ (int) ($row->extra_overtime_minutes ?? 0) }}"
                                                            data-penalty="{{ (float) ($row->penalty_amount ?? 0) }}"
                                                            data-note="{{ $row->note ?? '' }}"
                                                            data-auto-checkout="{{ $row->auto_checkout ? 1 : 0 }}">
                                                            Edit
                                                        </button>
                                                        <form method="POST"
                                                            action="{{ route('admin.attendance.delete', $row) }}"
                                                            class="d-inline"
                                                            onsubmit="return confirm('Delete this attendance record?')">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline-danger">Delete</button>
                                                        </form>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
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
                                <div class="form-group"><label>User</label><select class="form-control" name="user_id">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group"><label>Date</label><input type="date" class="form-control"
                                        name="date" value="{{ old('date', now()->toDateString()) }}" required></div>
                                <div class="form-group"><label>Check In</label><input type="time" class="form-control"
                                        name="check_in"
                                        value="{{ old('check_in', config('attendance.default_start_time')) }}" required>
                                </div>
                                <div class="form-group"><label>Check Out</label><input type="time" class="form-control"
                                        name="check_out"></div>
                                <div class="form-group"><label>Note</label>
                                    <textarea class="form-control" name="note"></textarea>
                                </div>
                                <button class="btn btn-success">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card mb-2">
                        <div class="card-header">Quick Actions</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.attendance.manual_checkin') }}" class="mb-2">
                                @csrf
                                <div class="form-row align-items-center">
                                    <div class="col"><select class="form-control" name="user_id">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col"><input type="date" class="form-control" name="date"
                                            value="{{ old('date', now()->toDateString()) }}" required></div>
                                    <div class="col"><input type="time" class="form-control" name="check_in"></div>
                                    <div class="col"><button class="btn btn-primary btn-block">Manual Check-in</button>
                                    </div>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('admin.attendance.manual_checkout') }}" class="mb-2">
                                @csrf
                                <div class="form-row align-items-center">
                                    <div class="col"><select class="form-control" name="user_id">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col"><input type="date" class="form-control" name="date"
                                            value="{{ old('date', now()->toDateString()) }}" required></div>
                                    <div class="col"><input type="time" class="form-control" name="check_out">
                                    </div>
                                    <div class="col"><button class="btn btn-info btn-block">Manual Check-out</button>
                                    </div>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('admin.attendance.mark_absent') }}">@csrf
                                <div class="form-row align-items-center">
                                    <div class="col"><select class="form-control" name="user_id">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select></div>
                                    <div class="col"><input type="date" class="form-control" name="date"
                                            value="{{ old('date', now()->toDateString()) }}" required></div>
                                    <div class="col"><button class="btn btn-danger btn-block">Mark Absent</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-attendance-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" id="edit-attendance-form" action="#">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Attendance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2"><strong id="edit-attendance-user"></strong></p>
                        <p class="text-muted mb-3" id="edit-attendance-date"></p>

                        <div class="form-group">
                            <label for="edit-check-in">Check In</label>
                            <input type="time" class="form-control" name="check_in" id="edit-check-in">
                        </div>

                        <div class="form-group">
                            <label for="edit-check-out">Check Out</label>
                            <input type="time" class="form-control" name="check_out" id="edit-check-out">
                        </div>

                        <div class="form-group">
                            <label for="edit-extra-overtime">Extra Overtime Minutes</label>
                            <input type="number" min="0" class="form-control" name="extra_overtime_minutes"
                                id="edit-extra-overtime" value="0">
                        </div>

                        <div class="form-group">
                            <label for="edit-penalty">Penalty Amount</label>
                            <input type="number" min="0" step="0.01" class="form-control"
                                name="penalty_amount" id="edit-penalty" value="0">
                        </div>

                        <div class="form-group">
                            <label for="edit-note">Note</label>
                            <textarea class="form-control" name="note" id="edit-note" rows="3"></textarea>
                        </div>

                        <div class="form-group form-check">
                            <input type="hidden" name="auto_checkout" value="0">
                            <input type="checkbox" class="form-check-input" id="edit-auto-checkout" name="auto_checkout"
                                value="1">
                            <label class="form-check-label" for="edit-auto-checkout">Mark as Auto Checkout</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        (function() {
            const form = document.getElementById('edit-attendance-form');
            const userText = document.getElementById('edit-attendance-user');
            const dateText = document.getElementById('edit-attendance-date');
            const checkIn = document.getElementById('edit-check-in');
            const checkOut = document.getElementById('edit-check-out');
            const extraOvertime = document.getElementById('edit-extra-overtime');
            const penalty = document.getElementById('edit-penalty');
            const note = document.getElementById('edit-note');
            const autoCheckout = document.getElementById('edit-auto-checkout');

            document.querySelectorAll('.js-edit-attendance').forEach(function(button) {
                button.addEventListener('click', function() {
                    form.action = button.getAttribute('data-update-url') || '#';
                    userText.textContent = button.getAttribute('data-user-name') || '';
                    dateText.textContent = button.getAttribute('data-date') || '';
                    checkIn.value = button.getAttribute('data-check-in') || '';
                    checkOut.value = button.getAttribute('data-check-out') || '';
                    extraOvertime.value = button.getAttribute('data-extra-overtime') || '0';
                    penalty.value = button.getAttribute('data-penalty') || '0';
                    note.value = button.getAttribute('data-note') || '';
                    autoCheckout.checked = (button.getAttribute('data-auto-checkout') || '0') === '1';
                });
            });
        })();
    </script>
@endsection

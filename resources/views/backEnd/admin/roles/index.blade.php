@extends('backEnd.admin.layouts.master')

@section('title')
    Roles
@endsection

@php
    $admins = $data['admin'] ?? [];
    $managers = $data['manager'] ?? [];
    $employees = $data['employee'] ?? [];
    $defaultDutyStart = date('h:i:s A', strtotime(config('attendance.default_start_time')));
    $defaultDutyEnd = date('h:i:s A', strtotime(config('attendance.default_end_time')));
@endphp
@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <!-- ============================================================== -->
                <!-- pageheader  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Roles</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : "")}}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Roles</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->

                <div class="row mb-3">
                    <div class="col-12">
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#add_user">Add User</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered text-center table-striped">
                                    <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Schedule</th>
                                        <th>Panel Window</th>
                                        <th>Order Window</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($i =1)
                                    @if(Auth::guard('admin')->check())
                                        @foreach($admins as $item)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->phone}}</td>
                                                <td>{{$item->email}}</td>
                                                <td>Admin</td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->start_time? date('h:i:s A',strtotime($item->start_time)):""}}<br>
                                                    <strong>End: </strong> {{$item->end_time? date('h:i:s A',strtotime($item->end_time)):""}}
                                                </td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->payroll_panel_start? date('h:i:s A',strtotime($item->payroll_panel_start)):""}}<br>
                                                    <strong>End: </strong> {{$item->payroll_panel_end? date('h:i:s A',strtotime($item->payroll_panel_end)):""}}
                                                </td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->payroll_order_start? date('h:i:s A',strtotime($item->payroll_order_start)):""}}<br>
                                                    <strong>End: </strong> {{$item->payroll_order_end? date('h:i:s A',strtotime($item->payroll_order_end)):""}}
                                                </td>
                                                <td>
                                                    @if($item->status ==1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="mr-1 edit_cat_btn" data-toggle="modal" data-target="#edit_role"
                                                       data-id="{{$item->id}}"
                                                       data-name="{{$item->name}}"
                                                       data-phone="{{$item->phone}}"
                                                       data-email="{{$item->email}}"
                                                       data-status="{{$item->status}}"
                                                       data-password="{{$item->password}}"
                                                                         data-start_time="{{$item->start_time?date('h:i:s A',strtotime($item->start_time)):""}}"
                                                                         data-end_time="{{$item->end_time?date('h:i:s A',strtotime($item->end_time)):""}}"
                                                                                                 data-panel_start="{{$item->payroll_panel_start?date('h:i:s A',strtotime($item->payroll_panel_start)):""}}"
                                                                                                 data-panel_end="{{$item->payroll_panel_end?date('h:i:s A',strtotime($item->payroll_panel_end)):""}}"
                                                                                                 data-order_start="{{$item->payroll_order_start?date('h:i:s A',strtotime($item->payroll_order_start)):""}}"
                                                                                                 data-order_end="{{$item->payroll_order_end?date('h:i:s A',strtotime($item->payroll_order_end)):""}}"
                                                                         data-monthly_salary="{{$item->payroll_monthly_salary ?? 0}}"
                                                                         data-off_days="{{$item->payroll_off_days ?? ''}}"
                                                       data-role="1"
                                                    >
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    @if($item->id != 1)
                                                        <a href="{{route('admin.roles.delete',[$item->id,1])}}"
                                                           onclick="return confirm('Are you sure to delete this?')"><i
                                                                class="fa fa-trash"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach

                                        @foreach($managers as $item)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->phone}}</td>
                                                <td>{{$item->email}}</td>
                                                <td>Manager</td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->start_time? date('h:i:s A',strtotime($item->start_time)):""}}<br>
                                                    <strong>End: </strong> {{$item->end_time? date('h:i:s A',strtotime($item->end_time)):""}}
                                                </td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->payroll_panel_start? date('h:i:s A',strtotime($item->payroll_panel_start)):""}}<br>
                                                    <strong>End: </strong> {{$item->payroll_panel_end? date('h:i:s A',strtotime($item->payroll_panel_end)):""}}
                                                </td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->payroll_order_start? date('h:i:s A',strtotime($item->payroll_order_start)):""}}<br>
                                                    <strong>End: </strong> {{$item->payroll_order_end? date('h:i:s A',strtotime($item->payroll_order_end)):""}}
                                                </td>
                                                <td>
                                                    @if($item->status ==1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="mr-1 edit_cat_btn" data-toggle="modal" data-target="#edit_role"
                                                       data-id="{{$item->id}}"
                                                       data-name="{{$item->name}}"
                                                       data-phone="{{$item->phone}}"
                                                       data-email="{{$item->email}}"
                                                       data-status="{{$item->status}}"
                                                       data-password="{{$item->password}}"
                                                                         data-start_time="{{$item->start_time?date('h:i:s A',strtotime($item->start_time)):""}}"
                                                                         data-end_time="{{$item->end_time?date('h:i:s A',strtotime($item->end_time)):""}}"
                                                                                                 data-panel_start="{{$item->payroll_panel_start?date('h:i:s A',strtotime($item->payroll_panel_start)):""}}"
                                                                                                 data-panel_end="{{$item->payroll_panel_end?date('h:i:s A',strtotime($item->payroll_panel_end)):""}}"
                                                                                                 data-order_start="{{$item->payroll_order_start?date('h:i:s A',strtotime($item->payroll_order_start)):""}}"
                                                                                                 data-order_end="{{$item->payroll_order_end?date('h:i:s A',strtotime($item->payroll_order_end)):""}}"
                                                                         data-monthly_salary="{{$item->payroll_monthly_salary ?? 0}}"
                                                                         data-off_days="{{$item->payroll_off_days ?? ''}}"
                                                       data-role="2"
                                                    >
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{route('admin.roles.delete',[$item->id,2])}}" onclick="return confirm('Are you sure to delete this?')"><i
                                                            class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach($employees as $item)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->phone}}</td>
                                                <td>{{$item->email}}</td>
                                                <td>Employee</td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->start_time? date('h:i:s A',strtotime($item->start_time)):""}}<br>
                                                    <strong>End: </strong> {{$item->end_time? date('h:i:s A',strtotime($item->end_time)):""}}
                                                </td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->payroll_panel_start? date('h:i:s A',strtotime($item->payroll_panel_start)):""}}<br>
                                                    <strong>End: </strong> {{$item->payroll_panel_end? date('h:i:s A',strtotime($item->payroll_panel_end)):""}}
                                                </td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->payroll_order_start? date('h:i:s A',strtotime($item->payroll_order_start)):""}}<br>
                                                    <strong>End: </strong> {{$item->payroll_order_end? date('h:i:s A',strtotime($item->payroll_order_end)):""}}
                                                </td>

                                                <td>
                                                    @if($item->status ==1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <a href="javascript:void(0)" class="mr-1 edit_cat_btn" data-toggle="modal" data-target="#edit_role"
                                                       data-id="{{$item->id}}"
                                                       data-name="{{$item->name}}"
                                                       data-phone="{{$item->phone}}"
                                                       data-email="{{$item->email}}"
                                                       data-status="{{$item->status}}"
                                                       data-password="{{$item->password}}"
                                                       data-start_time="{{$item->start_time?date('h:i:s A',strtotime($item->start_time)):""}}"
                                                       data-end_time="{{$item->end_time?date('h:i:s A',strtotime($item->end_time)):""}}"
                                                                         data-panel_start="{{$item->payroll_panel_start?date('h:i:s A',strtotime($item->payroll_panel_start)):""}}"
                                                                         data-panel_end="{{$item->payroll_panel_end?date('h:i:s A',strtotime($item->payroll_panel_end)):""}}"
                                                                         data-order_start="{{$item->payroll_order_start?date('h:i:s A',strtotime($item->payroll_order_start)):""}}"
                                                                         data-order_end="{{$item->payroll_order_end?date('h:i:s A',strtotime($item->payroll_order_end)):""}}"
                                                       data-monthly_salary="{{$item->payroll_monthly_salary ?? 0}}"
                                                       data-off_days="{{$item->payroll_off_days ?? ''}}"
                                                       data-role="3"
                                                    >
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{route('admin.roles.delete',[$item->id,3])}}" onclick="return confirm('Are you sure to delete this?')"><i
                                                            class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @elseif(Auth::guard('manager')->check())
                                        @foreach($employees as $item)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->phone}}</td>
                                                <td>{{$item->email}}</td>
                                                <td>Employee</td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->start_time? date('h:i:s A',strtotime($item->start_time)):""}}<br>
                                                    <strong>End: </strong> {{$item->end_time? date('h:i:s A',strtotime($item->end_time)):""}}
                                                </td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->payroll_panel_start? date('h:i:s A',strtotime($item->payroll_panel_start)):""}}<br>
                                                    <strong>End: </strong> {{$item->payroll_panel_end? date('h:i:s A',strtotime($item->payroll_panel_end)):""}}
                                                </td>
                                                <td>
                                                    <strong>Start: </strong> {{$item->payroll_order_start? date('h:i:s A',strtotime($item->payroll_order_start)):""}}<br>
                                                    <strong>End: </strong> {{$item->payroll_order_end? date('h:i:s A',strtotime($item->payroll_order_end)):""}}
                                                </td>
                                                <td>
                                                    @if($item->status ==1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="mr-1 edit_cat_btn" data-toggle="modal" data-target="#edit_role"
                                                       data-id="{{$item->id}}"
                                                       data-name="{{$item->name}}"
                                                       data-phone="{{$item->phone}}"
                                                       data-email="{{$item->email}}"
                                                       data-status="{{$item->status}}"
                                                       data-password="{{$item->password}}"
                                                       data-start_time="{{$item->start_time?date('h:i:s A',strtotime($item->start_time)):""}}"
                                                       data-end_time="{{$item->end_time?date('h:i:s A',strtotime($item->end_time)):""}}"
                                                                         data-panel_start="{{$item->payroll_panel_start?date('h:i:s A',strtotime($item->payroll_panel_start)):""}}"
                                                                         data-panel_end="{{$item->payroll_panel_end?date('h:i:s A',strtotime($item->payroll_panel_end)):""}}"
                                                                         data-order_start="{{$item->payroll_order_start?date('h:i:s A',strtotime($item->payroll_order_start)):""}}"
                                                                         data-order_end="{{$item->payroll_order_end?date('h:i:s A',strtotime($item->payroll_order_end)):""}}"
                                                       data-monthly_salary="{{$item->payroll_monthly_salary ?? 0}}"
                                                       data-off_days="{{$item->payroll_off_days ?? ''}}"
                                                       data-role="3"
                                                    >
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{route('manager.roles.delete',[$item->id,3])}}" onclick="return confirm('Are you sure to delete this?')"><i
                                                            class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--add modal--}}
    <div class="modal fade" id="add_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{Auth::guard('admin')->check() ? route('admin.roles.store') : (Auth::guard('manager')->check() ? route('manager.roles.store') : "")}}"
                          method="post">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="text" class="form-control" id="password" name="password" required>
                        </div>
                        @if(Auth::guard('admin')->check())
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select name="role" id="role" class="form-control">
                                    <option value="1">Admin</option>
                                    <option value="2">Manager</option>
                                    <option value="3">Employee</option>
                                </select>
                            </div>
                        @elseif(Auth::guard('manager')->check())
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select name="role" id="role" class="form-control">
                                    <option value="3">Employee</option>
                                </select>
                            </div>
                        @endif
                        <div class="">
                            <div class="form-group">
                                <label for="start_time">Duty Start Time</label>
                                <input name="start_time" id="start_time" class="form-control" value="{{$defaultDutyStart}}">
                            </div>
                            <div class="form-group">
                                <label for="end_time">Duty End Time</label>
                                <input name="end_time" id="end_time" class="form-control" value="{{$defaultDutyEnd}}">
                            </div>
                            <div class="form-group">
                                <label for="panel_start">Panel Login Start</label>
                                <input name="panel_start" id="panel_start" class="form-control" placeholder="09:00:00 AM">
                            </div>
                            <div class="form-group">
                                <label for="panel_end">Panel Login End</label>
                                <input name="panel_end" id="panel_end" class="form-control" placeholder="09:00:00 PM">
                            </div>
                            <div class="form-group">
                                <label for="order_start">Order Receive Start</label>
                                <input name="order_start" id="order_start" class="form-control" placeholder="10:00:00 AM">
                            </div>
                            <div class="form-group">
                                <label for="order_end">Order Receive End</label>
                                <input name="order_end" id="order_end" class="form-control" placeholder="10:00:00 PM">
                            </div>
                            <div class="form-group">
                                <label for="monthly_salary">Monthly Salary</label>
                                <input type="number" step="0.01" min="0" name="monthly_salary" id="monthly_salary" class="form-control" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label for="off_days">Off Days (comma separated)</label>
                                <input type="text" name="off_days" id="off_days" class="form-control" placeholder="Friday, Saturday">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-success" value="Add">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="edit_role" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form
                        action="{{Auth::guard('admin')->check() ? route('admin.roles.update') : (Auth::guard('manager')->check() ? route('manager.roles.update') : "")}}"
                        method="post">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="old_password" id="old_password">
                        <input type="hidden" name="old_role" id="old_role">
                        <div class="form-group">
                            <label for="name_e">Name</label>
                            <input type="text" class="form-control" id="name_e" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="phone_e">Phone</label>
                            <input type="text" class="form-control" id="phone_e" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label for="email_e">Email</label>
                            <input type="email" class="form-control" id="email_e" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password_e">Password</label>
                            <input type="text" class="form-control" id="password_e" name="password">
                        </div>

                        @if(Auth::guard('admin')->check())
                            <div class="form-group">
                                <label for="role_e">Role</label>
                                <select name="role" id="role_e" class="form-control">
                                    <option value="1">Admin</option>
                                    <option value="2">Manager</option>
                                    <option value="3">Employee</option>
                                </select>
                            </div>
                        @elseif(Auth::guard('manager')->check())
                            <div class="form-group">
                                <label for="role_e">Role</label>
                                <select name="role" id="role_e" class="form-control">
                                    <option value="3">Employee</option>
                                </select>
                            </div>
                        @endif

                        <div class="">
                            <div class="form-group">
                                <label for="start_time_e">Duty Start Time</label>
                                <input name="start_time" id="start_time_e" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="end_time_e">Duty End Time</label>
                                <input name="end_time" id="end_time_e" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="panel_start_e">Panel Login Start</label>
                                <input name="panel_start" id="panel_start_e" class="form-control" placeholder="09:00:00 AM">
                            </div>
                            <div class="form-group">
                                <label for="panel_end_e">Panel Login End</label>
                                <input name="panel_end" id="panel_end_e" class="form-control" placeholder="09:00:00 PM">
                            </div>
                            <div class="form-group">
                                <label for="order_start_e">Order Receive Start</label>
                                <input name="order_start" id="order_start_e" class="form-control" placeholder="10:00:00 AM">
                            </div>
                            <div class="form-group">
                                <label for="order_end_e">Order Receive End</label>
                                <input name="order_end" id="order_end_e" class="form-control" placeholder="10:00:00 PM">
                            </div>
                            <div class="form-group">
                                <label for="monthly_salary_e">Monthly Salary</label>
                                <input type="number" step="0.01" min="0" name="monthly_salary" id="monthly_salary_e" class="form-control" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label for="off_days_e">Off Days (comma separated)</label>
                                <input type="text" name="off_days" id="off_days_e" class="form-control" placeholder="Friday, Saturday">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status_e">Status</label>
                            <select name="status" id="status_e" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-success" value="Update">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const defaultDutyStart = @json($defaultDutyStart);
        const defaultDutyEnd = @json($defaultDutyEnd);

        $('.edit_cat_btn').on('click', function () {
            $('#id').val($(this).data('id'));
            $('#name_e').val($(this).data('name'));
            $('#email_e').val($(this).data('email'));
            $('#phone_e').val($(this).data('phone'));
            $('#status_e').val($(this).data('status'));
            $('#old_password').val($(this).data('password'));
            $('#old_role').val($(this).data('role'));
            $('#role_e').val($(this).data('role'));
            $('#start_time_e').val($(this).data('start_time') || defaultDutyStart);
            $('#end_time_e').val($(this).data('end_time') || defaultDutyEnd);
            $('#panel_start_e').val($(this).data('panel_start'));
            $('#panel_end_e').val($(this).data('panel_end'));
            $('#order_start_e').val($(this).data('order_start'));
            $('#order_end_e').val($(this).data('order_end'));
            $('#monthly_salary_e').val($(this).data('monthly_salary'));
            $('#off_days_e').val($(this).data('off_days'));

            if ($(this).data('role') == 3){
                $('.schedule').removeClass('d-none').addClass('d-block');
            }else {
                $('.schedule').removeClass('d-block').addClass('d-none');
            }
        });

        $('#role').on('change', function () {
            if ($(this).val() == 3){
                $('.schedule').removeClass('d-none').addClass('d-block');
            }else {
                $('.schedule').removeClass('d-block').addClass('d-none');
            }
        })
    </script>
@endsection

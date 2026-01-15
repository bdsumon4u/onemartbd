@extends('backEnd.admin.layouts.master')

@section('title')
    Order Status By Product
@endsection
@section('css')
    <style>
        @media (max-width: 576px) {
            .form-inline .form-control {
                display: inline-block;
                width: auto;
                vertical-align: middle;
            }
        }
    </style>
@endsection
@php
    $orders = $data['orders'] ?? [];
    $total_order = $data['total_order'] ?? [];
    $total_hold_orders = $data['total_hold_orders'] ?? [];
    $total_deliver_orders = $data['total_deliver_orders'] ?? [];
    $total_process_orders = $data['total_process_orders'] ?? [];
    $total_pend_pay_orders = $data['total_pend_pay_orders'] ?? [];
    $total_cancel_orders = $data['total_cancel_orders'] ?? [];

    $query = $query ?? null;
    $courier_id = $courier_id ?? null;
    $status = $sts ?? null;
    $employees = \Illuminate\Support\Facades\DB::table('employees')->where('status', 1)->pluck('name', 'id');
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
                            <h2 class="pageheader-title">Order Status By Product</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{ Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : '')) }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Order Status By Product</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="row mb-md-3 mb-2">
                    <div class="col-12">
                        <form action="{{route('admin.reports.employee_orders')}}" method="get">
                            <div class="row">
                                <div class="col-2">
                                    <select name="emp_id" id="emp_id" class="form-control select2">
                                        <option value="">--Select Employee--</option>
                                        @foreach ($employees as $key => $employee)
                                            <option value="{{$key}}" {{$key==$emp_id?"selected":""}}>{{$employee}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <button class="btn btn-info btn-sm">Search</button>
                                    <a href="{{route('admin.reports.employee_orders')}}" class="btn btn-dark btn-sm">Reset</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="row mb-md-4 mb-3">
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                        <a href="{{Auth::guard('admin')->check() ? route('admin.reports.employee_orders','emp_id='.$emp_id.'&status=') : (Auth::guard('manager')->check() ? route('manager.orders') : (Auth::guard('employee')->check() ? route('employee.orders') : ""))}}">
                            <div class="card border-3 border-top border-top-success">
                                <div class="card-body">
                                    <h5>Total Order</h5>
                                    <div class="metric-value d-inline-block">
                                        <h1 class="mb-1">{{$total_order}}</h1>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                        <a href="{{Auth::guard('admin')->check() ? route('admin.reports.employee_orders','emp_id='.$emp_id.'&status=2') : (Auth::guard('manager')->check() ? route('manager.orders.status.processing') : (Auth::guard('employee')->check() ? route('employee.orders.status.processing') : ""))}}">
                            <div class="card border-3 border-top border-top-success">
                                <div class="card-body">
                                    <h5 class="text-info">Total Processing</h5>
                                    <div class="metric-value d-inline-block">
                                        <h1 class="mb-1">{{$total_process_order}}</h1>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                        <a href="{{Auth::guard('admin')->check() ? route('admin.reports.employee_orders','emp_id='.$emp_id.'&status=3') : (Auth::guard('manager')->check() ? route('manager.orders.status.pending_payment') : (Auth::guard('employee')->check() ? route('employee.orders.status.pending_payment') : ""))}}">
                            <div class="card border-3 border-top border-top-success">
                                <div class="card-body">
                                    <h5 class="text-secondary">Total Pending Payment</h5>
                                    <div class="metric-value d-inline-block">
                                        <h1 class="mb-1">{{$total_pend_pay_order}}</h1>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                        <a href="{{Auth::guard('admin')->check() ? route('admin.reports.employee_orders','emp_id='.$emp_id.'&status=0') : (Auth::guard('manager')->check() ? route('manager.orders.status.hold') : (Auth::guard('employee')->check() ? route('employee.orders.status.hold') : ""))}}">
                            <div class="card border-3 border-top border-top-success">
                                <div class="card-body">
                                    <h5 class="text-warning">Total Hold</h5>
                                    <div class="metric-value d-inline-block">
                                        <h1 class="mb-1">{{$total_hold_order}}</h1>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                        <a href="{{Auth::guard('admin')->check() ? route('admin.reports.employee_orders','emp_id='.$emp_id.'&status=4') : (Auth::guard('manager')->check() ? route('manager.orders.status.canceled') : (Auth::guard('employee')->check() ? route('employee.orders.status.canceled') : ""))}}">
                            <div class="card border-3 border-top border-top-success">
                                <div class="card-body">
                                    <h5 class="text-danger">Total Canceled</h5>
                                    <div class="metric-value d-inline-block">
                                        <h1 class="mb-1">{{$total_cancel_order}}</h1>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                        <a href="{{Auth::guard('admin')->check() ? route('admin.reports.employee_orders','emp_id='.$emp_id.'&status=1') : (Auth::guard('manager')->check() ? route('manager.orders.status.completed') : (Auth::guard('employee')->check() ? route('employee.orders.status.completed') : ""))}}">
                            <div class="card border-3 border-top border-top-success">
                                <div class="card-body">
                                    <h5 class="text-success">Total Completed</h5>
                                    <div class="metric-value d-inline-block">
                                        <h1 class="mb-1">{{$total_deliver_order}}</h1>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>

                <div class="row mb-2">
                    <div class="col-md-2 col-12">
                        <a href="{{Auth::guard('admin')->check() ? route('admin.orders.create') : (Auth::guard('manager')->check() ? route('manager.orders.create') : (Auth::guard('employee')->check() ? route('employee.orders.create') : ""))}}"
                           class="btn btn-success btn-sm">Add Order</a>
                    </div>
                    @if (Auth::guard('admin')->check())
                        <div class="col-md-10 col-12 mt-md-0 mt-2">
                            <form action="{{route('admin.orders.p')}}" method="get" class="form-inline float-md-right">
                                <div class="form-group">
                                    <input type="hidden" name="status" value="{{$status??null}}">
                                    <input type="text" class="form-control mr-2" placeholder="Type here"
                                           value="{{$query}}"
                                           name="query">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-dark btn-sm mr-1">Search</button>
                                    <a href="{{route('admin.orders.p')}}" class="btn btn-info btn-sm">Reset</a>
                                </div>
                            </form>
                        </div>
                    @endif
                    @if (Auth::guard('manager')->check())
                        <div class="col-md-10 col-12 mt-md-0 mt-2">
                            <form action="{{route('manager.orders.p')}}" method="get"
                                  class="form-inline float-md-right">
                                <div class="form-group">
                                    <input type="text" class="form-control mr-2" placeholder="Type here"
                                           name="query" value="{{$query}}" required>
                                    <button class="btn btn-dark btn-sm">Search</button>
                                </div>
                            </form>
                        </div>
                    @endif
                    @if (Auth::guard('employee')->check())
                        <div class="col-md-10 col-12 mt-md-0 mt-2">
                            <form action="{{route('employee.orders.p')}}" method="get"
                                  class="form-inline float-md-right">
                                <div class="form-group">
                                    <input type="text" class="form-control mr-2" placeholder="Type here"
                                           name="query" value="{{$query}}" required>
                                    <button class="btn btn-dark btn-sm">Search</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
                @if (Auth::guard('admin')->check())
                    <div class="row mb-2">
                        <div class="col-md-2 col-12">
                            <form action="{{route('admin.orders.all.status')}}" method="post" id="all_status_form">
                                @csrf
                                <input type="hidden" id="all_status" name="all_status">
                                <select name="status" id="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="0">On Hold</option>
                                    <option value="2">Processing</option>
                                    <option value="3">Pending Payment</option>
                                    <option value="1">Delivered</option>
                                    <option value="4">Canceled</option>
                                </select>
                            </form>
                        </div>

                        <div class="col-md-2 col-12">
                            <form action="{{route('admin.orders.bulk.assign')}}" method="post" id="bulk_assign_form">
                                @csrf
                                <input type="hidden" id="all_order_id" name="all_order_id">
                                <select name="employee_id" id="employee_id" class="form-control">
                                    <option value="">Select Employee</option>
                                    @foreach ($employees as $id => $employee)
                                        <option value="{{$id}}">{{$employee}}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        <div class="col-md-1 col-12">
                            <form action="{{route('admin.orders.bulk.delete')}}" method="post" id="bulk_delete_form">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" id="all_id" name="all_id">
                                    <button type="button" id="bulk_delete" class="btn btn-danger btn-sm ml-1">Delete</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-1 p-0 col-12">
                            <form action="{{route('admin.orders.bulk.print')}}" method="post" id="all_print_form">
                                @csrf
                                <div class="form-group">
                                    <button type="button" id="bulk_print_btn" class="btn btn-warning btn-sm">Print Invoice
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-6 p-0 col-12">
                            <form action="{{route('admin.orders.order_export')}}" method="post" id="all_order_export">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" id="all_ord_id" name="all_ord_id">
                                    <button type="button" id="order_export" class="btn btn-info btn-sm ml-1">Export</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
                @if (Auth::guard('manager')->check())
                    <div class="row mb-2">
                        <div class="col-md-2 col-12">
                            <form action="{{route('manager.orders.all.status')}}" method="post" id="all_status_form">
                                @csrf
                                <input type="hidden" id="all_status" name="all_status">
                                <select name="status" id="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="0">On Hold</option>
                                    <option value="2">Processing</option>
                                    <option value="3">Pending Payment</option>
                                    <option value="1">Delivered</option>
                                    <option value="4">Canceled</option>
                                </select>
                            </form>
                        </div>

                        <div class="col-md-1 p-0 col-12">
                            <form action="{{route('manager.orders.bulk.print')}}" method="post" id="all_print_form">
                                @csrf
                                <div class="form-group">
                                    <button type="button" id="bulk_print_btn" class="btn btn-warning btn-sm">Print Invoice
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
                @if (Auth::guard('employee')->check())
                    <div class="row mb-2">
                        --}}{{-- <div class="col-md-2 col-12">
                            <form action="{{route('employee.orders.all.status')}}" method="post" id="all_status_form">
                                @csrf
                                <input type="hidden" id="all_status" name="all_status">
                                <select name="status" id="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="0">On Hold</option>
                                    <option value="2">Processing</option>
                                    <option value="3">Pending Payment</option>
                                    <option value="1">Delivered</option>
                                    <option value="4">Canceled</option>
                                </select>
                            </form>
                        </div> --}}{{--
                        <div class="col-md-1 col-12">
                            <form action="{{route('employee.orders.bulk.print')}}" method="post" id="all_print_form">
                                @csrf
                                <div class="form-group">
                                    <button type="button" id="bulk_print_btn" class="btn btn-warning btn-sm">Print Invoice
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL.</th>
                                            <th>Product Name</th>
                                            <th>Total Orders</th>
                                            <th>Total Active</th>
                                            <th>Total Processing</th>
                                            <th>Total NR1</th>
                                            <th>Total NR2</th>
                                            <th>Total Hold</th>
                                            <th>Total Canceled</th>
                                            <th>Total Pending Payment</th>
                                            <th>Total Pending Delivery</th>
                                            <th>Total On Delivery</th>
                                            <th>Total Courier Hold</th>
                                            <th>Total Returned</th>
                                            <th>Total Delivered</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($i = 1)
                                        @if (count($data) > 0)
                                            @foreach ($data as $key => $item)
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $item[$key] }}</td>
                                                    @foreach ($item[$key + 1] as $k => $kd)
                                                        <td>{{ $kd }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="10" class="text-center text-danger font-weight-bold">No
                                                    Data Found!
                                                </td>
                                            </tr>
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

    {{-- user assing modal --}}
    <div class="modal fade" id="user_assign" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Assign User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.orders.single.assign') }}" method="post">
                        @csrf
                        <input type="hidden" name="order_id" id="order_id_a">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <select name="employee_id" id="employee_id_modal" class="form-control select2" required>
                                    <option value="">Select Employee</option>
                                    @foreach ($employees as $id => $item)
                                        <option value="{{ $id }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group text-center mt-2">
                            <input type="submit" class="btn btn-success" id="form_add_btn" value="Assign">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('.print').on('click', function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{ Auth::guard('admin')->check() ? route('admin.orders.print') : (Auth::guard('manager')->check() ? route('manager.orders.print') : (Auth::guard('employee')->check() ? route('employee.orders.print') : '')) }}',
                type: 'POST',
                data: {
                    _token: CSRF_TOKEN,
                    id: $(this).data('id')
                },
                success: function(data) {
                    newWin = window.open("");
                    newWin.document.write(data);
                    newWin.document.close();
                }
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();

            $('#master').on('click', function(e) {
                if ($(this).is(':checked', true)) {
                    $(".sub_chk").prop('checked', true);
                } else {
                    $(".sub_chk").prop('checked', false);
                }
            });


            $('#status').on('change', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    $('#all_status').val(allVals);
                    $('#all_status_form').submit();
                }
            });

            $('#employee_id').on('change', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    $('#all_order_id').val(allVals);
                    $('#bulk_assign_form').submit();
                }
            });

            $('#bulk_delete').on('click', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    if (confirm('Are Your Sure To Delete?') == true) {
                        $('#all_id').val(allVals);
                        $('#bulk_delete_form').submit();
                    }
                }
            });

            $('#bulk_print_btn').on('click', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {

                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: '{{ Auth::guard('admin')->check() ? route('admin.orders.bulk.print') : (Auth::guard('manager')->check() ? route('manager.orders.bulk.print') : (Auth::guard('employee')->check() ? route('employee.orders.bulk.print') : '')) }}',
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN,
                            all_inv_id: allVals
                        },
                        success: function(data) {
                            newWin = window.open("");
                            newWin.document.write(data);
                            newWin.document.close();
                        }
                    });
                }
            });

            //order export
            $('#order_export').on('click', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    $('#all_ord_id').val(allVals);
                    $('#all_order_export').submit();
                }
            });

            //single assign
            $('.single-assign-btn').click(function() {
                $('#order_id_a').val($(this).data('order_id'));
                $('#user_assign').modal('show');
            });
        });
    </script>
@endsection

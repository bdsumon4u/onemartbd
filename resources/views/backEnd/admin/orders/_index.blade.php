@extends('backEnd.admin.layouts.master')

@section('title')
    All Orders
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.css') }}">
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
    $total_hold_order = $data['total_hold_order'] ?? [];
    $total_deliver_order = $data['total_deliver_order'] ?? [];
    $total_process_order = $data['total_process_order'] ?? [];
    $total_pend_pay_order = $data['total_pend_pay_order'] ?? [];
    $total_cancel_order = $data['total_cancel_order'] ?? [];
    $total_pending_delivery_order = $data['total_pending_delivery_order'] ?? [];
    $total_on_delivery_order = $data['total_on_delivery_order'] ?? [];
    $total_return_order = $data['total_return_order'] ?? [];

    $couriers = \Illuminate\Support\Facades\DB::table('couriers')->where('status', 1)->pluck('courier_name', 'id');
    $query = $query ?? null;
    $courier_id = $courier_id ?? null;
    $status = $status ?? null;
    $employees = \Illuminate\Support\Facades\DB::table('employees')->where('status', 1)->pluck('name', 'id');
    $last_order = \Illuminate\Support\Facades\DB::table('orders')->latest('id')->first()->created_at;
    $count = $data['count'] ?? 0;
@endphp
@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <!-- ============================================================== -->
                <!-- pageheader  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">{{ $status ?? 'All' }} Orders</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{ Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : '')) }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">All Orders</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 d-flex justify-content-end">
                        <form
                            action="{{ Auth::guard('admin')->check() ? route('admin.orders') : (Auth::guard('manager')->check() ? route('manager.orders') : (Auth::guard('employee')->check() ? route('employee.orders') : '')) }}"
                            method="get" id="" class="action_buttons">
                            <input type="hidden" name="query" value="{{ request()->query('query') ?? null }}">
                            <input type="hidden" name="status"
                                value="{{ $status ? $status : request()->query('status') ?? null }}">
                            <div class="form-group mr-1">
                                <select name="custom_range" id="custom_range" class="form-control h-34">
                                    <option value="">Custom Range</option>
                                    <option value="today"
                                        {{ request()->query('custom_range') == 'today' ? 'selected' : '' }}>
                                        Today</option>
                                    <option value="yesterday"
                                        {{ request()->query('custom_range') == 'yesterday' ? 'selected' : '' }}>Yesterday
                                    </option>
                                    <option value="last_7_days"
                                        {{ request()->query('custom_range') == 'last_7_days' ? 'selected' : '' }}>Last 7
                                        Days
                                    </option>
                                    <option value="this_month"
                                        {{ request()->query('custom_range') == 'this_month' ? 'selected' : '' }}>This Month
                                    </option>
                                    <option value="last_month"
                                        {{ request()->query('custom_range') == 'last_month' ? 'selected' : '' }}>Last Month
                                    </option>
                                    <option value="last_6_months"
                                        {{ request()->query('custom_range') == 'last_6_months' ? 'selected' : '' }}>Last 6
                                        Months
                                    </option>
                                </select>
                            </div>
                            <div class="form-group mr-1">
                                <input type="text" class="form-control mr-2 datetimepicker h-34" name="start_date"
                                    id="start_date" placeholder="Start Date" value="{{ request()->query('start_date') }}"
                                    {{ request()->query('custom_range') != null ? 'disabled' : '' }}>
                            </div>
                            <div class="form-group mr-1">
                                <input type="text" class="form-control mr-2 datetimepicker h-34" name="end_date"
                                    id="end_date" placeholder="End Date" value="{{ request()->query('end_date') }}"
                                    {{ request()->query('custom_range') != null ? 'disabled' : '' }}>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-dark btn-sm mr-1 h-34">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h4><b>Last Order:</b> {{ \Carbon\Carbon::parse($last_order)->diffForHumans() }}</h4>
                    </div>
                </div>
                <div class="row mb-3 order-card">
                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders') : (Auth::guard('manager')->check() ? route('manager.orders') : (Auth::guard('employee')->check() ? route('employee.orders') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value d-inline-block">
                                        <h2 class="mb-0">{{ $total_order }}</h2>
                                    </div>
                                    <h5 class="h5-s">Total Order</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status.processing') : (Auth::guard('manager')->check() ? route('manager.orders.status.processing') : (Auth::guard('employee')->check() ? route('employee.orders.status.processing') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value d-inline-block">
                                        <h2 class="mb-0">{{ $total_process_order }}</h2>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_process_order / $total_order) * 100) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-info h5-s">Total Processing</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status.pending_delivery') : (Auth::guard('manager')->check() ? route('manager.orders.status.pending_delivery') : (Auth::guard('employee')->check() ? route('employee.orders.status.pending_delivery') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value d-inline-block">
                                        <h2 class="mb-0">{{ $total_pending_delivery_order }}</h2>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_pending_delivery_order / $total_order) * 100) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-warning h5-s">Total Pend. Delivery</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status.pending_payment') : (Auth::guard('manager')->check() ? route('manager.orders.status.pending_payment') : (Auth::guard('employee')->check() ? route('employee.orders.status.pending_payment') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value d-inline-block">
                                        <h2 class="mb-0">{{ $total_pend_pay_order }}</h2>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_pend_pay_order / $total_order) * 100) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-secondary h5-s">Total Pend. Payment</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status.on_delivery') : (Auth::guard('manager')->check() ? route('manager.orders.status.on_delivery') : (Auth::guard('employee')->check() ? route('employee.orders.status.on_delivery') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value d-inline-block">
                                        <h2 class="mb-0">{{ $total_on_delivery_order }}</h2>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_on_delivery_order / $total_order) * 100) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-primary h5-s">Total On Delivery</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status.hold') : (Auth::guard('manager')->check() ? route('manager.orders.status.hold') : (Auth::guard('employee')->check() ? route('employee.orders.status.hold') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value d-inline-block">
                                        <h2 class="mb-0">{{ $total_hold_order }}</h2>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_hold_order / $total_order) * 100) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-warning h5-s">Total Hold</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status.canceled') : (Auth::guard('manager')->check() ? route('manager.orders.status.canceled') : (Auth::guard('employee')->check() ? route('employee.orders.status.canceled') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value d-inline-block">
                                        <h2 class="mb-0">{{ $total_cancel_order }}</h2>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_cancel_order / $total_order) * 100) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-danger h5-s">Total Canceled</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status.return') : (Auth::guard('manager')->check() ? route('manager.orders.status.return') : (Auth::guard('employee')->check() ? route('employee.orders.status.return') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value d-inline-block">
                                        <h2 class="mb-0">{{ $total_return_order }}</h2>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_return_order / $total_order) * 100) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-danger h5-s">Total Returned</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status.completed') : (Auth::guard('manager')->check() ? route('manager.orders.status.completed') : (Auth::guard('employee')->check() ? route('employee.orders.status.completed') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value d-inline-block">
                                        <h2 class="mb-0">{{ $total_deliver_order }}</h2>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_deliver_order / $total_order) * 100) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-success h5-s">Total Delivered</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                @if (Auth::guard('admin')->check())
                    <div class="row mb-2">
                        <div class="col-md-11 col-12 action_buttons">
                            <div class="form-group">
                                <a href="{{ route('admin.orders.create') }}" class="btn btn-success btn-sm mr-2 h-34">Add
                                    Order</a>
                            </div>
                            <form action="{{ route('admin.orders.all.status') }}" method="post" id="all_status_form"
                                class="mr-2">
                                @csrf
                                <input type="hidden" id="all_status" name="all_status">
                                <select name="status" id="status" class="form-control h-34">
                                    <option value="">Select Status</option>
                                    <option value="0">On Hold</option>
                                    <option value="2">Processing</option>
                                    <option value="3">Pending Payment</option>
                                    <option value="1">Delivered</option>
                                    <option value="4">Canceled</option>
                                    <option value="5">Pending Delivery</option>
                                    <option value="6">On Delivery</option>
                                    <option value="7">Returned</option>
                                </select>
                            </form>

                            <form action="{{ route('admin.orders.bulk.assign') }}" method="post" id="bulk_assign_form"
                                class="mr-2">
                                @csrf
                                <input type="hidden" id="all_order_id" name="all_order_id">
                                <select name="employee_id" id="employee_id" class="form-control h-34">
                                    <option value="">Select Employee</option>
                                    @foreach ($employees as $id => $employee)
                                        <option value="{{ $id }}">{{ $employee }}</option>
                                    @endforeach
                                </select>
                            </form>

                            <form action="{{ route('admin.orders.courier_csv') }}" method="post" id="all_courier_csv"
                                class="mr-2">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" id="all_ord_id" name="all_ord_id">
                                    <select name="courier_csv" id="courier_csv" class="form-control h-34">
                                        <option value="">Select Courier Export</option>
                                        <option value="1">Pathao</option>
                                        <option value="2">RedX</option>
                                        <option value="3">PaperFly</option>
                                        <option value="4">SteadFast</option>
                                        <option value="0">Order Export</option>
                                    </select>
                                </div>
                            </form>

                            <form action="{{ route('admin.orders.bulk.print') }}" method="post" id="all_print_form"
                                class="mr-2">
                                @csrf
                                <div class="form-group">
                                    <button type="button" id="bulk_print_btn" class="btn btn-info btn-sm h-34">Print
                                        Invoice
                                    </button>
                                </div>
                            </form>

                            <form action="{{ route('admin.orders.bulk.delete') }}" method="post" id="bulk_delete_form"
                                class="mr-2">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" id="all_id" name="all_id">
                                    <button type="button" id="bulk_delete"
                                        class="btn btn-danger btn-sm h-34">Delete</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-1 col-12 action_buttons justify-content-end">
                            <form action="" method="get" id="paginate_form">
                                <input type="hidden" name="status"
                                    value="{{ $status ? $status : request()->query('status') ?? null }}">
                                <input type="hidden" name="query" value="{{ request()->query('query') ?? null }}">
                                <input type="hidden" name="custom_range"
                                    value="{{ request()->query('custom_range') ?? null }}">
                                <input type="hidden" name="start_date"
                                    value="{{ request()->query('start_date') ?? null }}">
                                <input type="hidden" name="end_date"
                                    value="{{ request()->query('end_date') ?? null }}">
                                <div class="form-group">
                                    <select name="paginate" id="paginate" class="form-control h-34">
                                        <option value="10" {{ request()->input('paginate') == 10 ? 'selected' : '' }}>
                                            10
                                        </option>
                                        <option value="20" {{ request()->input('paginate') == 20 ? 'selected' : '' }}>
                                            20
                                        </option>
                                        <option value="100"
                                            {{ request()->input('paginate') == 100 ? 'selected' : '' }}>
                                            100</option>
                                        <option value="200"
                                            {{ request()->input('paginate') == 200 ? 'selected' : '' }}>
                                            200</option>
                                        <option value="500"
                                            {{ request()->input('paginate') == 500 ? 'selected' : '' }}>
                                            500</option>
                                        <option value="1000"
                                            {{ request()->input('paginate') == 1000 ? 'selected' : '' }}>
                                            1000</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif(Auth::guard('manager')->check())
                    <div class="row mb-2">
                        <div class="col-md-11 col-12 action_buttons">
                            <div class="form-group">
                                <a href="{{ route('manager.orders.create') }}"
                                    class="btn btn-success btn-sm mr-2 h-34">Add Order</a>
                            </div>
                            <form action="{{ route('manager.orders.all.status') }}" method="post" id="all_status_form"
                                class="mr-2">
                                @csrf
                                <input type="hidden" id="all_status" name="all_status">
                                <select name="status" id="status" class="form-control h-34">
                                    <option value="">Select Status</option>
                                    <option value="0">On Hold</option>
                                    <option value="2">Processing</option>
                                    <option value="3">Pending Payment</option>
                                    {{-- <option value="1">Delivered</option> --}}
                                    <option value="4">Canceled</option>
                                    <option value="5">Pending Delivery</option>
                                    <option value="6">On Delivery</option>
                                    {{-- <option value="7">Returned</option> --}}
                                </select>
                            </form>

                            <form action="{{ route('manager.orders.bulk.assign') }}" method="post"
                                id="bulk_assign_form" class="mr-2">
                                @csrf
                                <input type="hidden" id="all_order_id" name="all_order_id">
                                <select name="employee_id" id="employee_id" class="form-control h-34">
                                    <option value="">Select Employee</option>
                                    @foreach ($employees as $id => $employee)
                                        <option value="{{ $id }}">{{ $employee }}</option>
                                    @endforeach
                                </select>
                            </form>

                            <form action="{{ route('manager.orders.courier_csv') }}" method="post" id="all_courier_csv"
                                class="mr-2">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" id="all_ord_id" name="all_ord_id">
                                    <select name="courier_csv" id="courier_csv" class="form-control h-34">
                                        <option value="">Select Courier Export</option>
                                        <option value="1">Pathao</option>
                                        <option value="2">RedX</option>
                                        <option value="3">PaperFly</option>
                                        <option value="4">SteadFast</option>
                                        <option value="0">Order Export</option>
                                    </select>
                                </div>
                            </form>

                            <form action="{{ route('manager.orders.bulk.print') }}" method="post" id="all_print_form"
                                class="mr-2">
                                @csrf
                                <div class="form-group">
                                    <button type="button" id="bulk_print_btn" class="btn btn-info btn-sm h-34">Print
                                        Invoice
                                    </button>
                                </div>
                            </form>

                        </div>

                        <div class="col-md-1 col-12 action_buttons justify-content-end">
                            <form action="" method="get" id="paginate_form">
                                <input type="hidden" name="status"
                                    value="{{ $status ? $status : request()->query('status') ?? null }}">
                                <input type="hidden" name="query" value="{{ request()->query('query') ?? null }}">
                                <input type="hidden" name="custom_range"
                                    value="{{ request()->query('custom_range') ?? null }}">
                                <input type="hidden" name="start_date"
                                    value="{{ request()->query('start_date') ?? null }}">
                                <input type="hidden" name="end_date"
                                    value="{{ request()->query('end_date') ?? null }}">
                                <div class="form-group">
                                    <select name="paginate" id="paginate" class="form-control h-34">
                                        <option value="10" {{ request()->input('paginate') == 10 ? 'selected' : '' }}>
                                            10
                                        </option>
                                        <option value="20" {{ request()->input('paginate') == 20 ? 'selected' : '' }}>
                                            20
                                        </option>
                                        <option value="100"
                                            {{ request()->input('paginate') == 100 ? 'selected' : '' }}>
                                            100</option>
                                        <option value="200"
                                            {{ request()->input('paginate') == 200 ? 'selected' : '' }}>
                                            200</option>
                                        <option value="500"
                                            {{ request()->input('paginate') == 500 ? 'selected' : '' }}>
                                            500</option>
                                        <option value="1000"
                                            {{ request()->input('paginate') == 1000 ? 'selected' : '' }}>
                                            1000</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif(Auth::guard('employee')->check())
                    <div class="row mb-2">
                        <div class="col-md-11 col-12 action_buttons">
                            <div class="form-group">
                                <a href="{{ route('employee.orders.create') }}"
                                    class="btn btn-success btn-sm mr-2 h-34">Add Order</a>
                            </div>
                        </div>

                        <div class="col-md-1 col-12 action_buttons justify-content-end">
                            <form action="" method="get" id="paginate_form">
                                <input type="hidden" name="status"
                                    value="{{ $status ? $status : request()->query('status') ?? null }}">
                                <input type="hidden" name="query" value="{{ request()->query('query') ?? null }}">
                                <input type="hidden" name="custom_range"
                                    value="{{ request()->query('custom_range') ?? null }}">
                                <input type="hidden" name="start_date"
                                    value="{{ request()->query('start_date') ?? null }}">
                                <input type="hidden" name="end_date"
                                    value="{{ request()->query('end_date') ?? null }}">
                                <div class="form-group">
                                    <select name="paginate" id="paginate" class="form-control h-34">
                                        <option value="10" {{ request()->input('paginate') == 10 ? 'selected' : '' }}>
                                            10
                                        </option>
                                        <option value="20" {{ request()->input('paginate') == 20 ? 'selected' : '' }}>
                                            20
                                        </option>
                                        <option value="100"
                                            {{ request()->input('paginate') == 100 ? 'selected' : '' }}>
                                            100</option>
                                        <option value="200"
                                            {{ request()->input('paginate') == 200 ? 'selected' : '' }}>
                                            200</option>
                                        <option value="500"
                                            {{ request()->input('paginate') == 500 ? 'selected' : '' }}>
                                            500</option>
                                        <option value="1000"
                                            {{ request()->input('paginate') == 1000 ? 'selected' : '' }}>
                                            1000</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="row">
                                    <div class="px-3 pt-2">Showing <span
                                            class="font-weight-bold text-danger">{{ $count }}</span> results</div>
                                    <div class="col d-flex justify-content-end">
                                        @if (Auth::guard('admin')->check() || Auth::guard('manager')->check() || Auth::guard('employee')->check())
                                            <form
                                                action="{{ Auth::guard('admin')->check() ? route('admin.orders') : (Auth::guard('manager')->check() ? route('manager.orders') : (Auth::guard('employee')->check() ? route('employee.orders') : '')) }}"
                                                method="get" class="form-inline">
                                                <input type="hidden" name="status"
                                                    value="{{ $status ? $status : request()->query('status') ?? null }}">
                                                <input type="hidden" name="custom_range"
                                                    value="{{ request()->query('custom_range') ?? null }}">
                                                <input type="hidden" name="start_date"
                                                    value="{{ request()->query('start_date') ?? null }}">
                                                <input type="hidden" name="end_date"
                                                    value="{{ request()->query('end_date') ?? null }}">
                                                <div class="form-group mb-1">
                                                    <input type="text" class="form-control mr-2 h-34"
                                                        placeholder="Search..." value="{{ $query }}"
                                                        name="query">
                                                </div>
                                                <div class="form-group mb-1">
                                                    <button class="btn btn-dark btn-sm mr-1 h-34">Search</button>
                                                    <a href="{{ route('admin.orders') }}"
                                                        class="btn btn-info btn-sm h-34">Reset</a>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="master"></th>
                                            <th>SL.</th>
                                            <th>Invoice ID</th>
                                            <th>Customer Info</th>
                                            <th>Products</th>
                                            <th>Total</th>
                                            <th>Courier</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Payment</th>
                                            <th>Notes</th>
                                            <th>Assigned</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($i = 1)
                                        @if (Auth::guard('admin')->check() || Auth::guard('manager')->check())
                                            @if ($orders->count() > 0)
                                                @foreach ($orders as $item)
                                                    <?php
                                                    $check_duplicate = \Illuminate\Support\Facades\DB::table('orders')
                                                        ->where([['customer_phone', $item->customer_phone], ['status', '!=', 1]])
                                                        ->count();
                                                    ?>
                                                    <tr id="tr_{{ $item->id }}"
                                                        class="{{ $check_duplicate > 1 ? 'bg-danger-light' : '' }}">
                                                        <td><input type="checkbox" class="sub_chk"
                                                                data-id="{{ $item->id }}">
                                                        </td>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $item->invoice_id }}</td>
                                                        <td>
                                                            @if ($item->ip_address)
                                                                ip: <small class="text-muted"><a
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.ip.search', 'query=' . $item->ip_address) : (Auth::guard('manager')->check() ? route('manager.ip.search', 'query=' . $item->ip_address) : (Auth::guard('employee')->check() ? route('employee.ip.search', 'query=' . $item->ip_address) : '')) }}"
                                                                        target="_blank">{{ $item->ip_address }}</a></small><br>
                                                            @endif
                                                            <span>{{ $item->customer_name }}</span> <br>
                                                            <a
                                                                href="tel:{{ $item->customer_phone }}"><span>{{ $item->customer_phone }}</span></a>
                                                            <br>
                                                            <span>{{ $item->customer_address }}</span>
                                                        </td>
                                                        <td>
                                                            @foreach ($item->get_products as $product)
                                                                {{ $product->qty }} x {{ $product->get_product->name }}
                                                                <br>
                                                                @if ($product->attributes)
                                                                    @foreach (json_decode($product->attributes, true) as $key => $attr)
                                                                        <span class="text-primary">{{ $key }} -
                                                                            {{ $attr }}</span>
                                                                        <br>
                                                                    @endforeach
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                        <td>{{ $web_settings->currency_sign }} {{ $item->total }}</td>
                                                        <td>{{ $item->get_courier->courier_name ?? 'Not Selected' }}</td>
                                                        <td>
                                                            {{ date('d M, Y', strtotime($item->order_date)) }}<br>
                                                            {{ date('h:i:s A', strtotime($item->created_at)) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn {{ $item->status == 7 ? 'btn-danger' : '' }} {{ $item->status == 6 ? 'btn-primary' : '' }} {{ $item->status == 0 ? 'btn-warning' : '' }} {{ $item->status == 1 ? 'btn-success' : '' }}{{ $item->status == 2 ? 'btn-info' : '' }}{{ $item->status == 3 ? 'btn-secondary' : '' }}{{ $item->status == 4 ? 'btn-danger' : '' }}{{ $item->status == 5 ? 'btn-warning' : '' }} status_btn  btn-sm dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                @if ($item->status == 0)
                                                                    On Hold
                                                                @endif
                                                                @if ($item->status == 1)
                                                                    Delivered
                                                                @endif
                                                                @if ($item->status == 2)
                                                                    Processing
                                                                @endif
                                                                @if ($item->status == 3)
                                                                    Pending Payment
                                                                @endif
                                                                @if ($item->status == 4)
                                                                    Canceled
                                                                @endif
                                                                @if ($item->status == 5)
                                                                    Pending Delivery
                                                                @endif
                                                                @if ($item->status == 6)
                                                                    On Delivery
                                                                @endif
                                                                @if ($item->status == 7)
                                                                    Returned
                                                                @endif
                                                            </button>
                                                            @if (Auth::guard('admin')->check())
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item {{ $item->status == 0 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 0]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 0]) : '') }}">On
                                                                        Hold</a>
                                                                    <a class="dropdown-item {{ $item->status == 2 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 2]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 2]) : '') }}">Processing</a>
                                                                    <a class="dropdown-item {{ $item->status == 1 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 1]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 1]) : '') }}">Delivered</a>
                                                                    <a class="dropdown-item {{ $item->status == 3 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 3]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 3]) : '') }}">Pending
                                                                        Payment</a>
                                                                    <a class="dropdown-item {{ $item->status == 4 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 4]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 4]) : '') }}">Canceled</a>
                                                                    <a class="dropdown-item {{ $item->status == 5 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 5]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 5]) : '') }}">Pending
                                                                        Delivery</a>
                                                                    <a class="dropdown-item {{ $item->status == 6 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 6]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 6]) : '') }}">On
                                                                        Delivery</a>
                                                                    <a class="dropdown-item {{ $item->status == 7 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 7]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 7]) : '') }}">Returned</a>
                                                                </div>
                                                            @elseif(Auth::guard('manager')->check())
                                                                @if ($item->status == 5)
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item {{ $item->status == 4 ? 'd-none' : '' }}"
                                                                            href="{{ route('employee.orders.status', [$item->id, 4]) }}">Canceled</a>
                                                                    </div>
                                                                @elseif($item->status != 6 && $item->status != 1 && $item->status != 7)
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item {{ $item->status == 0 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 0]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 0]) : '') }}">On
                                                                            Hold</a>
                                                                        <a class="dropdown-item {{ $item->status == 2 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 2]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 2]) : '') }}">Processing</a>
                                                                        <a class="dropdown-item {{ $item->status == 3 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 3]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 3]) : '') }}">Pending
                                                                            Payment</a>
                                                                        <a class="dropdown-item {{ $item->status == 4 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 4]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 4]) : '') }}">Canceled</a>
                                                                        <a class="dropdown-item {{ $item->status == 5 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 5]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 5]) : '') }}">Pending
                                                                            Delivery</a>
                                                                        <a class="dropdown-item {{ $item->status == 6 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 6]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 6]) : '') }}">On
                                                                            Delivery</a>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn {{ $item->payment_status == 0 ? 'btn-danger' : '' }} {{ $item->payment_status == 1 ? 'btn-info' : '' }} {{ $item->payment_status == 2 ? 'btn-success' : '' }} status_btn  btn-sm dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                @if ($item->payment_status == 0)
                                                                    Unpaid
                                                                @endif
                                                                @if ($item->payment_status == 1)
                                                                    Partial
                                                                @endif
                                                                @if ($item->payment_status == 2)
                                                                    Paid
                                                                @endif
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item {{ $item->payment_status == 0 ? 'd-none' : '' }}"
                                                                    href="{{ Auth::guard('admin')->check() ? route('admin.orders.payment_status', [$item->id, 0]) : (Auth::guard('manager')->check() ? route('manager.orders.payment_status', [$item->id, 0]) : '') }}">Unpaid</a>
                                                                <a class="dropdown-item {{ $item->payment_status == 1 ? 'd-none' : '' }}"
                                                                    href="{{ Auth::guard('admin')->check() ? route('admin.orders.payment_status', [$item->id, 1]) : (Auth::guard('manager')->check() ? route('manager.orders.payment_status', [$item->id, 1]) : '') }}">Partial</a>
                                                                <a class="dropdown-item {{ $item->payment_status == 2 ? 'd-none' : '' }}"
                                                                    href="{{ Auth::guard('admin')->check() ? route('admin.orders.payment_status', [$item->id, 2]) : (Auth::guard('manager')->check() ? route('manager.orders.payment_status', [$item->id, 2]) : '') }}">Paid</a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if ($item->courier_note)
                                                                <span class="text-dark"><b>C:</b>
                                                                    {{ $item->courier_note }}</span>
                                                            @endif
                                                            @if ($item->staff_note)
                                                                <br>
                                                                <span class="text-primary"><b>S:</b>
                                                                    {{ $item->staff_note }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $item->get_assigned ? $item->get_assigned->get_employee->name : '' }}
                                                            <br>
                                                            <a href="javascript:void(0);" class="single-assign-btn"
                                                                data-order_id="{{ $item->id }}"><i
                                                                    class="fa fa-edit"></i></a>
                                                        </td>

                                                        <td class="text-center">
                                                            <a href="javascript:void(0)" class="d-block mb-1 print"
                                                                data-id="{{ $item->id }}"><i
                                                                    class="fa fa-print"></i></a>
                                                            @if (Auth::guard('admin')->check())
                                                                <a href="{{ route('admin.orders.edit', $item->id) }}"
                                                                    class="d-block mb-1">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <a href="javascript:void(0)"
                                                                    class="d-block mb-1 transaction_btn"
                                                                    data-id="{{ $item->id }}">
                                                                    <i class="fa fa-exchange-alt"></i>
                                                                </a>
                                                                <a href="{{ route('admin.orders.delete', $item->id) }}"
                                                                    class="d-block mb-1"
                                                                    onclick="return confirm('Are you sure to delete this?')"><i
                                                                        class="fa fa-trash"></i></a>
                                                            @endif

                                                            @if (Auth::guard('manager')->check())
                                                                <a href="{{ route('manager.orders.edit', $item->id) }}"
                                                                    class="d-block mb-1">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <a href="javascript:void(0)"
                                                                    class="d-block mb-1 transaction_btn"
                                                                    data-id="{{ $item->id }}">
                                                                    <i class="fa fa-exchange-alt"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="13" class="text-center text-danger font-weight-bold">No
                                                        Data Found!
                                                    </td>
                                                </tr>
                                            @endif
                                        @elseif(Auth::guard('employee')->check())
                                            @if ($orders->count() > 0)
                                                @foreach ($orders as $item)
                                                    <?php
                                                    $check_duplicate = \Illuminate\Support\Facades\DB::table('orders')
                                                        ->where([['customer_phone', $item->customer_phone], ['status', '!=', 1]])
                                                        ->count();
                                                    ?>
                                                    <tr id="tr_{{ $item->id }}"
                                                        class="{{ $check_duplicate > 1 ? 'bg-danger-light' : '' }}">
                                                        <td><input type="checkbox" class="sub_chk"
                                                                data-id="{{ $item->id }}">
                                                        </td>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $item->invoice_id }}</td>
                                                        <td>
                                                            @if ($item->ip_address)
                                                                ip: <small class="text-muted"><a
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.ip.search', 'query=' . $item->ip_address) : (Auth::guard('manager')->check() ? route('manager.ip.search', 'query=' . $item->ip_address) : (Auth::guard('employee')->check() ? route('employee.ip.search', 'query=' . $item->ip_address) : '')) }}"
                                                                        target="_blank">{{ $item->ip_address }}</a></small><br>
                                                            @endif
                                                            <span>{{ $item->customer_name }}</span> <br>
                                                            <a
                                                                href="tel:{{ $item->customer_phone }}"><span>{{ $item->customer_phone }}</span></a>
                                                            <br>
                                                            <span>{{ $item->customer_address }}</span>
                                                        </td>
                                                        <td>
                                                            @foreach ($item->get_products as $product)
                                                                {{ $product->qty }} x {{ $product->get_product->name }}
                                                                <br>
                                                                @if ($product->attributes)
                                                                    @foreach (json_decode($product->attributes, true) as $key => $attr)
                                                                        <span class="text-primary">{{ $key }} -
                                                                            {{ $attr }}</span>
                                                                        <br>
                                                                    @endforeach
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                        <td>{{ $web_settings->currency_sign }} {{ $item->total }}</td>
                                                        <td>{{ $item->get_courier->courier_name ?? 'Not Selected' }}</td>
                                                        <td>
                                                            {{ date('d M, Y', strtotime($item->order_date)) }}<br>
                                                            {{ date('h:i:s A', strtotime($item->created_at)) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn {{ $item->status == 7 ? 'btn-danger' : '' }} {{ $item->status == 6 ? 'btn-primary' : '' }} {{ $item->status == 0 ? 'btn-warning' : '' }} {{ $item->status == 1 ? 'btn-success' : '' }}{{ $item->status == 2 ? 'btn-info' : '' }}{{ $item->status == 3 ? 'btn-secondary' : '' }}{{ $item->status == 4 ? 'btn-danger' : '' }}{{ $item->status == 5 ? 'btn-warning' : '' }} status_btn  btn-sm dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                @if ($item->status == 0)
                                                                    On Hold
                                                                @endif
                                                                @if ($item->status == 1)
                                                                    Delivered
                                                                @endif
                                                                @if ($item->status == 2)
                                                                    Processing
                                                                @endif
                                                                @if ($item->status == 3)
                                                                    Pending Payment
                                                                @endif
                                                                @if ($item->status == 4)
                                                                    Canceled
                                                                @endif
                                                                @if ($item->status == 5)
                                                                    Pending Delivery
                                                                @endif
                                                                @if ($item->status == 6)
                                                                    On Delivery
                                                                @endif
                                                                @if ($item->status == 7)
                                                                    Returned
                                                                @endif
                                                            </button>
                                                            @if ($item->status == 5)
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item {{ $item->status == 4 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 4]) }}">Canceled</a>
                                                                </div>
                                                            @elseif($item->status != 6 && $item->status != 1 && $item->status != 7)
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item {{ $item->status == 0 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 0]) }}">On
                                                                        Hold</a>
                                                                    <a class="dropdown-item {{ $item->status == 2 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 2]) }}">Processing</a>
                                                                    <a class="dropdown-item {{ $item->status == 3 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 3]) }}">Pending
                                                                        Payment</a>
                                                                    <a class="dropdown-item {{ $item->status == 4 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 4]) }}">Canceled</a>
                                                                    <a class="dropdown-item {{ $item->status == 5 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 5]) }}">Pending
                                                                        Delivery</a>
                                                                    {{-- <a class="dropdown-item {{$item->status==6?"d-none":""}}"
                                                                   href="{{route('employee.orders.status',[$item->id,6])}}">On Delivery</a> --}}
                                                                </div>
                                                            @endif

                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn {{ $item->payment_status == 0 ? 'btn-danger' : '' }} {{ $item->payment_status == 1 ? 'btn-info' : '' }} {{ $item->payment_status == 2 ? 'btn-success' : '' }} status_btn  btn-sm dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                @if ($item->payment_status == 0)
                                                                    Unpaid
                                                                @endif
                                                                @if ($item->payment_status == 1)
                                                                    Partial
                                                                @endif
                                                                @if ($item->payment_status == 2)
                                                                    Paid
                                                                @endif
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item {{ $item->payment_status == 0 ? 'd-none' : '' }}"
                                                                    href="{{ route('employee.orders.payment_status', [$item->id, 0]) }}">Unpaid</a>
                                                                <a class="dropdown-item {{ $item->payment_status == 1 ? 'd-none' : '' }}"
                                                                    href="{{ route('employee.orders.payment_status', [$item->id, 1]) }}">Partial</a>
                                                                <a class="dropdown-item {{ $item->payment_status == 2 ? 'd-none' : '' }}"
                                                                    href="{{ route('employee.orders.payment_status', [$item->id, 2]) }}">Paid</a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if ($item->courier_note)
                                                                <span class="text-dark"><b>C:</b>
                                                                    {{ $item->courier_note }}</span>
                                                            @endif
                                                            @if ($item->staff_note)
                                                                <br>
                                                                <span class="text-primary"><b>S:</b>
                                                                    {{ $item->staff_note }}</span>
                                                            @endif
                                                        </td>
                                                        <td>Self</td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0)" class="d-block mb-1 print"
                                                                data-id="{{ $item->id }}"><i
                                                                    class="fa fa-print"></i></a>
                                                            <a href="{{ route('employee.orders.edit', $item->id) }}"
                                                                class="d-block mb-1">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="13" class="text-center text-danger font-weight-bold">No
                                                        Data Found!
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $orders->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- user assing modal --}}
    <div class="modal fade" id="user_assign" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
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

    {{-- transaction view modal --}}
    <div class="modal fade" id="transaction_view" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Transactions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body transaction_put"></div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('backEnd/assets/vendor/datetimepicker/moment.min.js') }}"></script>
    <script src="{{ asset('backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>

    <script>
        $('.datetimepicker').datetimepicker({
            icons: {
                next: 'fa fa-angle-right',
                previous: 'fa fa-angle-left'
            },
            format: 'DD-MM-YYYY',
            // defaultDate: new Date(),
        });

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

            //courier export
            $('#courier_csv').on('change', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    $('#all_ord_id').val(allVals);
                    $('#all_courier_csv').submit();
                }
            });

            //single assign
            $('.single-assign-btn').click(function() {
                $('#order_id_a').val($(this).data('order_id'));
                $('#user_assign').modal('show');
            });

            //paginate
            $('#paginate').on('change', function() {
                $('#paginate_form').submit();
            });

            //date range
            $('#custom_range').on('change', function() {
                var val = $(this).val();
                if (val != '') {
                    $('#start_date').val('').attr('disabled', true);
                    $('#end_date').val('').attr('disabled', true);
                } else {
                    $('#start_date').attr('disabled', false);
                    $('#end_date').attr('disabled', false);
                }
                //$('#paginate_form').submit();
            });

            //transaction view
            $('.transaction_btn').on('click', function() {
                $('#transaction_view').modal('show');
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ Auth::guard('admin')->check() ? route('admin.orders.transaction_view') : (Auth::guard('manager')->check() ? route('manager.orders.transaction_view') : '') }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        id: $(this).data('id')
                    },
                    success: function(data) {
                        $('.transaction_put').html(data);
                    }
                });
            });
        });
    </script>
@endsection

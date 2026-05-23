@extends('backEnd.admin.layouts.master')

@section('title')
    All Orders
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendor/bootstrap/css/bootstrap_toggle.min.css') }}">
    <style>
        @media (max-width: 576px) {
            .form-inline .form-control {
                display: inline-block;
                width: auto;
                vertical-align: middle;
            }
        }

        .total {
            display: none;
        }

        .tooltip-inner {
            text-align: left;
            white-space: break-spaces;
        }

        .trash {
            position: relative;
            height: 24.14px;
            display: inline-flex;
            align-items: center;
            background: black;
            color: white;
            font-size: 12px;
            padding: 15px 10px;
            border-radius: 3px;
            gap: 10px;
        }

        .trash:hover {
            color: white;
        }


        .products {
            display: flex;
            gap: 5px;
        }

        .products-td {
            display: flex;
            flex-direction: column;
            gap: 5px;
            border-bottom: none !important;
        }

        .product-info {
            display: flex;
            flex-direction: column;
        }

        .pageheader-title {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .copy-btn {
            cursor: pointer;
            display: inline-block;
            margin-left: 5px;
            position: relative;
            color: #007bff;
            transition: color 0.2s;
        }

        .copy-btn:hover {
            color: #0056b3;
        }

        .copy-btn .copy-tooltip {
            visibility: hidden;
            background-color: #28a745;
            color: #fff;
            text-align: center;
            border-radius: 3px;
            padding: 3px 8px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .copy-btn .copy-tooltip::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #28a745 transparent transparent transparent;
        }

        .copy-btn .copy-tooltip.show {
            visibility: visible;
            opacity: 1;
        }

        .dashboard-content {
            overflow-x: hidden;
        }

        #main_filter {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: flex-start;
        }

        @media (max-width: 768px) {
            #main_filter .form-group {
                width: 100%;
                margin-right: 0 !important;
            }

            #main_filter .form-group .form-control {
                width: 100%;
            }
        }

        @media (min-width: 769px) {
            #main_filter .form-group {
                width: 119px;
            }
        }

        @media (min-width: 992px) {
            .order-card {
                margin-left: -5px;
                margin-right: -5px;
                justify-content: space-between;
            }

            .order-card .card-body {
                padding: 5px 8px;
            }

            .order-card>.col-lg-2,
            .order-card>.col-xl-2 {
                flex: 0 0 10%;
                max-width: 10%;
                padding: 5px 5px;
            }

            .order-card h2 {
                font-size: 24px;
            }

            .order-card h5 {
                font-size: 14px;
            }

            .order-card h6 {
                font-size: 12px;
            }
        }
    </style>
@endsection
@php
    $orders = $data['orders'] ?? [];
    $total_order = $data['total_order'] ?? 0;
    $total_amount = $data['total_order_amount'] ?? 0;

    $total_hold_order = $data['total_hold_order'] ?? 0;
    $total_hold_amount = $data['total_hold_amount'] ?? 0;

    $total_deliver_order = $data['total_deliver_order'] ?? 0;
    $total_deliver_amount = $data['total_deliver_amount'] ?? 0;

    $total_process_order = $data['total_process_order'] ?? 0;
    $total_process_amount = $data['total_process_amount'] ?? 0;

    $total_pend_pay_order = $data['total_pend_pay_order'] ?? 0;
    $total_pend_pay_amount = $data['total_pend_pay_amount'] ?? 0;

    $total_cancel_order = $data['total_cancel_order'] ?? 0;
    $total_cancel_amount = $data['total_cancel_amount'] ?? 0;

    $total_pending_invoice_order = $data['total_pending_invoice_order'] ?? 0;
    $total_pending_invoice_amount = $data['total_pending_invoice_amount'] ?? 0;

    $total_on_delivery_order = $data['total_on_delivery_order'] ?? 0;
    $total_on_delivery_amount = $data['total_on_delivery_amount'] ?? 0;

    $total_pending_return_order = $data['total_pending_return_order'] ?? 0;
    $total_pending_return_amount = $data['total_pending_return_amount'] ?? 0;

    $total_paid_return_order = $data['total_paid_return_order'] ?? 0;
    $total_paid_return_amount = $data['total_paid_return_amount'] ?? 0;

    $total_exchange_order = $data['total_exchange_order'] ?? 0;
    $total_exchange_amount = $data['total_exchange_amount'] ?? 0;

    $total_courier_hold_order = $data['total_courier_hold_order'] ?? 0;
    $total_courier_hold_amount = $data['total_courier_hold_amount'] ?? 0;

    $total_nr_1_order = $data['total_nr_1_order'] ?? 0;
    $total_nr_1_amount = $data['total_nr_1_amount'] ?? 0;

    $total_invoiced_order = $data['total_invoiced_order'] ?? 0;
    $total_invoiced_amount = $data['total_invoiced_amount'] ?? 0;

    $total_return_order = $data['total_return_order'] ?? 0;
    $total_return_amount = $data['total_return_amount'] ?? 0;

    $total_incomplete_order = $data['total_incomplete_order'] ?? 0;
    $total_incomplete_amount = $data['total_incomplete_amount'] ?? 0;

    $total_confirmed_order = $data['total_confirmed_order'] ?? 0;
    $total_confirmed_amount = $data['total_confirmed_amount'] ?? 0;

    $total_stock_out_order = $data['total_stock_out_order'] ?? 0;
    $total_stock_out_amount = $data['total_stock_out_amount'] ?? 0;

    $total_partial_delivery_order = $data['total_partial_delivery_order'] ?? 0;
    $total_partial_delivery_amount = $data['total_partial_delivery_amount'] ?? 0;

    $total_lost_order = $data['total_lost_order'] ?? 0;
    $total_lost_amount = $data['total_lost_amount'] ?? 0;

    $total_timeover_order = $data['total_timeover_order'] ?? 0;
    $total_issue_order = $data['total_issue_order'] ?? 0;

    $shippings = $data['shippings'] ?? [];

    $shipping_id = $data['shipping_id'] ?? null;
    $courier_status = $data['courier_status'] ?? null;

    $couriers = $data['couriers'] ?? [];
    $sources = $data['sources'] ?? [];
    $utm_sources = $data['utm_sources'] ?? [];
    $slave_domains = $data['slave_domains'] ?? [];
    $query = $query ?? null;
    $courier_id = $data['courier_id'] ?? null;
    $status = $status ?? null;
    $employees = $data['employees'] ?? [];
    $last_order =
        \Illuminate\Support\Facades\DB::table('orders')->select('created_at')->latest('id')->first()->created_at ??
        null;
    $notes = \Illuminate\Support\Facades\DB::table('note_settings')->pluck('text', 'id');
    $count = $data['count'] ?? 0;
    $processingStatusValue = \App\Enums\OrderStatus::Processing->value;
    $confirmedStatusValue = \App\Enums\OrderStatus::Confirmed->value;
    $cancelledStatusValue = \App\Enums\OrderStatus::Cancelled->value;
@endphp
@section('body')
    {{-- @dd($sts) --}}
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <!-- ============================================================== -->
                <!-- pageheader  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12 pr-1">
                        <div class="page-header">
                            <h2 class="pageheader-title">{{ $status ?? 'All' }} Orders &nbsp;<a
                                    href="{{ route('orders.steadfast.order.sync') }}" class="text-primary font-14"><i
                                        class="fa fa-sync"></i>
                                </a>


                                <a href="{{ route('admin.orders.trash') }}" class="trash" title="Trash">
                                    Trash
                                    <span class="badge bg-danger trash-count">
                                        {{ $data['total_trash_order'] ?? 0 }}
                                    </span>
                                </a>


                            </h2>
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
                    <div class="col-md-9 col-12 d-flex justify-content-end pl-1">
                        <form
                            action="{{ Auth::guard('admin')->check() ? route('admin.orders') : (Auth::guard('manager')->check() ? route('manager.orders') : (Auth::guard('employee')->check() ? route('employee.orders') : '')) }}"
                            method="get" id="main_filter" class="action_buttons">
                            <input type="hidden" name="query" value="{{ request()->query('query') ?? null }}">
                            <input type="hidden" name="status"
                                value="{{ $status ? $status : request()->query('status') ?? null }}">
                            <input type="hidden" name="source" value="{{ request()->query('source') ?? null }}">
                            <input type="hidden" name="utm_source" value="{{ request()->query('utm_source') ?? null }}">
                            <input type="hidden" name="slave_domain"
                                value="{{ request()->query('slave_domain') ?? null }}">


                            <div class="form-group mr-1">
                                <select name="shipping_id" id="shipping_id" class="form-control h-34">
                                    <option value="">Select Shipping</option>
                                    @foreach ($shippings as $id => $shipping)
                                        <option value="{{ $id }}" {{ $shipping_id == $id ? 'selected' : '' }}>
                                            {{ $shipping }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mr-1">
                                <select name="courier_id" id="courier_id" class="form-control h-34">
                                    <option value="">Select Courier</option>
                                    @foreach ($couriers as $id => $courier)
                                        <option value="{{ $id }}" {{ $courier_id == $id ? 'selected' : '' }}>
                                            {{ $courier }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mr-1">
                                <select name="payment_status" id="payment_status" class="form-control h-34">
                                    <option value="">Payment Status</option>
                                    <option value="0"
                                        {{ request()->query('payment_status') == '0' ? 'selected' : '' }}>
                                        Unpaid
                                    </option>
                                    <option value="1"
                                        {{ request()->query('payment_status') == '1' ? 'selected' : '' }}>
                                        Partial Paid
                                    </option>
                                    <option value="2"
                                        {{ request()->query('payment_status') == '2' ? 'selected' : '' }}>Paid
                                    </option>
                                </select>
                            </div>

                            <div class="form-group mr-1">
                                <select name="custom_range" id="custom_range" class="form-control h-34">
                                    <option value="">Custom Range</option>
                                    <option value="today"
                                        {{ request()->query('custom_range') == 'today' ? 'selected' : '' }}>
                                        Today
                                    </option>
                                    <option value="yesterday"
                                        {{ request()->query('custom_range') == 'yesterday' ? 'selected' : '' }}>
                                        Yesterday
                                    </option>
                                    <option value="last_7_days"
                                        {{ request()->query('custom_range') == 'last_7_days' ? 'selected' : '' }}>Last
                                        7 Days
                                    </option>
                                    <option value="this_month"
                                        {{ request()->query('custom_range') == 'this_month' ? 'selected' : '' }}>This
                                        Month
                                    </option>
                                    <option value="last_month"
                                        {{ request()->query('custom_range') == 'last_month' ? 'selected' : '' }}>Last
                                        Month
                                    </option>
                                    <option value="last_6_months"
                                        {{ request()->query('custom_range') == 'last_6_months' ? 'selected' : '' }}>
                                        Last 6 Months
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
                    <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                        <h4><b>Last Order:</b> {{ \Carbon\Carbon::parse($last_order)->diffForHumans() }}</h4>
                        @if (Auth::guard('admin')->check() || Auth::guard('manager')->check())
                            <input type="checkbox" class="toggle_btn" data-size="sm" data-toggle="toggle"
                                data-offstyle="danger">
                        @endif

                    </div>
                </div>
                <div class="row mb-3 order-card">
                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders') : (Auth::guard('manager')->check() ? route('manager.orders') : (Auth::guard('employee')->check() ? route('employee.orders') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_order > 0 ? $total_order : 0 }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_amount > 0 ? number_format($total_amount, 2) : 0 }}</h6>
                                    </div>
                                    <h5 class="h5-s">Order</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Processing') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Processing') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Processing') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_process_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_process_amount > 0 ? number_format($total_process_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_process_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-warning h5-s">Processing</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=No Response') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=No Response') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=No Response') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_nr_1_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_nr_1_amount > 0 ? number_format($total_nr_1_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_nr_1_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-danger h5-s">No Response</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-2 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Hold') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Hold') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Hold') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_hold_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_hold_amount > 0 ? number_format($total_hold_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_hold_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-info h5-s">Hold</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Pending Payment') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Pending Payment') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Pending Payment') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_pend_pay_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_pend_pay_amount > 0 ? number_format($total_pend_pay_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_pend_pay_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-warning h5-s">Pending Payment</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Cancelled') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Cancelled') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Cancelled') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_cancel_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_cancel_amount > 0 ? number_format($total_cancel_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_cancel_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-danger h5-s">Cancelled</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Confirmed') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Confirmed') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Confirmed') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_confirmed_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_confirmed_amount > 0 ? number_format($total_confirmed_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_confirmed_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-success h5-s">Confirmed</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Pending Invoice') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Pending Invoice') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Pending Invoice') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_pending_invoice_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_pending_invoice_amount > 0 ? number_format($total_pending_invoice_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_pending_invoice_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-secondary h5-s">Pend. Invoice</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Invoiced') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Invoiced') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Invoiced') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_invoiced_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_invoiced_amount > 0 ? number_format($total_invoiced_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_invoiced_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-success h5-s">Invoiced</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Stock Out') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Stock Out') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Stock Out') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_stock_out_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_stock_out_amount > 0 ? number_format($total_stock_out_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_stock_out_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-success h5-s">Stock Out</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Courier') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Courier') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Courier') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_courier_hold_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_courier_hold_amount > 0 ? number_format($total_courier_hold_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_courier_hold_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-danger h5-s">Courier</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=On Delivery') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=On Delivery') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=On Delivery') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_on_delivery_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_on_delivery_amount > 0 ? number_format($total_on_delivery_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_on_delivery_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-info h5-s">On Delivery</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Delivered') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Delivered') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Delivered') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_deliver_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_deliver_amount > 0 ? number_format($total_deliver_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_deliver_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-warning h5-s">Delivered</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Partial Delivery') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Partial Delivery') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Partial Delivery') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_partial_delivery_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_partial_delivery_amount > 0 ? number_format($total_partial_delivery_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_partial_delivery_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-success h5-s">Partial Delivery</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Pending Return') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Pending Return') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Pending Return') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_pending_return_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_pending_return_amount > 0 ? number_format($total_pending_return_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_pending_return_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-warning h5-s">Pending Return</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Paid Return') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Paid Return') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Paid Return') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_paid_return_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_paid_return_amount > 0 ? number_format($total_paid_return_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_paid_return_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-warning h5-s">Paid Return</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Exchange') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Exchange') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Exchange') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_exchange_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_exchange_amount > 0 ? number_format($total_exchange_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_exchange_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-warning h5-s">Exchange</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Return') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Return') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Return') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_return_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_return_amount > 0 ? number_format($total_return_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_return_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-success h5-s">Return</h5>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-2">
                        <a
                            href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Lost') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Lost') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Lost') : '')) }}">
                            <div class="card border-3">
                                <div class="card-body">
                                    <div class="metric-value">
                                        <h2 class="mb-0">{{ $total_lost_order }}</h2>
                                        <h6 class="mb-0 total text-primary">৳
                                            {{ $total_lost_amount > 0 ? number_format($total_lost_amount, 2) : 0 }}
                                        </h6>
                                        <span
                                            class="percentage-badge">{{ $total_order > 0 ? number_format(($total_lost_order / $total_order) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <h5 class="text-dark h5-s">Lost</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                @if (Auth::guard('admin')->check())
                    <div class="row mb-2">
                        <div class="col-md-11 col-12 action_buttons">
                            <div class="form-group">
                                <a href="{{ route('admin.orders.create') }}"
                                    class="btn btn-success btn-sm mr-2 h-34">Add
                                    Order</a>
                            </div>
                            <form action="{{ route('admin.orders.send.to.courier') }}" method="post"
                                id="send_to_courier_form" class="mr-2 {{ $sts == 10 ? 'd-block' : 'd-none' }}">
                                @csrf
                                <input type="hidden" id="all_send_to_status" name="all_status">
                                <select name="send_to_courier" id="send_to_courier" class="form-control h-34">
                                    <option value="">--Send To--</option>
                                    @foreach (\App\Models\Courier::where('status', 1)->get() as $courier)
                                        <option value="{{ $courier->id }}">{{ $courier->courier_name }}</option>
                                    @endforeach
                                </select>
                            </form>

                            <form action="{{ route('admin.orders.all.status') }}" method="post" id="all_status_form"
                                class="mr-2">
                                @csrf
                                <input type="hidden" id="all_status" name="all_status">
                                <select name="status" id="status" class="form-control h-34">
                                    <option value="">Select Status</option>
                                    <option value="2">Processing</option>
                                    <option value="9">No Response</option>
                                    <option value="0">Hold</option>
                                    <option value="3">Pending Payment</option>
                                    <option value="4">Cancelled</option>
                                    <option value="13">Confirmed</option>
                                    <option value="5">Pending Invoice</option>
                                    <option value="10">Invoiced</option>
                                    <option value="14">Stock Out</option>
                                    <option value="8">Courier</option>
                                    <option value="6">On Delivery</option>
                                    <option value="1">Delivered</option>
                                    <option value="15">Partial Delivery</option>
                                    <option value="7">Pending Return</option>
                                    <option value="17">Paid Return</option>
                                    <option value="18">Exchange</option>
                                    <option value="11">Return</option>
                                    <option value="16">Lost</option>
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

                            <form action="{{ route('admin.orders.bulk.call') }}" method="post" id="bulk_call_form"
                                class="mr-1">
                                @csrf
                                <input type="hidden" name="all_order_id" id="all_call_order_id">
                                <button type="button" id="bulk_call_btn" class="btn btn-info btn-sm">
                                    <i class="fa fa-phone"></i> Bulk Call
                                </button>
                            </form>

                            <form action="{{ route('admin.orders.courier_csv') }}" method="post" id="all_courier_csv"
                                class="mr-1">
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
                                class="mr-1 {{ $sts == 5 ? 'd-block' : 'd-none' }}">
                                @csrf
                                <div class="form-group">
                                    <button type="button" id="bulk_print_btn" class="btn btn-info btn-sm h-34">Print
                                        Invoice
                                    </button>
                                </div>
                            </form>

                            <form action="{{ route('admin.orders.bulk.label.print') }}" method="post"
                                id="all_print_form" class="mr-1 {{ $sts == 5 ? 'd-block' : 'd-none' }}">
                                @csrf
                                <div class="form-group">
                                    <button type="button" id="bulk_label_print_btn" class="btn btn-warning btn-sm h-34">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-file-invoice" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                            <path
                                                d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                            </path>
                                            <path d="M9 7l1 0"></path>
                                            <path d="M9 13l6 0"></path>
                                            <path d="M13 17l2 0"></path>
                                        </svg>
                                    </button>
                                </div>
                            </form>

                            <form action="{{ route('admin.orders.bulk.delete') }}" method="post" id="bulk_delete_form"
                                class="mr-1">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" id="all_id" name="all_id">
                                    <button type="button" id="bulk_delete" class="btn btn-danger btn-sm h-34">Delete
                                    </button>
                                </div>
                            </form>

                            <form action="{{ route('admin.orders.bulk.equal.assign') }}" method="post"
                                id="equal_assign_form" class="mr-1">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" id="eq_assign_order_ids" name="eq_assign_order_ids">
                                    <button type="button" id="equal_assign" class="btn btn-primary btn-sm h-34">Eq.
                                        Assign
                                    </button>
                                </div>
                            </form>

                            @php
                                $isNonForwardedPage =
                                    request()->routeIs('admin.orders.filter.non_forwarded') ||
                                    request()->routeIs('manager.orders.filter.non_forwarded');
                            @endphp

                            @if (!$isNonForwardedPage)
                                {{-- Filter non-forwarded orders --}}
                                <form action="{{ route('admin.orders.filter.non_forwarded') }}" method="get"
                                    class="mr-2">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-info btn-sm h-34">
                                            <i class="fa fa-filter"></i> Non-Forwarded
                                        </button>
                                    </div>
                                </form>
                            @else
                                {{-- Bulk forward to master --}}
                                <form action="{{ route('admin.orders.bulk.forward_to_master') }}" method="post"
                                    id="bulk_forward_form" class="mr-2">
                                    @csrf
                                    <div class="form-group">
                                        <input type="hidden" id="bulk_forward_order_ids" name="order_ids">
                                        <button type="button" id="bulk_forward_btn" class="btn btn-success btn-sm h-34">
                                            <i class="fa fa-paper-plane"></i> Forward to Master
                                        </button>
                                    </div>
                                </form>
                            @endif
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
                                <input type="hidden" name="product_id"
                                    value="{{ request()->query('product_id') ?? null }}">
                                <input type="hidden" name="employee_id"
                                    value="{{ request()->query('employee_id') ?? null }}">
                                <input type="hidden" name="source" value="{{ request()->query('source') ?? null }}">
                                <input type="hidden" name="utm_source"
                                    value="{{ request()->query('utm_source') ?? null }}">
                                <input type="hidden" name="slave_domain"
                                    value="{{ request()->query('slave_domain') ?? null }}">
                                <div class="form-group">
                                    <select name="paginate" id="paginate" class="form-control h-34">
                                        <option value="10"
                                            {{ request()->input('paginate') == 10 ? 'selected' : '' }}>
                                            10
                                        </option>
                                        <option value="50"
                                            {{ request()->input('paginate') == 50 ? 'selected' : '' }}>
                                            50
                                        </option>
                                        <option value="20"
                                            {{ request()->input('paginate') == 20 ? 'selected' : '' }}>
                                            20
                                        </option>
                                        <option value="100"
                                            {{ request()->input('paginate') == 100 ? 'selected' : '' }}>
                                            100
                                        </option>
                                        <option value="200"
                                            {{ request()->input('paginate') == 200 ? 'selected' : '' }}>
                                            200
                                        </option>
                                        <option value="500"
                                            {{ request()->input('paginate') == 500 ? 'selected' : '' }}>
                                            500
                                        </option>
                                        <option value="1000"
                                            {{ request()->input('paginate') == 1000 ? 'selected' : '' }}>
                                            1000
                                        </option>
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
                                    <option value="2">Processing</option>
                                    <option value="9">No Response</option>
                                    <option value="0">Hold</option>
                                    <option value="3">Pending Payment</option>
                                    <option value="4">Cancelled</option>
                                    <option value="13">Confirmed</option>
                                    <option value="5">Pending Invoice</option>
                                    <option value="10">Invoiced</option>
                                    <option value="14">Stock Out</option>
                                    <option value="8">Courier</option>
                                    <option value="6">On Delivery</option>
                                    <option value="1">Delivered</option>
                                    <option value="15">Partial Delivery</option>
                                    <option value="7">Pending Return</option>
                                    <option value="17">Paid Return</option>
                                    <option value="18">Exchange</option>
                                    <option value="11">Return</option>
                                    <option value="16">Lost</option>
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

                            <form action="{{ route('manager.orders.courier_csv') }}" method="post"
                                id="all_courier_csv" class="mr-2">
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

                            <form action="{{ route('manager.orders.bulk.label.print') }}" method="post"
                                id="all_print_form" class="mr-2">
                                @csrf
                                <div class="form-group">
                                    <button type="button" id="bulk_label_print_btn" class="btn btn-warning btn-sm h-34">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-file-invoice" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                            <path
                                                d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                            </path>
                                            <path d="M9 7l1 0"></path>
                                            <path d="M9 13l6 0"></path>
                                            <path d="M13 17l2 0"></path>
                                        </svg>
                                    </button>
                                </div>
                            </form>

                            <form action="{{ route('manager.orders.bulk.call') }}" method="post" id="bulk_call_form"
                                class="mr-2">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" name="all_order_id" id="all_call_order_id">
                                    <button type="button" id="bulk_call_btn" class="btn btn-info btn-sm h-34">
                                        <i class="fa fa-phone"></i> Bulk Call
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
                                <input type="hidden" name="product_id"
                                    value="{{ request()->query('product_id') ?? null }}">
                                <input type="hidden" name="employee_id"
                                    value="{{ request()->query('employee_id') ?? null }}">
                                <input type="hidden" name="source" value="{{ request()->query('source') ?? null }}">
                                <input type="hidden" name="utm_source"
                                    value="{{ request()->query('utm_source') ?? null }}">
                                <input type="hidden" name="slave_domain"
                                    value="{{ request()->query('slave_domain') ?? null }}">
                                <div class="form-group">
                                    <select name="paginate" id="paginate" class="form-control h-34">
                                        <option value="10"
                                            {{ request()->input('paginate') == 10 ? 'selected' : '' }}>
                                            10
                                        </option>
                                        <option value="50"
                                            {{ request()->input('paginate') == 50 ? 'selected' : '' }}>
                                            50
                                        </option>
                                        <option value="20"
                                            {{ request()->input('paginate') == 20 ? 'selected' : '' }}>
                                            20
                                        </option>
                                        <option value="100"
                                            {{ request()->input('paginate') == 100 ? 'selected' : '' }}>
                                            100
                                        </option>
                                        <option value="200"
                                            {{ request()->input('paginate') == 200 ? 'selected' : '' }}>
                                            200
                                        </option>
                                        <option value="500"
                                            {{ request()->input('paginate') == 500 ? 'selected' : '' }}>
                                            500
                                        </option>
                                        <option value="1000"
                                            {{ request()->input('paginate') == 1000 ? 'selected' : '' }}>
                                            1000
                                        </option>
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

                            <form action="{{ route('employee.orders.bulk.call') }}" method="post" id="bulk_call_form"
                                class="mr-2">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" name="all_order_id" id="all_call_order_id">
                                    <button type="button" id="bulk_call_btn" class="btn btn-info btn-sm h-34">
                                        <i class="fa fa-phone"></i> Bulk Call
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
                                <input type="hidden" name="product_id"
                                    value="{{ request()->query('product_id') ?? null }}">
                                <input type="hidden" name="employee_id"
                                    value="{{ request()->query('employee_id') ?? null }}">
                                <input type="hidden" name="source" value="{{ request()->query('source') ?? null }}">
                                <input type="hidden" name="utm_source"
                                    value="{{ request()->query('utm_source') ?? null }}">
                                <input type="hidden" name="slave_domain"
                                    value="{{ request()->query('slave_domain') ?? null }}">
                                <div class="form-group">
                                    <select name="paginate" id="paginate" class="form-control h-34">
                                        <option value="10"
                                            {{ request()->input('paginate') == 10 ? 'selected' : '' }}>10
                                        </option>
                                        <option value="50"
                                            {{ request()->input('paginate') == 50 ? 'selected' : '' }}>50
                                        </option>
                                        <option value="20"
                                            {{ request()->input('paginate') == 20 ? 'selected' : '' }}>20
                                        </option>
                                        <option value="100"
                                            {{ request()->input('paginate') == 100 ? 'selected' : '' }}>
                                            100
                                        </option>
                                        <option value="200"
                                            {{ request()->input('paginate') == 200 ? 'selected' : '' }}>
                                            200
                                        </option>
                                        <option value="500"
                                            {{ request()->input('paginate') == 500 ? 'selected' : '' }}>
                                            500
                                        </option>
                                        <option value="1000"
                                            {{ request()->input('paginate') == 1000 ? 'selected' : '' }}>
                                            1000
                                        </option>
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
                                            class="font-weight-bold text-danger">{{ $count }}</span> results
                                    </div>
                                    <div class="col d-flex justify-content-end">
                                        @if (Auth::guard('admin')->check() || Auth::guard('manager')->check() || Auth::guard('employee')->check())
                                            <form
                                                action="{{ Auth::guard('admin')->check() ? route('admin.orders') : (Auth::guard('manager')->check() ? route('manager.orders') : (Auth::guard('employee')->check() ? route('employee.orders') : '')) }}"
                                                method="get" class="form-inline">
                                                {{-- <input type="hidden" name="status" value="{{$status ? $status : (request()->query('status')??null)}}"> --}}
                                                <input type="hidden" name="custom_range"
                                                    value="{{ request()->query('custom_range') ?? null }}">
                                                <input type="hidden" name="start_date"
                                                    value="{{ request()->query('start_date') ?? null }}">
                                                <input type="hidden" name="end_date"
                                                    value="{{ request()->query('end_date') ?? null }}">
                                                <div class="form-group mb-1">
                                                    <select name="source" class="form-control mr-2 h-34"
                                                        style="width: 130px;">
                                                        <option value="">Source</option>
                                                        @foreach ($sources as $source)
                                                            <option value="{{ $source }}"
                                                                {{ request()->query('source') == $source ? 'selected' : '' }}>
                                                                {{ ucfirst($source) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group mb-1">
                                                    <select name="utm_source" class="form-control mr-2 h-34"
                                                        style="width: 140px;">
                                                        <option value="">UTM Source</option>
                                                        @foreach ($utm_sources as $utm_source)
                                                            <option value="{{ $utm_source }}"
                                                                {{ request()->query('utm_source') == $utm_source ? 'selected' : '' }}>
                                                                {{ ucfirst($utm_source) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group mb-1">
                                                    <select name="slave_domain" class="form-control mr-2 h-34"
                                                        style="width: 150px;">
                                                        <option value="">Slave Domain</option>
                                                        @foreach ($slave_domains as $slave_domain)
                                                            <option value="{{ $slave_domain }}"
                                                                {{ request()->query('slave_domain') == $slave_domain ? 'selected' : '' }}>
                                                                {{ $slave_domain }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group mb-1">
                                                    <select name="courier_status" id="courier_status"
                                                        class="form-control mr-2 h-34" style="width: 150px;">
                                                        <option value="">Select Courier Status</option>
                                                        <option value="At Sorting Hub"
                                                            {{ request()->query('courier_status') == 'At Sorting Hub' ? 'selected' : '' }}>
                                                            At Sorting Hub
                                                        </option>
                                                        <option value="Out For Delivery"
                                                            {{ request()->query('courier_status') == 'Out For Delivery' ? 'selected' : '' }}>
                                                            Out For Delivery
                                                        </option>
                                                    </select>
                                                </div>
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
                                            <th style="width:8%">Invoice ID</th>
                                            <th>Customer Info</th>
                                            <th>Activity</th>
                                            {{-- <th>Products</th> --}}
                                            @if (Auth::guard('admin')->check())
                                                <th>
                                                    <select class="form-control form-control-sm select2 product_id"
                                                        name="product_id" id="">
                                                        <option value="">Products</option>
                                                        @foreach ($products as $product_id => $product_name)
                                                            <option value="{{ $product_id }}"
                                                                {{ request()->query('product_id') == $product_id ? 'selected' : '' }}>
                                                                {{ $product_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </th>
                                            @else
                                                <th>Products</th>
                                            @endif
                                            <th>Total</th>
                                            <th>Courier</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Payment</th>
                                            <th>Notes</th>
                                            <th>
                                                <select class="form-control form-control-sm select2 employee_id"
                                                    name="employee_id" id="">
                                                    <option value="">Users</option>
                                                    @foreach ($employees as $employee_id => $employee_name)
                                                        <option value="{{ $employee_id }}"
                                                            {{ request()->query('employee_id') == $employee_id ? 'selected' : '' }}>
                                                            {{ $employee_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @if (Auth::guard('admin')->check() || Auth::guard('manager')->check())
                                            @if ($orders->count() > 0)
                                                @foreach ($orders as $item)
                                                    <?php
                                                    $check_duplicate = \Illuminate\Support\Facades\DB::table('orders')->where('customer_phone', $item->customer_phone)->count();
                                                    ?>
                                                    <tr id="tr_{{ $item->id }}"
                                                        class="{{ $check_duplicate > 1 ? 'bg-danger-light' : '' }}">
                                                        <td><input type="checkbox" class="sub_chk"
                                                                data-id="{{ $item->id }}">
                                                        </td>
                                                        <td>
                                                            {{ $i++ }}
                                                            <a href="javascript:void(0)"
                                                                class="old_orders_btn text-info ml-1"
                                                                data-id="{{ $item->id }}"
                                                                title="Customer Previous Orders">
                                                                <i class="fa fa-history"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if ($item->source == 'page')
                                                                <span
                                                                    class="badge badge-primary">{{ ucfirst($item->source) }}</span>
                                                            @elseif ($item->source == 'whatsapp')
                                                                <span
                                                                    class="badge badge-success">{{ ucfirst($item->source) }}</span>
                                                            @elseif ($item->source == 'call')
                                                                <span
                                                                    class="badge badge-info">{{ ucfirst($item->source) }}</span>
                                                            @elseif ($item->source == 'direct')
                                                                <span
                                                                    class="badge badge-warning">{{ ucfirst($item->source) }}</span>
                                                            @elseif($item->source == 'incomplete')
                                                                <span
                                                                    class="badge badge-dark">{{ ucfirst($item->source) }}</span>
                                                            @else
                                                                <span
                                                                    class="badge badge-secondary">{{ ucfirst($item->source) }}</span>
                                                            @endif
                                                            <div style="white-space: nowrap;">
                                                                {{ $item->invoice_id }}
                                                                <span class="copy-btn"
                                                                    data-copy="{{ $item->invoice_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <rect x="9" y="9" width="13" height="13"
                                                                            rx="2" ry="2"></rect>
                                                                        <path
                                                                            d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                        </path>
                                                                    </svg>
                                                                    <span class="copy-tooltip">Copied</span>
                                                                </span>
                                                            </div>
                                                            @if ($item->discount)
                                                                <span class="badge badge-light">Discount</span>
                                                                <br>
                                                            @endif
                                                            <small>UTM:
                                                                <strong>{{ ucfirst($item->utm_source) }}</strong></small>
                                                            @if (empty($web_settings->master_domain) && $item->slave_id && $item->slave_domain)
                                                                <br>
                                                                <small>From: {{ $item->slave_domain }}</small>
                                                            @elseif(!empty($web_settings->master_domain))
                                                                @if ($item->master_id)
                                                                    <br>
                                                                    <small>Master ID: {{ $item->master_id }}</small>
                                                                @endif
                                                                @if ($item->forwarding_status !== 'success')
                                                                    <small>Forward:
                                                                        {{ $item->forwarding_status ?? 'pending' }}</small>
                                                                    <br>
                                                                    <form method="POST"
                                                                        action="{{ Auth::guard('admin')->check() ? route('admin.orders.forwarding.retry', $item->id) : (Auth::guard('manager')->check() ? route('manager.orders.forwarding.retry', $item->id) : (Auth::guard('employee')->check() ? route('employee.orders.forwarding.retry', $item->id) : '#')) }}">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-outline-danger mt-1">
                                                                            Retry
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @endif
                                                            @if ($item->is_fake == 1)
                                                                <br>
                                                                <small class="badge badge-danger">Fake! <a
                                                                        href="{{ route('admin.fake.remove', $item->id) }}"
                                                                        onclick="return confirm('Are You Sure?')"><i
                                                                            class="fa fa-trash-alt text-white"></i></a></small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($item->ip_address)
                                                                ip: <small class="text-muted"><a
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.ip.search', 'query=' . $item->ip_address) : (Auth::guard('manager')->check() ? route('manager.ip.search', 'query=' . $item->ip_address) : (Auth::guard('employee')->check() ? route('employee.ip.search', 'query=' . $item->ip_address) : '')) }}"
                                                                        target="_blank">{{ $item->ip_address }}</a></small><br>
                                                            @endif
                                                            <span>{{ $item->customer_name }}</span> <br>
                                                            <a
                                                                href="tel:{{ $item->customer_phone }}"><span>{{ $item->customer_phone }}</span></a>
                                                            <span class="copy-btn"
                                                                data-copy="{{ $item->customer_phone }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                    height="14" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <rect x="9" y="9" width="13" height="13"
                                                                        rx="2" ry="2"></rect>
                                                                    <path
                                                                        d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                    </path>
                                                                </svg>
                                                                <span class="copy-tooltip">Copied</span>
                                                            </span>
                                                            <a target="_blank" class="ml-2"
                                                                href="https://api.whatsapp.com/send?phone=88{{ ltrim($item->customer_phone, '+88') }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                                    height="18" viewBox="0 0 24 24" fill="none"
                                                                    stroke="#21ae41" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-brand-whatsapp">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
                                                                    <path
                                                                        d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
                                                                </svg>
                                                            </a>
                                                            <br>
                                                            <span>{{ $item->customer_address }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="text-left">
                                                                <a href="{{ Auth::guard('admin')->check() ? route('admin.fraud.check', $item->id) : (Auth::guard('manager')->check() ? route('manager.fraud.check', $item->id) : '') }}"
                                                                    onclick="return confirm('Do you want to update?')"><i
                                                                        class="fa fa-redo-alt"></i></a>
                                                                <a href="javascript:void(0)" class="customer_activity_btn"
                                                                    data-customer_phone="{{ $item->customer_phone }}"
                                                                    data-total="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total : 0 }}"
                                                                    data-total_delivered="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total_delivered : 0 }}"
                                                                    data-total_returned="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total_returned : 0 }}"
                                                                    data-pathao_delivered="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->pathao_delivered : 0 }}"
                                                                    data-pathao_returned="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->pathao_returned : 0 }}"
                                                                    data-steadfast_delivered="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->steadfast_delivered : 0 }}"
                                                                    data-steadfast_returned="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->steadfast_returned : 0 }}"
                                                                    data-redx_delivered="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->redx_delivered : 0 }}"
                                                                    data-redx_returned="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->redx_returned : 0 }}"><i
                                                                        class="fa fa-exclamation-circle"></i></a>
                                                            </div>
                                                            <div>
                                                                <b>T:</b>
                                                                {{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total : 0 }}<br>
                                                                <b class="text-success">D:</b>
                                                                {{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total_delivered : 0 }}
                                                                <br>
                                                                <b class="text-danger">R:</b>
                                                                {{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total_returned : 0 }}
                                                                @if (json_decode($item->customer_activity) &&
                                                                        (json_decode($item->customer_activity)->total_delivered > 0 &&
                                                                            json_decode($item->customer_activity)->total > 0))
                                                                    <br>
                                                                    <span style="padding: 2px 3px;"
                                                                        class="badge badge-dark"><small>{{ json_decode($item->customer_activity) ? number_format((json_decode($item->customer_activity)->total_delivered / json_decode($item->customer_activity)->total) * 100, 2) : 0 }}%</small></span>
                                                                @elseif(json_decode($item->customer_activity) && json_decode($item->customer_activity)->total == 0)
                                                                    <br>
                                                                    <span style="padding: 2px 3px;"
                                                                        class="badge badge-danger"><small>N.O</small></span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="products-td">
                                                            @foreach ($item->get_products as $product)
                                                                <div class="products">
                                                                    <div class="image">
                                                                        @if ($product->get_product->get_thumb)
                                                                            <img src="{{ asset($product->get_product->get_thumb->file_url) }}"
                                                                                alt=""
                                                                                style="width: 35px; height: 35px;">
                                                                        @endif
                                                                    </div>
                                                                    <div class="product-info">
                                                                        <div class="name">
                                                                            {{ $product->qty }} x <a target="_blank"
                                                                                href="{{ $product->get_product ? route('single.product', [$product->get_product->slug, $product->get_product->id]) : '' }}">{{ $product->get_product ? $product->get_product->name : '' }}</a>
                                                                        </div>
                                                                        <div class="attribute">
                                                                            @if ($product->attributes)
                                                                                @foreach (json_decode($product->attributes, true) as $key => $attr)
                                                                                    <small>
                                                                                        @if ($loop->last)
                                                                                            {{ $key }}:
                                                                                            {{ $attr }}
                                                                                        @else
                                                                                            {{ $key }}:
                                                                                            {{ $attr }},
                                                                                        @endif
                                                                                    </small>
                                                                                @endforeach
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            {{ $item->total }}<br>
                                                            <span class="text-success">P:-{{ $item->paid }}</span><br>
                                                            <span class="text-danger">D:-{{ $item->due }}</span>
                                                        </td>
                                                        <td style="white-space:nowrap;">
                                                            @php $courierName = $item->get_courier->courier_name ?? '---' @endphp
                                                            {{ $courierName }}
                                                            <br>
                                                            @if ($courierName == 'Pathao')
                                                                <div>City: {{ $item->courier_city_id }}</div>
                                                                <div>Zone: {{ $item->courier_zone_id }}</div>
                                                            @endif
                                                            <div>
                                                                {{ ['Outside Dhaka', 'Inside Dhaka'][$item->shipping_method] ?? '-' }}
                                                            </div>
                                                            @if ($item->handover_date)
                                                                <div>{{ $item->handover_date?->format('d-M-Y h:i A') }}
                                                                </div>
                                                            @endif
                                                            @if ($item->pathao_consignment_id)
                                                                <a href="https://merchant.pathao.com/tracking?consignment_id={{ $item->pathao_consignment_id }}&phone={{ $item->customer_phone }}"
                                                                    target="_blank">{{ $item->pathao_consignment_id }}<i
                                                                        class="fa fa-eye"></i></a>
                                                                <span class="copy-btn"
                                                                    data-copy="{{ $item->pathao_consignment_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <rect x="9" y="9" width="13" height="13"
                                                                            rx="2" ry="2"></rect>
                                                                        <path
                                                                            d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                        </path>
                                                                    </svg>
                                                                    <span class="copy-tooltip">Copied</span>
                                                                </span>
                                                            @elseif($item->redx_tracking_id)
                                                                <a href="https://redx.com.bd/track-parcel/?trackingId={{ $item->redx_tracking_id }}"
                                                                    target="_blank">{{ $item->redx_tracking_id }}<i
                                                                        class="fa fa-eye"></i></a>
                                                                <span class="copy-btn"
                                                                    data-copy="{{ $item->redx_tracking_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <rect x="9" y="9" width="13" height="13"
                                                                            rx="2" ry="2"></rect>
                                                                        <path
                                                                            d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                        </path>
                                                                    </svg>
                                                                    <span class="copy-tooltip">Copied</span>
                                                                </span>
                                                            @elseif($item->carrybee_consignment_id)
                                                                <a href="https://merchant.carrybee.com/order-track/{{ $item->carrybee_consignment_id }}"
                                                                    target="_blank">{{ $item->carrybee_consignment_id }}<i
                                                                        class="fa fa-eye"></i></a>
                                                                <span class="copy-btn"
                                                                    data-copy="{{ $item->carrybee_consignment_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <rect x="9" y="9" width="13" height="13"
                                                                            rx="2" ry="2"></rect>
                                                                        <path
                                                                            d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                        </path>
                                                                    </svg>
                                                                    <span class="copy-tooltip">Copied</span>
                                                                </span>
                                                            @elseif($item->steadfast_consignment_id)
                                                                <a href="https://www.steadfast.com.bd/user/consignment/{{ $item->steadfast_consignment_id }}"
                                                                    target="_blank">{{ $item->steadfast_consignment_id }}<i
                                                                        class="fa fa-eye"></i></a>
                                                                <span class="copy-btn"
                                                                    data-copy="{{ $item->steadfast_consignment_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <rect x="9" y="9" width="13" height="13"
                                                                            rx="2" ry="2"></rect>
                                                                        <path
                                                                            d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                        </path>
                                                                    </svg>
                                                                    <span class="copy-tooltip">Copied</span>
                                                                </span>
                                                            @endif
                                                            @if ($item->courier_api_response)
                                                                <span data-toggle="tooltip" data-placement="top"
                                                                    title="{{ $item->courier_api_response }}"><i
                                                                        class="fa fa-exclamation-circle text-warning"></i></span>
                                                            @endif
                                                            @if ($item->courier_status)
                                                                <br>
                                                                <small>{{ $item->courier_status ?? '' }}</small>
                                                            @endif
                                                            @if ($item->courier_status_reason)
                                                                <br>
                                                                <small
                                                                    style="color:#eab000">{{ $item->courier_status_reason ?? '' }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ date('d M, Y', strtotime($item->order_date)) }}<br>
                                                            {{ date('h:i:s A', strtotime($item->created_at)) }}
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $statusEnum = \App\Enums\OrderStatus::tryFrom(
                                                                    $item->status,
                                                                );
                                                                $variant = $statusEnum?->variant() ?? 'secondary';
                                                            @endphp
                                                            <button type="button"
                                                                class="btn btn-{{ $variant }} status_btn  btn-sm dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                {{ $statusEnum?->label() ?? 'Unknown' }}
                                                            </button>
                                                            @if (in_array($item->status, [$processingStatusValue, $confirmedStatusValue, $cancelledStatusValue], false) &&
                                                                    ($item->call_campaign_id || $item->ai_confirmation_status))
                                                                @php
                                                                    $aiConfirmationStatus =
                                                                        $item->ai_confirmation_status ?: 'pending';
                                                                    $aiBadgeVariant = match ($aiConfirmationStatus) {
                                                                        'confirmed' => 'success',
                                                                        'rejected' => 'danger',
                                                                        default => 'warning',
                                                                    };
                                                                    $aiBadgeLabel = match ($aiConfirmationStatus) {
                                                                        'confirmed' => 'AI Confirmed',
                                                                        'rejected' => 'AI Cancelled',
                                                                        default => $aiConfirmationStatus,
                                                                    };
                                                                @endphp
                                                                <br>
                                                                <small
                                                                    class="badge text-uppercase badge-{{ $aiBadgeVariant }}">{{ $aiBadgeLabel }}</small>
                                                            @endif
                                                            @if (Auth::guard('admin')->check())
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item {{ $item->status == 2 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 2]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 2]) : '') }}">Processing</a>
                                                                    <a class="dropdown-item {{ $item->status == 9 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 9]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 9]) : '') }}">No
                                                                        Response</a>
                                                                    <a class="dropdown-item {{ $item->status == 0 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 0]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 0]) : '') }}">
                                                                        Hold</a>
                                                                    <a class="dropdown-item {{ $item->status == 3 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 3]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 3]) : '') }}">Pending
                                                                        Payment</a>
                                                                    <a class="dropdown-item {{ $item->status == 4 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 4]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 4]) : '') }}">Cancelled</a>
                                                                    <a class="dropdown-item {{ $item->status == 13 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 13]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 13]) : '') }}">Confirmed</a>
                                                                    <a class="dropdown-item {{ $item->status == 5 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 5]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 5]) : '') }}">Pending
                                                                        Invoice</a>
                                                                    <a class="dropdown-item {{ $item->status == 10 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 10]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 10]) : '') }}">Invoiced</a>
                                                                    <a class="dropdown-item {{ $item->status == 14 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 14]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 14]) : '') }}">Stock
                                                                        Out</a>
                                                                    <a class="dropdown-item {{ $item->status == 8 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 8]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 8]) : '') }}">Courier
                                                                    </a>
                                                                    <a class="dropdown-item {{ $item->status == 6 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 6]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 6]) : '') }}">On
                                                                        Delivery</a>
                                                                    <a class="dropdown-item {{ $item->status == 1 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 1]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 1]) : '') }}">Delivered</a>
                                                                    <a class="dropdown-item {{ $item->status == 15 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 15]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 15]) : '') }}">Partial
                                                                        Delivery</a>
                                                                    <a class="dropdown-item {{ $item->status == 7 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 7]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 7]) : '') }}">Pending
                                                                        Return</a>
                                                                    <a class="dropdown-item {{ $item->status == 11 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 11]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 11]) : '') }}">
                                                                        Return</a>
                                                                    <a class="dropdown-item {{ $item->status == 16 ? 'd-none' : '' }}"
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 16]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 16]) : '') }}">Lost</a>
                                                                </div>
                                                            @elseif(Auth::guard('manager')->check())
                                                                @if ($item->status == 5)
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item {{ $item->status == 4 ? 'd-none' : '' }}"
                                                                            href="{{ route('employee.orders.status', [$item->id, 4]) }}">Cancelled</a>
                                                                    </div>
                                                                @elseif($item->status != 6 && $item->status != 1 && $item->status != 7)
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item {{ $item->status == 2 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 2]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 2]) : '') }}">Processing</a>
                                                                        <a class="dropdown-item {{ $item->status == 9 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 9]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 9]) : '') }}">No
                                                                            Response</a>
                                                                        <a class="dropdown-item {{ $item->status == 0 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 0]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 0]) : '') }}">
                                                                            Hold</a>
                                                                        <a class="dropdown-item {{ $item->status == 3 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 3]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 3]) : '') }}">Pending
                                                                            Payment</a>
                                                                        <a class="dropdown-item {{ $item->status == 4 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 4]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 4]) : '') }}">Cancelled</a>
                                                                        <a class="dropdown-item {{ $item->status == 13 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 13]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 13]) : '') }}">Confirmed</a>

                                                                        <a class="dropdown-item {{ $item->status == 5 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 5]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 5]) : '') }}">Pending
                                                                            Invoice</a>
                                                                        <a class="dropdown-item {{ $item->status == 10 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 10]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 10]) : '') }}">Invoiced</a>
                                                                        <a class="dropdown-item {{ $item->status == 14 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 14]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 14]) : '') }}">Stock
                                                                            Out</a>
                                                                        <a class="dropdown-item {{ $item->status == 8 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 8]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 8]) : '') }}">Courier
                                                                        </a>
                                                                        <a class="dropdown-item {{ $item->status == 15 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 15]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 15]) : '') }}">Partial
                                                                            Delivery</a>
                                                                        <a class="dropdown-item {{ $item->status == 7 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 7]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 7]) : '') }}">Pending
                                                                            Return</a>
                                                                        <a class="dropdown-item {{ $item->status == 11 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 11]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 11]) : '') }}">
                                                                            Return</a>
                                                                        <a class="dropdown-item {{ $item->status == 16 ? 'd-none' : '' }}"
                                                                            href="{{ Auth::guard('admin')->check() ? route('admin.orders.status', [$item->id, 16]) : (Auth::guard('manager')->check() ? route('manager.orders.status', [$item->id, 16]) : '') }}">Lost</a>

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
                                                            <i class="fa fa-edit note_btn"
                                                                data-id="{{ $item->id }}" data-type="courier"
                                                                data-note="{{ $item->courier_note }}"
                                                                style="cursor: pointer"></i> <span
                                                                class="text-dark"><b>C:</b>
                                                                {{ $item->courier_note }}</span>
                                                            <br>
                                                            <i class="fa fa-edit note_btn"
                                                                data-id="{{ $item->id }}" data-type="staff"
                                                                data-note="{{ $item->staff_note }}"
                                                                style="cursor: pointer"></i> <span
                                                                class="text-primary"><b>S:</b>
                                                                {{ $item->staff_note }}</span>
                                                        </td>
                                                        <td>
                                                            {{ $item->get_assigned?->get_employee?->name ?? '' }}
                                                            <br>
                                                            <a href="javascript:void(0);" class="single-assign-btn"
                                                                data-order_id="{{ $item->id }}"><i
                                                                    class="fa fa-edit"></i></a>
                                                        </td>

                                                        <td class="text-center">
                                                            <a href="javascript:void(0)" class="d-block mb-1 print"
                                                                data-id="{{ $item->id }}"><i
                                                                    class="fa fa-print"></i></a>
                                                            <form method="POST"
                                                                action="{{ Auth::guard('admin')->check() ? route('admin.orders.call.retry', $item->id) : (Auth::guard('manager')->check() ? route('manager.orders.call.retry', $item->id) : route('employee.orders.call.retry', $item->id)) }}"
                                                                style="display:grid;">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-link p-0 d-block mb-1 text-primary"
                                                                    title="{{ $item->call_campaign_id ? 'Retry call' : 'Request new call' }}"
                                                                    onclick="return confirm('{{ $item->call_campaign_id ? 'Retry call for this order?' : 'Request a new call for this order?' }}')">
                                                                    <i class="fa fa-phone"></i>
                                                                </button>
                                                            </form>
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
                                                                    title="Trash" class="d-block mb-1"
                                                                    onclick="return confirm('Are you sure to Trash?')"><i
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
                                                    <td colspan="14" class="text-center text-danger font-weight-bold">
                                                        No
                                                        Data Found!
                                                    </td>
                                                </tr>
                                            @endif
                                        @elseif(Auth::guard('employee')->check())
                                            @if ($orders->count() > 0)
                                                @foreach ($orders as $item)
                                                    <?php
                                                    $check_duplicate = \Illuminate\Support\Facades\DB::table('orders')->where('customer_phone', $item->customer_phone)->count();
                                                    ?>
                                                    <tr id="tr_{{ $item->id }}"
                                                        class="{{ $check_duplicate > 1 ? 'bg-danger-light' : '' }}">
                                                        <td><input type="checkbox" class="sub_chk"
                                                                data-id="{{ $item->id }}">
                                                        </td>
                                                        <td>
                                                            {{ $i++ }}
                                                            <a href="javascript:void(0)"
                                                                class="old_orders_btn text-info ml-1"
                                                                data-id="{{ $item->id }}"
                                                                title="Customer Previous Orders">
                                                                <i class="fa fa-history"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if ($item->source == 'page')
                                                                <span
                                                                    class="badge badge-primary">{{ ucfirst($item->source) }}</span>
                                                            @elseif ($item->source == 'whatsapp')
                                                                <span
                                                                    class="badge badge-success">{{ ucfirst($item->source) }}</span>
                                                            @elseif ($item->source == 'call')
                                                                <span
                                                                    class="badge badge-info">{{ ucfirst($item->source) }}</span>
                                                            @elseif ($item->source == 'direct')
                                                                <span
                                                                    class="badge badge-warning">{{ ucfirst($item->source) }}</span>
                                                            @elseif($item->source == 'incomplete')
                                                                <span
                                                                    class="badge badge-dark">{{ ucfirst($item->source) }}</span>
                                                            @else
                                                                <span
                                                                    class="badge badge-secondary">{{ ucfirst($item->source) }}</span>
                                                            @endif
                                                            <div style="white-space: nowrap;">
                                                                {{ $item->invoice_id }}
                                                                <span class="copy-btn"
                                                                    data-copy="{{ $item->invoice_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <rect x="9" y="9" width="13" height="13"
                                                                            rx="2" ry="2"></rect>
                                                                        <path
                                                                            d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                        </path>
                                                                    </svg>
                                                                    <span class="copy-tooltip">Copied</span>
                                                                </span>
                                                            </div>
                                                            @if ($item->discount)
                                                                <span class="badge badge-light">Discount</span>
                                                                <br>
                                                            @endif
                                                            <small>UTM:
                                                                <strong>{{ ucfirst($item->utm_source) }}</strong></small>
                                                            @if (empty($web_settings->master_domain) && $item->slave_id && $item->slave_domain)
                                                                <br>
                                                                <small>From: {{ $item->slave_domain }}</small>
                                                            @elseif(!empty($web_settings->master_domain))
                                                                @if ($item->master_id)
                                                                    <br>
                                                                    <small>Master ID: {{ $item->master_id }}</small>
                                                                @endif
                                                                @if ($item->forwarding_status !== 'success')
                                                                    <small>Forward:
                                                                        {{ $item->forwarding_status ?? 'pending' }}</small>
                                                                    <br>
                                                                    <form method="POST"
                                                                        action="{{ Auth::guard('admin')->check() ? route('admin.orders.forwarding.retry', $item->id) : (Auth::guard('manager')->check() ? route('manager.orders.forwarding.retry', $item->id) : (Auth::guard('employee')->check() ? route('employee.orders.forwarding.retry', $item->id) : '#')) }}">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-outline-danger mt-1">
                                                                            Retry
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @endif
                                                            @if ($item->is_fake == 1)
                                                                <br>
                                                                <small class="badge badge-danger">Fake!</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($item->ip_address)
                                                                ip: <small class="text-muted"><a
                                                                        href="{{ Auth::guard('admin')->check() ? route('admin.ip.search', 'query=' . $item->ip_address) : (Auth::guard('manager')->check() ? route('manager.ip.search', 'query=' . $item->ip_address) : (Auth::guard('employee')->check() ? route('employee.ip.search', 'query=' . $item->ip_address) : '')) }}"
                                                                        target="_blank">{{ $item->ip_address }}</a></small><br>
                                                            @endif
                                                            <span>{{ $item->customer_name }}</span> <br>
                                                            <a
                                                                href="tel:{{ $item->customer_phone }}"><span>{{ $item->customer_phone }}</span></a>
                                                            <span class="copy-btn"
                                                                data-copy="{{ $item->customer_phone }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                    height="14" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <rect x="9" y="9" width="13" height="13"
                                                                        rx="2" ry="2"></rect>
                                                                    <path
                                                                        d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                    </path>
                                                                </svg>
                                                                <span class="copy-tooltip">Copied</span>
                                                            </span>
                                                            <a target="_blank" class="ml-2"
                                                                href="https://api.whatsapp.com/send?phone=88{{ ltrim($item->customer_phone, '+88') }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                                    height="18" viewBox="0 0 24 24" fill="none"
                                                                    stroke="#21ae41" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-brand-whatsapp">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
                                                                    <path
                                                                        d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
                                                                </svg>
                                                            </a>
                                                            <br>
                                                            <span>{{ $item->customer_address }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="text-left">
                                                                <a href="{{ route('employee.fraud.check', $item->id) }}"
                                                                    onclick="return confirm('Do you want to update?')"><i
                                                                        class="fa fa-redo-alt"></i></a>
                                                                <a href="javascript:void(0)"
                                                                    class="customer_activity_btn"
                                                                    data-customer_phone="{{ $item->customer_phone }}"
                                                                    data-total="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total : 0 }}"
                                                                    data-total_delivered="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total_delivered : 0 }}"
                                                                    data-total_returned="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total_returned : 0 }}"
                                                                    data-pathao_delivered="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->pathao_delivered : 0 }}"
                                                                    data-pathao_returned="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->pathao_returned : 0 }}"
                                                                    data-steadfast_delivered="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->steadfast_delivered : 0 }}"
                                                                    data-steadfast_returned="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->steadfast_returned : 0 }}"
                                                                    data-redx_delivered="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->redx_delivered : 0 }}"
                                                                    data-redx_returned="{{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->redx_returned : 0 }}"><i
                                                                        class="fa fa-exclamation-circle"></i></a>
                                                            </div>
                                                            <div>
                                                                <b>T:</b>
                                                                {{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total : 0 }}<br>
                                                                <b class="text-success">D:</b>
                                                                {{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total_delivered : 0 }}
                                                                <br>
                                                                <b class="text-danger">R:</b>
                                                                {{ json_decode($item->customer_activity) ? json_decode($item->customer_activity)->total_returned : 0 }}
                                                                @if (json_decode($item->customer_activity) &&
                                                                        (json_decode($item->customer_activity)->total_delivered > 0 &&
                                                                            json_decode($item->customer_activity)->total > 0))
                                                                    <br>
                                                                    <span style="padding: 2px 3px;"
                                                                        class="badge badge-dark"><small>{{ json_decode($item->customer_activity) ? number_format((json_decode($item->customer_activity)->total_delivered / json_decode($item->customer_activity)->total) * 100, 2) : 0 }}%</small></span>
                                                                @elseif(json_decode($item->customer_activity) && json_decode($item->customer_activity)->total == 0)
                                                                    <br>
                                                                    <span style="padding: 2px 3px;"
                                                                        class="badge badge-danger"><small>N.O</small></span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="products-td">
                                                            @foreach ($item->get_products as $product)
                                                                <div class="products">
                                                                    <div class="image">
                                                                        @if ($product->get_product->get_thumb)
                                                                            <img src="{{ asset($product->get_product->get_thumb->file_url) }}"
                                                                                alt=""
                                                                                style="width: 35px; height: 35px;">
                                                                        @endif
                                                                    </div>
                                                                    <div class="product-info">
                                                                        <div class="name">
                                                                            {{ $product->qty }} x <a target="_blank"
                                                                                href="{{ $product->get_product ? route('single.product', [$product->get_product->slug, $product->get_product->id]) : '' }}">{{ $product->get_product ? $product->get_product->name : '' }}</a>
                                                                        </div>
                                                                        <div class="attribute">
                                                                            @if ($product->attributes)
                                                                                @foreach (json_decode($product->attributes, true) as $key => $attr)
                                                                                    <small>
                                                                                        @if ($loop->last)
                                                                                            {{ $key }}:
                                                                                            {{ $attr }}
                                                                                        @else
                                                                                            {{ $key }}:
                                                                                            {{ $attr }},
                                                                                        @endif
                                                                                    </small>
                                                                                @endforeach
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            {{ $web_settings->currency_sign }}
                                                            {{ $item->total }}<br>
                                                            <span class="text-success">P:-{{ $item->paid }}</span><br>
                                                            <span class="text-danger">D:-{{ $item->due }}</span>
                                                        </td>
                                                        <td>
                                                            @php $courierName = $item->get_courier->courier_name ?? '---' @endphp
                                                            {{ $courierName }}
                                                            <br>
                                                            @if ($courierName == 'Pathao')
                                                                <div>City: {{ $item->courier_city_id }}</div>
                                                                <div>Zone: {{ $item->courier_zone_id }}</div>
                                                            @endif
                                                            <div>
                                                                {{ ['Outside Dhaka', 'Inside Dhaka'][$item->shipping_method] ?? '-' }}
                                                            </div>
                                                            @if ($item->handover_date)
                                                                <div>{{ $item->handover_date?->format('d-M-Y h:i A') }}
                                                                </div>
                                                            @endif
                                                            @if ($item->pathao_consignment_id)
                                                                <a href="https://merchant.pathao.com/tracking?consignment_id={{ $item->pathao_consignment_id }}&phone={{ $item->customer_phone }}"
                                                                    target="_blank">{{ $item->pathao_consignment_id }}<i
                                                                        class="fa fa-eye"></i></a>
                                                                <span class="copy-btn"
                                                                    data-copy="{{ $item->pathao_consignment_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <rect x="9" y="9" width="13" height="13"
                                                                            rx="2" ry="2"></rect>
                                                                        <path
                                                                            d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                        </path>
                                                                    </svg>
                                                                    <span class="copy-tooltip">Copied</span>
                                                                </span>
                                                            @elseif($item->redx_tracking_id)
                                                                <a href="https://redx.com.bd/track-parcel/?trackingId={{ $item->redx_tracking_id }}"
                                                                    target="_blank">{{ $item->redx_tracking_id }}<i
                                                                        class="fa fa-eye"></i></a>
                                                                <span class="copy-btn"
                                                                    data-copy="{{ $item->redx_tracking_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <rect x="9" y="9" width="13" height="13"
                                                                            rx="2" ry="2"></rect>
                                                                        <path
                                                                            d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                        </path>
                                                                    </svg>
                                                                    <span class="copy-tooltip">Copied</span>
                                                                </span>
                                                            @elseif($item->carrybee_consignment_id)
                                                                <a href="https://merchant.carrybee.com/order-track/{{ $item->carrybee_consignment_id }}"
                                                                    target="_blank">{{ $item->carrybee_consignment_id }}<i
                                                                        class="fa fa-eye"></i></a>
                                                                <span class="copy-btn"
                                                                    data-copy="{{ $item->carrybee_consignment_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <rect x="9" y="9" width="13" height="13"
                                                                            rx="2" ry="2"></rect>
                                                                        <path
                                                                            d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                        </path>
                                                                    </svg>
                                                                    <span class="copy-tooltip">Copied</span>
                                                                </span>
                                                            @elseif($item->steadfast_consignment_id)
                                                                <a href="https://www.steadfast.com.bd/user/consignment/{{ $item->steadfast_consignment_id }}"
                                                                    target="_blank">{{ $item->steadfast_consignment_id }}<i
                                                                        class="fa fa-eye"></i></a>
                                                                <span class="copy-btn"
                                                                    data-copy="{{ $item->steadfast_consignment_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <rect x="9" y="9" width="13" height="13"
                                                                            rx="2" ry="2"></rect>
                                                                        <path
                                                                            d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                                        </path>
                                                                    </svg>
                                                                    <span class="copy-tooltip">Copied</span>
                                                                </span>
                                                            @endif
                                                            @if ($item->courier_api_response)
                                                                <span data-toggle="tooltip" data-placement="top"
                                                                    title="{{ $item->courier_api_response }}"><i
                                                                        class="fa fa-exclamation-circle text-warning"></i></span>
                                                            @endif
                                                            @if ($item->courier_status)
                                                                <br>
                                                                <small>{{ $item->courier_status ?? '' }}</small>
                                                            @endif
                                                            @if ($item->courier_status_reason)
                                                                <br>
                                                                <small
                                                                    style="color:#eab000">{{ $item->courier_status_reason ?? '' }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ date('d M, Y', strtotime($item->order_date)) }}<br>
                                                            {{ date('h:i:s A', strtotime($item->created_at)) }}
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $statusEnum = \App\Enums\OrderStatus::tryFrom(
                                                                    $item->status,
                                                                );
                                                                $variant = $statusEnum?->variant() ?? 'secondary';
                                                            @endphp
                                                            <button type="button"
                                                                class="btn btn-{{ $variant }} status_btn  btn-sm dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                {{ $statusEnum?->label() ?? 'Unknown' }}
                                                            </button>

                                                            @if (in_array($item->status, [$processingStatusValue, $confirmedStatusValue, $cancelledStatusValue], false) &&
                                                                    ($item->call_campaign_id || $item->ai_confirmation_status))
                                                                @php
                                                                    $aiConfirmationStatus =
                                                                        $item->ai_confirmation_status ?: 'pending';
                                                                    $aiBadgeVariant = match ($aiConfirmationStatus) {
                                                                        'confirmed' => 'success',
                                                                        'rejected' => 'danger',
                                                                        default => 'warning',
                                                                    };
                                                                    $aiBadgeLabel = match ($aiConfirmationStatus) {
                                                                        'confirmed' => 'AI Confirmed',
                                                                        'rejected' => 'AI Cancelled',
                                                                        default => $aiConfirmationStatus,
                                                                    };
                                                                @endphp
                                                                <br>
                                                                <small
                                                                    class="badge text-uppercase badge-{{ $aiBadgeVariant }}">{{ $aiBadgeLabel }}</small>
                                                            @endif
                                                            @if ($item->status == 5)
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item {{ $item->status == 4 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 4]) }}">Cancelled</a>
                                                                </div>
                                                            @elseif($item->status != 6 && $item->status != 1 && $item->status != 7)
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item {{ $item->status == 2 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 2]) }}">Processing</a>
                                                                    <a class="dropdown-item {{ $item->status == 9 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 9]) }}">No
                                                                        Response</a>
                                                                    <a class="dropdown-item {{ $item->status == 0 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 0]) }}">
                                                                        Hold</a>
                                                                    <a class="dropdown-item {{ $item->status == 3 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 3]) }}">Pending
                                                                        Payment</a>
                                                                    <a class="dropdown-item {{ $item->status == 4 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 4]) }}">Cancelled</a>
                                                                    <a class="dropdown-item {{ $item->status == 13 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 13]) }}">Confirmed</a>
                                                                    <a class="dropdown-item {{ $item->status == 5 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 5]) }}">Pending
                                                                        Invoice</a>
                                                                    <a class="dropdown-item {{ $item->status == 10 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 10]) }}">Invoiced</a>
                                                                    <a class="dropdown-item {{ $item->status == 14 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 14]) }}">Stock
                                                                        Out</a>
                                                                    <a class="dropdown-item {{ $item->status == 8 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 8]) }}">Courier
                                                                    </a>
                                                                    <a class="dropdown-item {{ $item->status == 15 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 15]) }}">Partial
                                                                        Delivery</a>
                                                                    <a class="dropdown-item {{ $item->status == 7 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 7]) }}">
                                                                        Pending Return</a>
                                                                    <a class="dropdown-item {{ $item->status == 11 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 11]) }}">
                                                                        Return</a>
                                                                    <a class="dropdown-item {{ $item->status == 16 ? 'd-none' : '' }}"
                                                                        href="{{ route('employee.orders.status', [$item->id, 16]) }}">Lost</a>
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
                                                                    href="{{ Auth::guard('admin')->check() ? route('admin.orders.payment_status', [$item->id, 0]) : (Auth::guard('manager')->check() ? route('manager.orders.payment_status', [$item->id, 0]) : '') }}">Unpaid</a>
                                                                <a class="dropdown-item {{ $item->payment_status == 1 ? 'd-none' : '' }}"
                                                                    href="{{ Auth::guard('admin')->check() ? route('admin.orders.payment_status', [$item->id, 1]) : (Auth::guard('manager')->check() ? route('manager.orders.payment_status', [$item->id, 1]) : '') }}">Partial</a>
                                                                <a class="dropdown-item {{ $item->payment_status == 2 ? 'd-none' : '' }}"
                                                                    href="{{ Auth::guard('admin')->check() ? route('admin.orders.payment_status', [$item->id, 2]) : (Auth::guard('manager')->check() ? route('manager.orders.payment_status', [$item->id, 2]) : '') }}">Paid</a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <i class="fa fa-edit note_btn"
                                                                data-id="{{ $item->id }}" data-type="courier"
                                                                data-note="{{ $item->courier_note }}"
                                                                style="cursor: pointer"></i> <span
                                                                class="text-dark"><b>C:</b>
                                                                {{ $item->courier_note }}</span>
                                                            <br>
                                                            <i class="fa fa-edit note_btn"
                                                                data-id="{{ $item->id }}" data-type="staff"
                                                                data-note="{{ $item->staff_note }}"
                                                                style="cursor: pointer"></i> <span
                                                                class="text-primary"><b>S:</b>
                                                                {{ $item->staff_note }}</span>
                                                        </td>
                                                        <td>
                                                            {{ $item->get_assigned ? $item->get_assigned->get_employee->name : '' }}
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0)" class="d-block mb-1 print"
                                                                data-id="{{ $item->id }}"><i
                                                                    class="fa fa-print"></i></a>
                                                            <form method="POST"
                                                                action="{{ Auth::guard('admin')->check() ? route('admin.orders.call.retry', $item->id) : (Auth::guard('manager')->check() ? route('manager.orders.call.retry', $item->id) : route('employee.orders.call.retry', $item->id)) }}"
                                                                style="display:grid;">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-link p-0 d-block mb-1 text-primary"
                                                                    title="{{ $item->call_campaign_id ? 'Retry call' : 'Request new call' }}"
                                                                    onclick="return confirm('{{ $item->call_campaign_id ? 'Retry call for this order?' : 'Request a new call for this order?' }}')">
                                                                    <i class="fa fa-phone"></i>
                                                                </button>
                                                            </form>
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
                                                                    title="Trash" class="d-block mb-1"
                                                                    onclick="return confirm('Are you sure to Trash?')"><i
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
                                                    <td colspan="14" class="text-center text-danger font-weight-bold">
                                                        No
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
                                <select name="employee_id" id="employee_id_modal" class="form-control select2"
                                    required>
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

    {{-- note update modal --}}
    <div class="modal fade" id="note_update_modal" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="note_update_modalTitle">Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form
                        action="{{ Auth::guard('admin')->check() ? route('admin.orders.note_update') : (Auth::guard('manager')->check() ? route('manager.orders.note_update') : (Auth::guard('employee')->check() ? route('employee.orders.note_update') : '')) }}"
                        method="post">
                        @csrf
                        <input type="hidden" name="id" id="order_id_e">
                        @foreach ($notes as $id => $text)
                            <div class="form-group mb-1 form-inline">
                                <input class="form-check-input note_text" type="radio" name="note_text"
                                    id="note_text{{ $id }}" value="{{ $text }}">
                                <label for="note_text{{ $id }}">{{ $text }}</label>
                            </div>
                        @endforeach

                        <div class="form-group courier">
                            <textarea name="courier_note" id="courier_note_e" class="form-control"></textarea>
                        </div>

                        <div class="form-group staff">
                            <textarea name="staff_note" id="staff_note_e" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- customer activity modal --}}
    <div class="modal fade" id="customer_activity_modal" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customer_activity_modalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table text-center table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Courier</th>
                                    <th>Total</th>
                                    <th class="table-success text-dark">Delivered</th>
                                    <th class="table-danger text-dark">Returned</th>
                                    <th>Success Ratio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Pathao</td>
                                    <td id="pathao_total"></td>
                                    <td id="pathao_delivered" class="table-success text-dark"></td>
                                    <td id="pathao_returned" class="table-danger text-dark"></td>

                                    <td id="pathao_success_ratio"></td>
                                </tr>
                                <tr>
                                    <td>Redx</td>
                                    <td id="redx_total"></td>
                                    <td id="redx_delivered" class="table-success text-dark"></td>
                                    <td id="redx_returned" class="table-danger text-dark"></td>

                                    <td id="redx_success_ratio"></td>
                                </tr>
                                <tr>
                                    <td>Steadfast</td>
                                    <td id="steadfast_total"></td>
                                    <td id="steadfast_delivered" class="table-success text-dark"></td>
                                    <td id="steadfast_returned" class="table-danger text-dark"></td>

                                    <td id="steadfast_success_ratio"></td>
                                </tr>
                                <tr class="table-dark">
                                    <td>Total</td>
                                    <td id="total_parcel"></td>
                                    <td id="total_delivered" class="table-success"></td>
                                    <td id="total_returned" class="table-danger"></td>
                                    <td id="total_success_ratio"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customer_old_orders_modal" role="dialog" aria-labelledby="customerOldOrdersLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerOldOrdersLabel">Customer Previous Orders</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="customer_old_orders_put">
                    <div class="text-center text-muted">Loading...</div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('backEnd/assets/vendor/datetimepicker/moment.min.js') }}"></script>
    <script src="{{ asset('backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('backEnd/assets/vendor/bootstrap/js/bootstrap_toggle.min.js') }}"></script>


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

            $('.toggle_btn').on('change', function() {
                if ($(this).prop('checked') == true) {
                    $('.total').removeClass('d-none');
                    $('.total').addClass('d-block');
                } else {
                    $('.total').removeClass('d-block');
                    $('.total').addClass('d-none');
                }

            });


            $('.select2').select2();

            $('#master').on('click', function(e) {
                if ($(this).is(':checked', true)) {
                    $(".sub_chk").prop('checked', true);
                } else {
                    $(".sub_chk").prop('checked', false);
                }
            });


            $('#send_to_courier').on('change', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    $('#all_send_to_status').val(allVals);
                    $('#send_to_courier_form').submit();
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

            $('#equal_assign').on('click', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    if (confirm('Are Your Sure To Assign?') == true) {
                        $('#eq_assign_order_ids').val(allVals);
                        $('#equal_assign_form').submit();
                    }
                }
            });

            $('#bulk_call_btn').on('click', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    if (confirm(
                            'Are you sure you want to initiate calls for the selected orders? This will be processed as a background job.'
                        ) == true) {
                        $('#all_call_order_id').val(allVals);
                        $('#bulk_call_form').submit();
                    }
                }
            });

            $('#bulk_forward_btn').on('click', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    if (confirm(
                            'Are you sure you want to forward these orders to master? This will be processed as a background job.'
                        ) == true) {
                        $('#bulk_forward_order_ids').val(JSON.stringify(allVals));
                        $('#bulk_forward_form').submit();
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

            $('#bulk_label_print_btn').on('click', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: '{{ Auth::guard('admin')->check() ? route('admin.orders.bulk.label.print') : (Auth::guard('manager')->check() ? route('manager.orders.bulk.label.print') : (Auth::guard('employee')->check() ? route('employee.orders.bulk.print') : '')) }}',
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

            $('.old_orders_btn').on('click', function() {
                $('#customer_old_orders_modal').modal('show');
                $('#customer_old_orders_put').html('<div class="text-center text-muted">Loading...</div>');
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ Auth::guard('admin')->check() ? route('admin.orders.customer_old_orders') : (Auth::guard('manager')->check() ? route('manager.orders.customer_old_orders') : (Auth::guard('employee')->check() ? route('employee.orders.customer_old_orders') : '')) }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        id: $(this).data('id')
                    },
                    success: function(data) {
                        $('#customer_old_orders_put').html(data);
                    },
                    error: function() {
                        $('#customer_old_orders_put').html(
                            '<div class="text-center text-danger">Failed to load previous orders.</div>'
                        );
                    }
                });
            });

            //note update
            $('.note_btn').on('click', function() {
                $('.note_text').prop('checked', false);
                $('#note_update_modal').modal('show');
                $('#order_id_e').val($(this).data('id'));
                if ($(this).data('type') == 'courier') {
                    $('.staff').addClass('d-none');
                    $('.courier').removeClass('d-none');
                    $('#staff_note_e').prop('disabled', true);
                    $('#courier_note_e').val($(this).data('note')).prop('disabled', false);
                    $('#note_update_modalTitle').text('Courier Note')
                } else if ($(this).data('type') == 'staff') {
                    $('.courier').addClass('d-none');
                    $('.staff').removeClass('d-none');
                    $('#courier_note_e').prop('disabled', true);
                    $('#staff_note_e').val($(this).data('note')).prop('disabled', false);
                    $('#note_update_modalTitle').text('Staff Note')
                } else {
                    $('.courier').removeClass('d-none');
                    $('.staff').removeClass('d-none');
                    $('#courier_note_e').prop('disabled', false);
                    $('#staff_note_e').prop('disabled', false);
                }
            });

            $('.note_text').on('click', function() {
                $('#courier_note_e').val($(this).val());
                $('#staff_note_e').val($(this).val());
            });

            $('.customer_activity_btn').on('click', function() {
                $('#total_parcel').text($(this).data('total'));
                $('#total_delivered').text($(this).data('total_delivered'));
                $('#total_returned').text($(this).data('total_returned'));
                var total_success_ratio = (parseFloat($(this).data('total_delivered')) / parseFloat($(this)
                    .data('total'))) * 100;
                $('#total_success_ratio').text(total_success_ratio.toFixed(2) + '%');

                $('#pathao_total').text(parseFloat($(this).data('pathao_delivered')) + parseFloat($(this)
                    .data('pathao_returned')));
                $('#pathao_delivered').text($(this).data('pathao_delivered'));
                $('#pathao_returned').text($(this).data('pathao_returned'));
                var pathao_success_ratio = (parseFloat($(this).data('pathao_delivered')) / (parseFloat($(
                    this).data('pathao_delivered')) + parseFloat($(this).data(
                    'pathao_returned')))) * 100;
                $('#pathao_success_ratio').text(pathao_success_ratio.toFixed(2) + '%');

                $('#redx_total').text(parseFloat($(this).data('redx_delivered')) + parseFloat($(this).data(
                    'redx_returned')));
                $('#redx_delivered').text($(this).data('redx_delivered'));
                $('#redx_returned').text($(this).data('redx_returned'));
                var redx_success_ratio = (parseFloat($(this).data('redx_delivered')) / (parseFloat($(this)
                    .data('redx_delivered')) + parseFloat($(this).data('redx_returned')))) * 100;
                $('#redx_success_ratio').text(redx_success_ratio.toFixed(2) + '%');

                $('#steadfast_total').text(parseFloat($(this).data('steadfast_delivered')) + parseFloat($(
                    this).data('steadfast_returned')));
                $('#steadfast_delivered').text($(this).data('steadfast_delivered'));
                $('#steadfast_returned').text($(this).data('steadfast_returned'));
                var steadfast_success_ratio = (parseFloat($(this).data('steadfast_delivered')) / (
                    parseFloat($(this).data('steadfast_delivered')) + parseFloat($(this).data(
                        'steadfast_returned')))) * 100;
                $('#steadfast_success_ratio').text(steadfast_success_ratio.toFixed(2) + '%');
                $('#customer_activity_modalTitle').text($(this).data('customer_phone'));


                $('#customer_activity_modal').modal('show');
            });

            //optional filter in table head
            $('.product_id').on('change', function() {
                let input = '<input type="hidden" name="product_id" value="' + $(this).val() + '">';
                $("#main_filter").append(input).submit();
            });

            $('.employee_id').on('change', function() {
                let input = '<input type="hidden" name="employee_id" value="' + $(this).val() + '">';
                $("#main_filter").append(input).submit();
            });

            // Copy to clipboard functionality
            $('.copy-btn').on('click', function() {
                var textToCopy = $(this).data('copy');
                var $tooltip = $(this).find('.copy-tooltip');

                // Create a temporary input element
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(textToCopy).select();
                document.execCommand("copy");
                $temp.remove();

                // Show tooltip
                $tooltip.addClass('show');

                // Hide tooltip after 1.5 seconds
                setTimeout(function() {
                    $tooltip.removeClass('show');
                }, 1500);
            });
        });
    </script>
@endsection

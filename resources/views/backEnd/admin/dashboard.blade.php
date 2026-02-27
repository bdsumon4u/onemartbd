@extends('backEnd.admin.layouts.master')

@section('title')
    Dashboard
@endsection
@php
    $total_revenue = $data['total_revenue'] ?? 0;
    $total_customer = $data['total_customer'] ?? 0;
    $total_product = $data['total_product'] ?? 0;
    $total_staff = $data['total_staff'] ?? 0;
    $employees = $data['employees'] ?? [];
    $admins = $data['admins'] ?? [];
    $managers = $data['managers'] ?? [];

    $total_order = $data['total_order'] ?? 0;
    $total_hold_order = $data['total_hold_order'] ?? 0;
    $total_deliver_order = $data['total_deliver_order'] ?? 0;
    $total_process_order = $data['total_process_order'] ?? 0;
    $total_pend_pay_order = $data['total_pend_pay_order'] ?? 0;
    $total_cancel_order = $data['total_cancel_order'] ?? 0;
    $total_pending_invoice_order = $data['total_pending_invoice_order'] ?? 0;
    $total_on_delivery_order = $data['total_on_delivery_order'] ?? 0;
    $total_pending_return_order = $data['total_pending_return_order'] ?? 0;
    $total_courier_hold_order = $data['total_courier_hold_order'] ?? 0;
    $total_nr_1_order = $data['total_nr_1_order'] ?? 0;
    $total_invoiced_order = $data['total_invoiced_order'] ?? 0;
    $total_return_order = $data['total_return_order'] ?? 0;
    $total_incomplete_order = $data['total_incomplete_order'] ?? 0;
    $total_confirmed_order = $data['total_confirmed_order'] ?? 0;
    $total_stock_out_order = $data['total_stock_out_order'] ?? 0;
    $total_partial_delivery_order = $data['total_partial_delivery_order'] ?? 0;
    $total_lost_order = $data['total_lost_order'] ?? 0;
    $total_paid_return_order = $data['total_paid_return_order'] ?? 0;
    $total_exchange_order = $data['total_exchange_order'] ?? 0;

    $today_all_orders = $data['today_all_orders'] ?? 0;
    $today_hold_orders = $data['today_hold_orders'] ?? 0;
    $today_deliver_orders = $data['today_deliver_orders'] ?? 0;
    $today_process_orders = $data['today_process_orders'] ?? 0;
    $today_pend_pay_orders = $data['today_pend_pay_orders'] ?? 0;
    $today_cancel_orders = $data['today_cancel_orders'] ?? 0;
    $today_pending_invoice_orders = $data['today_pending_invoice_orders'] ?? 0;
    $today_on_delivery_orders = $data['today_on_delivery_orders'] ?? 0;
    $today_pending_return_orders = $data['today_pending_return_orders'] ?? 0;
    $today_courier_hold_orders = $data['today_courier_hold_orders'] ?? 0;
    $today_nr_1_orders = $data['today_nr_1_orders'] ?? 0;
    $today_invoiced_orders = $data['today_invoiced_orders'] ?? 0;
    $today_return_orders = $data['today_return_orders'] ?? 0;
    $today_incomplete_orders = $data['today_incomplete_orders'] ?? 0;
    $today_confirmed_orders = $data['today_confirmed_orders'] ?? 0;
    $today_stock_out_orders = $data['today_stock_out_orders'] ?? 0;
    $today_partial_delivery_orders = $data['today_partial_delivery_orders'] ?? 0;
    $today_lost_orders = $data['today_lost_orders'] ?? 0;
    $today_paid_return_orders = $data['today_paid_return_orders'] ?? 0;
    $today_exchange_orders = $data['today_exchange_orders'] ?? 0;

    $recent_orders = $data['recent_orders'] ?? [];

    $last_order = $last_order ?? null;
    $topSellFilterUrl = Auth::guard('admin')->check()
        ? route('admin.dashboard.top_sell')
        : (Auth::guard('manager')->check()
            ? route('manager.dashboard.top_sell')
            : (Auth::guard('employee')->check()
                ? route('employee.dashboard.top_sell')
                : null));
    $trafficSourceFilterUrl = Auth::guard('admin')->check()
        ? route('admin.dashboard.traffic_sources')
        : (Auth::guard('manager')->check()
            ? route('manager.dashboard.traffic_sources')
            : (Auth::guard('employee')->check()
                ? route('employee.dashboard.traffic_sources')
                : null));
    $utmMediumFilterUrl = Auth::guard('admin')->check()
        ? route('admin.dashboard.utm_medium')
        : (Auth::guard('manager')->check()
            ? route('manager.dashboard.utm_medium')
            : (Auth::guard('employee')->check()
                ? route('employee.dashboard.utm_medium')
                : null));
    $utmCampaignFilterUrl = Auth::guard('admin')->check()
        ? route('admin.dashboard.utm_campaign')
        : (Auth::guard('manager')->check()
            ? route('manager.dashboard.utm_campaign')
            : (Auth::guard('employee')->check()
                ? route('employee.dashboard.utm_campaign')
                : null));
    $topCitiesFilterUrl = Auth::guard('admin')->check()
        ? route('admin.dashboard.top_cities')
        : (Auth::guard('manager')->check()
            ? route('manager.dashboard.top_cities')
            : (Auth::guard('employee')->check()
                ? route('employee.dashboard.top_cities')
                : null));
@endphp
@section('css')
    <link rel="stylesheet" href="{{ asset('/') }}backEnd/assets/vendor/charts/chartist-bundle/chartist.css">
    <style>
        .chart-tooltip {
            position: absolute;
            z-index: 9999;
            padding: 6px 10px;
            background: rgba(0, 0, 0, 0.75);
            color: #fff;
            border-radius: 4px;
            font-size: 12px;
            pointer-events: none;
            white-space: nowrap;
        }

        .top-cities-chart .ct-label.ct-horizontal.ct-end {
            transform: rotate(-55deg);
            transform-origin: center center;
            justify-content: flex-start;
            text-align: left;
            white-space: nowrap;
        }
    </style>
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <div class="ecommerce-widget">
                    <div class="row">
                        <div class="col-12">
                            <h4><b>Last Order:</b>
                                {{ $last_order ? \Carbon\Carbon::parse($last_order)->diffForHumans() : 'N/A' }}</h4>
                        </div>
                    </div>
                    <div class="row mb-md-4 mb-3">
                        @if (Auth::guard('admin')->check() || Auth::guard('manager')->check())
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <h5 class="text-muted">Total Revenue</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $web_settings->currency_sign }} {{ $total_revenue }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (Auth::guard('admin')->check() || Auth::guard('manager')->check())
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <h5 class="text-muted">Total Staff</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_staff }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (Auth::guard('admin')->check() || Auth::guard('manager')->check())
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <h5 class="text-muted">Total Customer</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_customer }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                                <a
                                    href="{{ Auth::guard('admin')->check() ? route('admin.product') : (Auth::guard('manager')->check() ? route('manager.product') : '') }}">
                                    <div class="card border-3 border-top border-top-primary">
                                        <div class="card-body">
                                            <h5 class="text-muted">Total Product</h5>
                                            <div class="metric-value d-inline-block">
                                                <h1 class="mb-1">{{ $total_product }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders') : (Auth::guard('manager')->check() ? route('manager.orders') : (Auth::guard('employee')->check() ? route('employee.orders') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5>Total Order</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Processing') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Processing') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Processing') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-warning">Total Processing</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_process_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=No Response') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=No Response') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=No Response') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-danger">Total No Response</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_nr_1_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Hold') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Hold') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Hold') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-info">Total Hold</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_hold_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Pending Payment') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Pending Payment') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Pending Payment') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-secondary">Total Pending Payment</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_pend_pay_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Cancelled') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Cancelled') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Cancelled') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-danger">Total Cancelled</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_cancel_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Confirmed') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Confirmed') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Confirmed') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-success">Total Confirmed</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_confirmed_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Pending Invoice') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Pending Invoice') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Pending Invoice') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-primary">Total Pend. Invoice</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_pending_invoice_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Invoiced') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Invoiced') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Invoiced') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-success">Total Invoiced</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_invoiced_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Stock Out') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Stock Out') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Stock Out') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-success">Total Stock Out</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_stock_out_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Courier') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Courier') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Courier') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-warning">Total Courier</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_courier_hold_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=On Delivery') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=On Delivery') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=On Delivery') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-primary">Total On Delivery</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_on_delivery_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Delivered') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Delivered') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Delivered') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-warning">Total Delivered</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_deliver_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Partial Delivery') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Partial Delivery') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Partial Delivery') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-success">Total Partial Delivery</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_partial_delivery_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Pending Return') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Pending Return') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Pending Return') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-danger">Total Pending Return</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_pending_return_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Paid Return') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Paid Return') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Paid Return') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-danger">Total Paid Return</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_paid_return_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Exchange') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Exchange') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Exchange') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-danger">Total Exchange</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_exchange_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Return') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Return') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Return') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-success">Total Return</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_return_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-md-4 mb-3">
                            <a
                                href="{{ Auth::guard('admin')->check() ? route('admin.orders', 'status=Lost') : (Auth::guard('manager')->check() ? route('manager.orders', 'status=Lost') : (Auth::guard('employee')->check() ? route('employee.orders', 'status=Lost') : '')) }}">
                                <div class="card border-3 border-top border-top-success">
                                    <div class="card-body">
                                        <h5 class="text-success">Total Lost</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $total_lost_order }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>

                    <div class="row mb-md-4 mb-3">
                        <div class="col-xl-5 col-lg-6 col-md-5 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">Today's Report</h5>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th>Orders</th>
                                                <td>{{ $today_all_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Processing</th>
                                                <td>{{ $today_process_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>No Response</th>
                                                <td>{{ $today_nr_1_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Hold</th>
                                                <td>{{ $today_hold_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Pending Payment</th>
                                                <td>{{ $today_pend_pay_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Cancelled</th>
                                                <td>{{ $today_cancel_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Confirmed</th>
                                                <td>{{ $today_confirmed_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Pending Invoice</th>
                                                <td>{{ $today_pending_invoice_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Invoiced</th>
                                                <td>{{ $today_invoiced_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Stock Out</th>
                                                <td>{{ $today_stock_out_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Courier</th>
                                                <td>{{ $today_courier_hold_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>On Delivery</th>
                                                <td>{{ $today_on_delivery_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Delivered</th>
                                                <td>{{ $today_deliver_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Partial Delivery</th>
                                                <td>{{ $today_partial_delivery_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Pending Return</th>
                                                <td>{{ $today_pending_return_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Paid Return</th>
                                                <td>{{ $today_paid_return_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Exchange</th>
                                                <td>{{ $today_exchange_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Return</th>
                                                <td>{{ $today_return_orders }}</td>
                                            </tr>
                                            <tr>
                                                <th>Lost</th>
                                                <td>{{ $today_lost_orders }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">Recent Orders</h5>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>SL.</th>
                                                    <th>Date</th>
                                                    <th>C. Name</th>
                                                    <th>C. Phone</th>
                                                    <th>Total</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (Auth::guard('admin')->check() || Auth::guard('manager')->check())
                                                    @php
                                                        $i = 1;
                                                    @endphp
                                                    @if ($recent_orders->count() > 0)
                                                        @foreach ($recent_orders as $item)
                                                            <tr>
                                                                <td>{{ $i++ }}</td>
                                                                <td>{{ date('d M', strtotime($item->order_date)) }}</td>
                                                                <td>{{ $item->customer_name }}</td>
                                                                <td><a
                                                                        href="tel:{{ $item->customer_phone }}">{{ $item->customer_phone }}</a>
                                                                </td>
                                                                <td>{{ $web_settings->currency_sign }} {{ $item->total }}
                                                                </td>
                                                                <td class="text-center">
                                                                    @php
                                                                        $statusEnum = \App\Enums\OrderStatus::tryFrom($item->status);
                                                                    @endphp
                                                                    @if ($statusEnum)
                                                                        <span class="badge badge-{{ $statusEnum->variant() }}">
                                                                            {{ $statusEnum->label() }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="7"
                                                                class="text-danger font-weight-bold text-center">No Order
                                                                Found
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    @php
                                                        $i = 1;
                                                    @endphp
                                                    @if ($recent_orders->count() > 0)
                                                        @foreach ($recent_orders as $item)
                                                            <tr>
                                                                <td>{{ $i++ }}</td>
                                                                <td>{{ date('d M', strtotime($item->order_date)) }}</td>
                                                                <td>{{ $item->customer_name }}</td>
                                                                <td><a
                                                                        href="tel:{{ $item->customer_phone }}">{{ $item->customer_phone }}</a>
                                                                </td>
                                                                <td>{{ $web_settings->currency_sign }} {{ $item->total }}
                                                                </td>
                                                                <td class="text-center">
                                                                    @php
                                                                        $statusEnum = \App\Enums\OrderStatus::tryFrom($item->status);
                                                                    @endphp
                                                                    @if ($statusEnum)
                                                                        <span class="badge badge-{{ $statusEnum->variant() }}">
                                                                            {{ $statusEnum->label() }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="7"
                                                                class="text-danger font-weight-bold text-center">No Order
                                                                Found
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (Auth::guard('admin')->check())
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <h5 class="card-header">Employee Activity <a id="employee-list-btn"
                                            href="javascript:void(0);" class="btn btn-info btn-sm">Reload</a>
                                    </h5>
                                    <div class="card-body p-0">
                                        <table class="table table-striped" id="employee-list">
                                            <thead>
                                                <tr>
                                                    <th>SL.</th>
                                                    <th>Name</th>
                                                    <th>Status</th>
                                                    <th>Last Active</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php($i = 1)
                                                @foreach ($admins as $admin)
                                                    <tr>
                                                        <th width="5%">{{ $i++ }}</th>
                                                        <td width="45%">
                                                            <small>IP: {{ $admin->last_login_ip }}</small><br>
                                                            {{ $admin->name }}
                                                        </td>
                                                        <td width="10%">
                                                            @if (\Illuminate\Support\Facades\Cache::has('admin-is-online-' . $admin->id))
                                                                <span class="badge badge-success">Online</span>
                                                            @else
                                                                <span class="badge badge-danger">Offline</span>
                                                            @endif
                                                        </td>
                                                        <td width="40%">
                                                            {{ Carbon\Carbon::parse($admin->last_seen)->diffForHumans() }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @foreach ($managers as $manager)
                                                    <tr>
                                                        <th width="5%">{{ $i++ }}</th>
                                                        <td width="45%">
                                                            <small>IP: {{ $manager->last_login_ip }}</small><br>
                                                            {{ $manager->name }}
                                                        </td>
                                                        <td width="10%">
                                                            @if (\Illuminate\Support\Facades\Cache::has('manager-is-online-' . $manager->id))
                                                                <span class="badge badge-success">Online</span>
                                                            @else
                                                                <span class="badge badge-danger">Offline</span>
                                                            @endif
                                                        </td>
                                                        <td width="40%">
                                                            {{ Carbon\Carbon::parse($manager->last_seen)->diffForHumans() }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @foreach ($employees as $employee)
                                                    <tr>
                                                        <th width="5%">{{ $i++ }}</th>
                                                        <td width="45%">
                                                            <small>IP: {{ $employee->last_login_ip }}</small><br>
                                                            {{ $employee->name }}
                                                        </td>
                                                        <td width="10%">
                                                            @if (\Illuminate\Support\Facades\Cache::has('employee-is-online-' . $employee->id))
                                                                <span class="badge badge-success">Online</span>
                                                            @else
                                                                <span class="badge badge-danger">Offline</span>
                                                            @endif
                                                        </td>
                                                        <td width="40%">
                                                            {{ Carbon\Carbon::parse($employee->last_seen)->diffForHumans() }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-md-4 mb-3 mt-4">
                        <div class="col-12">
                            <div class="card top-cities-card">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <span>Top Cities</span>
                                    <div class="d-flex align-items-center">
                                        <small class="mr-2 text-muted">Range</small>
                                        <select class="form-control form-control-sm" id="top-cities-range"
                                            data-url="{{ $topCitiesFilterUrl ?? '' }}"
                                            {{ $topCitiesFilterUrl ? '' : 'disabled' }}>
                                            <option value="today">Today</option>
                                            <option value="3days">3 Days</option>
                                            <option value="week">1 Week</option>
                                            <option value="month" selected>1 Month</option>
                                            <option value="3months">3 Months</option>
                                            <option value="6months">6 Months</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="top-cities-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-md-4 mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <span>Top Sell Products</span>
                                    <div class="d-flex align-items-center">
                                        <small class="mr-2 text-muted">Range</small>
                                        <select class="form-control form-control-sm" id="top-sell-range"
                                            data-url="{{ $topSellFilterUrl ?? '' }}"
                                            {{ $topSellFilterUrl ? '' : 'disabled' }}>
                                            <option value="today" {{ $topSellRange === 'today' ? 'selected' : '' }}>Today
                                            </option>
                                            <option value="3days" {{ $topSellRange === '3days' ? 'selected' : '' }}>3
                                                Days</option>
                                            <option value="week" {{ $topSellRange === 'week' ? 'selected' : '' }}>1 Week
                                            </option>
                                            <option value="month" {{ $topSellRange === 'month' ? 'selected' : '' }}>1
                                                Month</option>
                                            <option value="3months" {{ $topSellRange === '3months' ? 'selected' : '' }}>3
                                                Months</option>
                                            <option value="6months" {{ $topSellRange === '6months' ? 'selected' : '' }}>6
                                                Months</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="p-3">
                                        <div class="row">
                                            <div class="col-lg-7 col-md-7">
                                                <div class="top-sell-chart" style="height: 140px;"></div>
                                            </div>
                                            <div class="col-lg-5 col-md-5">
                                                <div class="top-sell-pie" style="height: 140px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%">SL.</th>
                                                    <th>Name</th>
                                                    <th class="text-right">Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody id="top-sell-table-body">
                                                @php($i = 1)
                                                @if ($top_sell->count() > 0)
                                                    @foreach ($top_sell as $item)
                                                        <tr>
                                                            <td>{{ $i++ }}</td>
                                                            <td>{{ $item->product_name }}</td>
                                                            <td class="text-right">
                                                                {{ $item->total }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="3"
                                                            class="text-danger font-weight-bold text-center">No Order
                                                            Found
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

                    <div class="row mb-md-4 mb-3">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                            <div class="card traffic-source-card">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <span>UTM Source</span>
                                    <div class="d-flex align-items-center">
                                        <small class="mr-2 text-muted">Range</small>
                                        <select class="form-control form-control-sm" id="traffic-source-range"
                                            data-url="{{ $trafficSourceFilterUrl ?? '' }}"
                                            {{ $trafficSourceFilterUrl ? '' : 'disabled' }}>
                                            <option value="today">Today</option>
                                            <option value="3days">3 Days</option>
                                            <option value="week">1 Week</option>
                                            <option value="month" selected>1 Month</option>
                                            <option value="3months">3 Months</option>
                                            <option value="6months">6 Months</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="traffic-source-chart" style="height: 260px;"></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">SL.</th>
                                                <th>Source</th>
                                                <th class="text-right">Visits</th>
                                            </tr>
                                        </thead>
                                        <tbody id="traffic-source-table-body">
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Select a range to load
                                                    data</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                            <div class="card utm-medium-card">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <span>UTM Medium</span>
                                    <div class="d-flex align-items-center">
                                        <small class="mr-2 text-muted">Range</small>
                                        <select class="form-control form-control-sm" id="utm-medium-range"
                                            data-url="{{ $utmMediumFilterUrl ?? '' }}"
                                            {{ $utmMediumFilterUrl ? '' : 'disabled' }}>
                                            <option value="today">Today</option>
                                            <option value="3days">3 Days</option>
                                            <option value="week">1 Week</option>
                                            <option value="month" selected>1 Month</option>
                                            <option value="3months">3 Months</option>
                                            <option value="6months">6 Months</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="utm-medium-chart" style="height: 260px;"></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">SL.</th>
                                                <th>Medium</th>
                                                <th class="text-right">Visits</th>
                                            </tr>
                                        </thead>
                                        <tbody id="utm-medium-table-body">
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Select a range to load
                                                    data</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                            <div class="card utm-campaign-card">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <span>UTM Campaign</span>
                                    <div class="d-flex align-items-center">
                                        <small class="mr-2 text-muted">Range</small>
                                        <select class="form-control form-control-sm" id="utm-campaign-range"
                                            data-url="{{ $utmCampaignFilterUrl ?? '' }}"
                                            {{ $utmCampaignFilterUrl ? '' : 'disabled' }}>
                                            <option value="today">Today</option>
                                            <option value="3days">3 Days</option>
                                            <option value="week">1 Week</option>
                                            <option value="month" selected>1 Month</option>
                                            <option value="3months">3 Months</option>
                                            <option value="6months">6 Months</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="utm-campaign-chart" style="height: 260px;"></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">SL.</th>
                                                <th>Campaign</th>
                                                <th class="text-right">Visits</th>
                                            </tr>
                                        </thead>
                                        <tbody id="utm-campaign-table-body">
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Select a range to load
                                                    data</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('/') }}backEnd/assets/vendor/charts/chartist-bundle/chartist.min.js"></script>
    <script>
        $(".show_notice_btn").click(function() {
            $("#notice_desc_details").text($(this).data('desc'));
        });

        $("#employee-list-btn").on('click', function() {
            $('#employee-list').load(' #employee-list');
        });

        @if (Auth::guard('admin')->check())
            $(document).ready(function() {
                setInterval(function() {
                    $('#employee-list').load(' #employee-list');
                }, 60000);
            });
        @endif

        const topSellInitialLabels = @json($topSellChart['labels']);
        const topSellInitialTotals = @json($topSellChart['totals']);
        const trafficSourceFilterUrl = @json($trafficSourceFilterUrl);
        const utmMediumFilterUrl = @json($utmMediumFilterUrl);
        const utmCampaignFilterUrl = @json($utmCampaignFilterUrl);
        const topCitiesFilterUrl = @json($topCitiesFilterUrl);
        let trafficSourceLoaded = false;
        let utmMediumLoaded = false;
        let utmCampaignLoaded = false;
        let topCitiesLoaded = false;

        function renderTopSellChart(labels, totals) {
            const chartElement = document.querySelector('.top-sell-chart');
            if (!chartElement) {
                return;
            }

            chartElement.innerHTML = '';
            const barTotal = totals.reduce(function(sum, value) {
                return sum + Number(value || 0);
            }, 0);
            chartElement.setAttribute('data-total', barTotal);

            const chart = new Chartist.Bar('.top-sell-chart', {
                labels: labels,
                series: [totals]
            }, {
                axisX: {
                    showLabel: false,
                    showGrid: false
                },
                axisY: {
                    onlyInteger: true
                },
                chartPadding: {
                    top: 10,
                    right: 10,
                    bottom: 0,
                    left: 0
                }
            });

            chart.on('draw', function(data) {
                if (data.type === 'bar') {
                    const label = labels[data.index] || '';
                    const value = data.value && data.value.y !== undefined ? data.value.y : data.value;
                    data.element.attr({
                        'data-label': label,
                        'data-value': value
                    });
                }
            });
        }

        function renderTopSellPie(labels, totals) {
            const pieElement = document.querySelector('.top-sell-pie');
            if (!pieElement) {
                return;
            }

            pieElement.innerHTML = '';
            const pieTotal = totals.reduce(function(sum, value) {
                return sum + Number(value || 0);
            }, 0);
            pieElement.setAttribute('data-total', pieTotal);

            const chart = new Chartist.Pie('.top-sell-pie', {
                labels: labels,
                series: totals
            }, {
                chartPadding: 10,
                labelInterpolationFnc: function(value) {
                    return '';
                }
            });

            chart.on('draw', function(data) {
                if (data.type === 'slice') {
                    const label = labels[data.index] || '';
                    const value = data.value;
                    data.element.attr({
                        'data-label': label,
                        'data-value': value
                    });
                }
            });
        }

        function bindTopSellTooltips() {
            const tooltip = $('.chart-tooltip');
            if (!tooltip.length) {
                $('body').append('<div class="chart-tooltip" style="display:none;"></div>');
            }

            $(document)
                .off('mouseenter.topSell', '.top-sell-chart .ct-bar, .top-sell-pie .ct-slice-pie')
                .on('mouseenter.topSell', '.top-sell-chart .ct-bar, .top-sell-pie .ct-slice-pie', function(event) {
                    const label = $(this).attr('data-label');
                    const value = Number($(this).attr('data-value') || 0);
                    const total = Number($(this).closest('.top-sell-chart, .top-sell-pie').attr('data-total') || 0);

                    if (!label || total <= 0) {
                        return;
                    }

                    const percent = ((value / total) * 100).toFixed(1);

                    $('.chart-tooltip')
                        .text(percent + '% - ' + label)
                        .show()
                        .css({
                            left: event.pageX + 12,
                            top: event.pageY - 24
                        });
                })
                .off('mousemove.topSell', '.top-sell-chart, .top-sell-pie')
                .on('mousemove.topSell', '.top-sell-chart, .top-sell-pie', function(event) {
                    $('.chart-tooltip').css({
                        left: event.pageX + 12,
                        top: event.pageY - 24
                    });
                })
                .off('mouseleave.topSell', '.top-sell-chart .ct-bar, .top-sell-pie .ct-slice-pie')
                .on('mouseleave.topSell', '.top-sell-chart .ct-bar, .top-sell-pie .ct-slice-pie', function() {
                    $('.chart-tooltip').hide();
                });
        }

        function renderTopSellTable(items) {
            const tableBody = $('#top-sell-table-body');
            if (!tableBody.length) {
                return;
            }

            if (!items || items.length === 0) {
                tableBody.html(
                    '<tr><td colspan="3" class="text-danger font-weight-bold text-center">No Order Found</td></tr>');
                return;
            }

            const rows = items.map(function(item, index) {
                return '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + item.name + '</td>' +
                    '<td class="text-right">' + item.total + '</td>' +
                    '</tr>';
            }).join('');

            tableBody.html(rows);
        }

        function fetchTopSell(range) {
            const select = $('#top-sell-range');
            const url = select.data('url');

            if (!url) {
                return;
            }

            $.get(url, {
                    range: range
                })
                .done(function(response) {
                    renderTopSellChart(response.labels || [], response.totals || []);
                    renderTopSellPie(response.labels || [], response.totals || []);
                    renderTopSellTable(response.items || []);
                    bindTopSellTooltips();
                });
        }

        function renderTrafficSourceChart(labels, totals) {
            const chartElement = document.querySelector('.traffic-source-chart');
            if (!chartElement) {
                return;
            }

            chartElement.innerHTML = '';
            const barTotal = totals.reduce(function(sum, value) {
                return sum + Number(value || 0);
            }, 0);
            chartElement.setAttribute('data-total', barTotal);

            const chart = new Chartist.Bar('.traffic-source-chart', {
                labels: labels,
                series: [totals]
            }, {
                axisX: {
                    showGrid: false
                },
                axisY: {
                    onlyInteger: true
                },
                chartPadding: {
                    top: 10,
                    right: 10,
                    bottom: 0,
                    left: 0
                }
            });

            chart.on('draw', function(data) {
                if (data.type === 'bar') {
                    const label = labels[data.index] || '';
                    const value = data.value && data.value.y !== undefined ? data.value.y : data.value;
                    data.element.attr({
                        'data-label': label,
                        'data-value': value
                    });
                }
            });
        }

        function renderTrafficSourceTable(items) {
            const tableBody = $('#traffic-source-table-body');
            if (!tableBody.length) {
                return;
            }

            if (!items || items.length === 0) {
                tableBody.html(
                    '<tr><td colspan="3" class="text-danger font-weight-bold text-center">No Traffic Found</td></tr>'
                );
                return;
            }

            const rows = items.map(function(item, index) {
                return '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + item.label + '</td>' +
                    '<td class="text-right">' + item.total + '</td>' +
                    '</tr>';
            }).join('');

            tableBody.html(rows);
        }

        function bindTrafficSourceTooltips() {
            const tooltip = $('.chart-tooltip');
            if (!tooltip.length) {
                $('body').append('<div class="chart-tooltip" style="display:none;"></div>');
            }

            $(document)
                .off('mouseenter.trafficSource', '.traffic-source-chart .ct-bar')
                .on('mouseenter.trafficSource', '.traffic-source-chart .ct-bar', function(event) {
                    const label = $(this).attr('data-label');
                    const value = Number($(this).attr('data-value') || 0);
                    const total = Number($(this).closest('.traffic-source-chart').attr('data-total') || 0);

                    if (!label || total <= 0) {
                        return;
                    }

                    const percent = ((value / total) * 100).toFixed(1);

                    $('.chart-tooltip')
                        .text(percent + '% - ' + label)
                        .show()
                        .css({
                            left: event.pageX + 12,
                            top: event.pageY - 24
                        });
                })
                .off('mousemove.trafficSource', '.traffic-source-chart')
                .on('mousemove.trafficSource', '.traffic-source-chart', function(event) {
                    $('.chart-tooltip').css({
                        left: event.pageX + 12,
                        top: event.pageY - 24
                    });
                })
                .off('mouseleave.trafficSource', '.traffic-source-chart .ct-bar')
                .on('mouseleave.trafficSource', '.traffic-source-chart .ct-bar', function() {
                    $('.chart-tooltip').hide();
                });
        }

        function fetchTrafficSources(range) {
            if (!trafficSourceFilterUrl) {
                console.error('Traffic source filter URL is not defined');
                return;
            }

            console.log('Fetching traffic sources for range:', range);
            $.get(trafficSourceFilterUrl, {
                    range: range
                })
                .done(function(response) {
                    console.log('Traffic sources response:', response);
                    renderTrafficSourceChart(response.labels || [], response.totals || []);
                    renderTrafficSourceTable(response.items || []);
                    bindTrafficSourceTooltips();
                })
                .fail(function(xhr, status, error) {
                    console.error('Failed to fetch traffic sources:', status, error);
                    console.error('Response:', xhr.responseText);
                });
        }

        function initTrafficSourceLazyLoad() {
            const trafficCard = document.querySelector('.traffic-source-card');
            const rangeSelect = document.getElementById('traffic-source-range');

            if (!trafficCard || !trafficSourceFilterUrl) {
                return;
            }

            const loadOnce = function() {
                if (trafficSourceLoaded) {
                    return;
                }

                trafficSourceLoaded = true;
                const range = rangeSelect ? rangeSelect.value : 'month';
                fetchTrafficSources(range);
            };

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            observer.disconnect();
                            loadOnce();
                        }
                    });
                }, {
                    rootMargin: '100px'
                });

                observer.observe(trafficCard);
            } else {
                loadOnce();
            }
        }

        $(document).ready(function() {
            renderTopSellChart(topSellInitialLabels, topSellInitialTotals);
            renderTopSellPie(topSellInitialLabels, topSellInitialTotals);
            bindTopSellTooltips();

            $('#top-sell-range').on('change', function() {
                fetchTopSell($(this).val());
            });

            const trafficRangeSelect = document.getElementById('traffic-source-range');
            if (trafficRangeSelect) {
                trafficRangeSelect.addEventListener('change', function() {
                    if (!trafficSourceLoaded) {
                        trafficSourceLoaded = true;
                    }

                    fetchTrafficSources(this.value);
                });
            }

            const mediumRangeSelect = document.getElementById('utm-medium-range');
            if (mediumRangeSelect) {
                mediumRangeSelect.addEventListener('change', function() {
                    if (!utmMediumLoaded) {
                        utmMediumLoaded = true;
                    }

                    fetchUtmMedium(this.value);
                });
            }

            const campaignRangeSelect = document.getElementById('utm-campaign-range');
            if (campaignRangeSelect) {
                campaignRangeSelect.addEventListener('change', function() {
                    if (!utmCampaignLoaded) {
                        utmCampaignLoaded = true;
                    }

                    fetchUtmCampaign(this.value);
                });
            }

            const citiesRangeSelect = document.getElementById('top-cities-range');
            if (citiesRangeSelect) {
                citiesRangeSelect.addEventListener('change', function() {
                    if (!topCitiesLoaded) {
                        topCitiesLoaded = true;
                    }

                    fetchTopCities(this.value);
                });
            }

            initTrafficSourceLazyLoad();
            initUtmMediumLazyLoad();
            initUtmCampaignLazyLoad();
            initTopCitiesLazyLoad();
        });

        // UTM Medium Chart Functions
        function renderUtmMediumChart(labels, totals) {
            const chartElement = document.querySelector('.utm-medium-chart');
            if (!chartElement) {
                return;
            }

            chartElement.innerHTML = '';
            const barTotal = totals.reduce(function(sum, value) {
                return sum + Number(value || 0);
            }, 0);
            chartElement.setAttribute('data-total', barTotal);

            const chart = new Chartist.Bar('.utm-medium-chart', {
                labels: labels,
                series: [totals]
            }, {
                axisX: {
                    showGrid: false
                },
                axisY: {
                    onlyInteger: true
                },
                chartPadding: {
                    top: 10,
                    right: 10,
                    bottom: 0,
                    left: 0
                }
            });

            chart.on('draw', function(data) {
                if (data.type === 'bar') {
                    const label = labels[data.index] || '';
                    const value = data.value && data.value.y !== undefined ? data.value.y : data.value;
                    data.element.attr({
                        'data-label': label,
                        'data-value': value
                    });
                }
            });
        }

        function renderUtmMediumTable(items) {
            const tableBody = $('#utm-medium-table-body');
            if (!tableBody.length) {
                return;
            }

            if (!items || items.length === 0) {
                tableBody.html(
                    '<tr><td colspan="3" class="text-danger font-weight-bold text-center">No Data Found</td></tr>'
                );
                return;
            }

            const rows = items.map(function(item, index) {
                return '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + item.label + '</td>' +
                    '<td class="text-right">' + item.total + '</td>' +
                    '</tr>';
            }).join('');

            tableBody.html(rows);
        }

        function bindUtmMediumTooltips() {
            const tooltip = $('.chart-tooltip');
            if (!tooltip.length) {
                $('body').append('<div class="chart-tooltip" style="display:none;"></div>');
            }

            $(document)
                .off('mouseenter.utmMedium', '.utm-medium-chart .ct-bar')
                .on('mouseenter.utmMedium', '.utm-medium-chart .ct-bar', function(event) {
                    const label = $(this).attr('data-label');
                    const value = Number($(this).attr('data-value') || 0);
                    const total = Number($(this).closest('.utm-medium-chart').attr('data-total') || 0);

                    if (!label || total <= 0) {
                        return;
                    }

                    const percent = ((value / total) * 100).toFixed(1);

                    $('.chart-tooltip')
                        .text(percent + '% - ' + label)
                        .show()
                        .css({
                            left: event.pageX + 12,
                            top: event.pageY - 24
                        });
                })
                .off('mousemove.utmMedium', '.utm-medium-chart')
                .on('mousemove.utmMedium', '.utm-medium-chart', function(event) {
                    $('.chart-tooltip').css({
                        left: event.pageX + 12,
                        top: event.pageY - 24
                    });
                })
                .off('mouseleave.utmMedium', '.utm-medium-chart .ct-bar')
                .on('mouseleave.utmMedium', '.utm-medium-chart .ct-bar', function() {
                    $('.chart-tooltip').hide();
                });
        }

        function fetchUtmMedium(range) {
            if (!utmMediumFilterUrl) {
                return;
            }

            $.get(utmMediumFilterUrl, {
                    range: range
                })
                .done(function(response) {
                    renderUtmMediumChart(response.labels || [], response.totals || []);
                    renderUtmMediumTable(response.items || []);
                    bindUtmMediumTooltips();
                });
        }

        function initUtmMediumLazyLoad() {
            const mediumCard = document.querySelector('.utm-medium-card');
            const rangeSelect = document.getElementById('utm-medium-range');

            if (!mediumCard || !utmMediumFilterUrl) {
                return;
            }

            const loadOnce = function() {
                if (utmMediumLoaded) {
                    return;
                }

                utmMediumLoaded = true;
                const range = rangeSelect ? rangeSelect.value : 'month';
                fetchUtmMedium(range);
            };

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            observer.disconnect();
                            loadOnce();
                        }
                    });
                }, {
                    rootMargin: '100px'
                });

                observer.observe(mediumCard);
            } else {
                loadOnce();
            }
        }

        // UTM Campaign Chart Functions
        function renderUtmCampaignChart(labels, totals) {
            const chartElement = document.querySelector('.utm-campaign-chart');
            if (!chartElement) {
                return;
            }

            chartElement.innerHTML = '';
            const barTotal = totals.reduce(function(sum, value) {
                return sum + Number(value || 0);
            }, 0);
            chartElement.setAttribute('data-total', barTotal);

            const chart = new Chartist.Bar('.utm-campaign-chart', {
                labels: labels,
                series: [totals]
            }, {
                axisX: {
                    showGrid: false
                },
                axisY: {
                    onlyInteger: true
                },
                chartPadding: {
                    top: 10,
                    right: 10,
                    bottom: 0,
                    left: 0
                }
            });

            chart.on('draw', function(data) {
                if (data.type === 'bar') {
                    const label = labels[data.index] || '';
                    const value = data.value && data.value.y !== undefined ? data.value.y : data.value;
                    data.element.attr({
                        'data-label': label,
                        'data-value': value
                    });
                }
            });
        }

        function renderUtmCampaignTable(items) {
            const tableBody = $('#utm-campaign-table-body');
            if (!tableBody.length) {
                return;
            }

            if (!items || items.length === 0) {
                tableBody.html(
                    '<tr><td colspan="3" class="text-danger font-weight-bold text-center">No Data Found</td></tr>'
                );
                return;
            }

            const rows = items.map(function(item, index) {
                return '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + item.label + '</td>' +
                    '<td class="text-right">' + item.total + '</td>' +
                    '</tr>';
            }).join('');

            tableBody.html(rows);
        }

        function bindUtmCampaignTooltips() {
            const tooltip = $('.chart-tooltip');
            if (!tooltip.length) {
                $('body').append('<div class="chart-tooltip" style="display:none;"></div>');
            }

            $(document)
                .off('mouseenter.utmCampaign', '.utm-campaign-chart .ct-bar')
                .on('mouseenter.utmCampaign', '.utm-campaign-chart .ct-bar', function(event) {
                    const label = $(this).attr('data-label');
                    const value = Number($(this).attr('data-value') || 0);
                    const total = Number($(this).closest('.utm-campaign-chart').attr('data-total') || 0);

                    if (!label || total <= 0) {
                        return;
                    }

                    const percent = ((value / total) * 100).toFixed(1);

                    $('.chart-tooltip')
                        .text(percent + '% - ' + label)
                        .show()
                        .css({
                            left: event.pageX + 12,
                            top: event.pageY - 24
                        });
                })
                .off('mousemove.utmCampaign', '.utm-campaign-chart')
                .on('mousemove.utmCampaign', '.utm-campaign-chart', function(event) {
                    $('.chart-tooltip').css({
                        left: event.pageX + 12,
                        top: event.pageY - 24
                    });
                })
                .off('mouseleave.utmCampaign', '.utm-campaign-chart .ct-bar')
                .on('mouseleave.utmCampaign', '.utm-campaign-chart .ct-bar', function() {
                    $('.chart-tooltip').hide();
                });
        }

        function fetchUtmCampaign(range) {
            if (!utmCampaignFilterUrl) {
                return;
            }

            $.get(utmCampaignFilterUrl, {
                    range: range
                })
                .done(function(response) {
                    renderUtmCampaignChart(response.labels || [], response.totals || []);
                    renderUtmCampaignTable(response.items || []);
                    bindUtmCampaignTooltips();
                });
        }

        function initUtmCampaignLazyLoad() {
            const campaignCard = document.querySelector('.utm-campaign-card');
            const rangeSelect = document.getElementById('utm-campaign-range');

            if (!campaignCard || !utmCampaignFilterUrl) {
                return;
            }

            const loadOnce = function() {
                if (utmCampaignLoaded) {
                    return;
                }

                utmCampaignLoaded = true;
                const range = rangeSelect ? rangeSelect.value : 'month';
                fetchUtmCampaign(range);
            };

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            observer.disconnect();
                            loadOnce();
                        }
                    });
                }, {
                    rootMargin: '100px'
                });

                observer.observe(campaignCard);
            } else {
                loadOnce();
            }
        }

        // Top Cities Chart Functions
        function renderTopCitiesChart(labels, totals) {
            const chartElement = document.querySelector('.top-cities-chart');
            if (!chartElement) {
                return;
            }

            chartElement.innerHTML = '';
            const barTotal = totals.reduce(function(sum, value) {
                return sum + Number(value || 0);
            }, 0);
            chartElement.setAttribute('data-total', barTotal);

            const chart = new Chartist.Bar('.top-cities-chart', {
                labels: labels,
                series: [totals]
            }, {
                axisX: {
                    showLabel: true,
                    showGrid: false,
                    offset: 25
                },
                axisY: {
                    onlyInteger: true
                },
                chartPadding: {
                    top: 10,
                    right: 5,
                    bottom: 100,
                    left: 10
                },
                height: '300px'
            });
        }

        function bindTopCitiesTooltips() {
            const total = Number($('.top-cities-chart').attr('data-total')) || 0;

            $(document)
                .off('mouseenter.topCities', '.top-cities-chart .ct-bar')
                .on('mouseenter.topCities', '.top-cities-chart .ct-bar', function() {
                    const $bar = $(this);
                    const value = $bar.attr('ct:value');
                    const seriesName = $bar.parent().attr('ct:series-name');
                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;

                    const $tooltip = $('<div class="chart-tooltip">')
                        .text(value + ' (' + percentage + '%)')
                        .appendTo('body');

                    const barOffset = $bar.offset();
                    $tooltip.css({
                        left: barOffset.left + ($bar.width() / 2) - ($tooltip.outerWidth() / 2),
                        top: barOffset.top - $tooltip.outerHeight() - 10
                    }).show();
                })
                .off('mouseleave.topCities', '.top-cities-chart .ct-bar')
                .on('mouseleave.topCities', '.top-cities-chart .ct-bar', function() {
                    $('.chart-tooltip').hide();
                });
        }

        function fetchTopCities(range) {
            if (!topCitiesFilterUrl) {
                console.error('Top cities filter URL is not defined');
                return;
            }

            console.log('Fetching top cities for range:', range);
            $.get(topCitiesFilterUrl, {
                    range: range
                })
                .done(function(response) {
                    console.log('Top cities response:', response);
                    renderTopCitiesChart(response.labels || [], response.totals || []);
                    bindTopCitiesTooltips();
                })
                .fail(function(xhr, status, error) {
                    console.error('Failed to fetch top cities:', status, error);
                    console.error('Response:', xhr.responseText);
                });
        }

        function initTopCitiesLazyLoad() {
            const citiesCard = document.querySelector('.top-cities-card');
            const rangeSelect = document.getElementById('top-cities-range');

            if (!citiesCard || !topCitiesFilterUrl) {
                return;
            }

            const loadOnce = function() {
                if (topCitiesLoaded) {
                    return;
                }

                topCitiesLoaded = true;
                const range = rangeSelect ? rangeSelect.value : 'month';
                fetchTopCities(range);
            };

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            observer.disconnect();
                            loadOnce();
                        }
                    });
                }, {
                    rootMargin: '100px'
                });

                observer.observe(citiesCard);
            } else {
                loadOnce();
            }
        }
    </script>
@endsection

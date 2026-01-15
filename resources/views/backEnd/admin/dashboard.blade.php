@extends('backEnd.admin.layouts.master')

@section('title')
    Dashboard
@endsection
@php
    $total_revenue = $data['total_revenue'] ?? [];
    $total_customer = $data['total_customer'] ?? [];
    $total_product = $data['total_product'] ?? [];
    $total_staff = $data['total_staff'] ?? [];
    $employees = $data['employees'] ?? [];
    $admins = $data['admins'] ?? [];
    $managers = $data['managers'] ?? [];

    $total_order = $data['total_order'] ?? [];
    $total_hold_orders = $data['total_hold_orders'] ?? [];
    $total_deliver_orders = $data['total_deliver_orders'] ?? [];
    $total_process_orders = $data['total_process_orders'] ?? [];
    $total_pend_pay_orders = $data['total_pend_pay_orders'] ?? [];
    $total_cancel_orders = $data['total_cancel_orders'] ?? [];
    $total_pending_invoice_orders = $data['total_pending_invoice_orders'] ?? [];
    $total_on_delivery_orders = $data['total_on_delivery_orders'] ?? [];
    $total_pending_return_orders = $data['total_pending_return_orders'] ?? [];
    $total_courier_hold_orders = $data['total_courier_hold_orders'] ?? [];
    $total_nr_1_orders = $data['total_nr_1_orders'] ?? [];
    $total_invoiced_orders = $data['total_invoiced_orders'] ?? [];
    $total_return_orders = $data['total_return_orders'] ?? [];
    $total_incomplete_orders = $data['total_incomplete_orders'] ?? [];
    $total_confirmed_orders = $data['total_confirmed_orders'] ?? [];
    $total_stock_out_orders = $data['total_stock_out_orders'] ?? [];
    $total_partial_delivery_orders = $data['total_partial_delivery_orders'] ?? [];
    $total_lost_orders = $data['total_lost_orders'] ?? [];

    $today_all_orders = $data['today_all_orders'] ?? [];
    $today_hold_orders = $data['today_hold_orders'] ?? [];
    $today_deliver_orders = $data['today_deliver_orders'] ?? [];
    $today_process_orders = $data['today_process_orders'] ?? [];
    $today_pend_pay_orders = $data['today_pend_pay_orders'] ?? [];
    $today_cancel_orders = $data['today_cancel_orders'] ?? [];
    $today_pending_invoice_orders = $data['today_pending_invoice_orders'] ?? [];
    $today_on_delivery_orders = $data['today_on_delivery_orders'] ?? [];
    $today_pending_return_orders = $data['today_pending_return_orders'] ?? [];
    $today_courier_hold_orders = $data['today_courier_hold_orders'] ?? [];
    $today_nr_1_orders = $data['today_nr_1_orders'] ?? [];
    $today_invoiced_orders = $data['today_invoiced_orders'] ?? [];
    $today_return_orders = $data['today_return_orders'] ?? [];
    $today_incomplete_orders = $data['today_incomplete_orders'] ?? [];
    $today_confirmed_orders = $data['today_confirmed_orders'] ?? [];
    $today_stock_out_orders = $data['today_stock_out_orders'] ?? [];
    $today_partial_delivery_orders = $data['today_partial_delivery_orders'] ?? [];
    $today_lost_orders = $data['today_lost_orders'] ?? [];

    $recent_orders = $data['recent_orders'] ?? [];

    $last_order = $last_order ?? null;
@endphp
@section('css')
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <div class="ecommerce-widget">
                    <div class="row">
                        <div class="col-12">
                            <h4><b>Last Order:</b> {{ $last_order ? \Carbon\Carbon::parse($last_order)->diffForHumans() : 'N/A' }}</h4>
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
                                            <h1 class="mb-1">{{ $total_process_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_nr_1_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_hold_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_pend_pay_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_cancel_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_confirmed_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_pending_invoice_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_invoiced_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_stock_out_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_courier_hold_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_on_delivery_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_deliver_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_partial_delivery_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_pending_return_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_return_orders }}</h1>
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
                                            <h1 class="mb-1">{{ $total_lost_orders }}</h1>
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
                                                    @php($i = 1)
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
                                                                    @if ($item->status == 0)
                                                                        <span class="badge badge-warning">Hold</span>
                                                                    @endif
                                                                    @if ($item->status == 1)
                                                                        <span class="badge badge-success">Delivered</span>
                                                                    @endif
                                                                    @if ($item->status == 2)
                                                                        <span class="badge badge-info">Processing</span>
                                                                    @endif
                                                                    @if ($item->status == 3)
                                                                        <span class="badge badge-secondary">Pending
                                                                            Payment</span>
                                                                    @endif
                                                                    @if ($item->status == 4)
                                                                        <span class="badge badge-danger">Cancelled</span>
                                                                    @endif
                                                                    @if ($item->status == 5)
                                                                        <span class="badge badge-warning">Pending
                                                                            Invoice</span>
                                                                    @endif
                                                                    @if ($item->status == 6)
                                                                        <span class="badge badge-primary">On
                                                                            Delivery</span>
                                                                    @endif
                                                                    @if ($item->status == 7)
                                                                        <span class="badge badge-danger">Pending
                                                                            Return</span>
                                                                    @endif
                                                                    @if ($item->status == 8)
                                                                        <span class="badge badge-warning">Courier
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 9)
                                                                        <span class="badge badge-warning">No Response
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 10)
                                                                        <span class="badge badge-warning"> Invoiced
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 11)
                                                                        <span class="badge badge-warning"> Return</span>
                                                                    @endif
                                                                    @if ($item->status == 12)
                                                                        <span class="badge badge-warning">Incomplete
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 13)
                                                                        <span class="badge badge-warning">Confirmed
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 14)
                                                                        <span class="badge badge-warning">Stock
                                                                            Out</span>
                                                                    @endif
                                                                    @if ($item->status == 15)
                                                                        <span class="badge badge-warning">Partial
                                                                            Delivery</span>
                                                                    @endif
                                                                    @if ($item->status == 16)
                                                                        <span class="badge badge-warning">Lost</span>
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
                                                    @php($i = 1)
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
                                                                    @if ($item->status == 0)
                                                                        <span class="badge badge-warning">Hold</span>
                                                                    @endif
                                                                    @if ($item->status == 1)
                                                                        <span class="badge badge-success">Delivered</span>
                                                                    @endif
                                                                    @if ($item->status == 2)
                                                                        <span class="badge badge-info">Processing</span>
                                                                    @endif
                                                                    @if ($item->status == 3)
                                                                        <span class="badge badge-secondary">Pending
                                                                            Payment</span>
                                                                    @endif
                                                                    @if ($item->status == 4)
                                                                        <span class="badge badge-danger">Cancelled</span>
                                                                    @endif
                                                                    @if ($item->status == 5)
                                                                        <span class="badge badge-warning">Pending
                                                                            Invoice</span>
                                                                    @endif
                                                                    @if ($item->status == 6)
                                                                        <span class="badge badge-primary">On
                                                                            Delivery</span>
                                                                    @endif
                                                                    @if ($item->status == 7)
                                                                        <span class="badge badge-danger">Pending
                                                                            Return</span>
                                                                    @endif
                                                                    @if ($item->status == 8)
                                                                        <span class="badge badge-warning">Courier
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 9)
                                                                        <span class="badge badge-warning">No Response
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 10)
                                                                        <span class="badge badge-warning"> Invoiced
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 11)
                                                                        <span class="badge badge-warning"> Return</span>
                                                                    @endif
                                                                    @if ($item->status == 12)
                                                                        <span class="badge badge-warning">Incomplete
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 13)
                                                                        <span class="badge badge-warning">Confirmed
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 14)
                                                                        <span class="badge badge-warning">Stock
                                                                            Out</span>
                                                                    @endif
                                                                    @if ($item->status == 15)
                                                                        <span class="badge badge-warning">Partial
                                                                            Delivery</span>
                                                                    @endif
                                                                    @if ($item->status == 16)
                                                                        <span class="badge badge-warning">Lost</span>
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
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">Top Cities</h5>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">SL.</th>
                                                <th>Name</th>
                                                <th class="text-right">Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php($i = 1)
                                            @foreach ($top_cities as $city)
                                                @if ($city->total > 0)
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $city->city_name }}</td>
                                                        <td class="text-right">{{ $city->total }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">Top Sell Products</h5>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%">SL.</th>
                                                    <th>Name</th>
                                                    <th class="text-right">Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (Auth::guard('admin')->check() || Auth::guard('manager')->check())
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
                                                            <td colspan="7"
                                                                class="text-danger font-weight-bold text-center">No Order
                                                                Found
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    @php($i = 1)
                                                    @if ($recent_orders->count() > 0)
                                                        @foreach ($recent_orders as $item)
                                                            <tr>
                                                                <td>{{ $i++ }}</td>
                                                                <td>{{ date('d M', strtotime($item->order_date)) }}</td>

                                                                <td class="text-center">
                                                                    @if ($item->status == 0)
                                                                        <span class="badge badge-warning">Hold</span>
                                                                    @endif
                                                                    @if ($item->status == 1)
                                                                        <span class="badge badge-success">Delivered</span>
                                                                    @endif
                                                                    @if ($item->status == 2)
                                                                        <span class="badge badge-info">Processing</span>
                                                                    @endif
                                                                    @if ($item->status == 3)
                                                                        <span class="badge badge-secondary">Pending
                                                                            Payment</span>
                                                                    @endif
                                                                    @if ($item->status == 4)
                                                                        <span class="badge badge-danger">Cancelled</span>
                                                                    @endif
                                                                    @if ($item->status == 5)
                                                                        <span class="badge badge-warning">Pending
                                                                            Invoice</span>
                                                                    @endif
                                                                    @if ($item->status == 6)
                                                                        <span class="badge badge-primary">On
                                                                            Delivery</span>
                                                                    @endif
                                                                    @if ($item->status == 7)
                                                                        <span class="badge badge-danger">Pending
                                                                            Return</span>
                                                                    @endif
                                                                    @if ($item->status == 8)
                                                                        <span class="badge badge-warning">Courier
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 9)
                                                                        <span class="badge badge-warning">No Response
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 10)
                                                                        <span class="badge badge-warning"> Invoiced
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 11)
                                                                        <span class="badge badge-warning"> Return</span>
                                                                    @endif
                                                                    @if ($item->status == 12)
                                                                        <span class="badge badge-warning">Incomplete
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 13)
                                                                        <span class="badge badge-warning">Confirmed
                                                                        </span>
                                                                    @endif
                                                                    @if ($item->status == 14)
                                                                        <span class="badge badge-warning">Stock
                                                                            Out</span>
                                                                    @endif
                                                                    @if ($item->status == 15)
                                                                        <span class="badge badge-warning">Partial
                                                                            Delivery</span>
                                                                    @endif
                                                                    @if ($item->status == 16)
                                                                        <span class="badge badge-warning">Lost</span>
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
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
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
    </script>
@endsection

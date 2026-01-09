@extends('backEnd.admin.layouts.master')

@section('title')
    Sales Report
@endsection
@section('css')
    <link rel="stylesheet" href="{{asset('backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.css')}}">
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
    //$pending_delivery = $data['pending_delivery'] ?? [];
    //$pending_delivery_discount = $data['pending_delivery_discount'] ?? [];
    //$on_delivery = $data['on_delivery'] ?? [];
    //$on_delivery_discount = $data['on_delivery_discount'] ?? [];
    $delivered = $data['delivered'] ?? [];
    $delivered_discount = $data['delivered_discount'] ?? [];
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
                            <h2 class="pageheader-title">Sales Report</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : ""))}}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Sales Report</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-md-3 mb-2">
                    <div class="col-12">
                        <form action="" method="get">
                            <div class="d-flex">
                                <div class="form-group mr-1">
                                    <select name="custom_range" id="custom_range" class="form-control h-34">
                                        <option value="">Custom Range</option>
                                        <option value="today" {{request()->query('custom_range')=='today'?"selected":""}}>Today</option>
                                        <option value="yesterday" {{request()->query('custom_range')=='yesterday'?"selected":""}}>Yesterday</option>
                                        <option value="last_7_days" {{request()->query('custom_range')=='last_7_days'?"selected":""}}>Last 7 Days</option>
                                        <option value="this_month" {{request()->query('custom_range')=='this_month'?"selected":""}}>This Month</option>
                                        <option value="last_month" {{request()->query('custom_range')=='last_month'?"selected":""}}>Last Month</option>
                                        <option value="last_6_months" {{request()->query('custom_range')=='last_6_months'?"selected":""}}>Last 6 Months</option>
                                    </select>
                                </div>
                                <div class="form-group mr-1">
                                    <input type="text" class="form-control mr-2 datetimepicker h-34"
                                           name="start_date" id="start_date" placeholder="Start Date"
                                           value="{{request()->query('start_date')}}" {{request()->query('custom_range')!=null?"disabled":""}}>
                                </div>
                                <div class="form-group mr-1">
                                    <input type="text" class="form-control mr-2 datetimepicker h-34"
                                           name="end_date" id="end_date" placeholder="End Date"
                                           value="{{request()->query('end_date')}}" {{request()->query('custom_range')!=null?"disabled":""}}>
                                </div>
                                <div class="col-md-2 col-12">
                                    <button class="btn btn-info btn-sm">Search</button>
                                    <a href="{{route('admin.reports.sales')}}" class="btn btn-dark btn-sm">Reset</a>
                                </div>
                            </div>
                        </form>

                        <a href="javascript:void(0)" class="btn btn-danger btn-sm print"><i class="fa fa-print"></i> Print</a>

                    </div>
                </div>

                {{--<div class="row mb-3 order-card">
                    <div class="col-12">
                        <h3 class="mb-2">Pending Delivery</h3>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <div class="card border-3">
                            <div class="card-body">
                                <div class="metric-value d-inline-block">
                                    <h2 class="mb-0">৳{{$pending_delivery}}</h2>
                                </div>
                                <h5 class="h5-s text-info">Sales</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <div class="card border-3">
                            <div class="card-body">
                                <div class="metric-value d-inline-block">
                                    <h2 class="mb-0">৳{{'-'.$pending_delivery_discount}}</h2>
                                </div>
                                <h5 class="h5-s text-danger">Discount</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <div class="card border-3">
                            <div class="card-body">
                                <div class="metric-value d-inline-block">
                                    <h2 class="mb-0">৳{{$pending_delivery - $pending_delivery_discount}}</h2>
                                </div>
                                <h5 class="h5-s text-success">Total</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 order-card">
                    <div class="col-12">
                        <h3 class="mb-2">On Delivery</h3>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <div class="card border-3">
                            <div class="card-body">
                                <div class="metric-value d-inline-block">
                                    <h2 class="mb-0">৳{{$on_delivery}}</h2>
                                </div>
                                <h5 class="h5-s text-info">Sales</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <div class="card border-3">
                            <div class="card-body">
                                <div class="metric-value d-inline-block">
                                    <h2 class="mb-0">৳{{'-'.$on_delivery_discount}}</h2>
                                </div>
                                <h5 class="h5-s text-danger">Discount</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <div class="card border-3">
                            <div class="card-body">
                                <div class="metric-value d-inline-block">
                                    <h2 class="mb-0">৳{{$on_delivery - $on_delivery_discount}}</h2>
                                </div>
                                <h5 class="h5-s text-success">Total</h5>
                            </div>
                        </div>
                    </div>
                </div>--}}

                <div class="row mb-3 order-card">
                    <div class="col-12">
                        <h3 class="mb-2">Delivered</h3>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <div class="card border-3">
                            <div class="card-body">
                                <div class="metric-value d-inline-block">
                                    <h2 class="mb-0">৳{{$total_sales}}</h2>
                                </div>
                                <h5 class="h5-s text-info">Sales</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <div class="card border-3">
                            <div class="card-body">
                                <div class="metric-value d-inline-block">
                                    <h2 class="mb-0">৳{{'-'.$total_discounts}}</h2>
                                </div>
                                <h5 class="h5-s text-danger">Discount</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 mb-md-4 mb-3">
                        <div class="card border-3">
                            <div class="card-body">
                                <div class="metric-value d-inline-block">
                                    <h2 class="mb-0">{{$total_sales-$total_discounts}}</h2>
                                </div>
                                <h5 class="h5-s text-success">Total</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{asset('backEnd/assets/vendor/datetimepicker/moment.min.js')}}"></script>
    <script src="{{asset('backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.js')}}"></script>

    <script>
        $('.datetimepicker').datetimepicker({
            icons:
                {
                    next: 'fa fa-angle-right',
                    previous: 'fa fa-angle-left'
                },
            format: 'DD-MM-YYYY',
            // defaultDate: new Date(),
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();

            //date range
            $('#custom_range').on('change', function () {
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

            $('.print').on('click', function () {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{Auth::guard('admin')->check() ? route('admin.reports.sales.print') : (Auth::guard('manager')->check() ? route('manager.orders.print') : (Auth::guard('employee')->check() ? route('employee.orders.print') : ""))}}',
                    type: 'POST',
                    data: {_token: CSRF_TOKEN, custom_range: $('#custom_range').val(), start_date: $('#start_date').val(), end_date: $('#end_date').val()},
                    success: function (data) {
                        newWin = window.open("");
                        newWin.document.write(data);
                        newWin.document.close();
                    }
                });
            });
        });
    </script>
@endsection

@extends('backEnd.admin.layouts.master')

@section('title')
    Sales Report
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
                                                href="{{ Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : '')) }}"
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
                                    <input type="text" class="form-control mr-2 h-34" name="total_dollar"
                                        id="total_dollar" placeholder="Total Dollar"
                                        value="{{ request()->query('total_dollar') }}">
                                </div>
                                <div class="form-group mr-1">
                                    <input type="text" class="form-control mr-2 h-34" name="dollar_rate" id="dollar_rate"
                                        placeholder="Dollar Rate" value="{{ request()->query('dollar_rate') }}">
                                </div>
                                <div class="form-group  mr-1">
                                    <input type="text" class="form-control mr-2 h-34" name="return_percentage"
                                        id="return_percentage" placeholder="Return Percentage"
                                        value="{{ request()->query('return_percentage') }}">
                                </div>

                            </div>

                            <div class="d-flex">
                                <div class="form-group mr-1">
                                    <select name="product_id" id="product_id" class="form-control h-34"
                                        style="width: 200px;">
                                        <option value="">Product Select</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ request()->query('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="form-group
                                    mr-1">
                                    <select name="status" id="status" class="form-control h-34" style="width: 150px;">
                                        <option value="">Status</option>
                                        <option value="0" {{ request()->query('status') == '0' ? 'selected' : '' }}>
                                            Hold</option>
                                        <option value="1" {{ request()->query('status') == '1' ? 'selected' : '' }}>
                                            Delivered</option>
                                        <option value="2" {{ request()->query('status') == '2' ? 'selected' : '' }}>
                                            Processing</option>
                                        <option value="3" {{ request()->query('status') == '3' ? 'selected' : '' }}>
                                            Pending
                                            Payment</option>
                                        <option value="4" {{ request()->query('status') == '4' ? 'selected' : '' }}>
                                            Cancelled</option>
                                        <option value="5" {{ request()->query('status') == '5' ? 'selected' : '' }}>
                                            Pending
                                            Invoice</option>
                                        <option value="6" {{ request()->query('status') == '6' ? 'selected' : '' }}>On
                                            Delivery</option>
                                        <option value="7" {{ request()->query('status') == '7' ? 'selected' : '' }}>
                                            Pending Return
                                        </option>
                                        <option value="8" {{ request()->query('status') == '8' ? 'selected' : '' }}>
                                            Courier
                                        </option>
                                        <option value="9" {{ request()->query('status') == '9' ? 'selected' : '' }}>No
                                            Response</option>
                                        <option value="10" {{ request()->query('status') == '10' ? 'selected' : '' }}>
                                            Invoiced</option>
                                        <option value="11" {{ request()->query('status') == '11' ? 'selected' : '' }}>
                                            Return</option>
                                        {{-- <option value="12" {{ request()->query('status') == '12' ? 'selected' : '' }}>
                                            Incomplete</option> --}}
                                        <option value="13" {{ request()->query('status') == '13' ? 'selected' : '' }}>
                                            Completed</option>
                                        <option value="14" {{ request()->query('status') == '14' ? 'selected' : '' }}>
                                            Stock
                                            Out</option>
                                        <option value="15" {{ request()->query('status') == '15' ? 'selected' : '' }}>
                                            Partial Delivery</option>
                                        <option value="16" {{ request()->query('status') == '16' ? 'selected' : '' }}>
                                            Lost
                                        </option>

                                    </select>
                                </div>
                                <div class="form-group mr-1">
                                    <select name="custom_range" id="custom_range" class="form-control  h-34">
                                        <option value="">Custom Range</option>
                                        <option value="today"
                                            {{ request()->query('custom_range') == 'today' ? 'selected' : '' }}>Today
                                        </option>
                                        <option value="yesterday"
                                            {{ request()->query('custom_range') == 'yesterday' ? 'selected' : '' }}>
                                            Yesterday
                                        </option>
                                        <option value="last_7_days"
                                            {{ request()->query('custom_range') == 'last_7_days' ? 'selected' : '' }}>Last
                                            7
                                            Days
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
                                            Last 6
                                            Months</option>
                                    </select>
                                </div>
                                <div class="form-group mr-1">
                                    <input type="text" class="form-control mr-2 datetimepicker h-34" name="start_date"
                                        id="start_date" placeholder="Start Date"
                                        value="{{ request()->query('start_date') }}"
                                        {{ request()->query('custom_range') != null ? 'disabled' : '' }}>
                                </div>
                                <div class="form-group mr-1">
                                    <input type="text" class="form-control mr-2 datetimepicker h-34" name="end_date"
                                        id="end_date" placeholder="End Date" value="{{ request()->query('end_date') }}"
                                        {{ request()->query('custom_range') != null ? 'disabled' : '' }}>
                                </div>
                                <div class="col-md-2 col-12 p-0">
                                    <button class="btn btn-info btn-sm">Search</button>
                                    <a href="{{ route('admin.reports.profit.loss') }}"
                                        class="btn btn-dark btn-sm">Reset</a>
                                </div>
                            </div>
                        </form>

                        <a href="javascript:void(0)" class="btn btn-danger btn-sm print"><i class="fa fa-print"></i>
                            Print</a>

                    </div>
                </div>



                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th>Total Sell (৳)</th>
                                            <th>Dollar Cost (৳)</th>
                                            <th>Per Cost Order ($)</th>
                                            <th>Product Purchase Cost (৳)</th>
                                            <th>Package Cost (৳)</th>
                                            <th>Return Cost (৳)</th>
                                            <th>Courier Charge Cost (৳)</th>
                                            {{-- <th>
                                                Discount (৳)
                                            </th> --}}
                                            <th>Net Sell (৳)</th>
                                            <th>Profit/ Loss (৳)</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td>{{ $grand_total }}</td>
                                            <td>{{ $dollar_cost }}</td>
                                            <td>{{ number_format($cost_per_order, 2) }}</td>
                                            <td>{{ $product_purchase_cost }}</td>
                                            <td>{{ $product_packaging_cost }}</td>
                                            {{-- <td>{{ $discount }}</td> --}}
                                            <td>
                                                {{ $return_cost }} ({{ $return_percentage }}%)
                                            </td>
                                            <td>{{ $courier_charge_cost }}</td>
                                            <td>{{ $net_sales }}</td>
                                            <td>
                                                @if ($profit_loss < 0)
                                                    <span class="text-danger">{{ $profit_loss }}</span>
                                                @else
                                                    <span class="text-success">{{ $profit_loss }}</span>
                                                @endif
                                            </td>

                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <td colspan="9" class="text-left text-muted">
                                            <span>Note:</span> <br>
                                            <span>
                                                Return Cost = Total Sell * Return Percentage / 100
                                            </span>
                                            <br>
                                            <span>
                                                Net Sell = Total Sell- Return Cost
                                            </span>
                                            <br>
                                            <span>
                                                Profit/Loss = Net Sell - (Dollar Cost + Product Purchase Cost + Package Cost + Return Cost + Courier Charge Cost)
                                            </span>
                                        </td>
                                    </tfoot>
                                </table>

                                <div class="mt-3">
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
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();

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

            $('.print').on('click', function() {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ Auth::guard('admin')->check() ? route('admin.reports.sales.print') : (Auth::guard('manager')->check() ? route('manager.orders.print') : (Auth::guard('employee')->check() ? route('employee.orders.print') : '')) }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        custom_range: $('#custom_range').val(),
                        start_date: $('#start_date').val(),
                        end_date: $('#end_date').val()
                    },
                    success: function(data) {
                        newWin = window.open("");
                        newWin.document.write(data);
                        newWin.document.close();
                    }
                });
            });
        });
    </script>
@endsection

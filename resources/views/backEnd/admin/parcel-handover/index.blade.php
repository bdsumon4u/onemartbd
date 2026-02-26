@extends('backEnd.admin.layouts.master')

@section('title')
    Order Handover
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

@endphp
@section('body')
    {{-- @dd($data) --}}
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <!-- ============================================================== -->
                <!-- pageheader  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Order Handover</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{ Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : '')) }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Handover Orders</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <form action="" method="get"
                            class="d-flex align-items-center flex-wrap flex-md-nowrap"
                            style="gap: 8px;">
                            <input type="text" class="form-control w-auto" name="invoice_id" autofocus placeholder="Invoice ID"
                                value="{{ old('invoice_id', request('invoice_id')) }}">
                            @error('invoice_id')
                                <div class="text-danger small ml-2">{{ $message }}</div>
                            @enderror
                            <a href="{{ route('admin.orders.parcel.handover.clear') }}"
                                style="margin-left: 4px; color: white;background-color:#000; padding: 5px 10px; display: none;">Clear</a>
                            <a href="{{ route('admin.orders.parcel.handover.print', ['range' => $range, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                class="{{ $orders->count() > 0 ? 'd-block' : 'd-none' }}"
                                style="color: white;background-color:red; padding: 5px 10px;">Print</a>

                            <div class="d-flex align-items-center flex-wrap flex-md-nowrap" style="gap: 6px;">
                                <label for="range" class="mb-0 small text-muted">Date Range</label>
                                <select name="range" id="range" class="form-control w-auto" style="min-width: 140px;">
                                    <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="yesterday" {{ $range === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                    <option value="last_3_days" {{ $range === 'last_3_days' ? 'selected' : '' }}>Last 3 Days
                                    </option>
                                    <option value="this_month" {{ $range === 'this_month' ? 'selected' : '' }}>This Month
                                    </option>
                                    <option value="last_month" {{ $range === 'last_month' ? 'selected' : '' }}>Last Month
                                    </option>
                                    <option value="last_3_months" {{ $range === 'last_3_months' ? 'selected' : '' }}>Last 3
                                        Months</option>
                                    <option value="last_6_months" {{ $range === 'last_6_months' ? 'selected' : '' }}>Last 6
                                        Months</option>
                                    <option value="custom" {{ $range === 'custom' ? 'selected' : '' }}>Custom Range</option>
                                </select>

                                <div id="custom-range-wrapper"
                                    class="d-none align-items-center flex-wrap flex-md-nowrap {{ $range === 'custom' ? '' : 'd-none' }}"
                                    style="gap: 4px;">
                                    <input type="text" class="form-control datetimepicker w-auto" name="start_date"
                                        id="start_date" placeholder="Start Date"
                                        value="{{ old('start_date', \Carbon\Carbon::parse($startDate)->format('d-m-Y')) }}">
                                    <span class="mx-1">to</span>
                                    <input type="text" class="form-control datetimepicker w-auto" name="end_date"
                                        id="end_date" placeholder="End Date"
                                        value="{{ old('end_date', \Carbon\Carbon::parse($endDate)->format('d-m-Y')) }}">
                                </div>

                                <button type="submit" class="btn btn-sm btn-primary ml-2">Filter</button>
                            </div>
                        </form>
                        @if ($orders->count() > 0)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 1%">SL.</th>
                                                        <th>Invoice ID</th>
                                                        <th>Customer Info</th>
                                                        <th>COD</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php($i = 1)
                                                    @foreach ($orders as $item)
                                                        <tr id="tr_{{ $item->id }}" class="">
                                                            <td>{{ $i++ }}</td>
                                                            <td>
                                                                {{ $item->invoice_no }}

                                                            </td>
                                                            <td>
                                                                <span> <strong>Name</strong>
                                                                    {{ $item->customer_name }}</span> <br>
                                                                <span> <strong>Phone</strong>
                                                                    {{ $item->customer_phone }}</span> <br>
                                                                <span> <strong>Address</strong>
                                                                    {{ $item->customer_address }}</span>
                                                            </td>
                                                            <td>
                                                                {{ $item->total }}<br>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="3" class="text-right">
                                                            @choice(':count order|:count orders', $orders->count())
                                                        </th>
                                                        <th>{{ $orders->sum('total') }}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
        });

        function toggleDateRangeFields() {
            var range = $('#range').val();
            $('#custom-range-wrapper')
                .toggleClass('d-none', range !== 'custom')
                .toggleClass('d-flex', range === 'custom');
        }

        $(function() {
            toggleDateRangeFields();

            $('#range').on('change', function() {
                toggleDateRangeFields();
            });
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
@endsection

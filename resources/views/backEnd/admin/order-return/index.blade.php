@extends('backEnd.admin.layouts.master')

@section('title')
    Order Return
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
                            <h2 class="pageheader-title">Return Received Orders</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{ Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : '')) }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Return Receive Orders</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <form action="" method="get" class="d-flex justify-center align-items-center flex-wrap">
                            <input type="text" class="form-control w-auto" name="invoice_id" autofocus
                                placeholder="Invoice ID" value="{{ old('invoice_id', request('invoice_id')) }}">
                            <a href="{{ route('admin.orders.return.receive.clear') }}"
                                style="margin-left: 4px; color: white;background-color:#000; padding: 5px 10px; display:none;">Clear</a>
                            <a href="{{ route('admin.orders.return.receive.print', ['date' => $selectedDate]) }}"
                                class="{{ $orders->count() > 0 ? 'd-block' : 'd-none' }}"
                                style="margin-left: 4px; color: white;background-color:red; padding: 5px 10px;">Print</a>
                            <input type="text" class="form-control datetimepicker w-auto" name="date"
                                style="margin-left: 4px;" placeholder="Select Date"
                                value="{{ old('date', \Carbon\Carbon::parse($selectedDate)->format('d-m-Y')) }}">
                            <button type="submit" class="btn btn-sm btn-primary" style="margin-left: 4px;">Filter</button>
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
                                                            <td>{{ $item->invoice_no }}</td>
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
                                                        <th colspan="3" class="text-right">Total Parcel</th>
                                                        <th>{{ $orders->count() }}</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3" class="text-right">Total Amount</th>
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
        });
    </script>
@endsection

@extends('backEnd.admin.layouts.master')

@section('title')
    Create Order
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('/') }}backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.css">
    <style>
        .redx {
            display: none;
        }

        .pathao {
            display: none;
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
                            <h2 class="pageheader-title">Create Order</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{ Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : '')) }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Create Order</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->

                <div class="row mb-2">
                    <div class="col-12">
                        <a href="{{ Auth::guard('admin')->check() ? route('admin.orders') : (Auth::guard('manager')->check() ? route('manager.orders') : (Auth::guard('employee')->check() ? route('employee.orders') : '')) }}"
                           class="btn btn-danger btn-sm">
                            <i class="fa fa-angle-double-left"></i>
                            Back
                        </a>
                    </div>
                </div>
                <form
                    action="{{ Auth::guard('admin')->check() ? route('admin.orders.store') : (Auth::guard('manager')->check() ? route('manager.orders.store') : (Auth::guard('employee')->check() ? route('employee.orders.store') : '')) }}"
                    method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="card">
                                <h4 class="card-header">Customer Info</h4>
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 col-12">
                                            <label for="order_date">Order Date <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control datetimepicker" id="order_date"
                                                   name="order_date" required>
                                        </div>

                                        <div class="form-group col-md-6 col-12">
                                            <label for="invoice_id">Invoice ID <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="invoice_id" name="invoice_id"
                                                   value="{{ $invoice_id }}" readonly required>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6 col-12">
                                            <label for="customer_name">Customer Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_name"
                                                   name="customer_name" required>
                                        </div>

                                        <div class="form-group col-md-6 col-12">
                                            <label for="customer_phone">Customer Phone <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_phone"
                                                   name="customer_phone" required>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <label for="customer_address">Customer Address <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="customer_address" id="customer_address" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <label for="courier_id">Courier Name</label>
                                            <select name="courier_id" id="courier_id" class="form-control select2">
                                                <option value="">Select A Courier</option>
                                                @foreach ($courier as $key => $item)
                                                    <option value="{{ $key }}">{{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="pathao">
                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label>City Name <span class="text-danger">*</span></label>
                                                <select name="courier_city_id" class="form-control select2 city_id" required disabled>
                                                    <option value="">Select A City</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label>Zone Name <span class="text-danger">*</span></label>
                                                <select name="courier_zone_id" class="form-control select2 zone_id" required disabled>
                                                    <option value="">Select A Zone</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="redx">
                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label>Division > District > Area Name</label>
                                                <select name="courier_city_id" class="form-control select2 city_id">
                                                    <option value="">Select Division > District > Area Name</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6 col-12">
                                            <label for="status">Status <span class="text-danger">*</span></label>
                                            @if (Auth::guard('admin')->check())
                                                <select name="status" id="status" class="form-control" required>
                                                    <option value="">Select Status</option>
                                                    <option value="2">
                                                        Processing
                                                    </option>
                                                    <option value="9">No
                                                        Response
                                                    </option>
                                                    <option value="0">Hold
                                                    </option>
                                                    <option value="3">
                                                        Pending
                                                        Payment
                                                    </option>
                                                    <option value="4">
                                                        Cancelled
                                                    </option>
                                                    <option value="13">
                                                        Confirmed
                                                    </option>
                                                    <option value="5">
                                                        Pending
                                                        Invoice
                                                    </option>
                                                    <option value="10">
                                                        Invoiced
                                                    </option>
                                                    <option value="14">
                                                        Stock
                                                        Out
                                                    </option>
                                                    <option value="8">
                                                        Courier
                                                    </option>
                                                    <option value="6">On
                                                        Delivery
                                                    </option>
                                                    <option value="1">
                                                        Delivered
                                                    </option>
                                                    <option value="15">
                                                        Partial Delivery
                                                    </option>
                                                    <option value="7">
                                                        Pending Return
                                                    </option>
                                                    <option value="11">
                                                        Return
                                                    </option>
                                                    <option value="16">
                                                        Lost
                                                    </option>
                                                </select>
                                            @elseif(Auth::guard('manager')->check())
                                                <select name="status" id="status" class="form-control" required>
                                                    <option value="2" selected>
                                                        Processing
                                                    </option>
                                                    <option value="9">No
                                                        Response
                                                    </option>
                                                    <option value="0">Hold
                                                    </option>
                                                    <option value="3">
                                                        Pending
                                                        Payment
                                                    </option>
                                                    <option value="4">
                                                        Cancelled
                                                    </option>
                                                    <option value="13">
                                                        Confirmed
                                                    </option>
                                                    <option value="5">
                                                        Pending
                                                        Invoice
                                                    </option>
                                                    <option value="10">
                                                        Invoiced
                                                    </option>
                                                    <option value="14">
                                                        Stock
                                                        Out
                                                    </option>
                                                    <option value="8">
                                                        Courier
                                                    </option>
                                                    <option value="6">On
                                                        Delivery
                                                    </option>
                                                    <option value="1">
                                                        Delivered
                                                    </option>
                                                    <option value="15">
                                                        Partial Delivery
                                                    </option>
                                                    <option value="7">
                                                        Pending Return
                                                    </option>
                                                    <option value="11">
                                                        Return
                                                    </option>
                                                    <option value="16">
                                                        Lost
                                                    </option>
                                                </select>
                                            @elseif(Auth::guard('employee')->check())
                                                <select name="status" id="status" class="form-control" required>
                                                    <option value="2" selected>
                                                        Processing
                                                    </option>
                                                    <option value="9">No
                                                        Response
                                                    </option>
                                                    <option value="0">Hold
                                                    </option>
                                                    <option value="3">
                                                        Pending
                                                        Payment
                                                    </option>
                                                    <option value="4">
                                                        Cancelled
                                                    </option>
                                                    <option value="13">
                                                        Confirmed
                                                    </option>
                                                    <option value="5">
                                                        Pending
                                                        Invoice
                                                    </option>
                                                    <option value="10">
                                                        Invoiced
                                                    </option>
                                                    <option value="14">
                                                        Stock
                                                        Out
                                                    </option>
                                                    <option value="8">
                                                        Courier
                                                    </option>
                                                    <option value="6">On
                                                        Delivery
                                                    </option>
                                                    <option value="1">
                                                        Delivered
                                                    </option>
                                                    <option value="15">
                                                        Partial Delivery
                                                    </option>
                                                    <option value="7">
                                                        Pending Return
                                                    </option>
                                                    <option value="11">
                                                        Return
                                                    </option>
                                                    <option value="16">
                                                        Lost
                                                    </option>
                                                </select>
                                            @endif
                                        </div>

                                        <div class="form-group col-md-6 col-12">
                                            <label for="payment_status">Payment Status <span class="text-danger">*</span></label>
                                            <select name="payment_status" id="payment_status" class="form-control"
                                                    required>
                                                <option value="">Select Status</option>
                                                <option value="0" selected>Unpaid</option>
                                                <option value="1">Partial</option>
                                                <option value="2">Paid</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-6 mb-0">
                                            <select name="source" id="source" class="form-control" required>
                                                <option value="">Select A Source <span class="text-danger">*</span></option>
                                                <option value="call">Call</option>
                                                <option value="page">Page</option>
                                                <option value="whatsapp">Whatsapp</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-6  mb-0  ">
                                            <select name="shipping_area" id="shipping_area" class="form-control" required
                                            >
                                                <option value="">Select an Area <span class="text-danger">*</span></option>
                                                <option value="1">Inside Dhaka</option>
                                                <option value="2">Outside Dhaka</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="card">
                                <h4 class="card-header">Product Info</h4>
                                <div class="card-body">
                                    <div class="table-responsive mb-3">
                                        <table class="table table-bordered text-center">
                                            <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>SKU</th>
                                                <th>Product Name</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="prod_row">
                                            </tbody>
                                            <tbody>
                                            <tr>
                                                <td colspan="6">
                                                    <div class="form-row">
                                                        <div class="form-group col-12 text-left">
                                                            <select id="product" class="form-control select2"
                                                                    required>
                                                                <option value="">Select A Product</option>
                                                                @foreach ($products as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="form-group row" style="padding: 6px 0;">

                                        <div class="form-group col-6 mb-0">
                                            <input type="text" class="form-control" id="memo_number"
                                                   name="memo_number" placeholder="Memo Number">
                                        </div>

                                        <label for="sub_total" class="col-md-2 col-form-label text-right">Sub
                                            Total</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="sub_total" name="sub_total"
                                                   min="0" value="0" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row" style="padding: 6px 0;">
                                        <label for="shipping_cost"
                                               class="offset-md-6 col-md-2 col-form-label text-right">Delivery</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="shipping_cost" min="0"
                                                   name="shipping_cost" value="0">
                                        </div>
                                    </div>

                                    <div class="form-group row" style="padding: 6px 0;">
                                        <label for="discount"
                                               class="offset-md-6 col-md-2 col-form-label text-right">Discount</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="discount" min="0"
                                                   name="discount" value="0">
                                        </div>
                                    </div>

                                    <div class="form-group row" style="padding: 6px 0;">
                                        <label for="total"
                                               class="offset-md-6 col-md-2 col-form-label text-right">Total</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="total" min="0"
                                                   name="total" value="0" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row" style="padding: 6px 0;">
                                        <label for="paid"
                                               class="offset-md-6 col-md-2 col-form-label text-right">Paid</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="paid" min="0"
                                                   name="paid" value="0">
                                        </div>
                                    </div>

                                    <div class="form-group row" style="padding: 6px 0;">
                                        <label for="due"
                                               class="offset-md-6 col-md-2 col-form-label text-right">Due</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="due" min="0"
                                                   name="due" value="0" readonly>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <textarea name="courier_note" id="courier_note" class="form-control" placeholder="Courier Note"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <textarea name="staff_note" id="staff_note" class="form-control" placeholder="Staff Note"></textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12 text-center">
                                            <input type="submit" value="Save" class="btn btn-success w-100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('/') }}backEnd/assets/vendor/datetimepicker/moment.min.js"></script>
    <script src="{{ asset('/') }}backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.datetimepicker').datetimepicker({
                icons: {
                    next: 'fa fa-angle-right',
                    previous: 'fa fa-angle-left'
                },
                format: 'DD-MM-YYYY',
                defaultDate: new Date(),
            });

            $('.select2').select2();

            $('#product').on('change', function () {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ Auth::guard('admin')->check() ? route('admin.ajax.get.products') : (Auth::guard('manager')->check() ? route('manager.ajax.get.products') : (Auth::guard('employee')->check() ? route('employee.ajax.get.products') : '')) }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        id: $(this).val()
                    },
                    success: function (data) {
                        $('#prod_row').append(data);
                        finalCalc();
                    }
                });
            });

            function pathao_input_off() {
                $('.pathao').find('.city_id').prop('disabled', true).prop('required', false);
                $('.pathao').find('.zone_id').prop('disabled', true).prop('required', false);
            }

            function pathao_input_on() {
                $('.pathao').find('.city_id').prop('disabled', false).prop('required', true);
                $('.pathao').find('.zone_id').prop('disabled', false).prop('required', true);
            }

            function redx_input_off() {
                $('.redx').find('.city_id').prop('disabled', true);
            }

            function redx_input_on() {
                $('.redx').find('.city_id').prop('disabled', false);
            }


            $("#courier_id").on('change', function () {
                $(".city_id").empty();
                $(".city_id").append('<option>Loading...</option>');
                $(".zone_id").empty();
                $(".zone_id").append('<option value="">Select A Zone</option>');
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                if ($(this).val() == 1) {
                    $('.pathao').css('display', 'block');
                    $('.redx').css('display', 'none');
                    pathao_input_on();
                    redx_input_off();
                    $.ajax({
                        url: '{{ Auth::guard('admin')->check() ? route('admin.courier.pataho.ajax.get.cities') : (Auth::guard('manager')->check() ? route('manager.courier.pataho.ajax.get.cities') : (Auth::guard('employee')->check() ? route('employee.courier.pataho.ajax.get.cities') : '')) }}',
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN,
                            id: $(this).val()
                        },
                        success: function (data) {
                            $(".city_id").empty();
                            $(".city_id").append('<option value="">Select A City</option>');
                            $.each(data, function (index, value) {
                                $(".pathao .city_id").append(new Option(value, index));
                            });


                            $.ajax({
                                url: '{{ route('pathao.address.parser') }}',
                                type: 'POST',
                                data: {
                                    _token: CSRF_TOKEN,
                                    address: $('#customer_address').val()
                                },
                                success: function (data) {
                                    $(".city_id option").filter(function () {
                                        return $.trim($(this).text()).toLowerCase() === $.trim(data.data.district_name).toLowerCase();
                                    }).attr('selected', true).trigger('change');
                                }
                            });
                        }
                    });
                } else if ($(this).val() == 2) {
                    $('.pathao').css('display', 'none');
                    $('.redx').css('display', 'block');
                    pathao_input_off();
                    redx_input_on();
                    $.ajax({
                        url: '{{ Auth::guard('admin')->check() ? route('admin.courier.redx.ajax.get.cities') : (Auth::guard('manager')->check() ? route('manager.courier.redx.ajax.get.cities') : (Auth::guard('employee')->check() ? route('employee.courier.redx.ajax.get.cities') : '')) }}',
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN,
                            id: $(this).val()
                        },
                        success: function (data) {
                            $(".city_id").empty();
                            $(".city_id").append('<option value="">Select A City</option>');
                            $.each(data, function (index, value) {
                                $(".redx .city_id").append(new Option(value, index));
                            });

                        }
                    });
                } else if ($(this).val() == 4) {
                    $('.pathao').css('display', 'block');
                    $('.redx').css('display', 'none');
                    pathao_input_on();
                    redx_input_off();
                    $.ajax({
                        url: '{{ Auth::guard('admin')->check() ? route('admin.courier.carrybee.ajax.get.cities') : (Auth::guard('manager')->check() ? route('manager.courier.carrybee.ajax.get.cities') : (Auth::guard('employee')->check() ? route('employee.courier.carrybee.ajax.get.cities') : '')) }}',
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN,
                            id: $(this).val()
                        },
                        success: function (data) {
                            $(".city_id").empty();
                            $(".city_id").append('<option value="">Select A City</option>');
                            $.each(data, function (index, value) {
                                $(".pathao .city_id").append(new Option(value, index));
                            });


                            /*$.ajax({
                                url: '{{ route('pathao.address.parser') }}',
                                type: 'POST',
                                data: {
                                    _token: CSRF_TOKEN,
                                    address: $('#customer_address').val()
                                },
                                success: function (data) {
                                    $(".city_id option").filter(function () {
                                        return $.trim($(this).text()).toLowerCase() === $.trim(data.data.district_name).toLowerCase();
                                    }).attr('selected', true).trigger('change');
                                }
                            });*/
                        }
                    });
                } else {
                    $('.pathao').css('display', 'block');
                    $('.redx').css('display', 'none');
                    pathao_input_on();
                    redx_input_off();
                    $.ajax({
                        url: '{{ Auth::guard('admin')->check() ? route('admin.courier.ajax.get.cities') : (Auth::guard('manager')->check() ? route('manager.courier.ajax.get.cities') : (Auth::guard('employee')->check() ? route('employee.courier.ajax.get.cities') : '')) }}',
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN,
                            id: $(this).val()
                        },
                        success: function (data) {
                            $(".city_id").empty();
                            $(".city_id").append('<option value="">Select A City</option>');
                            $.each(data, function (index, value) {
                                $(".city_id").append(new Option(value, index));
                            });

                        }
                    });
                }
            });


            $(".city_id").on('change', function () {
                $(".zone_id").empty();
                $(".zone_id").append('<option>Loading...</option>');
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                if ($('#courier_id').val() == 1) {
                    $.ajax({
                        url: '{{ Auth::guard('admin')->check() ? route('admin.courier.pataho.ajax.get.zones') : (Auth::guard('manager')->check() ? route('manager.courier.pataho.ajax.get.zones') : (Auth::guard('employee')->check() ? route('employee.courier.pataho.ajax.get.zones') : '')) }}',
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN,
                            id: $(this).val()
                        },
                        success: function (data) {
                            $(".zone_id").empty();
                            $(".zone_id").append('<option value="">Select A Zone</option>');
                            $.each(data, function (index, value) {
                                $(".zone_id").append(new Option(value, index));
                            });

                            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                url: '{{ route('pathao.address.parser') }}',
                                type: 'POST',
                                data: {
                                    _token: CSRF_TOKEN,
                                    address: $('#customer_address').val()
                                },
                                success: function (data) {
                                    $(".zone_id option").filter(function () {
                                        return $.trim($(this).text()).toLowerCase() === $.trim(data.data.zone_name).toLowerCase();
                                    }).attr('selected', true).trigger('change');
                                }
                            });

                        }
                    });
                }else if ($('#courier_id').val() == 4) {
                    $.ajax({
                        url: '{{ Auth::guard('admin')->check() ? route('admin.courier.carrybee.ajax.get.zones') : (Auth::guard('manager')->check() ? route('manager.courier.carrybee.ajax.get.zones') : (Auth::guard('employee')->check() ? route('employee.courier.carrybee.ajax.get.zones') : '')) }}',
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN,
                            id: $(this).val()
                        },
                        success: function (data) {
                            $(".zone_id").empty();
                            $(".zone_id").append('<option value="">Select A Zone</option>');
                            $.each(data, function (index, value) {
                                $(".zone_id").append(new Option(value, index));
                            });

                            /*var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                url: '{{ route('pathao.address.parser') }}',
                                type: 'POST',
                                data: {
                                    _token: CSRF_TOKEN,
                                    address: $('#customer_address').val()
                                },
                                success: function (data) {
                                    $(".zone_id option").filter(function () {
                                        return $.trim($(this).text()).toLowerCase() === $.trim(data.data.zone_name).toLowerCase();
                                    }).attr('selected', true).trigger('change');
                                }
                            });*/

                        }
                    });
                } else {
                    $.ajax({
                        url: '{{ Auth::guard('admin')->check() ? route('admin.courier.ajax.get.zones') : (Auth::guard('manager')->check() ? route('manager.courier.ajax.get.zones') : (Auth::guard('employee')->check() ? route('employee.courier.ajax.get.zones') : '')) }}',
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN,
                            id: $(this).val()
                        },
                        success: function (data) {
                            $(".zone_id").empty();
                            $(".zone_id").append('<option value="">Select A Zone</option>');
                            $.each(data, function (index, value) {
                                $(".zone_id").append(new Option(value, index));
                            });

                        }
                    });
                }

            });
        });
    </script>

    <script>
        function calcSubTotal() {
            var result = 0;
            $('#prod_row tr').each(function () {
                $('.total_price', this).each(function (index, val) {
                    result += parseInt($(val).text());
                });
            });

            $('#sub_total').val(result);
        }

        function pathao_address_parser(address) {
        // alert(address);
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{ route('pathao.address.parser') }}',
                type: 'POST',
                data: {
                    _token: CSRF_TOKEN,
                    address: address
                },
                success: function (data) {
                    $(".city_id option").filter(function () {
                        return $.trim($(this).text()).toLowerCase() === $.trim(data.data.district_name).toLowerCase();
                    }).attr('selected', true).trigger('change');

                    $(".zone_id option").filter(function () {
                        return $.trim($(this).text()).toLowerCase() === $.trim(data.data.zone_name).toLowerCase();
                    }).attr('selected', true).trigger('change');
                }
            });
        }

        function finalCalc() {
            calcSubTotal();
            var shipping_cost = parseFloat($('#shipping_cost').val());
            var discount = parseFloat($('#discount').val());
            var sub_total = parseFloat($('#sub_total').val());
            var paid = parseFloat($('#paid').val());
            var total = parseFloat((sub_total + shipping_cost) - discount);
            var due = total - paid;
            $('#due').val(due);
            $('#total').val(total);
        }


        $(document).on('click', '.remove_btn', function () {
            $(this).closest("tr").remove();
            finalCalc();
        });

        $(document).on('keyup change', '.qty', function () {
            var total_price = parseFloat($(this).next().val()) * parseInt($(this).val());
            $(this).parent().next().text(total_price);
            finalCalc();
        });

        $(document).on('keyup', '#shipping_cost,#discount,#paid', function () {
            finalCalc();
        });

        $(document).on('blur', '#customer_address', function () {
            if ($("#courier_id").val() == 1) {
                pathao_address_parser($(this).val())
            }
        });
        // $(document).on('change', '#shipping_area', function() {
        //     $.ajax({
        //         url: '{{ Auth::guard('admin')->check() ? route('admin.ajax.get.shipping') : (Auth::guard('manager')->check() ? route('manager.ajax.get.shipping') : (Auth::guard('employee')->check() ? route('employee.ajax.get.shipping') : '')) }}',
        //         type: 'POST',
        //         data: {
        //             _token: '{{ csrf_token() }}',
        //             id: $(this).val()
        //         },
        //         success: function(data) {
        //             // console.log(data.amount);
        //             $('#shipping_cost').val(data.amount);
        //             finalCalc();
        //         }
        //     });

        // });
    </script>
@endsection

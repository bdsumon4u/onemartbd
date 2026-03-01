@extends('backEnd.admin.layouts.master')

@section('title')
    Edit Order
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('/') }}backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.css">

    <style>
        .pathao {
            display: none;
        }

        .redx {
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
                            <h2 class="pageheader-title">Edit Order</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{ Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : '')) }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Edit Order</li>
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
                    action="{{ Auth::guard('admin')->check() ? route('admin.orders.update', $data->id) : (Auth::guard('manager')->check() ? route('manager.orders.update', $data->id) : (Auth::guard('employee')->check() ? route('employee.orders.update', $data->id) : '')) }}"
                    method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="card mb-3">
                                <h4 class="card-header">Customer Info</h4>
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 col-12">
                                            <label for="order_date">Order Date <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control datetimepicker" id="order_date"
                                                name="order_date"
                                                value="{{ date('d-m-Y', strtotime($data->order_date)) ?? null }}" required
                                                readonly>
                                        </div>

                                        <div class="form-group col-md-6 col-12">
                                            <label for="invoice_id">Invoice ID <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="invoice_id" name="invoice_id"
                                                value="{{ $data->invoice_id ?? null }}" readonly required>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6 col-12">
                                            <label for="customer_name">Customer Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_name"
                                                name="customer_name" value="{{ $data->customer_name ?? null }}" required>
                                        </div>

                                        <div class="form-group col-md-6 col-12">
                                            <label for="customer_phone">Customer Phone <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_phone"
                                                name="customer_phone" value="{{ $data->customer_phone ?? null }}" required>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <label for="customer_address">Customer Address <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="customer_address" id="customer_address" class="form-control" required>{{ $data->customer_address ?? null }}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <label for="courier_id">Courier Name</label>
                                            <select name="courier_id" id="courier_id" class="form-control select2">
                                                <option value="">Select A Courier</option>
                                                @foreach ($courier as $key => $item)
                                                    <option value="{{ $key }}"
                                                        {{ $data->courier_id == $key ? 'selected' : '' }}>
                                                        {{ $item }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="pathao" @if ($data->courier_id == 1 || $data->courier_id == 4) style="display: block" @endif>
                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label>City Name <span class="text-danger">*</span></label>
                                                <select name="courier_city_id" class="form-control select2 city_id"
                                                    @if ($data->courier_id == 1 || $data->courier_id == 4) required @endif
                                                    @if ($data->courier_id != 1 && $data->courier_id != 4) disabled @endif>
                                                    <option value="">Select A City</option>
                                                    @foreach ($courier_city as $key => $city)
                                                        <option value="{{ $key }}"
                                                            {{ $data->courier_city_id == $key ? 'selected' : '' }}>
                                                            {{ $city }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label>Zone Name <span class="text-danger">*</span></label>
                                                <select name="courier_zone_id" class="form-control select2 zone_id"
                                                    @if ($data->courier_id == 1 || $data->courier_id == 4) required @endif
                                                    @if ($data->courier_id != 1 && $data->courier_id != 4) disabled @endif>
                                                    <option value="">Select A Zone</option>
                                                    @foreach ($courier_zone as $key => $zone)
                                                        <option value="{{ $key }}"
                                                            {{ $data->courier_zone_id == $key ? 'selected' : '' }}>
                                                            {{ $zone }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="redx" @if ($data->courier_id == 2) style="display: block" @endif>
                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label>Division > District > Area Name</label>
                                                <select name="courier_city_id" class="form-control select2 city_id"
                                                    @if ($data->courier_id != 2) disabled @endif>
                                                    <option value="">Select Division > District > Area Name</option>
                                                    @foreach ($courier_city as $key => $city)
                                                        <option value="{{ $key }}"
                                                            {{ $data->courier_city_id == $key ? 'selected' : '' }}>
                                                            {{ $city }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6 col-12">
                                            <input type="hidden" name="old_status" value="{{ $data->status }}">
                                            <label for="status">Status <span class="text-danger">*</span></label>
                                            @if (Auth::guard('admin')->check())
                                                <select name="status" id="status" class="form-control" required>
                                                    <option value="">Select Status</option>
                                                    <option value="2" {{ $data->status == 2 ? 'selected' : '' }}>
                                                        Processing
                                                    </option>
                                                    <option value="9" {{ $data->status == 9 ? 'selected' : '' }}>No
                                                        Response
                                                    </option>
                                                    <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Hold
                                                    </option>
                                                    <option value="3" {{ $data->status == 3 ? 'selected' : '' }}>
                                                        Pending
                                                        Payment
                                                    </option>
                                                    <option value="4" {{ $data->status == 4 ? 'selected' : '' }}>
                                                        Cancelled
                                                    </option>
                                                    <option value="13" {{ $data->status == 13 ? 'selected' : '' }}>
                                                        Confirmed
                                                    </option>
                                                    <option value="5" {{ $data->status == 5 ? 'selected' : '' }}>
                                                        Pending
                                                        Invoice
                                                    </option>
                                                    <option value="10" {{ $data->status == 10 ? 'selected' : '' }}>
                                                        Invoiced
                                                    </option>
                                                    <option value="14" {{ $data->status == 14 ? 'selected' : '' }}>
                                                        Stock
                                                        Out
                                                    </option>
                                                    <option value="8" {{ $data->status == 8 ? 'selected' : '' }}>
                                                        Courier
                                                    </option>
                                                    <option value="6" {{ $data->status == 6 ? 'selected' : '' }}>On
                                                        Delivery
                                                    </option>
                                                    <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>
                                                        Delivered
                                                    </option>
                                                    <option value="15" {{ $data->status == 15 ? 'selected' : '' }}>
                                                        Partial Delivery
                                                    </option>
                                                    <option value="7" {{ $data->status == 7 ? 'selected' : '' }}>
                                                        Pending Return
                                                    </option>
                                                    <option value="17" {{ $data->status == 17 ? 'selected' : '' }}>
                                                        Paid Return
                                                    </option>
                                                    <option value="18" {{ $data->status == 18 ? 'selected' : '' }}>
                                                        Exchange
                                                    </option>
                                                    <option value="11" {{ $data->status == 11 ? 'selected' : '' }}>
                                                        Return
                                                    </option>
                                                    <option value="16" {{ $data->status == 16 ? 'selected' : '' }}>
                                                        Lost
                                                    </option>
                                                </select>
                                            @elseif(Auth::guard('manager')->check())
                                                @if ($data->status == 5 && $data->status == 4)
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="">Select Status</option>
                                                        <option value="4" {{ $data->status == 4 ? 'selected' : '' }}>
                                                            Cancelled
                                                        </option>
                                                    </select>
                                                @elseif($data->status != 6 && $data->status != 1 && $data->status != 7)
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="">Select Status</option>
                                                        <option value="2" {{ $data->status == 2 ? 'selected' : '' }}>
                                                            Processing
                                                        </option>
                                                        <option value="9" {{ $data->status == 9 ? 'selected' : '' }}>
                                                            No
                                                            Response
                                                        </option>
                                                        <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>
                                                            Hold
                                                        </option>
                                                        <option value="3" {{ $data->status == 3 ? 'selected' : '' }}>
                                                            Pending
                                                            Payment
                                                        </option>
                                                        <option value="4" {{ $data->status == 4 ? 'selected' : '' }}>
                                                            Cancelled
                                                        </option>
                                                        <option value="13"
                                                            {{ $data->status == 13 ? 'selected' : '' }}>
                                                            Confirmed
                                                        </option>
                                                        <option value="5" {{ $data->status == 5 ? 'selected' : '' }}>
                                                            Pending
                                                            Invoice
                                                        </option>
                                                        <option value="10"
                                                            {{ $data->status == 10 ? 'selected' : '' }}>
                                                            Invoiced
                                                        </option>
                                                        <option value="14"
                                                            {{ $data->status == 14 ? 'selected' : '' }}>
                                                            Stock
                                                            Out
                                                        </option>

                                                        <option value="8" {{ $data->status == 8 ? 'selected' : '' }}>
                                                            Courier
                                                        </option>
                                                        <option value="15"
                                                            {{ $data->status == 15 ? 'selected' : '' }}>
                                                            Partial Delivery
                                                        </option>
                                                        <option value="11"
                                                            {{ $data->status == 11 ? 'selected' : '' }}>
                                                            Return
                                                        </option>
                                                        <option value="16"
                                                            {{ $data->status == 16 ? 'selected' : '' }}>
                                                            Lost
                                                        </option>
                                                    </select>
                                                @else
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="">Select Status</option>
                                                    </select>
                                                @endif
                                            @elseif(Auth::guard('employee')->check())
                                                @if ($data->status == 5 || $data->status == 4)
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="">Select Status</option>
                                                        <option value="4" {{ $data->status == 4 ? 'selected' : '' }}>
                                                            Cancelled
                                                        </option>
                                                        <option value="5" {{ $data->status == 5 ? 'selected' : '' }}>
                                                            Pending Delivery
                                                        </option>
                                                    </select>
                                                @elseif($data->status != 6 && $data->status != 1 && $data->status != 7)
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="">Select Status</option>
                                                        <option value="2" {{ $data->status == 2 ? 'selected' : '' }}>
                                                            Processing
                                                        </option>
                                                        <option value="9" {{ $data->status == 9 ? 'selected' : '' }}>
                                                            No
                                                            Response
                                                        </option>
                                                        <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>
                                                            Hold
                                                        </option>
                                                        <option value="3" {{ $data->status == 3 ? 'selected' : '' }}>
                                                            Pending
                                                            Payment
                                                        </option>
                                                        <option value="4" {{ $data->status == 4 ? 'selected' : '' }}>
                                                            Cancelled
                                                        </option>
                                                        <option value="13"
                                                            {{ $data->status == 13 ? 'selected' : '' }}>
                                                            Confirmed
                                                        </option>
                                                        <option value="5" {{ $data->status == 5 ? 'selected' : '' }}>
                                                            Pending
                                                            Invoice
                                                        </option>
                                                        <option value="10"
                                                            {{ $data->status == 10 ? 'selected' : '' }}>
                                                            Invoiced
                                                        </option>
                                                        <option value="14"
                                                            {{ $data->status == 14 ? 'selected' : '' }}>
                                                            Stock
                                                            Out
                                                        </option>
                                                        <option value="8" {{ $data->status == 8 ? 'selected' : '' }}>
                                                            Courier
                                                        </option>
                                                        <option value="15"
                                                            {{ $data->status == 15 ? 'selected' : '' }}>
                                                            Partial Delivery
                                                        </option>
                                                        <option value="11"
                                                            {{ $data->status == 11 ? 'selected' : '' }}>
                                                            Return
                                                        </option>
                                                        <option value="16"
                                                            {{ $data->status == 16 ? 'selected' : '' }}>
                                                            Lost
                                                        </option>
                                                    </select>
                                                @else
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="">Select Status</option>
                                                    </select>
                                                @endif
                                            @endif
                                        </div>

                                        <div class="form-group col-md-6 col-12">
                                            <label for="payment_status">Payment Status <span
                                                    class="text-danger">*</span></label>
                                            <select name="payment_status" id="payment_status" class="form-control"
                                                required>
                                                <option value="">Select Status</option>
                                                <option value="0" {{ $data->payment_status == 0 ? 'selected' : '' }}>
                                                    Unpaid
                                                </option>
                                                <option value="1" {{ $data->payment_status == 1 ? 'selected' : '' }}>
                                                    Partial
                                                </option>
                                                <option value="2" {{ $data->payment_status == 2 ? 'selected' : '' }}>
                                                    Paid
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group col-6 mb-0">
                                            <select name="source" id="source" class="form-control" required>
                                                @if ($data->source == 'direct')
                                                    <option value="direct" selected>Direct</option>
                                                @else
                                                    <option value="">Select A Source <span
                                                            class="text-danger">*</span></option>
                                                    <option value="call"
                                                        {{ $data->source == 'call' ? 'selected' : '' }}>
                                                        Call
                                                    </option>
                                                    <option value="page"
                                                        {{ $data->source == 'page' ? 'selected' : '' }}>
                                                        Page
                                                    </option>
                                                    <option value="whatsapp"
                                                        {{ $data->source == 'whatsapp' ? 'selected' : '' }}>Whatsapp
                                                    </option>
                                                    <option value="incomplete"
                                                        {{ $data->source == 'incomplete' ? 'selected' : '' }}>
                                                        Incomplete
                                                    </option>
                                                    @foreach ($utmSources ?? [] as $utmSource)
                                                        <option value="{{ e($utmSource) }}"
                                                            {{ $data->source === $utmSource ? 'selected' : '' }}>
                                                            {{ e(str($utmSource)->title()) }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group col-6  mb-0  ">
                                            <select name="shipping_method" id="shipping_method" class="form-control"
                                                required>
                                                <option value="">Select an Area <span class="text-danger">*</span>
                                                </option>
                                                <option value="2" @selected($data->shipping_method == 2)>Inside Dhaka</option>
                                                <option value="1" @selected($data->shipping_method == 1)>Outside Dhaka</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">
                                    SMS
                                </div>
                                <div class="card-body">
                                    <form id="sms_send_form">
                                        <input type="hidden" name="order_id" id="order_id"
                                            value="{{ $data->id }}">
                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label for="sms_body">SMS Body <span class="text-danger">*</span></label>
                                                <textarea name="sms_body" id="sms_body" class="form-control">{{ $web_settings->order_custom_sms ?? null }}</textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-info" id="send_sms_btn">Send
                                                    SMS
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @if (Auth::guard('admin')->check() || Auth::guard('manager')->check())
                                <div class="card">
                                    <div class="card-header">
                                        Transactions
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>SL.</th>
                                                    <th>Message</th>
                                                    <th>Date & Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $i = 1;
                                                @endphp
                                                @if (count($data->get_transactions) > 0)
                                                    @foreach ($data->get_transactions as $transaction)
                                                        <tr>
                                                            <td>{{ $i++ }}</td>
                                                            <td>
                                                                @if ($transaction->type == 'api')
                                                                    <span class="badge badge-warning">API</span>
                                                                @elseif($transaction->type == 'local')
                                                                    <span class="badge badge-success">Local</span>
                                                                @endif
                                                                {{ $transaction->text }}
                                                            </td>
                                                            <td>{{ date('d M, Y', strtotime($transaction->created_at)) }}<br>
                                                                <small>{{ date('h:i:s A', strtotime($transaction->created_at)) }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="2" class="text-danger text-center">No Transaction
                                                            Found!
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if (Auth::guard('admin')->check() || Auth::guard('manager')->check())
                                <div class="card  mt-3">
                                    <div class="card-header">
                                        Note Activity
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>Message</th>
                                                    <th>Updated By</th>
                                                    <th>Date & Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $i = 1;
                                                @endphp
                                                @if (count($data->get_note_history) > 0)
                                                    @foreach ($data->get_note_history as $note)
                                                        <tr>
                                                            <td> {{ $note->text }}</td>
                                                            <td>
                                                                @if ($note->user_type == 'admin')
                                                                    <span class="badge badge-warning">
                                                                        {{ $note->get_admin->name }}
                                                                    </span>
                                                                @elseif($note->user_type == 'manager')
                                                                    <span class="badge badge-success">
                                                                        {{ $note->get_manager->name }}
                                                                    </span>
                                                                @elseif($note->user_type == 'employee')
                                                                    <span class="badge badge-info">
                                                                        {{ $note->get_employee->name }}
                                                                    </span>
                                                                @endif

                                                            </td>
                                                            <td>{{ date('d M, Y', strtotime($note->created_at)) }}<br>
                                                                <small>{{ date('h:i:s A', strtotime($note->created_at)) }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="2" class="text-danger text-center">No Note
                                                            Found!
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
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
                                                @foreach ($data->get_products as $item)
                                                    <tr>
                                                        <input type="hidden" name="product_id[]" id="product_id"
                                                            value="{{ $item->product_id }}">
                                                        <td>
                                                            @if ($item->get_product->get_thumb)
                                                                <img src="{{ asset($item->get_product->get_thumb->file_url) }}"
                                                                    alt="Product Image" style="width: 34px">
                                                            @else
                                                                <img src="{{ asset('backEnd/assets/images/no_image.png') }}"
                                                                    alt="Product Image" style="width: 34px">
                                                            @endif

                                                        </td>

                                                        <td>{{ $item->get_product->sku }}</td>
                                                        <td class="text-left">
                                                            {{ $item->get_product->name }}
                                                            @if (count($item->get_product->get_attributes) > 0)
                                                                <br>
                                                                @foreach ($item->get_product->get_attributes as $key => $att)
                                                                    @if ($key != 0)
                                                                        <br>
                                                                    @endif
                                                                    <label
                                                                        for=""><b>{{ $att->get_attribute->title }}</b></label>
                                                                    <br>
                                                                    <input type="hidden"
                                                                        name="attribute_id[{{ $item->get_product->id }}][]"
                                                                        value="{{ $att->get_attribute->id }}">
                                                                    @foreach ($att->get_attribute_items as $key2 => $attr_item)
                                                                        <input type="radio"
                                                                            id="val_{{ $key }}{{ $key2 }}"
                                                                            name="attribute_item_id[{{ $item->get_product->id }}][{{ $att->get_attribute->id }}][]"
                                                                            value="{{ $attr_item->get_attribute_item->id }}"
                                                                            class="attr_checkbox" {{--                                                                           {{$item->attribute_ids?json_decode($item->attribute_ids, true)[$att->get_attribute->id]==$attr_item->get_attribute_item->id?'checked':"":($key2==0?'checked':"")}} required> --}}
                                                                            {{ $item->attribute_ids ? (json_decode($item->attribute_ids, true)[$att->get_attribute->id] == $attr_item->get_attribute_item->id ? 'checked' : '') : ($key2 == 0 ? 'checked' : '') }}
                                                                            required>
                                                                        <label
                                                                            for="val_{{ $key }}{{ $key2 }}">
                                                                            <span>{{ $attr_item->get_attribute_item->item_title }}</span>
                                                                        </label>
                                                                    @endforeach
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input style="width: 60px;border: 1px solid #ddd;"
                                                                min="1" type="number" class="form-control qty"
                                                                name="qty[]" id="qty2"
                                                                value="{{ $item->qty }}">
                                                            <input type="hidden" name="price[]" id="price"
                                                                class="price"
                                                                value="{{ $item->get_product->sale_price > 0 ? $item->get_product->sale_price : $item->get_product->price }}">
                                                        </td>
                                                        <td class="total_price">{{ $item->price * $item->qty }}</td>
                                                        <td><i class="fa fa-trash remove_btn text-danger"
                                                                style="cursor: pointer"></i></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tbody>
                                                <tr>
                                                    <td colspan="6">
                                                        <div class="form-row">
                                                            <div class="form-group col-12 text-left">
                                                                <select id="product" class="form-control select2">
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

                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label for="memo_number">Memo Number</label>
                                                <input type="text" class="form-control" id="memo_number"
                                                    name="memo_number" value="{{ $data->memo_number ?? null }}"
                                                    placeholder="Memo Number">
                                            </div>

                                            @foreach ($courier as $key => $item)
                                                <?php $suffix = $item == 'Redx' ? 'Tracking ID' : 'Consignment ID'; ?>
                                                <div class="form-group mb-2 d-none tracking-field"
                                                    data-courier-id="{{ $key }}" tracking-{{ $key }}>
                                                    <?php $label = str($item)
                                                        ->append(' '.$suffix)
                                                        ->lower()
                                                        ->replace(' ', '_')
                                                        ->toString(); ?>
                                                    <label for="{{ $label }}">{{ $item }}
                                                        {{ $suffix }}</label>
                                                    <input type="text" class="form-control" id="{{ $label }}"
                                                        name="{{ $label }}" value="{{ $data->$label ?? null }}"
                                                        placeholder="{{ $item }} {{ $suffix }}">
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group row">
                                                <label for="sub_total" class="col-md-4 col-form-label text-right">Sub
                                                    Total</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="sub_total"
                                                        name="sub_total" min="0"
                                                        value="{{ $data->sub_total ?? 0 }}" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row" style="padding: 6px 0;">
                                                <label for="shipping_cost"
                                                    class="col-md-4 col-form-label text-right">Delivery</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="shipping_cost"
                                                        min="0" name="shipping_cost"
                                                        value="{{ $data->shipping_cost ?? 0 }}">
                                                </div>
                                            </div>

                                            <div class="form-group row" style="padding: 6px 0;">
                                                <label for="discount"
                                                    class="col-md-4 col-form-label text-right">Discount</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="discount"
                                                        min="0" name="discount"
                                                        value="{{ $data->discount ?? 0 }}">
                                                </div>
                                            </div>

                                            <div class="form-group row" style="padding: 6px 0;">
                                                <label for="total"
                                                    class="col-md-4 col-form-label text-right">Total</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="total"
                                                        min="0" name="total" value="{{ $data->total ?? 0 }}"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row" style="padding: 6px 0;">
                                                <label for="paid"
                                                    class="col-md-4 col-form-label text-right">Paid</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="paid"
                                                        min="0" name="paid" value="{{ $data->paid ?? 0 }}">
                                                </div>
                                            </div>

                                            <div class="form-group row" style="padding: 6px 0;">
                                                <label for="due"
                                                    class="col-md-4 col-form-label text-right">Due</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="due"
                                                        min="0" name="due" value="{{ $data->due ?? 0 }}"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <textarea name="courier_note" id="courier_note" class="form-control" placeholder="Courier Note">{{ $data->courier_note }}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <textarea name="staff_note" id="staff_note" class="form-control" placeholder="Staff Note">{{ $data->staff_note }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12 text-center">
                                            <input type="submit" value="Update" class="btn btn-success w-100">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">Customer Old Orders</div>
                                <div class="card-body table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Invoice ID</th>
                                                <th>Products</th>
                                                <th>Total</th>
                                                <th>Courier</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Payment</th>
                                                <th>Assigned</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (Auth::guard('admin')->check() || Auth::guard('manager')->check())
                                                @foreach ($data->get_customer?->get_orders?->where('id', '!=', $data->id) ?? [] as $item)
<tr id="tr_{{ $item->id }}">
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
@endif
                                                        <br>
                                                        {{ $item->invoice_id }}
                                                        @if ($item->is_fake == 1)
<br>
                                                            <small class="badge badge-danger">Fake! <a
                                                                    href="{{ route('admin.fake.remove', $item->id) }}"
                                                                    onclick="return confirm('Are You Sure?')"><i
                                                                                class="fa fa-trash-alt text-white"></i></a></small>
     @endif
                                                    </td>
                                                    <td>
                                                    @foreach ($item->get_products as $product)
                                                    {{ $product->qty }} x <a target="_blank"
                                                    href="{{ $product->get_product ? route('single.product', [$product->get_product->slug, $product->get_product->id]) : '' }}">{{ $product->get_product ? $product->get_product->name : '' }}</a>
                                                    <br>
                                                    @if ($product->attributes)
                                                    @foreach (json_decode($product->attributes, true) as $key =>
                                                    $attr)
                                                    <span class="text-primary">{{ $key }}
                                                    -
                                                    {{ $attr }}</span>
                                                    <br>
                                                @endforeach
                                            @endif
                                            @endforeach

                                            </td>
                                            <td>
                                            {{ $item->total }}<br>
                                            <span
                                            class="text-success">P:-{{ $item->paid }}</span><br>
                                            <span class="text-danger">D:-{{ $item->due }}</span>
                                            </td>
                                            <td>
                                            {{ $item->get_courier->courier_name ?? '---' }}<br>
                                            @if ($item->pathao_consignment_id)
                                            <a
                                            href="https://merchant.pathao.com/tracking?consignment_id={{ $item->pathao_consignment_id }}&phone={{ $item->customer_phone }}"
                                            target="_blank"><i class="fa fa-eye"></i></a>
                                        @elseif($item->redx_tracking_id)
                                            <a
                                            href="https://redx.com.bd/track-parcel/?trackingId={{ $item->redx_tracking_id }}"
                                            target="_blank"><i class="fa fa-eye"></i></a>
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
                                                    $statusEnum = \App\Enums\OrderStatus::tryFrom($item->status);
                                                    $variant = $statusEnum?->variant() ?? 'secondary';
                                                @endphp
                                                <button type="button"
                                                    class="btn btn-{{ $variant }} status_btn  btn-sm">
                                                    {{ $statusEnum?->label() ?? 'Unknown' }}
                                                </button>

                                            </td>
                                            <td>
                                            <button type="button"
                                            class="btn {{ $item->payment_status == 0 ? 'btn-danger' : '' }} {{ $item->payment_status == 1 ? 'btn-info' : '' }} {{ $item->payment_status == 2 ? 'btn-success' : '' }} status_btn  btn-sm ">
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

                                            </td>

                                            <td>
                                            {{ $item->get_assigned ? $item->get_assigned->get_employee->name : '' }}
                                            </td>


                                            </tr>
                                            @endforeach
                                            @endif
                                            </tbody>

                                            </table>

                                            <div class="mt-3">
                                            {{-- {{ $orders->links() }} --}}
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
                                                $(document).ready(function() {
                                                    $('.datetimepicker').datetimepicker({
                                                        icons: {
                                                            next: 'fa fa-angle-right',
                                                            previous: 'fa fa-angle-left'
                                                        },
                                                        format: 'DD-MM-YYYY',
                                                        defaultDate: new Date(),
                                                    });

                                                    $('.select2').select2();

                                                    $('#product').on('change', function() {
                                                        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                                                        $.ajax({
                                                            url: '{{ Auth::guard('admin')->check() ? route('admin.ajax.get.products') : (Auth::guard('manager')->check() ? route('manager.ajax.get.products') : (Auth::guard('employee')->check() ? route('employee.ajax.get.products') : '')) }}',
                                                            type: 'POST',
                                                            data: {
                                                                _token: CSRF_TOKEN,
                                                                id: $(this).val()
                                                            },
                                                            success: function(data) {
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

                                                    function toggleTrackingField() {
                                                        const courierId = Number($('#courier_id').val());
                                                        $('.tracking-field').addClass('d-none');
                                                        if (!Number.isNaN(courierId)) {
                                                            $('.tracking-field[data-courier-id="' + courierId + '"]').removeClass('d-none');
                                                        }
                                                    }

                                                    $("#courier_id").on('change', function() {
                                                        toggleTrackingField();
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
                                                                success: function(data) {
                                                                    $(".city_id").empty();
                                                                    $(".city_id").append('<option value="">Select A City</option>');
                                                                    $.each(data, function(index, value) {
                                                                        $(".pathao .city_id").append(new Option(value, index));
                                                                    });

                                                                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                                                                    $.ajax({
                                                                        url: '{{ route('pathao.address.parser') }}',
                                                                        type: 'POST',
                                                                        data: {
                                                                            _token: CSRF_TOKEN,
                                                                            address: $('#customer_address').val()
                                                                        },
                                                                        success: function(data) {
                                                                            $(".city_id option").filter(function() {
                                                                                return $.trim($(this).text())
                                                                                    .toLowerCase() === $.trim(data
                                                                                        .data.district_name)
                                                                                    .toLowerCase();
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
                                                                success: function(data) {
                                                                    $(".city_id").empty();
                                                                    $(".city_id").append('<option value="">Select A City</option>');
                                                                    $.each(data, function(index, value) {
                                                                        $(".redx .city_id").append(new Option(value, index));
                                                                    });

                                                                }
                                                            });
                                                        } else if ($(this).val() == 3) {
                                                            $('.pathao').css('display', 'none');
                                                            $('.redx').css('display', 'none');
                                                            pathao_input_off();
                                                            redx_input_off();
                                                            $(".city_id").empty();
                                                            $(".city_id").append('<option value="">Select A City</option>');
                                                            $(".zone_id").empty();
                                                            $(".zone_id").append('<option value="">Select A Zone</option>');
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
                                                                success: function(data) {
                                                                    $(".city_id").empty();
                                                                    $(".city_id").append('<option value="">Select A City</option>');
                                                                    $.each(data, function(index, value) {
                                                                        $(".pathao .city_id").append(new Option(value, index));
                                                                    });
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
                                                                success: function(data) {
                                                                    $(".city_id").empty();
                                                                    $(".city_id").append('<option value="">Select A City</option>');
                                                                    $.each(data, function(index, value) {
                                                                        $(".city_id").append(new Option(value, index));
                                                                    });

                                                                }
                                                            });

                                                        }


                                                        {{-- var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content'); --}}
                                                        {{-- $.ajax({ --}}
                                                        {{--    url: '{{Auth::guard('admin')->check() ? route('admin.courier.ajax.get.c_charge') : (Auth::guard('manager')->check() ? route('manager.courier.ajax.get.c_charge') : (Auth::guard('employee')->check() ? route('employee.courier.ajax.get.c_charge') : ""))}}', --}}
                                                        {{--    type: 'POST', --}}
                                                        {{--    data: {_token: CSRF_TOKEN, id: $(this).val()}, --}}
                                                        {{--    success: function (data) { --}}
                                                        {{--        $('#shipping_cost').val(data); --}}
                                                        {{--        finalCalc(); --}}
                                                        {{--    } --}}
                                                        {{-- }); --}}

                                                    });

                                                    toggleTrackingField();


                                                    $(".city_id").on('change', function() {
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
                                                                success: function(data) {
                                                                    $(".zone_id").empty();
                                                                    $(".zone_id").append('<option value="">Select A Zone</option>');
                                                                    $.each(data, function(index, value) {
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
                                                                        success: function(data) {
                                                                            $(".zone_id option").filter(function() {
                                                                                return $.trim($(this).text())
                                                                                    .toLowerCase() === $.trim(data
                                                                                        .data.zone_name)
                                                                                    .toLowerCase();
                                                                            }).attr('selected', true).trigger('change');
                                                                        }
                                                                    });
                                                                }
                                                            });
                                                        } else if ($('#courier_id').val() == 4) {
                                                            $.ajax({
                                                                url: '{{ Auth::guard('admin')->check() ? route('admin.courier.carrybee.ajax.get.zones') : (Auth::guard('manager')->check() ? route('manager.courier.carrybee.ajax.get.zones') : (Auth::guard('employee')->check() ? route('employee.courier.carrybee.ajax.get.zones') : '')) }}',
                                                                type: 'POST',
                                                                data: {
                                                                    _token: CSRF_TOKEN,
                                                                    id: $(this).val()
                                                                },
                                                                success: function(data) {
                                                                    $(".zone_id").empty();
                                                                    $(".zone_id").append('<option value="">Select A Zone</option>');
                                                                    $.each(data, function(index, value) {
                                                                        $(".zone_id").append(new Option(value, index));
                                                                    });

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
                                                                success: function(data) {
                                                                    $(".zone_id").empty();
                                                                    $(".zone_id").append('<option value="">Select A Zone</option>');
                                                                    $.each(data, function(index, value) {
                                                                        $(".zone_id").append(new Option(value, index));
                                                                    });

                                                                }
                                                            });
                                                        }

                                                    });
                                                });
                                            </script>
                                            <script>
                                                function pathao_address_parser(address) {
                                                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                                                    $.ajax({
                                                        url: '{{ route('pathao.address.parser') }}',
                                                        type: 'POST',
                                                        data: {
                                                            _token: CSRF_TOKEN,
                                                            address: address
                                                        },
                                                        success: function(data) {
                                                            $(".city_id option").filter(function() {
                                                                return $.trim($(this).text()).toLowerCase() === $.trim(data.data.district_name)
                                                                    .toLowerCase();
                                                            }).attr('selected', true).trigger('change');

                                                            $(".zone_id option").filter(function() {
                                                                return $.trim($(this).text()).toLowerCase() === $.trim(data.data.zone_name)
                                                                    .toLowerCase();
                                                            }).attr('selected', true).trigger('change');
                                                        }
                                                    });
                                                }

                                                function calcSubTotal() {
                                                    var result = 0;
                                                    $('#prod_row tr').each(function() {
                                                        $('.total_price', this).each(function(index, val) {
                                                            result += parseInt($(val).text());
                                                        });
                                                    });

                                                    $('#sub_total').val(result);
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


                                                $(document).on('click', '.remove_btn', function() {
                                                    $(this).closest("tr").remove();
                                                    finalCalc();
                                                });

                                                $(document).on('keyup change', '.qty', function() {
                                                    var total_price = parseFloat($(this).next().val()) * parseInt($(this).val());
                                                    $(this).parent().next().text(total_price);
                                                    finalCalc();
                                                });

                                                $(document).on('keyup', '#shipping_cost,#discount,#paid', function() {
                                                    finalCalc();
                                                });

                                                $(document).on('blur', '#customer_address', function() {
                                                    if ($("#courier_id").val() == 1) {
                                                        pathao_address_parser($(this).val())
                                                    }
                                                });

                                                $(document).on('click', '#send_sms_btn', function() {
                                                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                                                    $.ajax({
                                                        url: '{{ Auth::guard('admin')->check() ? route('admin.send.sms') : (Auth::guard('manager')->check() ? route('manager.send.sms') : (Auth::guard('employee')->check() ? route('employee.send.sms') : '')) }}',
                                                        type: 'POST',
                                                        data: {
                                                            _token: CSRF_TOKEN,
                                                            order_id: $('#order_id').val(),
                                                            customer_phone: $('#customer_phone').val(),
                                                            sms_body: $('#sms_body').val()
                                                        },
                                                        success: function(data) {
                                                            if (data.success) {
                                                                toastr.options = {
                                                                    "positionClass": "toast-bottom-right"
                                                                };
                                                                toastr.success(data.success);
                                                            } else if (data.error) {
                                                                toastr.options = {
                                                                    "positionClass": "toast-bottom-right"
                                                                };
                                                                toastr.error(data.error);
                                                            } else {
                                                                toastr.options = {
                                                                    "positionClass": "toast-bottom-right"
                                                                };
                                                                toastr.warning('Something Went Wrong!');
                                                            }
                                                        }
                                                    });
                                                });
                                            </script>
                                        @endsection


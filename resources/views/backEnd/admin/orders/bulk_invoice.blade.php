<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    <style>
        @media print {
            body {
                zoom: 75%;
            }

            .pagebreak {
                clear: both;
                page-break-after: always;
            }
        }

        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 0;
        }

        .main-body {
            min-height: 380px;
            height: 380px;
        }

        .header {
            min-height: 160px;
            border: 1px solid black;
            margin-bottom: 15px;
        }

        .header img {
            max-width: 200px;
        }

        .left-header {
            min-height: 160px;
            width: 30%;
            float: left;
            border-right: 1px solid black;
        }

        .left-header-inner {
            padding: 15px;
            height: 130px;
        }

        .middle-header-inner {
            padding: 15px;
        }

        .right-header-inner {
            padding: 15px;
        }

        .middle-header {
            min-height: 160px;
            border-right: 1px solid black;
            width: 35%;
            float: left;
        }

        .right-header {
            min-height: 160px;
            width: 34%;
            float: left;
            text-align: left;
        }

        .right-header h2 {
            margin: 0;
            font-size: 40px;
        }

        .info {
            height: 130px;
        }

        .customer_info {
            font-size: 14px;
        }

        .customer_info p {
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .owner_info {
            border: 1px solid black;
            width: 300px;
            height: 112px;
            float: right;
            font-size: 14px;
            padding: 5px 10px;
        }

        .owner_info p {
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .owner_info img {
            width: 200px;
        }

        .left_div {
            float: left;
            width: 28%;
        }

        .right_div {
            float: left;
            width: 72%;
        }

        .left_div2 {
            float: left;
            width: 86px;
        }

        .right_div2 {
            float: left;
        }

        .product_table {
        }

        .product_table table {
            border: 1px solid black;
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .product_table table thead {
        }

        .product_table table thead tr {
            border-bottom: 1px solid black;
            height: 35px;
        }

        .product_table table thead tr th {
            border-right: 1px solid black;
        }

        .product_table table tbody {
            vertical-align: top;
        }

        .product_table table tbody tr {
        }

        .product_table table tbody tr td {
            border-right: 1px solid black;
            padding-top: 10px;
            padding-bottom: 10px;
        }

    </style>
</head>
<body>
@php
    $k = 0;
    $l = 3;
@endphp
@foreach($data as $key => $item)
    <div class="main-body">
        <div class="header">
            <div class="left-header">
                <div class="left-header-inner">
                    <img src="{{asset($web_settings->get_logo->file_url)}}" alt="">
                    <p style="margin: 0;margin-bottom: 10px;margin-top: 10px">{{$web_settings->website_address}} <br>
                        <strong>Mobile: </strong>{{$web_settings->website_phone}}, {{$web_settings->website_phone2}}</p>
                </div>
            </div>

            <div class="middle-header">
                <div class="middle-header-inner">
                    <h3 style="margin: 0;text-decoration: underline;margin-bottom: 10px">Customer Info</h3>
                    <div class="customer_info">
                        <div class="right_div">
                            <p>
                                <span>{{$item->customer_name}}</span>
                            </p>

                            <p>
                                <span>{{$item->customer_phone}}</span>
                            </p>

                            <p style="max-height: 35px;">
                                <span>{{$item->customer_address}}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-header">
                <div class="right-header-inner">
                    <h3 style="margin: 0;margin-bottom: 10px">Invoice #{{$item->invoice_id}}</h3>
                    <div class="customer_info">
                        <div class="left_div2">
                            <p>
                                <strong>Order Date</strong>
                            </p>

                            @if($item->get_courier)
                                <p>
                                    <strong>Courier</strong>
                                </p>
                            @endif
                            @if($item->courier_inv_no)
                                <p>
                                    <strong>Courier Inv.</strong>
                                </p>
                            @endif
                        </div>

                        <div class="right_div2">
                            <p>
                                <strong>:</strong> &nbsp;<span>{{date('d M, Y',strtotime($item->order_date))}}</span>
                            </p>

                            @if($item->get_courier)
                                <p>
                                    <strong>:</strong> &nbsp;<span>{{$item->get_courier->courier_name}}</span>
                                </p>
                            @endif
                            @if($item->courier_inv_no)
                                <p>
                                    <strong>:</strong> &nbsp;<span>{{$item->courier_inv_no}}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="product_table">
            <table>
                <thead>
                <tr>
                    <th>SL #</th>
                    <th style="text-align: left;padding-left: 10px">Product(s)</th>
                    <th>Qty</th>
                    <th style="text-align: right;padding-right: 10px">Price</th>
                </tr>
                </thead>
                <tbody>
                @php($i=1)
                @foreach($item->get_products as $data)
                    <tr style="vertical-align: top">
                        <td style="text-align: center;width: 5%">{{$i++}}</td>
                        <td style="padding-left: 10px;width: 60%">
                            <span>{{$data->get_product->name}}</span><br>
                            @if($data->attributes)
                                @foreach(json_decode($data->attributes, true) as $key => $attr)
                                    <span style="font-size: 10px" class="text-primary">{{$key}} - {{$attr}}</span><br>
                                @endforeach
                            @endif
                        </td>
                        <td style="text-align: center;width: 10%">{{$data->qty}}</td>
                        <td style="text-align: right;width: 25%;padding-right: 10px">{{$web_settings->currency_sign}} {{$data->price}}</td>
                    </tr>
                @endforeach

                <tr style="border-top: 1px solid black;">
                    <td colspan="3"
                        style="padding-left: 10px;text-align: right;padding-right: 10px;padding-bottom: 0;">
                        <strong>Sub
                            Total</strong></td>
                    <td style="text-align: right;padding-right: 10px;padding-bottom: 0;">{{$web_settings->currency_sign}} {{$item->sub_total}}</td>
                </tr>

                <tr style="border-top: 1px solid black;">
                    <td colspan="3"
                        style="padding-left: 10px;text-align: right;padding-right: 10px;padding-bottom: 0;">
                        <strong>Delivery Cost
                            (+)</strong></td>
                    <td style="text-align: right;padding-right: 10px;padding-bottom: 0;">{{$web_settings->currency_sign}} {{$item->shipping_cost}}</td>
                </tr>

                {{--<tr style="border-top: 1px solid black;">
                    <td colspan="3"
                        style="padding-left: 10px;text-align: right;padding-right: 10px;padding-bottom: 0;">
                        <strong>Discount
                            (-)</strong></td>
                    <td style="text-align: right;padding-right: 10px;padding-bottom: 0;">{{$web_settings->currency_sign}} {{$item->discount}}</td>
                </tr>--}}

                <tr style="border-top: 1px solid black;">
                    <td colspan="3"
                        style="padding-left: 10px;text-align: right;padding-right: 10px;padding-bottom: 0;">
                        <strong>Total</strong>
                    </td>
                    <td style="text-align: right;padding-right: 10px;padding-bottom: 0;">{{$web_settings->currency_sign}} {{$item->total}}</td>
                </tr>
                </tbody>
            </table>

            @if($item->order_note)
                <p>Note: {{$item->order_note}}</p>
            @endif
        </div>
    </div>
    <hr style="margin-bottom: 40px;border: 1px dashed red">
    <?php  $k++; ?>
    @if($k == $l)
        <div class="pagebreak"></div>
        <?php $l = $l + 3; ?>
    @endif
@endforeach
<script>
    window.onload = function () {
        window.print();
        window.close();
    }
</script>
</body>
</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            height: 95px;
            border-bottom: 1px solid black;
            margin-bottom: 5px;
        }

        .header img {
            max-height: 90px;
        }

        .left-header {
            width: 50%;
            float: left;
        }

        .right-header {
            width: 50%;
            float: left;
            padding: 25px 0;
            text-align: right;
        }

        .right-header h2 {
            margin: 0;
            font-size: 40px;
        }

        /*.header h2 {
            margin-top: 5px;
            text-align: center;
            position: relative;
        }

        .header_title span {
            position: absolute;
            font-size: 12px;
            right: 0;
            bottom: 5px;
        }*/

        .info {
            height: 130px;
        }

        .customer_info {
            border: 1px solid black;
            width: 300px;
            height: 112px;
            float: left;
            font-size: 14px;
            padding: 5px 10px;
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
            width: 95px;
        }

        .right_div {
            float: left;
            width: 205px;
        }

        .product_table {
            height: 675px;
        }

        .product_table table {
            border: 1px solid black;
            width: 100%;
            max-height: 640px !important;
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

        .signature {
            height: 50px;
        }

        .sig_left {
            width: 50%;
            float: left;
        }

        .sig_right {
            width: 50%;
            float: right;
        }

        .footer {
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="left-header">
        <img src="{{asset($web_settings->get_logo->file_url)}}" alt="">
    </div>

    <div class="right-header">
        <h2>Invoice / Bill</h2>
    </div>

</div>

<div class="info">
    <div class="customer_info">
        <div class="left_div">
            <p>
                <strong>Name &emsp; &emsp; :</strong>
            </p>

            <p style="max-height: 35px;">
                <strong>Address &emsp; :</strong>
            </p>

            <p>
                <strong>Mobile &emsp; &nbsp; &nbsp;:</strong>
            </p>
        </div>

        <div class="right_div">
            <p>
                <span>{{$data->customer_name}}</span>
            </p>

            <p style="max-height: 35px;">
                <span>{{$data->customer_address}}</span>
            </p>

            <p>
                <span>{{$data->customer_phone}}</span>
            </p>
        </div>
    </div>

    <div class="owner_info">
        <div class="left_div">
            <p>
                <strong>Invoice no &nbsp; :</strong>
            </p>
            <p>
                <strong>Order Date &nbsp;:</strong>
            </p>

            <p>
                <strong>Print Date &nbsp;&nbsp;&nbsp;:</strong>
            </p>

            @if($data->get_courier)
                <p>
                    <strong>Courier &emsp;&emsp;:</strong>
                </p>
            @endif
        </div>

        <div class="right_div">
            <p>
                <span>{{$data->invoice_id}}</span>
            </p>
            <p>
                <span>{{date('d M, Y',strtotime($data->order_date))}}</span>
            </p>
            <p>
                <span>{{date('d M, Y - h:i A',strtotime(\Carbon\Carbon::now()))}}</span>
            </p>

            @if($data->get_courier)
                <p>
                    <span>{{$data->get_courier->courier_name}}</span>
                </p>
            @endif
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
        @foreach($data->get_products as $item)
            <tr style="vertical-align: top">
                <td style="text-align: center;width: 5%">{{$i++}}</td>
                <td style="padding-left: 10px;width: 60%">
                    <span>{{$item->get_product->name}}</span><br>
                    @if($item->attributes)
                        @foreach(json_decode($item->attributes, true) as $key => $attr)
                            <span style="font-size: 10px" class="text-primary">{{$key}} - {{$attr}}</span><br>
                        @endforeach
                    @endif
                </td>
                <td style="text-align: center;width: 10%">{{$item->qty}}</td>
                <td style="text-align: right;width: 25%;padding-right: 10px">{{$web_settings->currency_sign}} {{$item->price}}</td>
            </tr>
        @endforeach

        <tr style="border: 1px solid transparent;border-top: 1px solid black;">
            <td colspan="3" style="padding-left: 10px;text-align: right;padding-right: 10px;border-right: 1px solid transparent;padding-bottom: 0;"><strong>Sub
                    Total</strong></td>
            <td style="text-align: right;padding-right: 10px;border-right: 1px solid transparent;padding-bottom: 0;">{{$web_settings->currency_sign}} {{$data->sub_total}}</td>
        </tr>

        <tr style="border: 1px solid transparent;">
            <td colspan="3" style="padding-left: 10px;text-align: right;padding-right: 10px;border-right: 1px solid transparent;padding-bottom: 0;"><strong>Delivery Cost
                    (+)</strong></td>
            <td style="text-align: right;padding-right: 10px;border-right: 1px solid transparent;padding-bottom: 0;">{{$web_settings->currency_sign}} {{$data->shipping_cost}}</td>
        </tr>

        <tr style="border: 1px solid transparent;">
            <td colspan="3" style="padding-left: 10px;text-align: right;padding-right: 10px;border-right: 1px solid transparent;padding-bottom: 0;"><strong>Discount
                    (-)</strong></td>
            <td style="text-align: right;padding-right: 10px;border-right: 1px solid transparent;padding-bottom: 0;">{{$web_settings->currency_sign}} {{$data->discount}}</td>
        </tr>

        <tr style="border: 1px solid transparent; {{$data->order_note ? "border-bottom: 1px solid #00000024":""}};">
            <td colspan="3" style="padding-left: 10px;text-align: right;padding-right: 10px;border-right: 1px solid transparent;padding-bottom: 0;"><strong>Total</strong>
            </td>
            <td style="text-align: right;padding-right: 10px;border-right: 1px solid transparent;padding-bottom: 0;">{{$web_settings->currency_sign}} {{$data->total}}</td>
        </tr>
        </tbody>
    </table>

    @if($data->order_note)
        <p>Note: {{$data->order_note}}</p>
    @endif
</div>


<div class="signature">
    <div class="sig_left">
        <span style="border-top: 1px solid black;padding: 5px 0 0 0;float:left;">Receiver's Signature</span>
    </div>

    <div class="sig_right">
        <span style="border-top: 1px solid black;padding: 5px 0 0 0;float:right;">Authorized Signature</span>
    </div>
</div>


<div class="footer">
    <div>
        ©{!! $web_settings->website_copyright_text !!}<br>
        {{$web_settings->website_address}}<br>
        {{$web_settings->website_phone}} {{$web_settings->website_phone2}} | {{$web_settings->website_email}}<br>
        <span class="text-center" style="padding: 0px;">Fb: {{$web_settings->website_facebook}} | Web: www.raysbd.com</span><br>
    </div>
    <div>
        <p style="">Developed by: Pro Debuggers | Web: www.prodebuggers.com</p>
    </div>
</div>

<script>
    window.print();
    window.close();
</script>
</body>
</html>

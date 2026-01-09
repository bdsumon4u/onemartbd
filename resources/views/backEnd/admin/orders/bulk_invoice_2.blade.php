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

            body {
                font-family: sans-serif;
                font-size: 14px;
                margin: 0;
            }

        }

        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 0;
        }

        .product_table {
            overflow: hidden;
            /*min-height: 134px;*/
            /*max-height: 165px;*/
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
            /*height: 35px;*/
        }

        .product_table table thead tr th {
            border-right: 1px solid black;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .product_table table tbody {
            vertical-align: top;
        }

        .product_table table tbody tr {
        }

        .product_table table tbody tr td {
            border-right: 1px solid black;
            padding-top: 5px;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
@php
    $k = 0;
    $l = 8;
@endphp
<div class="row">
    @foreach($data as $key => $item)
        <div class="block"
             style="min-height: 295px; overflow:hidden; margin-left:15px;margin-right:15px; margin-bottom: 20px;margin-top: 20px;border: 1px solid #000;border-radius: 5px;width: 46%;float:left;">
            <div style="min-height: 60px">
                <div style="float:left;">
                    <img style="height: 50px; margin-left: 5px; margin-top: 5px;" src="{{asset($web_settings->get_logo->file_url)}}" alt="">
                </div>
                <div style="float: right;margin-right: 5px;margin-top: 5px;">
                    <span><strong>Invoice #</strong>{{$item->invoice_id}}</span> <br>
                    <span><strong>Date :</strong> {{date('d M, Y',strtotime($item->order_date))}}</span> <br>
                    <span><strong>Payment :</strong>
                        @if($item->payment_status==0)
                            <span style="color: red">Unpaid</span>
                        @elseif($item->payment_status==1)
                            <span style="color: deepskyblue">Partial</span>
                        @elseif($item->payment_status==2)
                            <span style="color: green">Paid</span>
                        @endif
                    </span>
                </div>
            </div>
            <div style="margin-left: 5px;min-height: 70px;max-height:80px;overflow: hidden">
                <p style="margin: 0; margin-bottom: 5px;"><strong>Name: </strong>{{$item->customer_name}}</p>
                <p style="margin: 0; margin-bottom: 5px;"><strong>Phone: </strong>{{$item->customer_phone}}</p>
                <p style="margin: 0; margin-bottom: 5px;"><strong>Address: </strong>{{$item->customer_address}}</p>
            </div>
            <div class="product_table">
                <table style="border: 1px solid #000;border-left:none;border-right:none;border-bottom:none;width: 100%;">
                    <thead>
                    <tr>
                        <th>SL.</th>
                        <th>Item(s)</th>
                        <th>Qty</th>
                        <th style="border-right: none">Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($item->get_products as $data)
                        <tr style="vertical-align: top">
                            <td style="text-align: center;width: 5%">{{$i++}}</td>
                            <td style="padding-left: 10px;width: 60%">
                                <div style="display:flex;align-items: center">
                                    <div>
                                        <img style="width: 30px;margin-right: 2px;" src="{{asset($data->get_product->get_thumb->file_url)}}" alt="">
                                    </div>
                                    <div>
                                        <span>{{\Illuminate\Support\Str::limit($data->get_product->name,30)}}</span><br>
                                        @if($data->attributes)
                                            @foreach(json_decode($data->attributes, true) as $key => $attr)
                                                <span style="font-size: 10px" class="text-primary"><b>{{$key}}</b> - {{$attr}}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;width: 10%">
                                <span>{{$data->qty}}</span>
                            </td>
                            <td style="text-align: right;width: 25%;padding-right: 10px;border-right: none">
                                <span>{{$web_settings->currency_sign}} {{$data->price}}</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tr style="border-top: 1px solid black;">
                        <td colspan="3"
                            style="padding-left: 10px;text-align: right;padding-right: 10px;padding-bottom: 0;">
                            <strong>Sub
                                Total</strong></td>
                        <td style="text-align: right;padding-right: 10px;padding-bottom: 0;border-right: none">{{$web_settings->currency_sign}} {{$item->sub_total}}</td>
                    </tr>

                    <tr style="border-top: 1px solid black;">
                        <td colspan="3"
                            style="padding-left: 10px;text-align: right;padding-right: 10px;padding-bottom: 0;">
                            <strong>Delivery Cost
                                (+)</strong></td>
                        <td style="text-align: right;padding-right: 10px;padding-bottom: 0;border-right: none">{{$web_settings->currency_sign}} {{$item->shipping_cost}}</td>
                    </tr>
                    <tr style="border-top: 1px solid black;">
                        <td colspan="3"
                            style="padding-left: 10px;text-align: right;padding-right: 10px;padding-bottom: 0;border-right: none">
                            <strong>Total</strong>
                        </td>
                        <td style="text-align: right;padding-right: 10px;padding-bottom: 0;border-right: none">{{$web_settings->currency_sign}} {{$item->total}}</td>
                    </tr>
                </table>
            </div>
        </div>

        <?php  $k++; ?>
        @if($k == $l)
            <div class="pagebreak"></div>
            <?php $l = $l + 8; ?>
        @endif
    @endforeach
</div>
<script>
     window.onload = function () {
         window.print();
         window.close();
     }
</script>
</body>
</html>

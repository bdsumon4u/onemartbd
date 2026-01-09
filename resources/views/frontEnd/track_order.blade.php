@extends('frontEnd.layouts.master')

@section('title')
    Track Order
@endsection
@section('style')
    <style>
        p {
            color: grey
        }

        #heading {
            color: #673AB7;
            margin-bottom: 40px;
        }

        .card {
            z-index: 0;
            border: 1px solid #e6eaef;
            position: relative;
            background: #f1f5f9;
        }

        .cus_info {
            text-align: left;
        }

        .cus_info ul {
            list-style: none;
        }

        .cus_info ul li {
        }

        .cus_info ul li p {
            margin-bottom: 3px;
        }

        .fs-title {
            font-size: 25px;
            color: #2ec551;
            margin-bottom: 15px;
            font-weight: normal;
            text-align: left
        }

        .purple-text {
            color: #2ec551;
            font-weight: normal
        }

        .steps {
            font-size: 25px;
            color: gray;
            margin-bottom: 10px;
            font-weight: normal;
            text-align: right
        }

        .fieldlabels {
            color: gray;
            text-align: left
        }

        #progressbar {
            margin-bottom: 50px;
            overflow: hidden;
            color: lightgrey
        }

        #progressbar .active {
            color: #2ec551
        }

        #progressbar li {
            list-style-type: none;
            font-size: 18px;
            width: 33%;
            float: left;
            position: relative;
            font-weight: 400
        }

        #progressbar #processing:before {
            font-family: Jost-Medium;
            content: "1"
        }

        #progressbar #on_delivery:before {
            font-family: Jost-Medium;
            content: "2"
        }

        #progressbar #delivered:before {
            font-family: Jost-Medium;
            content: "3"
        }

        #progressbar #returned:before {
            font-family: Jost-Medium;
            content: "3";
            background: #FF0000;
        }

        #progressbar #returned:after {
            background: #FF0000;
        }

        #progressbar #returned.active {
            color: #FF0000;
        }

        #progressbar #cancelled:before {
            font-family: Jost-Medium;
            content: "2";
            background: #FF0000;
        }

        #progressbar #cancelled:after {
            background: #FF0000;
        }

        #progressbar #cancelled {
            color: #FF0000;
        }

        #progressbar #hold:before {
            font-family: Jost-Medium;
            content: "1";
            background: #ffc108;
        }

        #progressbar #hold:after {
            background: #ffc108;
        }

        #progressbar #hold {
            color: #ffc108;
        }

        #progressbar li:before {
            width: 50px;
            height: 50px;
            line-height: 45px;
            display: block;
            font-size: 20px;
            color: #ffffff;
            background: lightgray;
            border-radius: 50%;
            margin: 0 auto 10px auto;
            padding: 2px
        }

        #progressbar li:after {
            content: '';
            width: 100%;
            height: 2px;
            background: lightgray;
            position: absolute;
            left: 0;
            top: 25px;
            z-index: -1
        }

        #progressbar li.active:before,
        #progressbar li.active:after {
            background: #2ec551
        }

        .progress {
            height: 20px
        }

        .progress-bar {
            background-color: #2ec551
        }

        .fit-image {
            width: 100%;
            object-fit: cover
        }

        @media (max-width: 575.98px) {
            #progressbar {
                margin-bottom: 30px;
            }

            .cus_info {
                margin-bottom: 25px;
            }

            #progressbar li {
                font-size: 13px;
            }

            #progressbar li:before {
                width: 40px;
                height: 40px;
                line-height: 35px;
                margin: 0 auto 0 auto;
            }

            #progressbar li:after {
                top: 20px
            }
        }
    </style>
@endsection

@section('body')
    <section class="py-md-3">
        <div class="cart-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1 id="heading">Track Order(s)</h1>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-5 col-12">
                        <form action="{{route('track.order')}}" method="get" class="d-flex">
                            <input type="text" class="form-control mr-1" name="q" value="{{request()->query('q')}}" placeholder="Enter You Phone Number">
                            <input type="submit" class="btn btn-dark" value="Track">
                        </form>
                    </div>
                </div>
                <div class="row justify-content-center">
                    @if(count($data)>0)
                        <div class="col-md-10 col-11 p-0 mt-md-0 mt-2">
                            <p class="mb-0">{{count($data)}} Orders Found</p>
                        </div>
                        @foreach($data as $item)
                            <div class="col-11 col-sm-10 col-md-10 col-lg-10 col-xl-10 text-center p-0 mt-3 mb-2">
                                <div class="card mt-3 mb-3">
                                    <div class="card-body">
                                        <ul id="progressbar">
                                            @if($item->status == 0)
                                                <li id="hold"><strong>On Hold</strong></li>
                                            @else
                                                <li class="{{
                                    $item->status==2||
                                    $item->status==5||
                                    $item->status==3||
                                    $item->status==6||
                                    $item->status==0||
                                    $item->status==4||
                                    $item->status==7||
                                    $item->status==1
                                    ?"active":""}}" id="processing"><strong>Processing</strong></li>
                                            @endif

                                            @if($item->status == 4)
                                                <li id="cancelled"><strong>Cancelled</strong>
                                                </li>
                                            @else
                                                <li class="{{$item->status==6||$item->status==1||$item->status==7?"active":""}}" id="on_delivery"><strong>On
                                                        Delivery</strong>
                                                </li>
                                            @endif
                                            <li class="{{$item->status==1||$item->status==7?"active":""}}" @if($item->status==1) id="delivered"
                                                @elseif($item->status==7) id="returned"
                                                @else id="delivered" @endif>
                                                @if($item->status==1) <strong>Delivered</strong> @elseif($item->status==7) <strong>Returned</strong> @else
                                                    <strong>Delivered</strong> @endif
                                            </li>
                                        </ul>

                                        <div class="row">
                                            <div class="col-md-4 col-12 cus_info">
                                                <ul>
                                                    <li>
                                                        <p><b>Payment :</b>
                                                            @if($item->payment_status==0)
                                                                <span class="badge badge-danger">Unpaid</span>
                                                            @elseif($item->payment_status==1)
                                                                <span class="badge badge-info">Partial Paid</span>
                                                            @elseif($item->payment_status==2)
                                                                <span class="badge badge-success">Paid</span>
                                                            @endif

                                                        </p>
                                                        <p><b>Order Date :</b> {{date('d M, Y',strtotime($item->order_date))}}</p>
                                                        <p><b>Order No. :</b> {{$item->invoice_id}}</p>
                                                        <p><b>Customer Name :</b> {{$item->customer_name}}</p>
                                                        <p><b>Customer Phone :</b> {{$item->customer_phone}}</p>
                                                        <p><b>Customer Address :</b> {{$item->customer_address}}</p>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="col-md-8 col-12 ord_info">
                                                <table class="cart_table table text-center mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Price</th>
                                                        <th>Quantity</th>
                                                        <th>Total</th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    @foreach($item->get_products as $c_item)
                                                        <tr>
                                                            <td class="text-left">
                                                                <img width="40" src="{{$c_item->get_product->get_thumb->file_url}}" alt="">
                                                                <a style="font-size: 14px"
                                                                   href="{{route('single.product',[$c_item->get_product->slug,$c_item->get_product->id])}}">{{$c_item->get_product->name}}</a>
                                                            </td>
                                                            <td>{{$c_item->price}}</td>
                                                            <td width="15%" class="cart_qty">{{$c_item->qty}}</td>
                                                            <td>{{$c_item->price}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <th colspan="3" class="text-right pr-2">Sub Total</th>
                                                        <td><span id="net_total">{{$item->sub_total}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3" class="text-right pr-2">Shipping Cost</th>
                                                        <td>
                                                            <span id="cart_shipping_cost">{{$item->shipping_cost}}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3" class="text-right pr-2">Total</th>
                                                        <td>
                                                            <span id="grand_total">{{$item->total}}</span>
                                                        </td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-11 col-sm-10 col-md-10 col-lg-10 col-xl-10 text-center pb-5 mt-3 mb-2">
                            <div class="card mt-3 mb-3">
                                <div class="card-body">
                                    <span class="text-danger font-weight-bolder">No Order Found!</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
@endsection

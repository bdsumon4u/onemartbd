@extends('frontEnd.layouts.master')

@section('title')
    Checkout
@endsection
@section('style')
    <style>
        * {
            font-family: Li-Ador-Noirrit-R;
        }

        p,
        span,
        h5 {
            font-size: 17px;
            font-family: Li-Ador-Noirrit-R;
        }

        h5 {
            font-size: 20px;
        }

        .checkout-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(4px);
        }

        .checkout-modal-overlay.hidden {
            display: none;
        }

        .checkout-modal {
            background: #ffffff;
            border-radius: 8px;
            max-width: 1100px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            padding: 16px 16px 24px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        .checkout-modal-close {
            position: absolute;
            top: 10px;
            right: 14px;
            border: none;
            background: transparent;
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
            color: #555555;
            padding: 0;
        }

        .checkout-modal-close:focus {
            outline: none;
        }

        .discount-offer-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(4px);
        }

        .discount-offer-modal {
            background: linear-gradient(145deg, #ffe259 0%, #ffa751 45%, #ffbb33 100%);
            border-radius: 18px;
            max-width: 420px;
            width: 100%;
            padding: 26px 22px 22px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .discount-offer-modal::before,
        .discount-offer-modal::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.18);
            z-index: 0;
        }

        .discount-offer-modal::before {
            width: 120px;
            height: 120px;
            top: -40px;
            left: -30px;
        }

        .discount-offer-modal::after {
            width: 160px;
            height: 160px;
            bottom: -60px;
            right: -40px;
        }

        .discount-offer-inner {
            position: relative;
            z-index: 1;
        }

        .discount-offer-title {
            font-size: 19px;
            font-weight: 700;
            line-height: 1.5;
            margin-bottom: 14px;
            color: #2c1f00;
        }

        .discount-offer-title span.icon {
            margin-right: 4px;
        }

        .discount-offer-highlight {
            font-weight: 700;
            color: #c01a00;
        }

        .discount-offer-badge-wrapper {
            margin: 6px 0 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .discount-offer-badge {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 20%, #ffffff 0, #ffe077 40%, #ff8a00 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            color: #c01a00;
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.35);
        }

        .discount-offer-badge span {
            display: block;
        }

        .discount-offer-text {
            font-size: 15px;
            margin-bottom: 12px;
            color: #3b2600;
        }

        .discount-offer-strip {
            background: #ff4b2b;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 10px;
            margin: 6px 0 10px;
        }

        .discount-offer-actions .btn {
            min-width: 120px;
        }

        .discount-offer-accept-btn {
            font-weight: 700;
            border-radius: 999px;
            box-shadow: 0 10px 20px rgba(191, 87, 0, 0.45);
        }

        .discount-offer-reject-btn {
            font-weight: 600;
            border-radius: 999px;
            background: #ffffff;
            border: 2px solid #e3a300;
            color: #c67500;
        }

        .discount-offer-close {
            position: absolute;
            top: 8px;
            right: 12px;
            border: none;
            background: transparent;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            color: #777777;
            padding: 0;
        }

        .discount-offer-close:focus {
            outline: none;
        }

        @keyframes zoom-in-out {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .btn-drift {
            animation: zoom-in-out 1.5s infinite;
        }
    </style>
@endsection
@section('gTag')
    <script>
        dataLayer.push({
            ecommerce: null
        }); // Clear the previous ecommerce object.
        dataLayer.push({
            event: "begin_checkout",
            ecommerce: {
                currency: "BDT",
                value: {{ session('api_begin_checkout_data.value', 0) }},
                coupon: "",
                items: {!! session('api_begin_checkout_data.products', '[]') !!}
            }
        });
    </script>
@endsection
@section('body')
    @php
        $extraDiscountAmount = (int) ($web_settings?->extra_special_discount_amount ?? 30);
        $extraDiscountDisplay = number_format($extraDiscountAmount, 2);
        $checkoutModalEnabled = false; // Set to true to enable modal, false to show directly on page
    @endphp
    @if (\Cart::getContent()->count() > 0)
        @if ($checkoutModalEnabled)
            <div id="checkout-modal-overlay" class="checkout-modal-overlay">
                <div class="checkout-modal m-2" role="dialog" aria-modal="true" aria-labelledby="checkout-modal-title">
                    <button type="button" class="checkout-modal-close" id="checkout-modal-close">&times;</button>

                    <section>
                        <div class="cart-section">
                            <div class="container p-0">
                                <div class="row">
                                    <div class="col-md-5 col-12 mb-md-0 mb-4">
                                        <div class="card" style="border: none">
                                            <div class="card-body p-2">
                                                @if (session('defender_error'))
                                                    <div class="alert alert-warning border-warning mb-3" role="alert">
                                                        <p class="mb-2 mb-lg-3">
                                                            {{ session('defender_error') }}
                                                        </p>
                                                        <p class="mb-0 d-flex align-items-center flex-wrap"
                                                            style="gap: 8px;">
                                                            <span>জরুরি হলে আমাদের সাথে যোগাযোগ করুন:</span>
                                                            @if (filled($web_settings->whatsapp_number ?? null))
                                                                <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/[^0-9]/', '', $web_settings->whatsapp_number) }}"
                                                                    target="_blank" rel="noopener"
                                                                    class="btn btn-success btn-sm d-inline-flex align-items-center"
                                                                    style="background-color: #25D366;">
                                                                    <i class="fa fa-whatsapp mr-1"
                                                                        style="font-size: 18px;"></i> WhatsApp
                                                                </a>
                                                            @endif
                                                        </p>
                                                    </div>
                                                @endif
                                                <p class="text-center">অর্ডারটি কনফার্ম করতে আপনার নাম, ঠিকানা, মোবাইল
                                                    নাম্বার, লিখে
                                                    <span class="text-danger">অর্ডার কনফার্ম করুন</span> বাটনে ক্লিক করুন
                                                </p>
                                                <form action="{{ route('place.order') }}" method="post" id="checkout_form"
                                                    class="checkout_form">
                                                    @csrf
                                                    <input type="hidden" name="shipping_cost" id="shipping_cost">
                                                    <input type="hidden" name="extra_discount" id="extra_discount"
                                                        value="0">
                                                    <div class="form-group">
                                                        <label for="customer_name">আপনার নাম </label>
                                                        <input type="text" class="form-control" id="customer_name"
                                                            name="customer_name" placeholder="আপনার নাম লিখুন"
                                                            value="{{ old('customer_name') }}" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="customer_phone">আপনার মোবাইল</label>
                                                        <input type="number"
                                                            class="form-control @error('customer_phone')is-invalid @enderror"
                                                            id="customer_phone" name="customer_phone"
                                                            placeholder="আপনার মোবাইল নাম্বার লিখুন"
                                                            value="{{ old('customer_phone') }}" minlength="11"
                                                            pattern="01[3-9][0-9]{8}"
                                                            maxlength="11" required>
                                                        @error('customer_phone')
                                                            <span
                                                                class="text-danger font-weight-bold">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="customer_address">আপনার ঠিকানা</label>
                                                        <input type="text" class="form-control" id="customer_address"
                                                            name="customer_address" value="{{ old('customer_address') }}"
                                                            placeholder="আপনার ঠিকানা লিখুন" required>
                                                    </div>

                                                    <div class="form-group d-none" area-box>
                                                        <label for="shipping_method">আপনার এরিয়া সিলেক্ট করুন</label>
                                                        <select name="shipping_method" id="shipping_method"
                                                            class="form-control" required>
                                                            @foreach ($shipping_methods as $item)
                                                                <option value="{{ $item->id }}">{{ $item->type }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-success w-100 mb-2 btn-drift"
                                                        style="height: 50px" id="conf_order_btn">অর্ডার কনফার্ম করুন <span
                                                            id="confirm-button-total-amount"></span></button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-7 col-12">
                                        <div class="card" style="border: 1px solid #e9e9e9">
                                            <h5 id="checkout-modal-title" class="font-weight-bold card-header">আপনার অর্ডার
                                            </h5>
                                            <div class="card-body p-2 table-responsive" id="order_info_table">
                                                <table class="cart_table table text-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>Product</th>
                                                            <th>Price</th>
                                                            <th>Quantity</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>


                                                    <tbody>
                                                        @foreach (\Cart::getContent()->sort() as $item)
                                                            <tr>
                                                                <td>
                                                                    <a href="{{ route('cart.item.delete', $item->id) }}"><i
                                                                            class="fa fa-trash-o text-danger"></i></a>
                                                                </td>
                                                                <td class="text-left">
                                                                    <img width="35"
                                                                        src="{{ $item->associatedModel->get_thumb->file_url ?? '' }}"
                                                                        alt="">
                                                                    <a style="font-size: 14px"
                                                                        href="{{ route('single.product', [$item->associatedModel->slug, $item->associatedModel->id]) }}">{{ $item->name }}</a>
                                                                </td>
                                                                <td>{{ $item->price }}</td>
                                                                <td width="15%" class="cart_qty">
                                                                    <a href="javascript:void(0);"><i
                                                                            class="fa fa-minus qty_minus" id=""
                                                                            data-id="{{ $item->id }}"></i></a>
                                                                    <input type="text" name="qty" id="qty"
                                                                        min="1" value="{{ $item->quantity }}"
                                                                        readonly>
                                                                    <a href="javascript:void(0);"><i
                                                                            class="fa fa-plus qty_plus" id=""
                                                                            data-id="{{ $item->id }}"></i></a>
                                                                </td>
                                                                <td>{{ $item->getPriceSum() }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="4" class="text-right pr-2">Sub Total</th>
                                                            <td><span id="net_total">{{ \Cart::getTotal() }}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="4" class="text-right pr-2">Shipping Cost</th>
                                                            <td>
                                                                <span id="cart_shipping_cost">0</span>
                                                            </td>
                                                        </tr>
                                                        <tr id="extra_discount_row" style="display: none;">
                                                            <th colspan="4" class="text-right pr-2 text-success">
                                                                Special Discount
                                                            </th>
                                                            <td class="text-success">
                                                                -<span id="extra_discount_amount">0</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="4" class="text-right pr-2">Total</th>
                                                            <td>
                                                                <span id="grand_total"></span>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                            {{-- <div class="card-footer">
                                            <a href="{{route('home')}}" class="btn btn-info btn-sm ">
                                                <i class="fa fa-angle-left"></i> Back To Shopping
                                            </a>
                                            <a href="{{route('cart.clear')}}" class="btn btn-danger btn-sm float-right">Cart Clear</a>
                                        </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        @else
            <section>
                <div class="cart-section">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-5 col-12 mb-md-0 mb-4">
                                <div class="card" style="border: none">
                                    <div class="card-body p-2">
                                        @if (session('defender_error'))
                                            <div class="alert alert-warning border-warning mb-3" role="alert">
                                                <p class="mb-2 mb-lg-3">
                                                    {{ session('defender_error') }}
                                                </p>
                                                <p class="mb-0 d-flex align-items-center flex-wrap" style="gap: 8px;">
                                                    <span>জরুরি হলে আমাদের সাথে যোগাযোগ করুন:</span>
                                                    @if (filled($web_settings->whatsapp_number ?? null))
                                                        <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/[^0-9]/', '', $web_settings->whatsapp_number) }}"
                                                            target="_blank" rel="noopener"
                                                            class="btn btn-success btn-sm d-inline-flex align-items-center"
                                                            style="background-color: #25D366;">
                                                            <i class="fa fa-whatsapp mr-1" style="font-size: 18px;"></i>
                                                            WhatsApp
                                                        </a>
                                                    @endif
                                                </p>
                                            </div>
                                        @endif
                                        <p class="text-center">অর্ডারটি কনফার্ম করতে আপনার নাম, ঠিকানা, মোবাইল নাম্বার,
                                            লিখে
                                            <span class="text-danger">অর্ডার কনফার্ম করুন</span> বাটনে ক্লিক করুন
                                        </p>
                                        <form action="{{ route('place.order') }}" method="post" id="checkout_form"
                                            class="checkout_form">
                                            @csrf
                                            <input type="hidden" name="shipping_cost" id="shipping_cost">
                                            <input type="hidden" name="extra_discount" id="extra_discount"
                                                value="0">
                                            <div class="form-group">
                                                <label for="customer_name">আপনার নাম </label>
                                                <input type="text" class="form-control" id="customer_name"
                                                    name="customer_name" placeholder="আপনার নাম লিখুন"
                                                    value="{{ old('customer_name') }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="customer_phone">আপনার মোবাইল</label>
                                                <input type="number"
                                                    class="form-control @error('customer_phone')is-invalid @enderror"
                                                    id="customer_phone" name="customer_phone"
                                                    placeholder="আপনার মোবাইল নাম্বার লিখুন"
                                                    value="{{ old('customer_phone') }}" minlength="11" maxlength="11"
                                                    required>
                                                @error('customer_phone')
                                                    <span class="text-danger font-weight-bold">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="customer_address">আপনার ঠিকানা</label>
                                                <input type="text" class="form-control" id="customer_address"
                                                    name="customer_address" value="{{ old('customer_address') }}"
                                                    placeholder="আপনার ঠিকানা লিখুন" required>
                                            </div>

                                            <div class="form-group d-none" area-box>
                                                <label for="shipping_method">আপনার এরিয়া সিলেক্ট করুন</label>
                                                <select name="shipping_method" id="shipping_method" class="form-control"
                                                    required>
                                                    @foreach ($shipping_methods as $item)
                                                        <option value="{{ $item->id }}">{{ $item->type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-success w-100 mb-2 btn-drift"
                                                style="height: 50px" id="conf_order_btn">অর্ডার কনফার্ম করুন <span
                                                    id="confirm-button-total-amount"></span></button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7 col-12">
                                <div class="card" style="border: 1px solid #e9e9e9">
                                    <h5 id="checkout-modal-title" class="font-weight-bold card-header">আপনার অর্ডার</h5>
                                    <div class="card-body p-2 table-responsive" id="order_info_table">
                                        <table class="cart_table table text-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>


                                            <tbody>
                                                @foreach (\Cart::getContent()->sort() as $item)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('cart.item.delete', $item->id) }}"><i
                                                                    class="fa fa-trash-o text-danger"></i></a>
                                                        </td>
                                                        <td class="text-left">
                                                            <img width="35"
                                                                src="{{ $item->associatedModel->get_thumb->file_url ?? '' }}"
                                                                alt="">
                                                            <a style="font-size: 14px"
                                                                href="{{ route('single.product', [$item->associatedModel->slug, $item->associatedModel->id]) }}">{{ $item->name }}</a>
                                                        </td>
                                                        <td>{{ $item->price }}</td>
                                                        <td width="15%" class="cart_qty">
                                                            <a href="javascript:void(0);"><i class="fa fa-minus qty_minus"
                                                                    id="" data-id="{{ $item->id }}"></i></a>
                                                            <input type="text" name="qty" id="qty"
                                                                min="1" value="{{ $item->quantity }}" readonly>
                                                            <a href="javascript:void(0);"><i class="fa fa-plus qty_plus"
                                                                    id="" data-id="{{ $item->id }}"></i></a>
                                                        </td>
                                                        <td>{{ $item->getPriceSum() }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4" class="text-right pr-2">Sub Total</th>
                                                    <td><span id="net_total">{{ \Cart::getTotal() }}</span></td>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-right pr-2">Shipping Cost</th>
                                                    <td>
                                                        <span id="cart_shipping_cost">0</span>
                                                    </td>
                                                </tr>
                                                <tr id="extra_discount_row" style="display: none;">
                                                    <th colspan="4" class="text-right pr-2 text-success">
                                                        Special Discount
                                                    </th>
                                                    <td class="text-success">
                                                        -<span id="extra_discount_amount">0</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-right pr-2">Total</th>
                                                    <td>
                                                        <span id="grand_total"></span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <div id="discount-offer-modal-overlay" class="discount-offer-modal-overlay">
            <div class="discount-offer-modal" role="dialog" aria-modal="true">
                <button type="button" class="discount-offer-close" id="discount-offer-close">&times;</button>
                <div class="discount-offer-inner">
                    <h4 class="discount-offer-title">
                        <span class="icon">💎</span> সবাই পায় না – আপনি পেয়েছেন!
                    </h4>
                    <p class="discount-offer-text">
                        <span class="discount-offer-highlight">এই মুহূর্তে অর্ডার কনফার্ম করলে</span> পাচ্ছেন অতিরিক্ত
                        <strong>{{ $extraDiscountDisplay }} টাকা</strong> ছাড় – কারণ আপনি স্পেশাল কাস্টমার!
                    </p>
                    <div class="discount-offer-badge-wrapper">
                        <div class="discount-offer-badge">
                            <span>{{ $extraDiscountDisplay }}</span>
                            <span>TK</span>
                        </div>
                    </div>
                    <p class="discount-offer-text mb-1">
                        নিচের বোতামে ক্লিক করলেই {{ $extraDiscountAmount }} টাকা ডিসকাউন্ট আপনার অর্ডারে যুক্ত হবে।
                    </p>
                    <div class="discount-offer-strip">
                        অর্ডার কনফার্ম করলেই পাচ্ছেন অতিরিক্ত {{ $extraDiscountAmount }} টাকা ছাড় – দেরি করবেন না!
                    </div>
                    <div class="discount-offer-actions d-flex flex-column">
                        <button type="button" id="accept-extra-discount"
                            class="btn btn-warning discount-offer-accept-btn mb-2">হ্যাঁ, {{ $extraDiscountAmount }} টাকা
                            ছাড়টি যুক্ত করুন</button>
                        <button type="button" id="reject-extra-discount" class="btn discount-offer-reject-btn">অফারটি
                            প্রয়োজন নেই, ধন্যবাদ</button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <section class="py-md-5">
            <div class="cart-section">
                <div class="container">
                    <div class="row py-md-5">
                        <div class="col-12 text-center">
                            <h1 class="mb-md-4">কোন প্রোডাক্ট নেই</h1>
                            <a href="{{ route('home') }}" class="btn btn-info px-5">প্রোডাক্ট বাছাই করুন</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection



@section('script')
    <script>
        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var cart = $('#cart').val()
            $.ajax({
                url: '{{ route('ajax.get.shipp.meth') }}',
                type: 'POST',
                data: {
                    _token: CSRF_TOKEN,
                    id: $('#shipping_method').val(),
                    cart: cart

                },
                success: function(data) {
                    if (data != 0) {
                        $('[area-box]').removeClass('d-none');
                    } else {
                        $('[area-box]').html(`
                            <div class="free-delivery-text text-center">
                                <i class="fa fa-check-circle text-success"></i>
                                <span>ফ্রি ডেলিভারি</span>
                            </div>
                        `);
                        $('[area-box]').removeClass('d-none');
                    }
                    $("#cart_shipping_cost").text(data);
                    $("#shipping_cost").val(data);
                    calculate();
                }
            });

            $("#shipping_method").on('change', function() {
                var cart = $('#cart').val()
                if ($(this).val()) {
                    $.ajax({
                        url: '{{ route('ajax.get.shipp.meth') }}',
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN,
                            id: $(this).val(),
                            cart: cart
                        },
                        success: function(data) {
                            $("#cart_shipping_cost").text(data);
                            $("#shipping_cost").val(data);
                            calculate();
                        }
                    });
                } else {
                    $("#cart_shipping_cost").text(0);
                    $("#shipping_cost").val(0);
                    calculate();
                }

            });

            function getExtraDiscount() {
                var value = parseFloat($('#extra_discount').val());

                if (isNaN(value)) {
                    return 0;
                }

                return value;
            }

            function syncDiscountRowState() {
                var extraDiscount = getExtraDiscount();

                if (extraDiscount > 0) {
                    $('#extra_discount_amount').text(extraDiscount);
                    $('#extra_discount_row').show();
                } else {
                    $('#extra_discount_amount').text(0);
                    $('#extra_discount_row').hide();
                }
            }

            function refreshOrderInfoTable(data) {
                var currentShippingCost = parseFloat($('#shipping_cost').val());

                if (isNaN(currentShippingCost)) {
                    currentShippingCost = 0;
                }

                $('#order_info_table').empty();
                $('#order_info_table').append(data);
                $('#cart_shipping_cost').text(currentShippingCost);
                syncDiscountRowState();
                calculate();
            }

            function calculate() {
                var net_total = parseFloat($('#net_total').text());
                var cart_shipping_cost = parseFloat($('#cart_shipping_cost').text());
                var extra_discount = getExtraDiscount();

                if (isNaN(net_total)) {
                    net_total = 0;
                }

                if (isNaN(cart_shipping_cost)) {
                    cart_shipping_cost = 0;
                }

                var grand_total = net_total + cart_shipping_cost - extra_discount;

                if (grand_total < 0) {
                    grand_total = 0;
                }

                $('#grand_total').text(grand_total);
                $('#confirm-button-total-amount').html(`
                    (<span>&#2547; ${grand_total}</span>)
                `);
            }


            $("#checkout_form").submit(function() {
                $("#conf_order_btn").attr("disabled", true).text('সাবমিট হচ্ছে...');
            });

            $(document).on('click', '.qty_plus', function() {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ route('cart.item.plus') }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        id: $(this).data('id')
                    },
                    success: function(data) {
                        refreshOrderInfoTable(data);
                    }
                });
            });

            $(document).on('click', '.qty_minus', function() {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ route('cart.item.minus') }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        id: $(this).data('id')
                    },
                    success: function(data) {
                        refreshOrderInfoTable(data);
                    }
                });
            });
        });
    </script>
    <script>
        $('#customer_phone').on('blur', function() {
            var phone = $(this).val();
            var name = $('#customer_name').val();
            var address = $('#customer_address').val();
            var shipping_method = $('#shipping_method').val();
            var shipping_cost = $('#shipping_cost').val();
            var data = {
                phone: phone,
                name: name,
                address: address,
                shipping_method: shipping_method,
                shipping_cost: shipping_cost
            };

            if (phone.length == 11) {
                $.ajax({
                    url: '{{ route('abandoned.cart') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        data: data
                    },
                    success: function(data) {
                        // console.log(data)
                    }
                });
            }

        });
    </script>
    <script>
        $(function() {
            var DISCOUNT_AMOUNT = {{ $extraDiscountAmount }};
            var DISCOUNT_CHANCE = {{ (int) ($web_settings->extra_special_discount_chance ?? 100) }};
            var CHECKOUT_MODAL_ENABLED = {{ $checkoutModalEnabled ? 'true' : 'false' }};
            if (DISCOUNT_CHANCE < 0) {
                DISCOUNT_CHANCE = 0;
            }
            if (DISCOUNT_CHANCE > 100) {
                DISCOUNT_CHANCE = 100;
            }

            var didWinDiscount = false;
            if (DISCOUNT_CHANCE >= 100) {
                didWinDiscount = true;
            } else if (DISCOUNT_CHANCE <= 0) {
                didWinDiscount = false;
            } else {
                didWinDiscount = Math.random() * 100 < DISCOUNT_CHANCE;
            }

            var checkoutOverlay = $('#checkout-modal-overlay');
            var discountOverlay = $('#discount-offer-modal-overlay');
            var offerShown = false;
            var offerAccepted = false;
            var pendingCloseAfterOffer = false;
            var pendingNavigationUrl = null;

            function openDiscountOffer(triggeredByClose) {
                offerShown = true;
                pendingCloseAfterOffer = !!triggeredByClose;
                discountOverlay.fadeIn(150);
                discountOverlay.css('display', 'flex');
            }

            function closeCheckoutModal() {
                var checkoutSection = checkoutOverlay.find('section').first();

                if (checkoutSection.length) {
                    checkoutSection.insertAfter(checkoutOverlay);
                }

                checkoutOverlay.addClass('hidden');
            }

            function closeDiscountModal() {
                discountOverlay.fadeOut(150, function() {
                    if (pendingCloseAfterOffer && !offerAccepted) {
                        closeCheckoutModal();
                    }
                    pendingCloseAfterOffer = false;
                });
            }

            function applyExtraDiscount() {
                offerAccepted = true;
                $('#extra_discount').val(DISCOUNT_AMOUNT);
                $('#extra_discount_amount').text(DISCOUNT_AMOUNT);
                $('#extra_discount_row').show();
                pendingCloseAfterOffer = false;
                discountOverlay.fadeOut(150);

                var net_total = parseFloat($('#net_total').text());
                var cart_shipping_cost = parseFloat($('#cart_shipping_cost').text());

                if (isNaN(net_total)) {
                    net_total = 0;
                }

                if (isNaN(cart_shipping_cost)) {
                    cart_shipping_cost = 0;
                }

                var grand_total = net_total + cart_shipping_cost - DISCOUNT_AMOUNT;

                if (grand_total < 0) {
                    grand_total = 0;
                }

                $('#grand_total').text(grand_total);
                $('#confirm-button-total-amount').html(`
                    (<span>&#2547; ${grand_total}</span>)
                `);
            }

            function handleCheckoutCloseAttempt() {
                if (didWinDiscount && !offerShown) {
                    openDiscountOffer(true);
                } else if (CHECKOUT_MODAL_ENABLED) {
                    closeCheckoutModal();
                }
            }

            // Only add modal-specific event listeners if modal is enabled
            if (CHECKOUT_MODAL_ENABLED) {
                $('#checkout-modal-close').on('click', function(e) {
                    e.preventDefault();
                    handleCheckoutCloseAttempt();
                });

                checkoutOverlay.on('click', function(e) {
                    if (e.target === this) {
                        handleCheckoutCloseAttempt();
                    }
                });
            } else {
                // When modal is disabled, intercept normal link clicks and show custom discount modal
                $(document).on('click', 'a[href]', function(e) {
                    if (!didWinDiscount || offerShown || offerAccepted) {
                        return;
                    }

                    var href = $(this).attr('href');
                    var target = ($(this).attr('target') || '').toLowerCase();
                    var isDownload = $(this).is('[download]');

                    if (!href || href === '#' || href.indexOf('javascript:') === 0) {
                        return;
                    }

                    if (href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) {
                        return;
                    }

                    if (target === '_blank' || isDownload) {
                        return;
                    }

                    var destinationUrl = new URL(href, window.location.href);
                    var currentUrl = new URL(window.location.href);

                    if (destinationUrl.href === currentUrl.href) {
                        return;
                    }

                    e.preventDefault();
                    pendingNavigationUrl = destinationUrl.href;
                    openDiscountOffer(false);
                });
            }

            $('#accept-extra-discount').on('click', function(e) {
                e.preventDefault();
                applyExtraDiscount();
            });

            $('#reject-extra-discount, #discount-offer-close').on('click', function(e) {
                e.preventDefault();
                closeDiscountModal();

                if (pendingNavigationUrl) {
                    var redirectUrl = pendingNavigationUrl;
                    pendingNavigationUrl = null;

                    setTimeout(function() {
                        window.location.href = redirectUrl;
                    }, 160);
                }
            });

            discountOverlay.on('click', function(e) {
                if (e.target === this) {
                    closeDiscountModal();
                }
            });
        });
    </script>
@endsection

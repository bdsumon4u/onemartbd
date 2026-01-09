@extends('frontEnd.layouts.master')

@section('title')
    Order Confirmed
@endsection
@section('style')
    <style>
        * {
            font-family: Li-Ador-Noirrit-R;
        }

        p, span, h5 {
            font-size: 17px;
            font-family: Li-Ador-Noirrit-R;
        }

        h5 {
            font-size: 20px;
        }
    </style>
@endsection
@section('gTag')
    @if(session()->has('api_purchase_data'))
        <script>
            dataLayer.push({ecommerce: null});  // Clear the previous ecommerce object.
            dataLayer.push({
                event: "purchase",
                customer: {
                    id: "{{session('api_purchase_data')['customer_id']}}",
                    first_name: "{{session('api_purchase_data')['full_name']}}",
                    last_name: "",
                    full_name: "{{session('api_purchase_data')['full_name']}}",
                    email: "{{session('api_purchase_data')['email']}}",
                    phone: "{{session('api_purchase_data')['phone']}}",
                    address: {
                        address_summary: "{{session('api_purchase_data')['address_summary']}}",
                        address1: "{{session('api_purchase_data')['address_summary']}}",
                        address2: "",
                        city: "",
                        street: "",
                        zip_code: "",
                        company: "",
                        country: "Bangladesh",
                        province: ""
                    }
                },
                ecommerce: {
                    transaction_id: "{{session('api_purchase_data')['invoice_id']}}",
                    value: {{number_format(session('api_purchase_data')['sub_total'],2,'.','')}},
                    tax: 0,
                    shipping: {{number_format(session('api_purchase_data')['shipping_cost'],2,'.','')}},
                    currency: "BDT",
                    coupon: "",
                    items: {!! session('api_purchase_data')['products'] !!}
                }
            });
        </script>
    @endif
@endsection
@section('body')
    <section class="py-md-5">
        <div class="cart-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 mx-md-auto">
                        @if(session()->has('order_info'))
                            <svg xmlns="http://www.w3.org/2000/svg" width="70" height="90" fill="#008000" class="bi bi-bag-check" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                      d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0z"></path>
                                <path
                                    d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z"></path>
                            </svg>
                            <p class="confirm-order-text">প্রিয়, <b>{{session('order_info')['name'] ?? null}}</b> <br>
                                আপনার অর্ডারটি গ্রহণ করা হয়েছে। <br>

                            </p>
                            <p class="confirm-order-text">
                                অর্ডার নাম্বার - <b>{{session('order_info')['order_id']??null}}</b> <br>
                                ডেলিভারি চার্জ সহ পণ্যের মূল্য - <b>{{session('order_info')['total']??null}} টাকা</b> <br>
                            </p>
                            <p class="confirm-order-text">
                                ধন্যবাদান্তে <br>
                                <b>{{env('APP_NAME')}}</b>
                            </p>
                            <br>
                        @endif
                        <a href="{{route('home')}}" class="btn btn-secondary px-5">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
        @if(!session()->has('order_info'))
            window.location.href = "{{route('home')}}";
        @endif
    </script>
@endsection

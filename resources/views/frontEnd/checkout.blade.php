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
                value: {{ session('api_begin_checkout_data')['value'] }},
                coupon: "",
                items: {!! session('api_begin_checkout_data')['products'] !!}
            }
        });
    </script>
@endsection
@section('body')
    @if (\Cart::getContent()->count() > 0)
        <section>
            <div class="cart-section">
                <div class="container">
                    <div class="row">
                        <div class="col-md-5 col-12 mb-md-0 mb-4">
                            <div class="card" style="border: none">
                                <div class="card-body p-2">
                                    <p class="text-center">অর্ডারটি কনফার্ম করতে আপনার নাম, ঠিকানা, মোবাইল নাম্বার, লিখে
                                        <span class="text-danger">অর্ডার কনফার্ম করুন</span> বাটনে ক্লিক করুন
                                    </p>
                                    <form action="{{ route('place.order') }}" method="post" id="checkout_form"
                                        class="checkout_form">
                                        @csrf
                                        <input type="hidden" name="shipping_cost" id="shipping_cost">
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
                                                   placeholder="আপনার মোবাইল নাম্বার লিখুন" value="{{ old('customer_phone') }}"
                                                   minlength="11" maxlength="11" required>
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

                                        <div class="form-group">
                                            <label for="shipping_method">আপনার এরিয়া সিলেক্ট করুন</label>
                                            <select name="shipping_method" id="shipping_method" class="form-control"
                                                required>
                                                @foreach ($shipping_methods as $item)
                                                    <option value="{{ $item->id }}">{{ $item->type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100 mb-2" style="height: 50px"
                                            id="conf_order_btn">অর্ডার কনফার্ম করুন</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7 col-12">
                            <div class="card" style="border: 1px solid #e9e9e9">
                                <h5 class="font-weight-bold card-header">আপনার অর্ডার</h5>
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
                                                        <input type="text" name="qty" id="qty" min="1"
                                                            value="{{ $item->quantity }}" readonly>
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

            function calculate() {
                var net_total = parseFloat($('#net_total').text());
                var cart_shipping_cost = parseFloat($('#cart_shipping_cost').text());
                $('#grand_total').text(net_total + cart_shipping_cost);
            }


            $("#checkout_form").submit(function() {
                $("#conf_order_btn").attr("disabled", true).text('সাবমিট হচ্ছে...');
            });

            $(".qty_plus").click(function() {
                var qty = $('#qty').val();
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ route('cart.item.plus') }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        id: $(this).data('id')
                    },
                    success: function(data) {
                        $('#order_info_table').empty();
                        $('#order_info_table').append(data);
                        calculate();
                    }
                });
            });

            $(".qty_minus").click(function() {
                var qty = $('#qty').val();
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ route('cart.item.minus') }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        id: $(this).data('id')
                    },
                    success: function(data) {
                        $('#order_info_table').empty();
                        $('#order_info_table').append(data);
                        calculate();
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
@endsection

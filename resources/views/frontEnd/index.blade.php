@extends('frontEnd.layouts.master')

@section('title')
    Home
@endsection

@section('body')
    <section>
        <div class="slider">
            <div class="container-fluid container-97">
                <div class="row">
                    <div class="col-md-12">
                        <div id="home_slider" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                @foreach ($sliders as $key => $item)
                                    <li data-target="#home_slider" data-slide-to="{{ $key }}"
                                        @if ($key == 0) class="active" @endif></li>
                                @endforeach
                            </ol>
                            <div class="carousel-inner">
                                @foreach ($sliders as $key => $item)
                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                        <img src="{{ $item->get_img ? asset($item->get_img->file_url) : asset('frontEnd/images/no_image.png') }}"
                                            class="d-block w-100" alt="">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="product_categories">
            <div class="container-fluid container-97">
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3">প্রোডাক্ট ক্যাটেগরীজ</h5>
                        <div class="horiz_cat">
                            <ul>
                                @foreach ($categories as $cat)
                                    <li>
                                        <a href="{{ route('single.category', $cat->id) }}">{{ $cat->category_name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="hot-deals-product-section">
            <div class="container-fluid container-97">
                <div class="row">
                    <div class="col-12">
                        <div class="hot-deals-inner-wrapper">
                            <div class="row mb-3">
                                <div class="col-md-6 col-6">
                                    <div class="hot-deals-gif">
                                        <img src="{{ asset('frontEnd/images/hot-deal-logo.gif') }}" alt="">
                                    </div>
                                </div>
                                <div class="col-md-6 col-6">
                                    <div class="all-hot-deals-btn text-right mt-2">
                                        <a href="{{ route('all.hot.deals') }}">সকল হট ডিল <i
                                                class="fa fa-angle-right"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row m-0">
                                <div class="owl-carousel">
                                    @foreach ($hot_deal_1 as $item)
                                        <div class="hot-deals-product">
                                            <div class="hot-deals-product-inner-wrapper text-center">
                                                @if ($item->sale_price > 0 && $item->sale_price != $item->price)
                                                    <?php
                                                    $percentage = round(100 - ($item->sale_price / $item->price) * 100);
                                                    ?>
                                                    <p class="float_price_2">{{ $percentage }}% Off</p>
                                                @endif
                                                <a href="{{ route('single.product', [$item->slug, $item->id]) }}">
                                                    <img src="{{ $item->get_thumb ? asset($item->get_thumb->file_url) : asset('frontEnd/images/no_image.png') }}"
                                                        alt="{{ $item->name }}">
                                                </a>
                                                @if ($item->sale_price != 0)
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <p class="font-weight-bold mb-0"
                                                            style="color: #b8b8b8">
                                                            {{ $web_settings->currency_sign }} {{ $item->sale_price }}</p>
                                                        <p class="font-weight-bold mb-0 ml-2"
                                                            style="color: #fca204;text-decoration: line-through">
                                                            {{ $web_settings->currency_sign }} {{ $item->price }}</p>
                                                    </div>
                                                @else
                                                    <p class="font-weight-bold mb-0"
                                                        style="color: #fca204">
                                                        {{ $web_settings->currency_sign }} {{ $item->price }}</p>
                                                @endif
                                                <p class="mb-0 prod_name"><a
                                                        href="{{ route('single.product', [$item->slug, $item->id]) }}">{{ $item->name }}</a>
                                                </p>
                                            </div>
                                            <form action="{{ route('add.cart', $item->id) }}" method="post"
                                                class="order-div">
                                                @csrf
                                                <input type="hidden" name="qty" value="1">
                                                <input type="submit" class="order_now_btn" name="order_now"
                                                    value="অর্ডার করুন">
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- <div class="owl-carousel mb-3">
                                @foreach ($hot_deal_1 as $item)
                                    <?php
                                    $percentage = round(100 - ($item->sale_price / $item->price) * 100);
                                    ?>
                                    <div class="hot-deals-product">
                                        <a href="{{route('single.product',[$item->slug,$item->id])}}">
                                            <div class="discount">
                                                <div class="discount-wrapper">
                                                    <img src="{{asset('frontEnd/images/flash-deal-percentage.png')}}" alt="">
                                                    <span>{{$percentage}}%</span> <br>
                                                    <span>ছাড়</span>
                                                </div>
                                            </div>
                                            <p class="float_price">{{$web_settings->currency_sign}} {{$item->sale_price}}</p>
                                            <img src="{{$item->get_thumb ? asset($item->get_thumb->file_url): asset('frontEnd/images/no_image.png')}}" alt="{{$item->name}}">
                                        </a>
                                    </div>
                                @endforeach
                            </div> --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($best_selling->count() > 0)
        <section>
            <div class="main-products-section">
                <div class="container-fluid container-97">
                    <div class="row mx-0">
                        <div class="col-md-12 d-flex justify-content-between align-items-center">
                            <h4 class="mb-3">Best Selling Products</h4>

                            {{-- <div class="all-hot-deals-btn ">
                                <a href="#">সকল প্রোডাক্ট <i
                                        class="fa fa-angle-right"></i></a>
                            </div> --}}


                        </div>
                    </div>
                    <div class="row m-0">
                        @foreach ($best_selling->take(6) as $item)
                            {{-- @dd($item) --}}
                            <div class="col-md-2 col-6 main-product">
                                <div class="main-product-inner-wrapper text-center">
                                    @if ($item->get_product->sale_price > 0 && $item->get_product->sale_price < $item->get_product->price)
                                        <?php
                                        $percentage = round(100 - ($item->get_product->sale_price / $item->get_product->price) * 100);
                                        ?>
                                        <p class="float_price_2">{{ $percentage }}% Off</p>
                                        {{-- @if ($item->start_date && $item->end_date)
                                     <div class="free_shipping">
                                     <p class="mb-0">Free Shipping</p>
                                    <p class="mb-0"> <span class="days"></span> D <span class="hours"></span> H
                                        <span class="mins"></span> M <span class="secs"></span> S
                                    </p>
                                    </div>
                                    @endif --}}
                                    @endif
                                    @if ($item->get_product->start_date && $item->get_product->end_date)
                                        <div class="free_shipping">
                                            <p class="mb-0">Free Shipping</p>
                                        </div>
                                    @endif
                                    <a
                                        href="{{ route('single.product', [$item->get_product->slug, $item->get_product->id]) }}">
                                        <img src="{{ $item->get_product->get_thumb ? asset($item->get_product->get_thumb->file_url) : asset('frontEnd/images/no_image.png') }}"
                                            alt="{{ $item->get_product->name }}">
                                    </a>
                                    @if ($item->get_product->sale_price != 0)
                                        <div class="d-flex justify-content-center align-items-center">
                                            <p class="mb-0" style="text-decoration: line-through;color: #b8b8b8">
                                                {{ $web_settings->currency_sign }} {{ $item->get_product->price }}</p>
                                            <p class="font-weight-bold mb-0 ml-2" style="color: #fca204">
                                                {{ $web_settings->currency_sign }} {{ $item->get_product->sale_price }}
                                            </p>
                                        </div>
                                    @else
                                        <p class="font-weight-bold mb-0" style="color: #fca204">
                                            {{ $web_settings->currency_sign }} {{ $item->get_product->price }}</p>
                                    @endif
                                    <p class="mb-0 prod_name"><a
                                            href="{{ route('single.product', [$item->get_product->slug, $item->get_product->id]) }}">{{ $item->get_product->name }}</a>
                                    </p>
                                </div>
                                <form action="{{ route('add.cart', $item->get_product->id) }}" method="post"
                                    class="order-div">
                                    @csrf
                                    <input type="hidden" name="qty" value="1">
                                    <input type="submit" class="order_now_btn" name="order_now" value="অর্ডার করুন">
                                </form>
                            </div>
                        @endforeach
                    </div>

                    {{-- <div class="row mt-md-4 mt-2">
                    <div class="col-12">
                        {{ $products->links() }}
                    </div>
                </div> --}}
                </div>
            </div>
        </section>
    @endif
    {{-- SECTIONS --}}
    @if ($sections->count() > 0)
        @foreach ($sections as $section)
            @if ($section->activeProducts->count() > 0)
                <section>
                    <div class="main-products-section">
                        <div class="container-fluid container-97">
                            <div class="row mx-0">
                                <div class="col-md-12 d-flex justify-content-between align-items-center">
                                    <h4 class="mb-3">{{ $section->name }}</h4>
                                </div>
                            </div>
                            <div class="row m-0">
                                @foreach ($section->activeProducts->take(12) as $item)
                                    <div class="col-md-2 col-6 main-product">
                                        <div class="main-product-inner-wrapper text-center">
                                            @if ($item->sale_price > 0 && $item->sale_price != $item->price)
                                                <?php
                                                $percentage = round(100 - ($item->sale_price / $item->price) * 100);
                                                ?>
                                                <p class="float_price_2">{{ $percentage }}% Off</p>
                                                </p>
                                            @endif
                                            @if ($item->start_date && $item->end_date)
                                                <div class="free_shipping">
                                                    <p class="mb-0">Free Shipping</p>
                                                </div>
                                            @endif
                                            <a href="{{ route('single.product', [$item->slug, $item->id]) }}">
                                                <img src="{{ $item->get_thumb ? asset($item->get_thumb->file_url) : asset('frontEnd/images/no_image.png') }}"
                                                    alt="{{ $item->name }}">
                                            </a>
                                            @if ($item->sale_price != 0)
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <p class="mb-0"
                                                        style="text-decoration: line-through;color: #b8b8b8">
                                                        {{ $web_settings->currency_sign }} {{ $item->price }}</p>
                                                    <p class="font-weight-bold mb-0 ml-2" style="color: #fca204">
                                                        {{ $web_settings->currency_sign }} {{ $item->sale_price }}</p>
                                                </div>
                                            @else
                                                <p class="font-weight-bold mb-0" style="color: #fca204">
                                                    {{ $web_settings->currency_sign }} {{ $item->price }}</p>
                                            @endif
                                            <p class="mb-0 prod_name"><a
                                                    href="{{ route('single.product', [$item->slug, $item->id]) }}">{{ $item->name }}</a>
                                            </p>
                                        </div>
                                        <form action="{{ route('add.cart', $item->id) }}" method="post"
                                            class="order-div">
                                            @csrf
                                            <input type="hidden" name="qty" value="1">
                                            <input type="submit" class="order_now_btn" name="order_now"
                                                value="অর্ডার করুন">
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @endforeach
    @endif

    @if ($categoryProducts->count() > 0)
        @foreach ($categoryProducts as $category)
            {{-- @dd($category) --}}
            @if ($category->get_products->count() > 0)
                <section>
                    <div class="main-products-section">
                        <div class="container-fluid container-97">
                            <div class="row mx-0">
                                <div class="col-md-12 d-flex justify-content-between align-items-center">
                                    <h4 class="mb-3">{{ $category->category_name }}</h4>

                                    <div class="all-hot-deals-btn ">
                                        <a href="{{ route('single.category', $category->id) }}">সকল প্রোডাক্ট <i
                                                class="fa fa-angle-right"></i></a>
                                    </div>


                                </div>
                            </div>
                            <div class="row m-0">
                                @foreach ($category->get_products->take(12) as $item)
                                    <div class="col-md-2 col-6 main-product">
                                        <div class="main-product-inner-wrapper text-center">
                                            <input type="hidden" class="end-date" value="{{ $item->end_date }}">
                                            @if ($item->sale_price > 0 && $item->sale_price != $item->price)
                                                <?php
                                                $percentage = round(100 - ($item->sale_price / $item->price) * 100);
                                                ?>
                                                <p class="float_price_2">{{ $percentage }}% Off</p>
                                                </p>
                                                {{-- @if ($item->start_date && $item->end_date)
                                                 <div class="free_shipping">
                                                 <p class="mb-0">Free Shipping</p>
                                                <p class="mb-0"> <span class="days"></span> D <span class="hours"></span> H
                                                    <span class="mins"></span> M <span class="secs"></span> S
                                                </p>
                                                </div>
                                                @endif --}}
                                            @endif
                                            @if ($item->start_date && $item->end_date)
                                                <div class="free_shipping">
                                                    <p class="mb-0">Free Shipping</p>
                                                </div>
                                            @endif
                                            <a href="{{ route('single.product', [$item->slug, $item->id]) }}">
                                                <img src="{{ $item->get_thumb ? asset($item->get_thumb->file_url) : asset('frontEnd/images/no_image.png') }}"
                                                    alt="{{ $item->name }}">
                                            </a>
                                            @if ($item->sale_price != 0)
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <p class="mb-0"
                                                        style="text-decoration: line-through;color: #b8b8b8">
                                                        {{ $web_settings->currency_sign }} {{ $item->price }}</p>
                                                    <p class="font-weight-bold mb-0 ml-2" style="color: #fca204">
                                                        {{ $web_settings->currency_sign }} {{ $item->sale_price }}</p>
                                                </div>
                                            @else
                                                <p class="font-weight-bold mb-0" style="color: #fca204">
                                                    {{ $web_settings->currency_sign }} {{ $item->price }}</p>
                                            @endif
                                            <p class="mb-0 prod_name"><a
                                                    href="{{ route('single.product', [$item->slug, $item->id]) }}">{{ $item->name }}</a>
                                            </p>
                                        </div>
                                        <form action="{{ route('add.cart', $item->id) }}" method="post"
                                            class="order-div">
                                            @csrf
                                            <input type="hidden" name="qty" value="1">
                                            <input type="submit" class="order_now_btn" name="order_now"
                                                value="অর্ডার করুন">
                                        </form>
                                    </div>
                                @endforeach
                            </div>

                            {{-- <div class="row mt-md-4 mt-2">
                                <div class="col-12">
                                    {{ $products->links() }}
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </section>
            @endif
        @endforeach
    @endif
@endsection



@section('script')
    <script>
        var date = $('.end-date').val();
        var count_date = new Date(date).getTime();
        var x = setInterval(() => {
            var now = new Date().getTime();
            var distance = count_date - now;
            var day = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hour = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var min = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var sec = Math.floor((distance % (1000 * 60)) / 1000);

            $('.days').text(day);
            $('.hours').text(hour);
            $('.mins').text(min);
            $('.secs').text(sec);

            if (distance < 0) {
                clearInterval(x);
                $('.days').text('0');
                $('.hours').text('0');
                $('.mins').text('0');
                $('.secs').text('0');
            }

        }, 1000);
        // console.log(count_date);
    </script>
    <script>
        $(document).ready(function() {
            $(".owl-carousel").owlCarousel({
                margin: 15,
                loop: true,
                dots: false,
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                responsiveClass: true,
                responsive: {
                    0: {
                        margin: 10,
                        items: 2,
                        nav: true
                    },
                    600: {
                        items: 3,
                        nav: false
                    },
                    1000: {
                        items: 6,
                        nav: true,
                        loop: false
                    }
                }
            });

            $('.owl-nav').remove();
        });
    </script>
@endsection

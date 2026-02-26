@php
    $categories = \App\Models\Category::tree();
@endphp
<header>
    @if (!request()->is('product*'))
        <div class="header-top">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="header-left d-flex">
                            @if ($web_settings->marquee_text)
                                <marquee behavior="scroll" direction="left" scrollamount="6">{!! str_repeat($web_settings->marquee_text . str_repeat('&nbsp;', 33), 6) !!}
                                </marquee>
                            @else
                                <p>Welcome to {{ config('app.name') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="header-right">
                            <ul>
                                <li>
                                    <i class="fa fa-search"></i>
                                    <a href="{{ route('track.order') }}">
                                        <span>Track Order</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="header">
        <div class="container">
            <div class="row">
                <div class="col-1 d-md-none cat_menu_btn_m">
                    <ul>
                        <li>
                            <a href="#" class=" pull-bs-canvas-left"><i class="fa fa-bars"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-3 col-6 logo-m">
                    <div class="logo">
                        <a href="{{ route('home') }}"><img
                                src="{{ $web_settings->get_logo ? asset($web_settings->get_logo->file_url) : asset('frontEnd/images/no_image.png') }}"
                                alt=""></a>
                    </div>
                </div>

                <div class="col-md-6 py-md-3 d-none d-md-block">
                    <div class="search">
                        <form action="{{ route('search') }}" method="get">
                            <select name="category" id="category" class="search-select">
                                <option value="">ক্যাটেগরীজ</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="query" class="search-input" placeholder="সার্চ করুন">
                            <button type="submit" class="search-btn"></button>
                        </form>
                    </div>
                </div>

                <div class="col-md-3 text-md-right text-center py-3 d-none d-md-block">
                    <span class="cart-number d-none d-md-inline-block"><i class="fa fa-phone"></i>
                        {{ $web_settings->website_phone ?? null }}</span>
                    <div class="cart d-inline-block position-relative">
                        @if (Cart::getContent()->count() > 0)
                            <span class="badge badge-danger rounded-circle">{{ Cart::getContent()->count() }}</span>
                        @endif
                        <a href="{{ route('checkout') }}"><i style="color: #fff"
                                class="fa fa-2x fa-shopping-cart"></i></a>
                    </div>
                </div>

                <div class="col-5 d-md-none logo_right_options">
                    <ul>
                        <li>
                            <i class="fa fa-search" id="search_mobile_btn"></i>
                        </li>
                        <li class="d-none">
                            <a href="tel:{{ $web_settings->website_phone ?? null }}"><i
                                    class="fa fa-phone-square"></i></a>
                        </li>
                        <li>
                            <span class="cart-number d-none d-md-inline-block"><i class="fa fa-phone"></i>
                                {{ $web_settings->website_phone ?? null }}</span>
                            <div class="cart d-inline-block position-relative">
                                @if (Cart::getContent()->count() > 0)
                                    <span
                                        class="badge badge-danger rounded-circle">{{ Cart::getContent()->count() }}</span>
                                @endif
                                <a href="{{ route('checkout') }}"><i style="color: #fff"
                                        class="fa fa-2x fa-shopping-cart"></i></a>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
        </div>

        <div class="bs-canvas bs-canvas-left position-fixed bg-light h-100">
            <header class="bs-canvas-header">
                <h4 class="d-inline-block text-light mb-0">Categories</h4>
                <button type="button" class="bs-canvas-close close" aria-label="Close"><span aria-hidden="true"
                        class="text-light">&times;</span></button>
            </header>
            <div class="bs-canvas-content">
                <div class="cat_menu_m">
                    <ul>
                        <li>
                            <a href="{{ route('home') }}">Home</a>
                        </li>
                        @foreach ($categories as $cat)
                            <li>
                                <a href="{{ route('single.category', $cat->id) }}">{{ $cat->category_name }}</a>
                                @if (count($cat->children) > 0)
                                    <button class="toggle-children" onclick="toggleChildren(this)">+</button>
                                    <div class="sub-category-m" style="display: none;">
                                        <ul>
                                            @include('frontEnd.inc.child_category_m', [
                                                'child_categories' => $cat->children,
                                            ])
                                        </ul>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('track.order') }}" class="btn btn-dark btn-sm w-100 mt-5">Track Order</a>
                </div>

            </div>
        </div>

        <div class="search-form-m">
            <form action="{{ route('search') }}" method="get">
                <input type="text" name="query" class="form-control" placeholder="সার্চ করুন" autocomplete="off">
                <button type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>
            {{-- <input type="text" name="q" id="searchMf" class="form-control" value="" placeholder="সার্চ করুন" autocomplete="off"><span
                role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
            <button type="submit">
                <i class="fa fa-search"></i>
            </button> --}}
            <button class="search_btnclose">
                <i class="fa fa-times-circle"></i>
            </button>
        </div>
    </div>

    <div class="header-bottom d-md-block d-none">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="cat_menu">
                        <ul>
                            <li>
                                <a href="{{ route('home') }}">Home</a>
                            </li>
                            @foreach ($categories as $cat)
                                <li>
                                    <a href="{{ route('single.category', $cat->id) }}">{{ $cat->category_name }}
                                        @if (count($cat->children) > 0)
                                            <span class="caret"></span>
                                        @endif
                                    </a>
                                    @if (count($cat->children) > 0)
                                        <button class="toggle-children" onclick="toggleChildren(this)">+</button>
                                        <div class="sub_category">
                                            <ul>
                                                @include('frontEnd.inc.child_category', [
                                                    'child_categories' => $cat->children,
                                                ])
                                            </ul>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleChildren(button) {
        var subCategory = button.nextElementSibling;
        if (subCategory.style.display === "none") {
            button.textContent = "-";
            subCategory.classList.toggle('animate');
        } else {
            subCategory.style.display = "none";
            button.textContent = "+";
        }

    }
</script>

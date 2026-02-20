<!doctype html>
<html lang="en">
<head>
    {!! $web_settings->gtm_script_head !!}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{config('app.name')}} - @yield('title')</title>
    <meta name="description"
          content="">
    <meta property="og:site_name" content="{{env('APP_NAME')}}">
    <meta property="og:image" content="{{asset($web_settings->get_logo->file_url)}}">
    <meta property="og:title" content="{{env('APP_NAME')}}">
    <meta property="og:description"
          content="">
    <meta property="og:url" content="{{env('APP_URL')}}">
    <meta property="og:type" content="e-commerce">
    <meta name="twitter:title" content="{{env('APP_NAME')}}">
    <meta name="twitter:description"
          content="">

    <link rel="shortcut icon" href="{{$web_settings->get_fav ? asset($web_settings->get_fav->file_url) : asset('frontEnd/images/no_image.png')}}">
    {{--Google fonts--}}
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">
    {{--Font Awesome--}}
    <link href="{{asset('frontEnd/plugins/font-awesome/font-awesome.css')}}" rel="stylesheet">
    {{--Bootstrap CSS--}}
    <link rel="stylesheet" href="{{asset('frontEnd/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontEnd/css/style.css')}}">
    {{--Owl-Carousel--}}
    <link rel="stylesheet" href="{{asset('frontEnd/plugins/owl-carousel/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontEnd/plugins/owl-carousel/owl.theme.default.min.css')}}">
    {{--toastr--}}{{--
    <link rel="stylesheet" type="text/css" href="{{asset('/')}}backEnd/assets/vendor/toastr/toastr.min.css">--}}

    @yield('style')
    <style>
        :root {
            --primary-color: {{$web_settings->primary_color}};
            --secondary-color: {{$web_settings->secondary_color}};
        }

        .header-top {
            background: {{$web_settings->header_top_color}};
        }

        .header {
            background: {{$web_settings->header_color}};
        }

        .header-bottom {
            background: {{$web_settings->header_bottom_color}};
        }

        .order-div .order_now_btn {
            background: {{$web_settings->button_color}}

        }

        .order_now_btn:hover {
            background: {{$web_settings->button_hover_color}}

        }
    </style>
    @yield('gTag')

    <x-metapixel-head/>
</head>
<body>
<x-metapixel-body/>
{!! $web_settings->gtm_script_body !!}
@yield('fb_share')
<div class="main-wrapper">
    @include('frontEnd.inc.header')

    @yield('body')

    @include('frontEnd.inc.footer')
</div>

{{--Back to top button--}}
<a id="back-to-top-btn"></a>

<a style="-webkit-appearance: none;" target="_blank" type="button" id="live_chat_btn" href="https://api.whatsapp.com/send?phone={{$web_settings->whatsapp_number}}">
    <img class="wapp_chat" src="{{asset('frontEnd/images/wapp_logo.png')}}" alt="Whats App Chat">
</a>
{{--<script src="{{asset('frontEnd/js/jquery.slim.min.js')}}"></script>--}}
<script src="{{asset('frontEnd/js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('frontEnd/js/bootstrap.bundle.min.js')}}"></script>

{{--Owl-Carousel--}}
<script src="{{asset('frontEnd/plugins/owl-carousel/owl.carousel.min.js')}}"></script>

{{--Sweet Alert--}}
<script src="{{asset('frontEnd/js/sweetalert.min.js')}}"></script>

<script>
    $(document).ready(function () {
        $('.order_now_btn, .add_cart_btn').click(function () {
            swal({
                title: "Product Added To Cart Successfully",
                icon: "success",
                timer: 500
            });

        });

        var back_to_top_btn = $('#back-to-top-btn');

        $(window).scroll(function () {
            if ($(window).scrollTop() > 600) {
                back_to_top_btn.addClass('show');
            } else {
                back_to_top_btn.removeClass('show');
            }
        });

        back_to_top_btn.on('click', function (e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, '300');
        });

    });
</script>

{{--toastr--}}
{{--<script src="{{asset('/')}}backEnd/assets/vendor/toastr/toastr.min.js"></script>
<script>
    @if(session()->has('success'))
        toastr.options = {
        "positionClass": "toast-bottom-left"
    };
    toastr.success("{{ session('success') }}");
    @endif
        @if(Session::has('error'))
        toastr.options = {
        "positionClass": "toast-bottom-left"
    };
    toastr.error("{{ session('error') }}");
    @endif
        @if(Session::has('info'))
        toastr.options = {
        "positionClass": "toast-bottom-left"
    };
    toastr.info("{{ session('info') }}");
    @endif
        @if(Session::has('warning'))
        toastr.options = {
        "positionClass": "toast-bottom-left"
    };
    toastr.warning("{{ session('warning') }}");
    @endif
</script>--}}

<script>
    $('#account-btn').on('click', function () {
        $('.login-float').toggle()
    });

    $('#header-top-menu-btn').on('click', function () {
        $('.header-top-menu-m').toggle()
    });

    $('#cat_menu_mobile_btn').on('click', function () {
        $('.cat_menu_m').toggle()
    });

    $('#search_mobile_btn').on('click', function () {
        $('.search-form-m').toggle()
    });

    $('.search_btnclose').on('click', function () {
        $('.search-form-m').toggle()
    });
</script>

<script>
    $(document).on('click', '.pull-bs-canvas-left', function () {
        $('body').prepend('<div class="bs-canvas-overlay bg-dark position-fixed w-100 h-100"></div>');
        if ($(this).hasClass('pull-bs-canvas-right'))
            $('.bs-canvas-right').addClass('mr-0');
        else
            $('.bs-canvas-left').addClass('ml-0');
        return false;
    });

    $(document).on('click', '.bs-canvas-close, .bs-canvas-overlay', function () {
        var elm = $(this).hasClass('bs-canvas-close') ? $(this).closest('.bs-canvas') : $('.bs-canvas');
        elm.removeClass('mr-0 ml-0');
        $('.bs-canvas-overlay').remove();
        return false;
    });
</script>

<script>
    function toggleChildren(button) {
        const subCategory = button.nextElementSibling;
        if (subCategory.style.display === "none") {
            subCategory.style.display = "block";
            button.textContent = "-";
        } else {
            subCategory.style.display = "none";
            button.textContent = "+";
        }
    }
</script>

@yield('script')
</body>
</html>

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

        .floating-contact {
            position: fixed;
            right: 24px;
            bottom: 100px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .fab-btn {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: none;
            background-color: #ff5722;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .fab-btn img {
            width: 32px;
            height: 32px;
        }

        .contact-icons {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .contact-icons.show {
            display: flex;
        }

        .contact-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-decoration: none;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        }

        .contact-icon.whatsapp {
            background-color: #25d366;
        }

        .contact-icon.messenger {
            background-color: #0084ff;
        }

        .contact-icon.phone {
            background-color: #000000;
        }

        @media (max-width: 575.98px) {
            .floating-contact {
                right: 16px;
                bottom: 70px;
            }

            .fab-btn {
                width: 52px;
                height: 52px;
            }
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

@php
    $hasWhatsapp = filled($web_settings->whatsapp_number);
    $hasMessenger = filled($web_settings->messenger_link);
    $whatsappNumber = preg_replace('/[^0-9]/', '', $web_settings->whatsapp_number ?? '');
    $isSingleProductPage = request()->routeIs('single.product') && isset($data);
    $whatsappLink = "https://api.whatsapp.com/send?phone={$whatsappNumber}";

    if ($isSingleProductPage) {
        $productName = $data->name ?? '';
        $productUrl = url()->current();
        $whatsappMessage = rawurlencode("Hello, I'm interested in this product: {$productName} {$productUrl}");
        $whatsappLink .= "&text={$whatsappMessage}";
    }
@endphp

@if($hasWhatsapp && $hasMessenger)
    <div class="floating-contact">
        <div class="contact-icons" id="contactIcons">
            <a href="{{ $whatsappLink }}" target="_blank" class="contact-icon whatsapp" title="WhatsApp">
                <svg width="32" height="32" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                    <path fill="#ffffff" d="M16.04 5C10.51 5 6 9.35 6 14.76c0 2.16.73 4.16 2 5.8L7 27l6.66-2.1c1.01.28 2.08.44 3.18.44 5.53 0 10.04-4.35 10.04-9.76C26.88 9.35 21.57 5 16.04 5Zm0 16.9c-.94 0-1.86-.15-2.73-.45l-.2-.07-3.94 1.24 1.28-3.73-.13-.19a7.16 7.16 0 0 1-1.2-3.97c0-4.03 3.34-7.3 7.46-7.3 4.11 0 7.46 3.27 7.46 7.3 0 4.02-3.35 7.3-7.46 7.3Zm3.99-5.46c-.22-.11-1.3-.64-1.5-.71-.2-.07-.35-.11-.49.11-.15.22-.56.71-.69.86-.13.15-.26.17-.48.06-.22-.11-.93-.34-1.77-1.06-.65-.57-1.09-1.27-1.22-1.48-.13-.22-.01-.33.1-.44.1-.1.22-.26.33-.39.11-.13.15-.22.22-.37.07-.15.04-.28-.02-.39-.07-.11-.49-1.18-.67-1.62-.18-.44-.36-.37-.49-.37h-.42c-.15 0-.39.06-.59.28-.2.22-.78.76-.78 1.85 0 1.09.8 2.14.9 2.29.11.15 1.57 2.39 3.82 3.26.53.22.95.35 1.28.45.54.17 1.04.15 1.43.09.44-.07 1.3-.53 1.48-1.05.18-.52.18-.96.13-1.05-.04-.09-.2-.15-.42-.26Z"/>
                </svg>
            </a>

            {{-- Phone Call --}}
            <a href="tel:{{ $web_settings->website_phone }}" target="_blank" class="contact-icon phone" title="Phone">
                <svg width="36" height="36" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                    <circle cx="18" cy="18" r="18" />
                    <path fill="#ffffff" d="M23.44 20.77l-2.36-1.08a1.22 1.22 0 0 0-1.42.26l-.98 1a10.05 10.05 0 0 1-4.82-4.82l1-1a1.22 1.22 0 0 0 .26-1.42l-1.08-2.36a1.21 1.21 0 0 0-1.32-.69c-1.1.22-1.9 1.19-1.9 2.31 0 6.07 4.93 11 11 11 1.13 0 2.1-.8 2.31-1.9a1.21 1.21 0 0 0-.69-1.32z"/>
                </svg>
            </a>

            <a href="{{ $web_settings->messenger_link }}" target="_blank" class="contact-icon messenger" title="Messenger">
                <svg width="32" height="32" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                    <path fill="#ffffff" d="M16 4C9.37 4 4 8.96 4 15.02c0 3.44 1.65 6.43 4.35 8.39V28l3.97-2.18c1.13.32 2.33.5 3.68.5 6.63 0 12-4.96 12-11.02C28 8.96 22.63 4 16 4Zm1.01 13.4-2.92-3.13-5.7 3.13 6.24-6.62 2.97 3.13 5.63-3.13-6.22 6.62Z"/>
                </svg>
            </a>
        </div>
        <button class="fab-btn" id="contactToggle" type="button" aria-label="Open chat options">
            <svg chat-icon version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="-496 507.7 54 54" xml:space="preserve"><style type="text/css">.chaty-sts4-0{fill: #ffffff;}.chaty-st0{fill: #808080;}</style><g><circle cx="-469" cy="534.7" r="27" fill="#A886CD"></circle></g><path class="chaty-sts4-0" d="M-459.9,523.7h-20.3c-1.9,0-3.4,1.5-3.4,3.4v15.3c0,1.9,1.5,3.4,3.4,3.4h11.4l5.9,4.9c0.2,0.2,0.3,0.2,0.5,0.2 h0.3c0.3-0.2,0.5-0.5,0.5-0.8v-4.2h1.7c1.9,0,3.4-1.5,3.4-3.4v-15.3C-456.5,525.2-458,523.7-459.9,523.7z"></path><path class="chaty-st0" d="M-477.7,530.5h11.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-11.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,530.8-478.2,530.5-477.7,530.5z"></path><path class="chaty-st0" d="M-477.7,533.5h7.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-7.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,533.9-478.2,533.5-477.7,533.5z"></path></svg>
            <svg close-icon class="d-none" width="32" height="32" viewBox="0 0 20 20" aria-hidden="true" focusable="false">
                <line x1="4" y1="4" x2="16" y2="16" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
                <line x1="16" y1="4" x2="4" y2="16" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
@elseif($hasWhatsapp)
    <div class="floating-contact">
        <a href="{{ $whatsappLink }}" target="_blank" class="fab-btn contact-icon whatsapp" title="WhatsApp">
            <svg width="32" height="32" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                <path fill="#ffffff" d="M16.04 5C10.51 5 6 9.35 6 14.76c0 2.16.73 4.16 2 5.8L7 27l6.66-2.1c1.01.28 2.08.44 3.18.44 5.53 0 10.04-4.35 10.04-9.76C26.88 9.35 21.57 5 16.04 5Zm0 16.9c-.94 0-1.86-.15-2.73-.45l-.2-.07-3.94 1.24 1.28-3.73-.13-.19a7.16 7.16 0 0 1-1.2-3.97c0-4.03 3.34-7.3 7.46-7.3 4.11 0 7.46 3.27 7.46 7.3 0 4.02-3.35 7.3-7.46 7.3Zm3.99-5.46c-.22-.11-1.3-.64-1.5-.71-.2-.07-.35-.11-.49.11-.15.22-.56.71-.69.86-.13.15-.26.17-.48.06-.22-.11-.93-.34-1.77-1.06-.65-.57-1.09-1.27-1.22-1.48-.13-.22-.01-.33.1-.44.1-.1.22-.26.33-.39.11-.13.15-.22.22-.37.07-.15.04-.28-.02-.39-.07-.11-.49-1.18-.67-1.62-.18-.44-.36-.37-.49-.37h-.42c-.15 0-.39.06-.59.28-.2.22-.78.76-.78 1.85 0 1.09.8 2.14.9 2.29.11.15 1.57 2.39 3.82 3.26.53.22.95.35 1.28.45.54.17 1.04.15 1.43.09.44-.07 1.3-.53 1.48-1.05.18-.52.18-.96.13-1.05-.04-.09-.2-.15-.42-.26Z"/>
            </svg>
        </a>
    </div>
@elseif($hasMessenger)
    <div class="floating-contact">
        <a href="{{ $web_settings->messenger_link }}" target="_blank" class="fab-btn contact-icon messenger" title="Messenger">
            <svg width="32" height="32" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                <path fill="#ffffff" d="M16 4C9.37 4 4 8.96 4 15.02c0 3.44 1.65 6.43 4.35 8.39V28l3.97-2.18c1.13.32 2.33.5 3.68.5 6.63 0 12-4.96 12-11.02C28 8.96 22.63 4 16 4Zm1.01 13.4-2.92-3.13-5.7 3.13 6.24-6.62 2.97 3.13 5.63-3.13-6.22 6.62Z"/>
            </svg>
        </a>
    </div>
@endif
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

        $('#contactToggle').on('click', function () {
            $('#contactIcons').toggleClass('show');
            $(this).find('[chat-icon]').toggleClass('d-none');
            $(this).find('[close-icon]').toggleClass('d-none');
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

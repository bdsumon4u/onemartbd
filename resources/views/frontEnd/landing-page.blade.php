<!DOCTYPE html>
<html lang="en">

<head>
    {!! $web_settings->gtm_script_head !!}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $landingPage->title }} - {{ config('app.name') }}</title>

    <link rel="shortcut icon"
        href="{{ $web_settings->get_fav ? asset($web_settings->get_fav->file_url) : asset('frontEnd/images/no_image.png') }}">
    <link rel="stylesheet" href="{{ asset('frontEnd/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontEnd/plugins/font-awesome/font-awesome.css') }}">

    <style>
        * {
            font-family: Li-Ador-Noirrit-R, 'Helvetica Neue', Arial, sans-serif;
            box-sizing: border-box;
        }

        body {
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .lp-container {
            max-width: 768px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* ---- Hero Banner (full width) ---- */
        .lp-hero-section {
            background-color: #2d5f2e;
            color: white;
            padding: 40px 15px 30px;
            text-align: center;
        }

        .lp-hero-inner {
            max-width: 768px;
            margin: 0 auto;
        }

        .lp-hero-title-box {
            border: 2px dashed #f0c040;
            border-radius: 16px;
            padding: 30px 24px;
            margin-bottom: 20px;
        }

        .lp-hero-title-box h1 {
            font-size: 2.25rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.5;
        }

        .lp-hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin: 0;
            line-height: 1.7;
        }

        /* ---- Countdown ---- */
        .lp-countdown-section {
            padding: 22px 15px;
            text-align: center;
            background: #fff;
        }

        .lp-countdown-section .countdown-label {
            font-size: 1.5rem;
            font-weight: 700;
            color: #e65100;
            margin-bottom: 12px;
        }

        .lp-countdown-boxes {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .lp-countdown-box {
            background: white;
            border: 2px solid #000;
            border-radius: 10px;
            padding: 16px 28px;
            min-width: 76px;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .lp-countdown-box .count {
            font-size: 2.2rem;
            font-weight: 700;
            color: #e65100;
            display: block;
            line-height: 1.2;
        }

        .lp-countdown-box .label {
            font-size: 1rem;
            color: #444;
            display: block;
            margin-top: 10px;
        }

        /* ---- Banner Image / Video ---- */
        .lp-banner-section {
            padding: 0 15px 22px;
            background: #fff;
        }

        .lp-banner-img,
        .lp-banner-video {
            width: 100%;
            border-radius: 12px;
            display: block;
            max-width: 700px;
            margin: 0 auto;
        }

        .lp-video-wrap {
            position: relative;
            max-width: 700px;
            margin: 0 auto;
        }

        .lp-unmute-overlay {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.65);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            font-size: 1.2rem;
            transition: background 0.2s;
            animation: lp-pulse 1.5s infinite;
        }

        .lp-unmute-overlay:hover {
            background: rgba(0, 0, 0, 0.85);
        }

        .lp-unmute-overlay.hidden {
            display: none;
        }

        @keyframes lp-pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.15);
            }
        }

        /* ---- Section Cards ---- */
        .lp-section-wrap {
            padding: 0 15px 26px;
        }

        .lp-section-card {
            border: 2px solid #2d5f2e;
            border-radius: 14px;
            overflow: hidden;
            max-width: 768px;
            margin: 0 auto;
        }

        .lp-section-header {
            background-color: #2d5f2e;
            color: white;
            padding: 14px 20px;
            font-size: 1.2rem;
            font-weight: 700;
            text-align: center;
        }

        .lp-section-body {
            background: white;
            padding: 22px;
            font-size: 0.95rem;
            line-height: 1.7;
            color: #333;
        }

        /* ---- Gallery ---- */
        .lp-gallery-img {
            width: 100%;
            border-radius: 8px;
            object-fit: contain;
            max-height: 420px;
            background: #f8f8f8;
            display: block;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(0, 0, 0, 0.25);
            border-radius: 50%;
            padding: 18px;
        }

        .carousel-indicators li {
            background-color: #2d5f2e;
        }

        .lp-gallery-thumbs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .lp-gallery-thumbs img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #e0e0e0;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        @media (min-width: 768px) {
            .lp-gallery-thumbs img {
                width: 60px;
                height: 60px;
            }
        }

        .lp-gallery-thumbs img.active,
        .lp-gallery-thumbs img:hover {
            border-color: #2d5f2e;
        }

        /* ---- Order Button (centered, auto-width) ---- */
        .lp-order-btn-section {
            text-align: center;
            padding: 15px 15px 15px;
            background: #fff;
        }

        @keyframes zoom-in-out {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.06);
            }

            100% {
                transform: scale(1);
            }
        }

        .btn-lp-order {
            background: linear-gradient(135deg, #e65100, #f44336);
            border: none;
            padding: 14px 50px;
            font-size: 1.15rem;
            font-weight: 700;
            border-radius: 8px;
            color: white;
            animation: zoom-in-out 1.5s infinite;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
        }

        .btn-lp-order:hover {
            color: white;
            text-decoration: none;
            animation: none;
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(230, 81, 0, 0.45);
            filter: brightness(1.1);
        }

        /* ---- Price Box ---- */
        .lp-price-section {
            padding: 0 15px 8px;
        }

        .lp-price-box {
            background: #f5f5f5;
            border: 2px solid #2d5f2e;
            border-radius: 16px;
            padding: 28px 24px 20px;
            text-align: center;
            max-width: 768px;
            margin: 0 auto;
        }

        .lp-price-row {
            display: flex;
            align-items: stretch;
            justify-content: center;
            gap: 0;
            margin-bottom: 20px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .lp-price-tag {
            flex: 1;
            padding: 12px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .lp-price-tag-regular {
            background: linear-gradient(135deg, #e65100, #f44336);
            color: white;
        }

        .lp-price-tag-regular .lp-price-tag-label {
            color: rgba(255, 255, 255, 0.85);
        }

        .lp-price-tag-discount {
            background: white;
        }

        .lp-price-tag-discount .lp-price-tag-label {
            color: #666;
        }

        .lp-price-tag-label {
            font-size: 0.85rem;
            display: block;
            font-weight: 600;
        }

        .lp-price-regular {
            font-size: 2rem;
            font-weight: 700;
            display: block;
            text-decoration: line-through;
        }

        .lp-price-regular-only {
            background: #d4690e;
            color: white;
            padding: 16px 40px;
            border-radius: 12px;
            font-size: 2rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 20px;
        }

        .lp-price-sale {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1b5e20;
            display: block;
        }

        .lp-price-meta {
            font-size: 1.25rem;
            color: #2d5f2e;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .lp-price-meta i {
            margin-right: 8px;
            color: #2d5f2e;
        }

        .lp-price-meta a {
            color: #2d5f2e;
            text-decoration: none;
            font-weight: 700;
        }

        .lp-price-meta a:hover {
            text-decoration: underline;
        }

        /* ---- Order Card ---- */
        .lp-order-section {
            padding: 10px 15px 30px;
        }

        .lp-order-card {
            background: white;
            border: 2px solid #2d5f2e;
            border-radius: 14px;
            overflow: hidden;
            max-width: 768px;
            margin: 0 auto;
        }

        .lp-order-card-header {
            background: #2d5f2e;
            color: white;
            padding: 14px 20px;
            font-size: 1.1rem;
            font-weight: 700;
            text-align: center;
        }

        .lp-order-form-body {
            padding: 24px;
        }

        /* ---- Order Summary ---- */
        .lp-order-summary {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }

        .lp-order-summary table {
            margin-bottom: 0;
            font-size: 0.88rem;
        }

        .lp-qty-control {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 6px;
        }

        .lp-qty-btn {
            background: #2d5f2e;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .lp-qty-input {
            width: 46px;
            text-align: center;
            border: 1px solid #ced4da;
            height: 30px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.95rem;
        }

        /* ---- COD Payment Card ---- */
        .lp-cod-card {
            background: #f8f9fa;
            border: 1.5px solid #dee2e6;
            border-radius: 12px;
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 16px;
        }

        .lp-cod-icon {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lp-cod-icon svg {
            width: 32px;
            height: 32px;
        }

        .lp-cod-text strong {
            font-size: 1.05rem;
            display: block;
            margin-bottom: 2px;
        }

        .lp-cod-text small {
            color: #6c757d;
        }

        /* ---- Confirm Button ---- */
        .btn-lp-confirm {
            background: linear-gradient(135deg, #e65100, #f44336);
            color: white;
            border: none;
            padding: 14px 30px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 8px;
            width: 90%;
            margin: 0 auto;
            display: block;
            text-align: center;
            cursor: pointer;
            margin-top: 16px;
            transition: box-shadow 0.2s, filter 0.2s, transform 0.2s;
            animation: lp-pulse 1.5s infinite;
        }

        .btn-lp-confirm:hover {
            background: linear-gradient(135deg, #bf360c, #d32f2f);
            color: white;
            box-shadow: 0 4px 16px rgba(230, 81, 0, 0.4);
            filter: brightness(1.08);
        }

        /* ---- Footer ---- */
        .lp-footer {
            background: #1b2631;
            color: #ccc;
            padding: 30px 15px;
            text-align: center;
        }

        .lp-footer .footer-brand {
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .lp-footer .footer-brand i {
            margin-right: 6px;
        }

        .lp-footer .footer-address {
            font-size: 0.82rem;
            color: #aaa;
            margin-bottom: 6px;
        }

        .lp-footer .footer-contact {
            font-size: 0.85rem;
            color: #bbb;
            margin-bottom: 6px;
        }

        .lp-footer .footer-contact i {
            margin-right: 4px;
        }

        .lp-footer .footer-contact span {
            margin: 0 8px;
            color: #666;
        }

        .lp-footer .footer-copy {
            font-size: 0.72rem;
            color: #666;
            margin-top: 10px;
        }

        /* ---- Responsive ---- */
        @media (max-width: 576px) {
            .lp-hero-title-box h1 {
                font-size: 1.45rem;
            }

            .lp-hero-title-box {
                padding: 20px 16px;
            }

            .lp-gallery-img {
                max-height: 280px;
            }

            .lp-price-row {
                flex-wrap: wrap;
                gap: 14px;
            }

            .btn-lp-order {
                padding: 12px 36px;
                font-size: 1.05rem;
            }
        }


        /* ---- Floating Contact Button ---- */

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

        /* ---- FAQ Section ---- */
        .lp-faq-section {
            padding: 30px 15px 10px;
            max-width: 768px;
            margin: 0 auto;
        }

        .lp-faq-section h2 {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #222;
        }

        .lp-faq-item {
            background: #f8f6f2;
            border-radius: 12px;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .lp-faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            cursor: pointer;
            font-size: 17px;
            font-weight: 600;
            color: #333;
            user-select: none;
            gap: 10px;
        }

        .lp-faq-question .lp-faq-icon {
            font-size: 20px;
            transition: transform 0.3s ease;
            flex-shrink: 0;
            color: #666;
        }

        .lp-faq-item.active .lp-faq-icon {
            transform: rotate(180deg);
        }

        .lp-faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease, padding 0.35s ease;
            padding: 0 20px;
            font-size: 15px;
            line-height: 1.6;
            color: #555;
        }

        .lp-faq-item.active .lp-faq-answer {
            max-height: 300px;
            padding: 0 20px 16px;
        }
    </style>

    {{-- GTM dataLayer + view_item event --}}
    <script>
        window.dataLayer = window.dataLayer || [];
    </script>

    @if (session()->has('api_view_item_data'))
        <script>
            dataLayer.push({
                ecommerce: null
            });
            dataLayer.push({
                event: "view_item",
                ecommerce: {
                    currency: "BDT",
                    value: {{ session('api_view_item_data.value', 0) }},
                    items: {!! session('api_view_item_data.products', '[]') !!}
                }
            });
        </script>
    @endif

    <x-metapixel-head />
</head>

<body>
    <x-metapixel-body />
    {!! $web_settings->gtm_script_body !!}

    {{-- Hero Section (full width green) --}}
    <section class="lp-hero-section">
        <div class="lp-hero-inner">
            <div class="lp-hero-title-box">
                <h1>{{ $landingPage->title }}</h1>
            </div>
            @if ($landingPage->subtitle)
                <p class="lp-hero-subtitle">{!! nl2br(e($landingPage->subtitle)) !!}</p>
            @endif
        </div>
    </section>

    {{-- Countdown Timer --}}
    <section class="lp-countdown-section">
        <div class="countdown-label">অফার শেষ হচ্ছে</div>
        <div class="lp-countdown-boxes">
            <div class="lp-countdown-box">
                <span class="count" id="countdown-hours">--</span>
                <span class="label">ঘন্টা</span>
            </div>
            <div class="lp-countdown-box">
                <span class="count" id="countdown-minutes">--</span>
                <span class="label">মিনিট</span>
            </div>
            <div class="lp-countdown-box">
                <span class="count" id="countdown-seconds">--</span>
                <span class="label">সেকেন্ড</span>
            </div>
        </div>
    </section>

    {{-- Banner Image / Video --}}
    <section class="lp-banner-section">
        @if ($landingPage->is_banner_video)
            <div class="lp-video-wrap">
                <video class="lp-banner-video" id="lpBannerVideo" controls loop playsinline>
                    <source src="{{ $landingPage->display_banner }}"
                        type="video/{{ pathinfo($landingPage->bannerMedia->file_url, PATHINFO_EXTENSION) }}">
                </video>
                @if ($landingPage->banner_autoplay)
                    <button class="lp-unmute-overlay" id="lpUnmuteBtn" title="Tap to unmute">
                        <i class="fa fa-volume-off"></i>
                    </button>
                @endif
            </div>
        @else
            <img src="{{ $landingPage->display_banner }}" alt="{{ $landingPage->title }}" class="lp-banner-img">
        @endif
    </section>

    {{-- Order Button (top) --}}
    <section class="lp-order-btn-section">
        <a href="#lp-order-section" class="btn-lp-order">{{$isFreeDelivery ? 'ফ্রি ডেলিভারিতে অর্ডার করুন' : 'অর্ডার করতে চাই'}}</a>
    </section>

    {{-- About Section --}}
    @if ($landingPage->about_section_body)
        <section class="lp-section-wrap">
            <div class="lp-section-card">
                <div class="lp-section-header">{{ $landingPage->display_about_head }}</div>
                <div class="lp-section-body">
                    {!! $landingPage->about_section_body !!}
                </div>
            </div>
        </section>
    @endif

    {{-- Gallery Section --}}
    @php $galleryImages = $landingPage->gallery_images_array; @endphp
    @if (count($galleryImages) > 0)
        <section class="lp-section-wrap">
            <div class="lp-section-card">
                <div class="lp-section-header">{{ $landingPage->gallery_section_head }}</div>
                <div class="lp-section-body">

                    <div id="lpGalleryCarousel" class="carousel slide" data-ride="carousel" data-interval="3000">
                        <div class="carousel-inner">
                            @foreach ($galleryImages as $key => $image)
                                <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                    <img src="{{ $image }}" alt="Gallery {{ $key + 1 }}"
                                        class="lp-gallery-img d-block mx-auto">
                                </div>
                            @endforeach
                        </div>

                        @if (count($galleryImages) > 1)
                            <a class="carousel-control-prev" href="#lpGalleryCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </a>
                            <a class="carousel-control-next" href="#lpGalleryCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </a>

                            <ol class="carousel-indicators" style="bottom: -28px;">
                                @foreach ($galleryImages as $key => $image)
                                    <li data-target="#lpGalleryCarousel" data-slide-to="{{ $key }}"
                                        class="{{ $key === 0 ? 'active' : '' }}"></li>
                                @endforeach
                            </ol>
                        @endif
                    </div>

                    @if (count($galleryImages) > 1)
                        <div class="lp-gallery-thumbs" style="margin-top: 20px;">
                            @foreach ($galleryImages as $key => $image)
                                <img src="{{ $image }}" alt="Thumb {{ $key + 1 }}"
                                    class="{{ $key === 0 ? 'active' : '' }}"
                                    onclick="lpGoToSlide({{ $key }})">
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </section>
    @endif

    {{-- Order Button (middle) --}}
    <section class="lp-order-btn-section">
        <a href="#lp-order-section" class="btn-lp-order">{{$isFreeDelivery ? 'ফ্রি ডেলিভারিতে অর্ডার করুন' : 'অর্ডার করতে চাই'}}</a>
    </section>

    {{-- Why This Product Section --}}
    @if ($landingPage->why_section_body)
        <section class="lp-section-wrap">
            <div class="lp-section-card">
                <div class="lp-section-header">{{ $landingPage->display_why_head }}</div>
                <div class="lp-section-body">
                    {!! $landingPage->why_section_body !!}
                </div>
            </div>
        </section>
    @endif

    {{-- Product Reviews Section --}}
    @php $reviewImages = $landingPage->review_images_array; @endphp
    @if (count($reviewImages) > 0)
        <section class="lp-section-wrap">
            <div class="lp-section-card">
                <div class="lp-section-header">{{ $landingPage->display_review_head }}</div>
                <div class="lp-section-body">

                    <div id="lpReviewCarousel" class="carousel slide" data-ride="carousel" data-interval="3000">
                        <div class="carousel-inner">
                            @foreach ($reviewImages as $key => $image)
                                <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                    <img src="{{ $image }}" alt="Review {{ $key + 1 }}"
                                        class="lp-gallery-img d-block mx-auto">
                                </div>
                            @endforeach
                        </div>

                        @if (count($reviewImages) > 1)
                            <a class="carousel-control-prev" href="#lpReviewCarousel" role="button"
                                data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </a>
                            <a class="carousel-control-next" href="#lpReviewCarousel" role="button"
                                data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </a>

                            <ol class="carousel-indicators" style="bottom: -28px;">
                                @foreach ($reviewImages as $key => $image)
                                    <li data-target="#lpReviewCarousel" data-slide-to="{{ $key }}"
                                        class="{{ $key === 0 ? 'active' : '' }}"></li>
                                @endforeach
                            </ol>
                        @endif
                    </div>

                    @if (count($reviewImages) > 1)
                        <div class="lp-gallery-thumbs" style="margin-top: 20px;">
                            @foreach ($reviewImages as $key => $image)
                                <img src="{{ $image }}" alt="Review Thumb {{ $key + 1 }}"
                                    class="{{ $key === 0 ? 'active' : '' }}"
                                    onclick="lpGoToReviewSlide({{ $key }})">
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </section>
    @endif

    {{-- Price Box --}}
    @php
        $hasDiscount =
            $landingPage->product->sale_price > 0 && $landingPage->product->sale_price < $landingPage->product->price;
        $displayPrice = $hasDiscount ? $landingPage->product->sale_price : $landingPage->product->price;
    @endphp

    @php
        $hasWhatsapp = filled($web_settings->whatsapp_number);
        $hasMessenger = filled($web_settings->messenger_link);
    @endphp

    <section class="lp-price-section">
        <div class="lp-price-box">
            @if ($hasDiscount)
                <div class="lp-price-row">
                    <div class="lp-price-tag lp-price-tag-regular">
                        <span class="lp-price-tag-label">Regular Price:</span>
                        <span class="lp-price-regular">৳{{ number_format($landingPage->product->price) }}</span>
                    </div>
                    <div class="lp-price-tag lp-price-tag-discount">
                        <span class="lp-price-tag-label">Discount Price:</span>
                        <span class="lp-price-sale">৳{{ number_format($landingPage->product->sale_price) }}</span>
                    </div>
                </div>
            @else
                <div class="lp-price-regular-only">৳{{ number_format($landingPage->product->price) }}</div>
            @endif
            @if ($isFreeDelivery)
                <div class="lp-price-meta">
                    <i class="fa fa-truck"></i> ফ্রি ডেলিভারি চার্জে অর্ডার করুন
                </div>
            @else
                <div class="lp-price-meta">
                    <i class="fa fa-truck"></i> ডেলিভারি চার্জ প্রযোজ্য
                </div>
            @endif
            @if ($web_settings->website_phone)
                <div class="lp-price-meta">
                    @if ($hasWhatsapp)
                        <i class="fa fa-whatsapp"></i> WhatsApp: <a
                            href="https://api.whatsapp.com/send?phone={{ $web_settings->whatsapp_number }}"
                            target="_blank">
                            {{ $web_settings->whatsapp_number }}
                        </a>
                    @else
                        <i class="fa fa-phone"></i> Call Us: <a
                            href="tel:{{ $web_settings->website_phone }}">{{ $web_settings->website_phone }}</a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- Order Button (below price) --}}
    <section class="lp-order-btn-section" style="padding-top:18px;">
        <a href="#lp-order-section" class="btn-lp-order">{{$isFreeDelivery ? 'ফ্রি ডেলিভারিতে অর্ডার করুন' : 'অর্ডার করতে চাই'}}</a>
    </section>

    {{-- FAQ Section --}}
    @if ($landingPage->faqs && count($landingPage->faqs) > 0)
        <section class="lp-faq-section">
            <h2>{{ $landingPage->faq_section_head ?? 'সচরাচর জিজ্ঞাসা' }}</h2>
            @foreach ($landingPage->faqs as $index => $faq)
                <div class="lp-faq-item">
                    <div class="lp-faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>{{ $faq['question'] }}</span>
                        <i class="fa fa-chevron-down lp-faq-icon"></i>
                    </div>
                    <div class="lp-faq-answer">
                        {!! nl2br(e($faq['answer'])) !!}
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    {{-- Order Form --}}
    <section class="lp-order-section" id="lp-order-section">
        <div class="lp-order-card">
            <div class="lp-order-card-header">
                অর্ডার করতে নিচের ফর্মটি সঠিক তথ্য দিয়ে পূরণ করুন
            </div>
            <div class="lp-order-form-body">

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('landing.order') }}" method="POST" id="lpOrderForm">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $landingPage->product->id }}">
                    <input type="hidden" name="landing_page_id" value="{{ $landingPage->id }}">

                    <div class="row">
                        {{-- Billing --}}
                        <div class="col-md-6 col-12 mb-4">
                            <h5 class="font-weight-bold mb-3">Billing Address</h5>

                            <div class="form-group">
                                <label style="font-weight:600;">আপনার নাম *</label>
                                <input type="text" class="form-control" name="customer_name"
                                    placeholder="আপনার নাম লিখুন" value="{{ old('customer_name') }}" required>
                            </div>

                            <div class="form-group">
                                <label style="font-weight:600;">আপনার ১১ ডিজিটের মোবাইল নম্বর *</label>
                                <input type="text" class="form-control" name="customer_phone"
                                    minlength="11" pattern="01[3-9][0-9]{8}" maxlength="11"
                                    placeholder="01XXXXXXXXX" value="{{ old('customer_phone') }}" required>
                                @error('customer_phone')
                                    <span class="text-danger small font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label style="font-weight:600;">আপনার সম্পূর্ণ ঠিকানা *</label>
                                <input type="text" class="form-control" name="customer_address"
                                    placeholder="গ্রাম, উপজেলা, জেলা, বিভাগ" value="{{ old('customer_address') }}"
                                    required>
                            </div>

                            @if (!$isFreeDelivery && $shippingMethods->count() > 0)
                                <div class="form-group">
                                    <label style="font-weight:600;">আপনার এরিয়া সিলেক্ট করুন *</label>
                                    <select name="shipping_method" id="lp-shipping-method" class="form-control"
                                        required>
                                        @foreach ($shippingMethods as $method)
                                            <option value="{{ $method->id }}" data-amount="{{ $method->amount }}">
                                                {{ $method->type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        {{-- Order Summary --}}
                        <div class="col-md-6 col-12">
                            <h5 class="font-weight-bold mb-3">Your Order</h5>

                            <div class="lp-order-summary">
                                <table class="table table-bordered text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-left">Product</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-left" style="vertical-align:middle;">
                                                @if ($landingPage->is_banner_video)
                                                    <video width="36" muted autoplay loop playsinline
                                                        class="img-thumbnail mr-1"
                                                        style="display:inline-block;vertical-align:middle;">
                                                        <source src="{{ $landingPage->display_banner }}"
                                                            type="video/{{ pathinfo($landingPage->bannerMedia->file_url, PATHINFO_EXTENSION) }}">
                                                    </video>
                                                @else
                                                    <img width="36" src="{{ $landingPage->display_banner }}"
                                                        alt="{{ $landingPage->product->name }}"
                                                        class="img-thumbnail mr-1">
                                                @endif
                                                <span
                                                    style="font-size:0.83rem;">{{ $landingPage->product->name }}</span>
                                            </td>
                                            <td style="vertical-align:middle;" id="lp-row-total">
                                                ৳{{ number_format($displayPrice) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left" style="vertical-align:middle;">
                                                Select Quantity:
                                                <div class="lp-qty-control">
                                                    <button type="button" class="lp-qty-btn"
                                                        onclick="lpDecreaseQty()">-</button>
                                                    <input type="number" class="lp-qty-input" id="lp-qty"
                                                        name="qty" value="1" min="1" max="10"
                                                        readonly>
                                                    <button type="button" class="lp-qty-btn"
                                                        onclick="lpIncreaseQty()">+</button>
                                                </div>
                                            </td>
                                            <td style="vertical-align:middle;" id="lp-qty-total">
                                                ৳{{ number_format($displayPrice) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-right" style="vertical-align:middle;">Subtotal</th>
                                            <td style="vertical-align:middle;" id="lp-subtotal">
                                                ৳{{ number_format($displayPrice) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-right" style="vertical-align:middle;">Shipping</th>
                                            <td style="vertical-align:middle;" id="lp-shipping-cost">
                                                @if ($isFreeDelivery)
                                                    Free
                                                @else
                                                    ৳<span id="lp-shipping-amount">0</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-success">
                                            <th class="text-right" style="vertical-align:middle;">Total</th>
                                            <td class="font-weight-bold" style="vertical-align:middle;"
                                                id="lp-total">
                                                ৳{{ number_format($displayPrice) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <label class="lp-cod-card" for="cod">
                                <input type="radio" name="payment_method" id="cod" value="cod" checked
                                    style="display:none;">
                                <div class="lp-cod-icon">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12 2L3 7v6c0 5.25 3.83 10.17 9 11.38C17.17 23.17 21 18.25 21 13V7l-9-5z"
                                            fill="#e8f5e9" stroke="#2d5f2e" stroke-width="1.5" />
                                        <path d="M9 12l2 2 4-4" stroke="#2d5f2e" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div class="lp-cod-text">
                                    <strong>Cash on delivery</strong>
                                    <small>Pay with cash upon delivery.</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <p class="text-danger text-center font-weight-bold mb-3">
                        * ১০০% শিউর হয়ে অর্ডার করুন, অহেতুক অর্ডার করবেন না।
                    </p>

                    <button type="submit" class="btn-lp-confirm">{{$isFreeDelivery ? 'ফ্রি ডেলিভারিতে অর্ডার করুন' : 'অর্ডার কনফার্ম করুন'}}</button>
                </form>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="lp-footer">
        <div style="max-width:768px;margin:0 auto;">
            @if ($web_settings->get_logo)
                <img src="{{ asset($web_settings->get_logo->file_url) }}" alt="{{ config('app.name') }}"
                    style="max-height:40px;margin-bottom:8px;">
            @endif
            <div class="footer-brand"><i class="fa fa-instagram"></i> {{ config('app.name') }}</div>
            <div class="footer-address">প্রোডাক্টের অর্ডার একটি কল দিয়ে দিন — এক জায়গায় সব, সারাদেশে!</div>
            <div class="footer-contact">
                @if ($web_settings->website_phone)
                    <i class="fa fa-phone"></i> {{ $web_settings->website_phone }}
                @endif
                @if ($web_settings->website_phone && $web_settings->website_email)
                    <span>&middot;</span>
                @endif
                @if ($web_settings->website_email)
                    <i class="fa fa-envelope"></i> {{ $web_settings->website_email }}
                @endif
            </div>
            <div class="footer-copy">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
        </div>
    </footer>


    @if ($hasWhatsapp && $hasMessenger)
        <div class="floating-contact">
            <div class="contact-icons" id="contactIcons">
                <a href="https://api.whatsapp.com/send?phone={{ $web_settings->whatsapp_number }}" target="_blank"
                    class="contact-icon whatsapp" title="WhatsApp">
                    <svg width="32" height="32" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                        <path fill="#ffffff"
                            d="M16.04 5C10.51 5 6 9.35 6 14.76c0 2.16.73 4.16 2 5.8L7 27l6.66-2.1c1.01.28 2.08.44 3.18.44 5.53 0 10.04-4.35 10.04-9.76C26.88 9.35 21.57 5 16.04 5Zm0 16.9c-.94 0-1.86-.15-2.73-.45l-.2-.07-3.94 1.24 1.28-3.73-.13-.19a7.16 7.16 0 0 1-1.2-3.97c0-4.03 3.34-7.3 7.46-7.3 4.11 0 7.46 3.27 7.46 7.3 0 4.02-3.35 7.3-7.46 7.3Zm3.99-5.46c-.22-.11-1.3-.64-1.5-.71-.2-.07-.35-.11-.49.11-.15.22-.56.71-.69.86-.13.15-.26.17-.48.06-.22-.11-.93-.34-1.77-1.06-.65-.57-1.09-1.27-1.22-1.48-.13-.22-.01-.33.1-.44.1-.1.22-.26.33-.39.11-.13.15-.22.22-.37.07-.15.04-.28-.02-.39-.07-.11-.49-1.18-.67-1.62-.18-.44-.36-.37-.49-.37h-.42c-.15 0-.39.06-.59.28-.2.22-.78.76-.78 1.85 0 1.09.8 2.14.9 2.29.11.15 1.57 2.39 3.82 3.26.53.22.95.35 1.28.45.54.17 1.04.15 1.43.09.44-.07 1.3-.53 1.48-1.05.18-.52.18-.96.13-1.05-.04-.09-.2-.15-.42-.26Z" />
                    </svg>
                </a>

                {{-- Phone Call --}}
                <a href="tel:{{ $web_settings->website_phone }}" target="_blank" class="contact-icon phone"
                    title="Phone">
                    <svg width="36" height="36" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                        <circle cx="18" cy="18" r="18" />
                        <path fill="#ffffff"
                            d="M23.44 20.77l-2.36-1.08a1.22 1.22 0 0 0-1.42.26l-.98 1a10.05 10.05 0 0 1-4.82-4.82l1-1a1.22 1.22 0 0 0 .26-1.42l-1.08-2.36a1.21 1.21 0 0 0-1.32-.69c-1.1.22-1.9 1.19-1.9 2.31 0 6.07 4.93 11 11 11 1.13 0 2.1-.8 2.31-1.9a1.21 1.21 0 0 0-.69-1.32z" />
                    </svg>
                </a>

                <a href="{{ $web_settings->messenger_link }}" target="_blank" class="contact-icon messenger"
                    title="Messenger">
                    <svg width="32" height="32" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                        <path fill="#ffffff"
                            d="M16 4C9.37 4 4 8.96 4 15.02c0 3.44 1.65 6.43 4.35 8.39V28l3.97-2.18c1.13.32 2.33.5 3.68.5 6.63 0 12-4.96 12-11.02C28 8.96 22.63 4 16 4Zm1.01 13.4-2.92-3.13-5.7 3.13 6.24-6.62 2.97 3.13 5.63-3.13-6.22 6.62Z" />
                    </svg>
                </a>
            </div>
            <button class="fab-btn" id="contactToggle" type="button" aria-label="Open chat options">
                <svg chat-icon version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                    viewBox="-496 507.7 54 54" xml:space="preserve">
                    <style type="text/css">
                        .chaty-sts4-0 {
                            fill: #ffffff;
                        }

                        .chaty-st0 {
                            fill: #808080;
                        }
                    </style>
                    <g>
                        <circle cx="-469" cy="534.7" r="27" fill="#A886CD"></circle>
                    </g>
                    <path class="chaty-sts4-0"
                        d="M-459.9,523.7h-20.3c-1.9,0-3.4,1.5-3.4,3.4v15.3c0,1.9,1.5,3.4,3.4,3.4h11.4l5.9,4.9c0.2,0.2,0.3,0.2,0.5,0.2 h0.3c0.3-0.2,0.5-0.5,0.5-0.8v-4.2h1.7c1.9,0,3.4-1.5,3.4-3.4v-15.3C-456.5,525.2-458,523.7-459.9,523.7z">
                    </path>
                    <path class="chaty-st0"
                        d="M-477.7,530.5h11.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-11.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,530.8-478.2,530.5-477.7,530.5z">
                    </path>
                    <path class="chaty-st0"
                        d="M-477.7,533.5h7.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-7.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,533.9-478.2,533.5-477.7,533.5z">
                    </path>
                </svg>
                <svg close-icon class="d-none" width="32" height="32" viewBox="0 0 20 20" aria-hidden="true"
                    focusable="false">
                    <line x1="4" y1="4" x2="16" y2="16" stroke="#ffffff"
                        stroke-width="2" stroke-linecap="round" />
                    <line x1="16" y1="4" x2="4" y2="16" stroke="#ffffff"
                        stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>
    @elseif($hasWhatsapp)
        <div class="floating-contact">
            <a href="https://api.whatsapp.com/send?phone={{ $web_settings->whatsapp_number }}" target="_blank"
                class="fab-btn contact-icon whatsapp" title="WhatsApp">
                <svg width="32" height="32" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                    <path fill="#ffffff"
                        d="M16.04 5C10.51 5 6 9.35 6 14.76c0 2.16.73 4.16 2 5.8L7 27l6.66-2.1c1.01.28 2.08.44 3.18.44 5.53 0 10.04-4.35 10.04-9.76C26.88 9.35 21.57 5 16.04 5Zm0 16.9c-.94 0-1.86-.15-2.73-.45l-.2-.07-3.94 1.24 1.28-3.73-.13-.19a7.16 7.16 0 0 1-1.2-3.97c0-4.03 3.34-7.3 7.46-7.3 4.11 0 7.46 3.27 7.46 7.3 0 4.02-3.35 7.3-7.46 7.3Zm3.99-5.46c-.22-.11-1.3-.64-1.5-.71-.2-.07-.35-.11-.49.11-.15.22-.56.71-.69.86-.13.15-.26.17-.48.06-.22-.11-.93-.34-1.77-1.06-.65-.57-1.09-1.27-1.22-1.48-.13-.22-.01-.33.1-.44.1-.1.22-.26.33-.39.11-.13.15-.22.22-.37.07-.15.04-.28-.02-.39-.07-.11-.49-1.18-.67-1.62-.18-.44-.36-.37-.49-.37h-.42c-.15 0-.39.06-.59.28-.2.22-.78.76-.78 1.85 0 1.09.8 2.14.9 2.29.11.15 1.57 2.39 3.82 3.26.53.22.95.35 1.28.45.54.17 1.04.15 1.43.09.44-.07 1.3-.53 1.48-1.05.18-.52.18-.96.13-1.05-.04-.09-.2-.15-.42-.26Z" />
                </svg>
            </a>
        </div>
    @elseif($hasMessenger)
        <div class="floating-contact">
            <a href="{{ $web_settings->messenger_link }}" target="_blank" class="fab-btn contact-icon messenger"
                title="Messenger">
                <svg width="32" height="32" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                    <path fill="#ffffff"
                        d="M16 4C9.37 4 4 8.96 4 15.02c0 3.44 1.65 6.43 4.35 8.39V28l3.97-2.18c1.13.32 2.33.5 3.68.5 6.63 0 12-4.96 12-11.02C28 8.96 22.63 4 16 4Zm1.01 13.4-2.92-3.13-5.7 3.13 6.24-6.62 2.97 3.13 5.63-3.13-6.22 6.62Z" />
                </svg>
            </a>
        </div>
    @endif

    <script src="{{ asset('frontEnd/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('frontEnd/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        var lpUnitPrice = {{ (float) $displayPrice }};
        var lpIsFreeDelivery = {{ $isFreeDelivery ? 'true' : 'false' }};

        // --- GTM dataLayer: add_to_cart (fires once when user clicks order button) ---
        var lpAddToCartFired = false;
        var lpProductData = {
            item_id: {{ $landingPage->product->id }},
            item_name: @json($landingPage->product->name),
            item_category: @json($landingPage->product->get_categories[0]->category_name ?? ''),
            price: {{ (float) $displayPrice }},
            quantity: 1
        };

        function lpFireAddToCart() {
            if (lpAddToCartFired) return;
            lpAddToCartFired = true;
            var qty = parseInt(document.getElementById('lp-qty')?.value) || 1;
            var itemData = Object.assign({}, lpProductData, {
                quantity: qty
            });
            dataLayer.push({
                ecommerce: null
            });
            dataLayer.push({
                event: "add_to_cart",
                ecommerce: {
                    currency: "BDT",
                    value: (lpUnitPrice * qty).toFixed(2),
                    items: [itemData]
                }
            });
        }

        function lpGetShippingCost() {
            if (lpIsFreeDelivery) return 0;
            var select = document.getElementById('lp-shipping-method');
            if (!select) return 0;
            var selected = select.options[select.selectedIndex];
            return parseFloat(selected.getAttribute('data-amount')) || 0;
        }

        function lpIncreaseQty() {
            var input = document.getElementById('lp-qty');
            var qty = parseInt(input.value);
            if (qty < 10) {
                input.value = qty + 1;
                lpUpdateTotal(qty + 1);
            }
        }

        function lpDecreaseQty() {
            var input = document.getElementById('lp-qty');
            var qty = parseInt(input.value);
            if (qty > 1) {
                input.value = qty - 1;
                lpUpdateTotal(qty - 1);
            }
        }

        function lpUpdateTotal(qty) {
            var subtotal = lpUnitPrice * qty;
            var shipping = lpGetShippingCost();
            var total = subtotal + shipping;
            var fmtSubtotal = '৳' + subtotal.toLocaleString('en-IN');
            var fmtTotal = '৳' + total.toLocaleString('en-IN');
            document.getElementById('lp-row-total').textContent = fmtSubtotal;
            document.getElementById('lp-qty-total').textContent = fmtSubtotal;
            document.getElementById('lp-subtotal').textContent = fmtSubtotal;
            document.getElementById('lp-total').textContent = fmtTotal;

            var shippingAmountEl = document.getElementById('lp-shipping-amount');
            if (shippingAmountEl) {
                shippingAmountEl.textContent = shipping.toLocaleString('en-IN');
            }
        }

        function lpGoToSlide(index) {
            $('#lpGalleryCarousel').carousel(index);
            document.querySelectorAll('#lpGalleryCarousel ~ .lp-gallery-thumbs img').forEach(function(img, i) {
                img.classList.toggle('active', i === index);
            });
        }

        function lpGoToReviewSlide(index) {
            $('#lpReviewCarousel').carousel(index);
            document.querySelectorAll('#lpReviewCarousel ~ .lp-gallery-thumbs img').forEach(function(img, i) {
                img.classList.toggle('active', i === index);
            });
        }

        $('#lpGalleryCarousel').on('slid.bs.carousel', function(e) {
            var index = e.to;
            document.querySelectorAll('#lpGalleryCarousel ~ .lp-gallery-thumbs img').forEach(function(img, i) {
                img.classList.toggle('active', i === index);
            });
        });

        $('#lpReviewCarousel').on('slid.bs.carousel', function(e) {
            var index = e.to;
            document.querySelectorAll('#lpReviewCarousel ~ .lp-gallery-thumbs img').forEach(function(img, i) {
                img.classList.toggle('active', i === index);
            });
        });

        document.querySelectorAll('a[href="#lp-order-section"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                lpFireAddToCart();
                document.getElementById('lp-order-section').scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        (function() {
            var storageKey = 'lp_countdown_deadline';
            var minHours = 1;

            function randomDeadline() {
                var hours = 8 + Math.random() * 2; // 8–10 hours
                return new Date().getTime() + (hours * 60 * 60 * 1000);
            }

            function getDeadline() {
                var saved = localStorage.getItem(storageKey);
                if (saved) {
                    var deadline = parseInt(saved, 10);
                    var remaining = deadline - new Date().getTime();
                    if (remaining > minHours * 60 * 60 * 1000) {
                        return deadline;
                    }
                }
                var deadline = randomDeadline();
                localStorage.setItem(storageKey, deadline);
                return deadline;
            }

            var deadline = getDeadline();

            var timer = setInterval(function() {
                var now = new Date().getTime();
                var diff = deadline - now;

                if (diff <= minHours * 60 * 60 * 1000) {
                    deadline = randomDeadline();
                    localStorage.setItem(storageKey, deadline);
                    diff = deadline - now;
                }

                var hours = Math.floor(diff / (1000 * 60 * 60));
                var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((diff % (1000 * 60)) / 1000);
                document.getElementById('countdown-hours').textContent = String(hours).padStart(2, '0');
                document.getElementById('countdown-minutes').textContent = String(minutes).padStart(2, '0');
                document.getElementById('countdown-seconds').textContent = String(seconds).padStart(2, '0');
            }, 1000);
        })();

        $(document).ready(function() {
            $('#lpGalleryCarousel').carousel({
                interval: 3000,
                ride: 'carousel'
            });
            $('#lpReviewCarousel').carousel({
                interval: 3000,
                ride: 'carousel'
            });

            // Handle shipping method change
            $('#lp-shipping-method').on('change', function() {
                var qty = parseInt(document.getElementById('lp-qty').value) || 1;
                lpUpdateTotal(qty);
            });

            // Trigger initial shipping calculation
            if (!lpIsFreeDelivery && document.getElementById('lp-shipping-method')) {
                var qty = parseInt(document.getElementById('lp-qty').value) || 1;
                lpUpdateTotal(qty);
            }

            $('#contactToggle').on('click', function() {
                $('#contactIcons').toggleClass('show');
                $(this).find('[chat-icon]').toggleClass('d-none');
                $(this).find('[close-icon]').toggleClass('d-none');
            });

            // GTM dataLayer: begin_checkout on form submit
            $('#lpOrderForm').on('submit', function() {
                var qty = parseInt(document.getElementById('lp-qty').value) || 1;
                var shipping = lpGetShippingCost();
                var subtotal = lpUnitPrice * qty;
                var itemData = Object.assign({}, lpProductData, {
                    quantity: qty
                });

                dataLayer.push({
                    ecommerce: null
                });
                dataLayer.push({
                    event: "begin_checkout",
                    ecommerce: {
                        currency: "BDT",
                        value: (subtotal + shipping).toFixed(2),
                        coupon: "",
                        items: [itemData]
                    }
                });
            });

            // Banner video: autoplay logic (only if autoplay is enabled)
            var bannerVideo = document.getElementById('lpBannerVideo');
            var unmuteBtn = document.getElementById('lpUnmuteBtn');
            @if (isset($landingPage) && $landingPage->is_banner_video && $landingPage->banner_autoplay)
                if (bannerVideo) {
                    // First try: play with sound
                    bannerVideo.muted = false;
                    var playPromise = bannerVideo.play();
                    if (playPromise !== undefined) {
                        playPromise.catch(function() {
                            // Browser blocked unmuted autoplay — fallback to muted
                            bannerVideo.muted = true;
                            bannerVideo.play();
                            if (unmuteBtn) unmuteBtn.classList.remove('hidden');
                        }).then(function() {
                            // Unmuted autoplay worked — hide the button
                            if (unmuteBtn && !bannerVideo.muted) {
                                unmuteBtn.classList.add('hidden');
                            }
                        });
                    }

                    // Unmute button click
                    if (unmuteBtn) {
                        unmuteBtn.addEventListener('click', function() {
                            bannerVideo.muted = false;
                            unmuteBtn.classList.add('hidden');
                        });
                    }

                    // Auto-unmute on first user interaction anywhere on page
                    function lpAutoUnmute() {
                        if (bannerVideo && bannerVideo.muted) {
                            bannerVideo.muted = false;
                            if (unmuteBtn) unmuteBtn.classList.add('hidden');
                        }
                        document.removeEventListener('click', lpAutoUnmute);
                        document.removeEventListener('touchstart', lpAutoUnmute);
                        document.removeEventListener('scroll', lpAutoUnmute);
                    }
                    document.addEventListener('click', lpAutoUnmute, {
                        once: true
                    });
                    document.addEventListener('touchstart', lpAutoUnmute, {
                        once: true
                    });
                    document.addEventListener('scroll', lpAutoUnmute, {
                        once: true
                    });
                }
            @endif
        });
    </script>

</body>

</html>

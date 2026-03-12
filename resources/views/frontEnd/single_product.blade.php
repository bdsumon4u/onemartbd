@extends('frontEnd.layouts.master')

@section('title')
    {{ $data->name }}
@endsection
@section('gTag')
    <style>
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

        .reviews-container {
            border: 1px solid #e5e5e5;
            border-radius: 4px;
            padding: 20px;
            background-color: #ffffff;
        }

        .reviews-header {
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .rating-summary-stars i {
            color: #f6b01e;
            margin-right: 2px;
        }

        .review-form-label {
            font-weight: 600;
        }

        .review-help-text {
            font-size: 12px;
            color: #6c757d;
        }

        .rating-stars i {
            cursor: pointer;
            font-size: 20px;
            color: #d2d2d2;
            margin-right: 2px;
        }

        .rating-stars i.active {
            color: #f6b01e;
        }

        .review-item-card {
            border-bottom: 1px solid #f0f0f0;
            padding: 12px 0;
        }

        .review-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #f1f3f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #555555;
            margin-right: 10px;
            font-size: 14px;
        }

        .review-rating-stars i {
            color: #f6b01e;
            font-size: 14px;
        }
        #productTab a {
            font-size: 80%;
            padding: .5rem .425rem;
        }
    </style>
    @if (session()->has('api_add_to_cart_data'))
        <script>
            dataLayer.push({
                ecommerce: null
            }); // Clear the previous ecommerce object.
            dataLayer.push({
                event: "add_to_cart",
                ecommerce: {
                    currency: "BDT",
                    value: {{ session('api_add_to_cart_data')['value'] }},
                    items: {!! session('api_add_to_cart_data')['products'] !!}
                }
            });
        </script>
    @endif
    @if (session()->has('api_view_item_data'))
        <script>
            dataLayer.push({
                ecommerce: null
            }); // Clear the previous ecommerce object.
            dataLayer.push({
                event: "view_item",
                ecommerce: {
                    currency: "BDT",
                    value: {{ session('api_view_item_data')['value'] }},
                    items: {!! session('api_view_item_data')['products'] !!}
                }
            });
        </script>
    @endif
@endsection
@section('body')
    <section>
        <div class="category_breadcrumb">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <p>
                            <a href="{{ route('home') }}">Home</a>
                            /
                            <a href="javascript:void(0);">{{ $data->name }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="products-details-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-12 mb-md-3 mb-2">
                        {{-- @php
                            if ($data->gallery_images){
                                $photos = explode(',', $data->gallery_images);
                            }else{
                                $photos = [];
                            }
                        @endphp --}}
                        <div id="sing_prod_img_slider" class="carousel slide" data-ride="carousel">
                            @php $galleryPhotos = $data->gallery_images ? $data->images : [] @endphp
                            @if ($data->sale_price > 0 && $data->sale_price < $data->price)
                                <?php
                                $percentage = round(100 - ($data->sale_price / $data->price) * 100);
                                ?>
                                <p class="float_price_2 d-none">{{ $percentage }}% Off</p>
                            @endif
                            @if ($data->gallery_images)
                                <ol class="carousel-indicators">
                                    <li data-target="#sing_prod_img_slider" data-slide-to="0" class="active"></li>
                                    @if ($data->gallery_images)
                                        @foreach ($galleryPhotos as $key => $photo)
                                            <li data-target="#sing_prod_img_slider" data-slide-to="{{ $key = $key + 1 }}">
                                            </li>
                                        @endforeach
                                    @endif
                                </ol>
                            @endif
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="{{ $data->get_image ? asset($data->get_image->file_url) : asset('frontEnd/images/no_image.png') }}"
                                        class="d-block w-100" alt="">
                                </div>
                                @if ($data->gallery_images)
                                    @foreach ($galleryPhotos as $photo)
                                        <div class="carousel-item">
                                            <img src="{{ $photo ? asset($photo) : asset('frontEnd/images/no_image.png') }}"
                                                class="d-block w-100" alt="">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @if ($data->gallery_images)
                                <button class="carousel-control-prev" type="button" data-target="#sing_prod_img_slider"
                                    data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </button>

                                <button class="carousel-control-next" type="button" data-target="#sing_prod_img_slider"
                                    data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-5 mb-3">
                        <h2 class="text-capitalize single_prod_title">{{ $data->name }}</h2>
                        <h3 class="font-weight-bold single_prod_prices">
                            @if ($data->sale_price > 0 && $data->sale_price < $data->price)
                                <span class="old_price"
                                    style="text-decoration: line-through; color: #555;opacity: .5">{{ $web_settings->currency_sign }}
                                    {{ $data->price }}</span>
                                <span style="color: #fb4907">{{ $web_settings->currency_sign }}
                                    {{ $data->sale_price }}</span>

                                {{-- Save {{ $data->price - $data->sale_price }} Taka --}}
                                <small style="font-size: 16px; vertical-align: middle; border: 1px dashed #fb4907; padding: 0px 5px; color: #fb4907;">Save {{ $data->price - $data->sale_price }} Taka</small>
                            @else
                                <span style="color: #fb4907">{{ $web_settings->currency_sign }} {{ $data->price }}</span>
                            @endif
                        </h3>
                        {{-- <p class="sku_text"><span>প্রোডাক্ট কোড: </span> <span class="p-0 pr-1">{{$data->sku}}</span></p>
                        <h4 class="single_prod_in_stock">স্টক : @if ($data->stock > 0)<span class="text-success">ইন স্টক</span> @else <span
                                class="text-danger">স্টক আউট</span>@endif</h4> --}}

                        {{-- <div class="qty_div">
                            <a style="color: black" href="{{route('cart.item.minus',$data->id)}}"><i class="fa fa-minus" id="qty_minus"></i></a>
                            <input type="text" name="qty" id="qty" min="1" value="{{$qty ?? 1}}" readonly>
                            <a style="color: black" href="{{route('cart.item.plus',$data->id)}}"><i class="fa fa-plus" id="qty_plus"></i></a>
                        </div> --}}

                        <form action="{{ route('add.cart', $data->id) }}" method="post">
                            @csrf
                            <div class="d-flex">
                                <div class="qty-text-div">
                                    <span>পরিমান : </span>
                                </div>
                                <div class="qty_div">
                                    <div class="minus-qty-div">
                                        <i class="fa fa-minus qty_minus" id="qty_minus"></i>
                                    </div>
                                    <div class="qty-div">
                                        <input type="text" name="qty" id="qty" class="qty" min="1"
                                            value="{{ $qty ?? 1 }}" readonly>
                                    </div>
                                    <div class="plus-qty-div">
                                        <i class="fa fa-plus qty_plus" id="qty_plus"></i>
                                    </div>
                                </div>
                            </div>

                            @if (count($data->get_attributes) > 0)
                                <div class="attributes">
                                    <div class="item">
                                        @foreach ($data->get_attributes as $key => $att)
                                            <div class="row mb-2">
                                                <div class="col-md-12 col-12">
                                                    <label
                                                        class="mb-0"><b>{{ $att->get_attribute->title }}</b></label><br>
                                                    <input type="hidden" name="attribute_id[]"
                                                        value="{{ $att->get_attribute->id }}">
                                                    @foreach ($att->get_attribute_items as $key2 => $attr_item)
                                                        <input type="radio"
                                                            id="val_{{ $key }}{{ $key2 }}"
                                                            name="attribute_item_id[{{ $att->get_attribute->id }}][]"
                                                            value="{{ $attr_item->get_attribute_item->id }}"
                                                            class="attr_checkbox" {{ $key2 == 0 ? 'checked' : '' }}>
                                                        <label class="mb-0"
                                                            for="val_{{ $key }}{{ $key2 }}">
                                                            <span>{{ $attr_item->get_attribute_item->item_title }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($data->start_date && $data->end_date)
                                <div class="mt-md-4 mt-2 single_product">
                                    <input type="submit" class="btn px-4 order_now_btn btn-drift order_now_btn_m w-100"
                                        name="order_now" value="ফ্রি ডেলিভারিতে অর্ডার করুন">
                                    <input type="submit" class="btn px-4 add_cart_btn mt-1 w-100" name="add_cart"
                                        value="কার্টে যোগ করুন">
                                </div>
                            @else
                                <div class="mt-md-4 mt-2 d-md-flex single_product">
                                    <input type="submit" class="btn px-4 order_now_btn btn-drift order_now_btn_m"
                                        name="order_now" value="অর্ডার করুন">
                                    <input type="submit" class="btn px-4 add_cart_btn" name="add_cart"
                                        value="কার্টে রাখুন">
                                </div>
                            @endif

                            {{-- <div class="mt-md-4 mt-2">
                                <input type="submit" class="btn px-4 order_now_btn order_now_btn_m" name="order_now" value="অর্ডার করুন">
                            </div>

                            <div class="mt-md-3 mt-2">
                                <input type="submit" class="btn px-4 add_cart_btn" name="add_cart" value="কার্টে রাখুন">
                            </div> --}}
                        </form>


                        <div class="mt-md-2 mt-2">
                            @if ($web_settings->website_phone)
                                <h4 class="font-weight-bold">
                                    <a class="btn btn-success w-100 call_now_btn"
                                        href="tel:{{ $web_settings->website_phone }}">
                                        <i class="fa fa-phone-square"></i>
                                        {{ $web_settings->website_phone }}
                                    </a>
                                </h4>
                            @endif

                            @if ($web_settings->website_phone2)
                                <h4 class="font-weight-bold">
                                    <a class="btn btn-success w-100 call_now_btn"
                                        href="tel:{{ $web_settings->website_phone2 }}">
                                        <i class="fa fa-phone-square"></i>
                                        {{ $web_settings->website_phone2 }}
                                    </a>
                                </h4>
                            @endif

                            @if ($web_settings->website_phone3)
                                <h4 class="font-weight-bold">
                                    <a class="btn btn-success w-100 call_now_btn"
                                        href="tel:{{ $web_settings->website_phone3 }}">
                                        <i class="fa fa-phone-square"></i>
                                        {{ $web_settings->website_phone3 }}
                                    </a>
                                </h4>
                            @endif

                            <div class="d-flex flex-column" style="gap: 10px;">
                                @if (filled($web_settings->whatsapp_number ?? null))
                                    <a
                                        class="btn call_now_btn flex-1"
                                        style="background-color: #25D366; color: #fff; border:none; flex: 1;"
                                        href="https://api.whatsapp.com/send?phone={{ preg_replace('/[^0-9]/', '', $web_settings->whatsapp_number) }}"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        <span style="vertical-align: middle; display:inline-block;">
                                            <svg width="33" height="33" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                                                <path fill="#ffffff" d="M16.04 5C10.51 5 6 9.35 6 14.76c0 2.16.73 4.16 2 5.8L7 27l6.66-2.1c1.01.28 2.08.44 3.18.44 5.53 0 10.04-4.35 10.04-9.76C26.88 9.35 21.57 5 16.04 5Zm0 16.9c-.94 0-1.86-.15-2.73-.45l-.2-.07-3.94 1.24 1.28-3.73-.13-.19a7.16 7.16 0 0 1-1.2-3.97c0-4.03 3.34-7.3 7.46-7.3 4.11 0 7.46 3.27 7.46 7.3 0 4.02-3.35 7.3-7.46 7.3Zm3.99-5.46c-.22-.11-1.3-.64-1.5-.71-.2-.07-.35-.11-.49.11-.15.22-.56.71-.69.86-.13.15-.26.17-.48.06-.22-.11-.93-.34-1.77-1.06-.65-.57-1.09-1.27-1.22-1.48-.13-.22-.01-.33.1-.44.1-.1.22-.26.33-.39.11-.13.15-.22.22-.37.07-.15.04-.28-.02-.39-.07-.11-.49-1.18-.67-1.62-.18-.44-.36-.37-.49-.37h-.42c-.15 0-.39.06-.59.28-.2.22-.78.76-.78 1.85 0 1.09.8 2.14.9 2.29.11.15 1.57 2.39 3.82 3.26.53.22.95.35 1.28.45.54.17 1.04.15 1.43.09.44-.07 1.3-.53 1.48-1.05.18-.52.18-.96.13-1.05-.04-.09-.2-.15-.42-.26Z"/>
                                            </svg>
                                        </span>
                                        WhatsApp: {{ str_starts_with($web_settings->whatsapp_number, '8801') ? str($web_settings->whatsapp_number)->replaceFirst('88', '') : $web_settings->whatsapp_number }}
                                    </a>
                                @endif

                                @if (filled($web_settings->messenger_link ?? null))
                                    <a
                                        class="btn call_now_btn flex-1"
                                        style="background-color: #0084ff; color: #fff; border:none; flex: 1;"
                                        href="{{ $web_settings->messenger_link }}"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        <span style="vertical-align: middle; display:inline-block;">
                                            <svg width="30" height="30" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                                                <path fill="#ffffff" d="M16 4C9.37 4 4 8.96 4 15.02c0 3.44 1.65 6.43 4.35 8.39V28l3.97-2.18c1.13.32 2.33.5 3.68.5 6.63 0 12-4.96 12-11.02C28 8.96 22.63 4 16 4Zm1.01 13.4-2.92-3.13-5.7 3.13 6.24-6.62 2.97 3.13 5.63-3.13-6.22 6.62Z"/>
                                            </svg>
                                        </span>
                                        মেসেজ করতে ক্লিক করুন
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- <div class="fb-share-button mt-3" data-href="https://developers.facebook.com/docs/plugins/" data-layout="button" data-size="large">
                            <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore"></a>
                        </div> --}}

                        @if ($data->start_date && $data->end_date)
                            <div class="col-12 mt-3 delivery_details" style="padding: 0">
                                <table class="table" style="color:#08c !important; font-weight: bold">
                                    <tbody>
                                        <tr>
                                            <td style="padding-left: 0;border-bottom: 1px solid #ddd; text-align: center;">
                                                আজ অর্ডার করলে পাবেন <strong
                                                    style="font-size: 150%; color:red">ফ্রি</strong> ডেলিভারির সুবিধা
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @elseif($shipping_methods->count() > 0)
                            <div class="col-12 mt-3 delivery_details" style="padding: 0">
                                <table class="table" style="color:#08c !important">
                                    <tbody>
                                        @foreach ($shipping_methods as $item)
                                            <tr>
                                                <td style="padding-left: 0;border-bottom: 1px solid #ddd;">
                                                    {{ $item->text }}
                                                </td>
                                                <td style="border-bottom: 1px solid #ddd;">
                                                    <b>{{ $web_settings->currency_sign }} {{ $item->amount }}</b>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        {{-- <h6 class="font-weight-bold text-danger mt-md-3 mt-2">বিকাশ নাম্বার : {{$web_settings->bkash_merchant_numb}}</h6> --}}
                    </div>

                    <div class="col-md-3 mb-3 d-md-block d-none">
                        <div class="features">
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="icon"><i class="fa fa-thumbs-up" style="color: #666"></i></td>
                                        <td class="text">100% original products</td>
                                    </tr>

                                    <tr>
                                        <td class="icon"><i class="fa fa-money" style="color: #666"></i></td>
                                        <td class="text">Pay cash on delivery</td>
                                    </tr>

                                    <tr>
                                        <td class="icon"><i class="fa fa-shopping-cart"
                                                style="color: #666;vertical-align: top"></i></td>
                                        <td class="text">Delivery within: 2-3 business days</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="feature-products d-md-block d-none">
                            <p>প্রয়োজনীয় প্রোডাক্ট</p>
                            <div class="feature-products-wrapper">
                                <table>
                                    @foreach ($feature_prod as $item)
                                        <tr>
                                            <td class="img">
                                                <a href="{{ route('single.product', [$item->slug, $item->id]) }}">
                                                    <img width="50"
                                                        src="{{ $item->get_thumb ? asset($item->get_thumb->file_url) : asset('frontEnd/images/no_image.png') }}"
                                                        alt="">
                                                </a>
                                            </td>
                                            <td class="title">
                                                <a href="{{ route('single.product', [$item->slug, $item->id]) }}"
                                                    class="text-dark">
                                                    {{ $item->name }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-tabs nav-tabs-mod" id="productTab">
                            <li class="nav-item border">
                                <a class="nav-link active" id="desc-tab" data-toggle="tab"
                                    href="#desc-pane">পন্যের বিবরণ</a>
                            </li>
                            <li class="nav-item border">
                                <a class="nav-link" id="specs-tab" data-toggle="tab"
                                    href="#return-policy">Delivery & Return Policy</a>
                            </li>
                            <li class="nav-item border">
                                <a class="nav-link" id="reviews-tab" data-toggle="tab"
                                    href="#reviews-pane">Reviews</a>
                            </li>
                        </ul>
                        <div class="tab-content tab-content-mod">
                            <div class="tab-pane fade show active" id="desc-pane">
                                <div>
                                    {!! $data->description !!}
                                </div>
                            </div>
                            <div class="tab-pane fade" id="return-policy">
                                @php $pageSettings = \DB::table('page_settings')->where('id', 1)->first() @endphp
                                <div>
                                    {!! $pageSettings?->delivery_policy; !!}
                                </div>
                                <div>
                                    {!! $pageSettings?->return_policy; !!}
                                </div>
                            </div>
                            <div class="tab-pane fade" id="reviews-pane">
                                @php $overallAverage = $data->averageRating('overall') @endphp
                                @php $reviewCount = $data->getReviews(true, false)->count() @endphp
                                <div id="product-reviews-section" class="reviews-container">
                                    <div class="reviews-header d-flex justify-content-center align-items-center">
                                        <div>
                                            <h4 class="mb-1 text-center">Customer Reviews</h4>
                                            <div class="d-flex align-items-center flex-column flex-md-row">
                                                <div class="rating-summary-stars mr-2">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="fa {{ $overallAverage ? ($i <= round($overallAverage) ? 'fa-star' : 'fa-star-o') : 'fa-star-o' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="font-weight-bold">
                                                    {{ $overallAverage ? number_format($overallAverage, 1) : '0.0' }}
                                                    out of 5
                                                </span>
                                                <span class="text-muted ml-1">
                                                    ({{ $reviewCount }} reviews)
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="review-form-wrapper mt-3">
                                        <h5 class="mb-2">Write a Review</h5>
                                        <p class="text-muted small mb-3">
                                            To submit a review, please provide your order ID and phone number to verify
                                            your purchase.
                                        </p>
                                        <form id="review-form">
                                            <input type="hidden" name="product_id" value="{{ $data->id }}">
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label class="review-form-label" for="order_id">
                                                        Order ID <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="order_id" id="order_id"
                                                        class="form-control" placeholder="Enter your order ID" required>
                                                    <small class="review-help-text d-block mt-1">
                                                        You can find your order ID in your order confirmation.
                                                    </small>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="review-form-label" for="phone">
                                                        Phone Number <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="phone" id="phone" class="form-control"
                                                        placeholder="Enter your phone number" required>
                                                    <small class="review-help-text d-block mt-1">
                                                        Must match the phone number used for the order.
                                                    </small>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="review-form-label d-block">
                                                        Rating <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="d-flex align-items-center">
                                                        <div id="rating-stars" class="rating-stars">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <i class="fa fa-star-o" data-value="{{ $i }}"></i>
                                                            @endfor
                                                        </div>
                                                        <span class="small text-muted ml-2" id="rating-text">
                                                            Select rating
                                                        </span>
                                                    </div>
                                                    <input type="hidden" name="rating" id="rating-input">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="review-form-label" for="review">
                                                    Your Review <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="review" id="review" class="form-control" rows="4"
                                                    placeholder="Share your experience with this product..." required></textarea>
                                                <small class="review-help-text d-block mt-1">
                                                    Minimum 10 characters, maximum 1000 characters.
                                                </small>
                                            </div>
                                            <button type="submit" class="btn btn-warning font-weight-bold px-4">
                                                Submit Review
                                            </button>
                                            <div id="review-message" class="mt-3"></div>
                                        </form>
                                    </div>

                                    <div class="recent-reviews mt-4">
                                        <h5 class="mb-3">Recent Reviews</h5>
                                        <div id="reviews-list"></div>
                                        <button id="load-reviews-btn"
                                            class="btn btn-outline-secondary btn-sm mt-2" style="display: none;">Load More
                                            Reviews</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3 d-block d-md-none">
                        <div class="features">
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="icon"><i class="fa fa-thumbs-up" style="color: #666"></i></td>
                                        <td class="text">100% original products</td>
                                    </tr>

                                    <tr>
                                        <td class="icon"><i class="fa fa-money" style="color: #666"></i></td>
                                        <td class="text">Pay cash on delivery</td>
                                    </tr>

                                    <tr>
                                        <td class="icon"><i class="fa fa-shopping-cart"
                                                style="color: #666;vertical-align: top"></i></td>
                                        <td class="text">Delivery within: 2-3 business days</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="main-products-section">
            <div class="container-fluid container-97">
                <div class="row mt-5 related-products">
                    <div class="col-md-12">
                        <h4 class="mb-3">রিলেটেড প্রোডাক্ট</h4>
                    </div>
                </div>

                <div class="row m-0">
                    @foreach ($related_prod as $item)
                        <div class="col-md-2 col-6 main-product">
                            <div class="main-product-inner-wrapper text-center">
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
                                        <p class="mb-0" style="text-decoration: line-through;color: #b8b8b8">
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
                            <form action="{{ route('add.cart', $data->id) }}" method="post" class="order-div">
                                @csrf
                                <input type="hidden" name="qty" value="1">
                                <input type="submit" class="order_now_btn" name="order_now" value="অর্ডার করুন">
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- bottom menu show in mobile view --}}
    <div class="bottom_menu hide" id="bottom_menu">
        <form action="{{ route('add.cart', $data->id) }}" method="post">
            @csrf
            <div class="d-flex single_product justify-content-between">
                <div class="qty_div">
                    <div class="minus-qty-div">
                        <i class="fa fa-minus qty_minus" id="qty_minus"></i>
                    </div>
                    <div class="qty-div">
                        <input type="text" name="qty" id="qty" class="qty" min="1"
                            value="{{ $qty ?? 1 }}" readonly>
                    </div>
                    <div class="plus-qty-div">
                        <i class="fa fa-plus qty_plus" id="qty_plus"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <input type="submit" class="btn px-4 order_now_btn order_now_btn_m" name="order_now"
                        value="অর্ডার করুন">
                    {{-- <input type="submit" class="btn px-4 add_cart_btn" name="add_cart" value="কার্টে রাখুন"> --}}
                    <a type="button" class="btn btn-info call_now_float_btn"
                        href="tel:{{ $web_settings->website_phone }}">কল করুন</a>
                </div>
            </div>

            {{-- @if (count($data->get_attributes) > 0)
                <div class="attributes">
                    <div class="item">
                        @foreach ($data->get_attributes as $key => $att)
                            <div class="row mb-2 mt-1">
                                <div class="col-md-12 col-12">
                                    <label class="mb-0"><b>{{$att->get_attribute->title}}</b></label><br>
                                    <input type="hidden" name="attribute_id[]" value="{{$att->get_attribute->id}}">
                                    @foreach ($att->get_attribute_items as $key2 => $attr_item)
                                        <input type="radio" id="val_{{$key}}{{$key2}}" name="attribute_item_id[{{$att->get_attribute->id}}][]"
                                               value="{{$attr_item->get_attribute_item->id}}"
                                               class="attr_checkbox" {{$key2==0?'checked':""}}>
                                        <label class="mb-0" for="val_{{$key}}{{$key2}}">
                                            <span>{{$attr_item->get_attribute_item->item_title}}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif --}}
        </form>
    </div>
@endsection



@section('script')
    <script>
        function highlightRatingStars(value) {
            $('#rating-stars i').each(function() {
                var starValue = $(this).data('value');

                if (value && starValue <= value) {
                    $(this).addClass('active').removeClass('fa-star-o').addClass('fa-star');
                } else {
                    $(this).removeClass('active fa-star').addClass('fa-star-o');
                }
            });

            if (value) {
                $('#rating-text').text(value + ' out of 5');
            } else {
                $('#rating-text').text('Select rating');
            }
        }

        $('#rating-stars').on('mouseenter', 'i', function() {
            var value = $(this).data('value');
            highlightRatingStars(value);
        }).on('mouseleave', function() {
            var current = $('#rating-input').val();
            highlightRatingStars(current);
        }).on('click', 'i', function() {
            var value = $(this).data('value');
            $('#rating-input').val(value);
            highlightRatingStars(value);
        });

        $(document).on('click', '.qty_plus', function() {
            var qty = $('.qty').val();
            qty++;
            $('.qty').val(qty);
        });

        $(document).on('click', '.qty_minus', function() {
            var qty = $('.qty').val();
            qty--;
            if (qty < 1) {
                $('.qty').val(1);
            } else {
                $('.qty').val(qty);
            }

        });

        var bottom_menu = $('#bottom_menu');

        $(window).scroll(function() {
            if ($(window).scrollTop() > 650) {
                bottom_menu.addClass('show').removeClass('hide');
            } else {
                bottom_menu.removeClass('show').addClass('hide');
            }
        });

        var reviewFetchUrl = '{{ route('product.review.fetch', ['product' => $data->id]) }}';
        var reviewStoreUrl = '{{ route('product.review.store', ['product' => $data->id]) }}';
        var currentReviewPage = 1;
        var hasMoreReviews = false;

        function getInitials(name) {
            if (!name) {
                return 'C';
            }
            var parts = name.trim().split(' ');
            if (parts.length === 1) {
                return parts[0].charAt(0).toUpperCase();
            }
            return (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase();
        }

        function renderReviews(reviews, reset) {
            if (reset) {
                $('#reviews-list').empty();
            }

            if ((!reviews || reviews.length === 0) && reset) {
                $('#reviews-list').html('<div class="text-muted">No reviews yet.</div>');
                return;
            }

            var html = '';
            reviews.forEach(function(r) {
                var safeReview = $('<div>').text(r.review).html();
                var name = r.customer_name || 'Customer';
                var initials = getInitials(name);
                var ratingStars = '';

                for (var i = 1; i <= 5; i++) {
                    if (i <= r.rating) {
                        ratingStars += '<i class="fa fa-star"></i>';
                    } else {
                        ratingStars += '<i class="fa fa-star-o"></i>';
                    }
                }

                html += '<div class="review-item-card d-flex justify-content-between">';
                html += '  <div class="d-flex align-items-start">';
                html += '      <div class="review-avatar">' + initials + '</div>';
                html += '      <div>';
                html += '          <div class="font-weight-bold mb-1">' + name + '</div>';
                html += '          <div class="text-muted small mb-2 d-none">' + (r.created_at || '') + '</div>';
                html += '          <div>' + safeReview + '</div>';
                html += '      </div>';
                html += '  </div>';
                html += '  <div class="review-rating-stars ml-3">' + ratingStars + '</div>';
                html += '</div>';
            });

            $('#reviews-list').append(html);
        }

        function loadReviews(reset) {
            if (reset) {
                currentReviewPage = 1;
                $('#reviews-list').html('<div>Loading...</div>');
            }

            $.get(reviewFetchUrl + '?page=' + currentReviewPage, function(res) {
                var reviews = res.data || [];
                hasMoreReviews = !!res.has_more;

                renderReviews(reviews, reset);

                if (hasMoreReviews) {
                    $('#load-reviews-btn').show();
                } else {
                    $('#load-reviews-btn').hide();
                }
            });
        }

        // Load more reviews on button click
        $('#load-reviews-btn').on('click', function() {
            if (!hasMoreReviews) {
                return;
            }
            currentReviewPage++;
            loadReviews(false);
        });

        // Submit review
        $('#review-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);

            if (!$('#rating-input').val()) {
                $('#review-message').html('<div class="alert alert-danger">Please select a rating.</div>');
                return;
            }

            var formData = form.serialize();
            $('#review-message').html('Submitting...');
            $.ajax({
                url: reviewStoreUrl,
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content')
                },
                success: function(resp) {
                    $('#review-message').html('<div class="alert alert-success">' + resp.message + '</div>');
                    form[0].reset();
                    loadReviews(true);
                },
                error: function(xhr) {
                    var msg = 'Failed to submit review.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        msg = xhr.responseJSON.error;
                    }
                    $('#review-message').html('<div class="alert alert-danger">' + msg + '</div>');
                }
            });
        });

        // Initial load of first page of reviews
        loadReviews(true);
    </script>
@endsection

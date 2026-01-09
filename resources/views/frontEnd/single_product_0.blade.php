@extends('frontEnd.layouts.master')

@section('title')
    {{$data->name}}
@endsection
{{--@section('fb_share')
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v12.0&appId=784363395789860&autoLogAppEvents=1"
            nonce="txEoFmhv"></script>
@endsection--}}
@section('body')
    <section>
        <div class="category_breadcrumb">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <p>
                            <a href="{{route('home')}}">Home</a>
                            /
                            <a href="javascript:void(0);">{{$data->name}}</a>
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
                        {{--@php
                            if ($data->gallery_images){
                                $photos = explode(',', $data->gallery_images);
                            }else{
                                $photos = [];
                            }
                        @endphp--}}
                        <div id="sing_prod_img_slider" class="carousel slide" data-ride="carousel">
                            @if($data->gallery_images)
                                <ol class="carousel-indicators">
                                    <li data-target="#sing_prod_img_slider" data-slide-to="0" class="active"></li>
                                    @if($data->gallery_images)
                                        @foreach($data->images as $key => $photo)
                                            <li data-target="#sing_prod_img_slider" data-slide-to="{{$key=$key+1}}"></li>
                                        @endforeach
                                    @endif
                                </ol>
                            @endif
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="{{$data->get_image ? asset($data->get_image->file_url) : asset('frontEnd/images/no_image.png')}}" class="d-block w-100"
                                         alt="">
                                </div>
                                @if($data->gallery_images)
                                    @foreach($data->images as $photo)
                                        <div class="carousel-item">
                                            <img src="{{$photo ? asset($photo) : asset('frontEnd/images/no_image.png')}}" class="d-block w-100" alt="">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @if($data->gallery_images)
                                <button class="carousel-control-prev" type="button" data-target="#sing_prod_img_slider" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </button>

                                <button class="carousel-control-next" type="button" data-target="#sing_prod_img_slider" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-5 mb-3">
                        <h2 class="text-capitalize single_prod_title">{{$data->name}}</h2>
                        <h3 class="font-weight-bold single_prod_prices">
                            @if($data->sale_price > 0)
                                <span class="old_price"
                                      style="text-decoration: line-through; color: #555;opacity: .5">{{$web_settings->currency_sign}} {{$data->price}}</span>
                                <span style="color: #c9151b">{{$web_settings->currency_sign}} {{$data->sale_price}}</span>
                            @else
                                <span style="color: #c9151b">{{$web_settings->currency_sign}} {{$data->price}}</span>
                            @endif
                        </h3>
                        {{--<p class="sku_text"><span>প্রোডাক্ট কোড: </span> <span class="p-0 pr-1">{{$data->sku}}</span></p>
                        <h4 class="single_prod_in_stock">স্টক : @if($data->stock > 0)<span class="text-success">ইন স্টক</span> @else <span
                                class="text-danger">স্টক আউট</span>@endif</h4>--}}

                        {{--<div class="qty_div">
                            <a style="color: black" href="{{route('cart.item.minus',$data->id)}}"><i class="fa fa-minus" id="qty_minus"></i></a>
                            <input type="text" name="qty" id="qty" min="1" value="{{$qty ?? 1}}" readonly>
                            <a style="color: black" href="{{route('cart.item.plus',$data->id)}}"><i class="fa fa-plus" id="qty_plus"></i></a>
                        </div>--}}

                        <form action="{{route('add.cart',$data->id)}}" method="post">
                            @csrf
                            <div class="d-flex">
                                <div class="qty-text-div">
                                    <span>পরিমান : </span>
                                </div>
                                <div class="qty_div">
                                    <div class="minus-qty-div">
                                        <i class="fa fa-minus" id="qty_minus"></i>
                                    </div>
                                    <div class="qty-div">
                                        <input type="text" name="qty" id="qty" min="1" value="{{$qty ?? 1}}" readonly>
                                    </div>
                                    <div class="plus-qty-div">
                                        <i class="fa fa-plus" id="qty_plus"></i>
                                    </div>
                                </div>
                            </div>

                            @if(count($data->get_attributes) >0)
                                <br>
                                @foreach($data->get_attributes as $att)
                                    <div class="row mb-2">
                                        <div class="col-md-6 col-12">
                                            <label for="">{{$att->get_attribute->title}}</label>
                                            <input type="hidden" name="attribute_id[]" value="{{$att->get_attribute->id}}">
                                            <select name="attribute_item_id[]" id="" class="form-control attribute_item_id">
                                                @foreach($att->get_attribute_items as $attr_item)
                                                    <option value="{{$attr_item->get_attribute_item->id}}">{{$attr_item->get_attribute_item->item_title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="mt-md-4 mt-2 d-md-flex">
                                <input type="submit" class="btn px-4 order_now_btn order_now_btn_m" name="order_now" value="অর্ডার করুন">
                                <input type="submit" class="btn px-4 add_cart_btn" name="add_cart" value="কার্টে রাখুন">
                            </div>

                            {{--<div class="mt-md-4 mt-2">
                                <input type="submit" class="btn px-4 order_now_btn order_now_btn_m" name="order_now" value="অর্ডার করুন">
                            </div>

                            <div class="mt-md-3 mt-2">
                                <input type="submit" class="btn px-4 add_cart_btn" name="add_cart" value="কার্টে রাখুন">
                            </div>--}}
                        </form>


                        <div class="mt-md-2 mt-2">
                            @if($web_settings->website_phone)
                                <h4 class="font-weight-bold">
                                    <a class="btn btn-success w-100" href="tel:{{$web_settings->website_phone}}">
                                        <i class="fa fa-phone-square"></i>
                                        {{$web_settings->website_phone}}
                                    </a>
                                </h4>
                            @endif

                            @if($web_settings->website_phone2)
                                <h4 class="font-weight-bold">
                                    <a class="btn btn-success w-100" href="tel:{{$web_settings->website_phone2}}">
                                        <i class="fa fa-phone-square"></i>
                                        {{$web_settings->website_phone2}}
                                    </a>
                                </h4>
                            @endif

                            @if($web_settings->website_phone3)
                                <h4 class="font-weight-bold">
                                    <a class="btn btn-success w-100" href="tel:{{$web_settings->website_phone3}}">
                                        <i class="fa fa-phone-square"></i>
                                        {{$web_settings->website_phone3}}
                                    </a>
                                </h4>
                            @endif
                        </div>

                        {{--<div class="fb-share-button mt-3" data-href="https://developers.facebook.com/docs/plugins/" data-layout="button" data-size="large">
                            <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore"></a>
                        </div>--}}

                        @if($shipping_methods->count() > 0)
                            <div class="col-12 mt-3 delivery_details" style="padding: 0">
                                <table class="table" style="color:#08c !important">
                                    <tbody>
                                    @foreach($shipping_methods as $item)
                                        <tr>
                                            <td style="padding-left: 0;border-bottom: 1px solid #ddd;">
                                                {{$item->text}}
                                            </td>
                                            <td style="border-bottom: 1px solid #ddd;">
                                                <b>{{$web_settings->currency_sign}} {{$item->amount}}</b>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        {{--<h6 class="font-weight-bold text-danger mt-md-3 mt-2">বিকাশ নাম্বার : {{$web_settings->bkash_merchant_numb}}</h6>--}}
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
                                    <td class="icon"><i class="fa fa-shopping-cart" style="color: #666;vertical-align: top"></i></td>
                                    <td class="text">Delivery within: 2-3 business days</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="feature-products d-md-block d-none">
                            <p>প্রয়োজনীয় প্রোডাক্ট</p>
                            <div class="feature-products-wrapper">
                                <table>
                                    @foreach($feature_prod as $item)
                                        <tr>
                                            <td class="img">
                                                <a href="{{route('single.product',[$item->slug,$item->id])}}">
                                                    <img width="50" src="{{$item->get_thumb?asset($item->get_thumb->file_url):asset('frontEnd/images/no_image.png')}}"
                                                         alt="">
                                                </a>
                                            </td>
                                            <td class="title">
                                                <a href="{{route('single.product',[$item->slug,$item->id])}}" class="text-dark">
                                                    {{$item->name}}
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
                        <ul class="nav nav-tabs nav-tabs-mod">
                            <li class="nav-item">
                                <a class="nav-link active" href="#">পন্যের বিবরণ</a>
                            </li>
                        </ul>
                        <div class="tab-content tab-content-mod">
                            <div class="tab-pane active">
                                <div>
                                    {!! $data->description !!}
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
                                    <td class="icon"><i class="fa fa-shopping-cart" style="color: #666;vertical-align: top"></i></td>
                                    <td class="text">Delivery within: 2-3 business days</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-5 related-products">
                    <div class="col-md-12">
                        <h4 class="mb-3">রিলেটেড প্রোডাক্ট</h4>
                    </div>
                </div>

                <div class="row m-0">
                    @foreach($related_prod as $item)
                        <div class="col-md-2 col-6 main-product">
                            <div class="main-product-inner-wrapper text-center">
                                <a href="{{route('single.product',[$item->slug,$item->id])}}">
                                    <img src="{{$item->get_thumb ? asset($item->get_thumb->file_url): asset('frontEnd/images/no_image.png')}}" alt="{{$item->name}}">
                                </a>
                                @if($item->sale_price != 0)
                                    <p class="mb-0" style="text-decoration: line-through;color: #b8b8b8">{{$web_settings->currency_sign}} {{$item->price}}</p>
                                    <p class="font-weight-bold mb-0" style="color: #fca204">{{$web_settings->currency_sign}} {{$item->sale_price}}</p>
                                @else
                                    <p class="font-weight-bold mb-0" style="margin-top: 24px;color: #fca204">{{$web_settings->currency_sign}} {{$item->price}}</p>
                                @endif
                                <p class="mb-0 prod_name"><a href="{{route('single.product',[$item->slug,$item->id])}}">{{$item->name}}</a></p>
                                <form action="{{route('add.cart',$data->id)}}" method="post">
                                    @csrf
                                    <input type="hidden" name="qty" value="1">
                                    <input type="submit" class="btn btn-sm w-100 mb-2 order_now_btn" name="order_now" value="অর্ডার করুন">
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>


            </div>
        </div>
    </section>
@endsection



@section('script')
    <script>
        $('#qty_plus').on('click', function () {
            var qty = $('#qty').val();
            qty++;
            $('#qty').val(qty);
        });

        $('#qty_minus').on('click', function () {
            var qty = $('#qty').val();
            qty--;
            if (qty < 1) {
                $('#qty').val(1);
            } else {
                $('#qty').val(qty);
            }

        });
    </script>
@endsection

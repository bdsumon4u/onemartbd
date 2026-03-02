@extends('backEnd.admin.layouts.master')

@section('title')
    Web Settings
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendor/summernote/css/summernote-bs4.css') }}">
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <!-- ============================================================== -->
                <!-- pageheader  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Web Settings</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Web Settings</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->
                <form action="{{ route('admin.settings.web.update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                        <div class="col-md-6 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="website_address">Website Address</label>
                                        <textarea class="form-control form-control-textarea" name="website_address" id="website_address">{!! $data->website_address ?? null !!}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="marquee_text">Header Marquee Text</label>
                                        <input type="text" class="form-control" name="marquee_text" id="marquee_text"
                                            value="{{ $data->marquee_text ?? null }}"
                                            placeholder="Enter scrolling text for the header top bar">
                                    </div>

                                    <div class="form-group">
                                        <label for="website_phone">Website Phone</label>
                                        <input type="text" class="form-control" name="website_phone" id="website_phone"
                                            value="{{ $data->website_phone ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="website_phone2">Website Phone 2</label>
                                        <input type="text" class="form-control" name="website_phone2" id="website_phone2"
                                            value="{{ $data->website_phone2 ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="website_phone3">Website Phone 3</label>
                                        <input type="text" class="form-control" name="website_phone3" id="website_phone3"
                                            value="{{ $data->website_phone3 ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="website_email">Website Email</label>

                                        <input type="email" class="form-control" name="website_email" id="website_email"
                                            value="{{ $data->website_email ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="website_email">Website Email 2</label>

                                        <input type="email" class="form-control" name="website_email2" id="website_email2"
                                            value="{{ $data->website_email2 ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="website_facebook">Website Facebook Link</label>

                                        <input type="text" class="form-control" name="website_facebook"
                                            id="website_facebook" value="{{ $data->website_facebook ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="website_twitter">Website Twitter Link</label>
                                        <input type="text" class="form-control" name="website_twitter"
                                            id="website_twitter" value="{{ $data->website_twitter ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="website_youtube">Website Youtube Link</label>
                                        <input type="text" class="form-control" name="website_youtube"
                                            id="website_youtube" value="{{ $data->website_youtube ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="website_instagram">Website Instagram Link</label>
                                        <input type="text" class="form-control" name="website_instagram"
                                            id="website_instagram" value="{{ $data->website_instagram ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="messenger_link">Website Messenger Link</label>
                                        <input type="text" class="form-control" name="messenger_link"
                                            id="messenger_link" value="{{ $data->messenger_link ?? null }}"
                                            placeholder="https://m.me/your-page or profile link">
                                    </div>

                                    <div class="form-group">
                                        <label for="whatsapp_number">Website Whatsapp</label>
                                        <input type="text" class="form-control" name="whatsapp_number"
                                            id="whatsapp_number" value="{{ $data->whatsapp_number ?? null }}"
                                            placeholder="Start without + eg. 88017.....">
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-2">
                                <div class="card-header">
                                    <b>SMS Templates</b>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" id="is_order_confirm_sms"
                                            name="is_order_confirm_sms"
                                            {{ $data->is_order_confirm_sms == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_order_confirm_sms">Order Confirm
                                            SMS?</label>
                                    </div>

                                    <div class="form-group">
                                        <label for="order_confirm_sms">Order Confirm SMS</label>
                                        <textarea class="form-control form-control-textarea" name="order_confirm_sms" id="order_confirm_sms" rows="10">{!! $data->order_confirm_sms ?? null !!}</textarea>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="order_custom_sms">Order Custom SMS</label>
                                        <textarea class="form-control form-control-textarea" name="order_custom_sms" id="order_custom_sms" rows="4">{!! $data->order_custom_sms ?? null !!}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-2">
                                <div class="card-header">
                                    <b>API</b>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="api_access_token">API Access Token</label>
                                        <textarea class="form-control form-control-textarea" name="api_access_token" id="order_confirm_sms" rows="4"
                                            readonly>{!! $data->api_access_token ?? null !!}</textarea>
                                    </div>
                                    <a href="{{ route('admin.generate_api_token') }}" class="btn btn-info btn-sm"
                                        onclick="return confirm('Are you sure?')">Generate
                                        Token</a>
                                </div>
                            </div>

                            <div class="card mt-2">
                                <div class="card-header">
                                    <b>Forwarding</b>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="master_domain">Master Domain (for order forwarding)</label>
                                        <input type="text" class="form-control" name="master_domain" id="master_domain"
                                               value="{{ $data->master_domain ?? null }}"
                                               placeholder="e.g. https://onemartbd.test">
                                        <small class="form-text text-muted">
                                            Leave empty to treat this site as a master. Set a full domain/URL to forward
                                            orders from this site to that master.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="website_copyright_text">Website Copyright Text</label>
                                        <textarea class="form-control form-control-textarea" name="website_copyright_text" id="website_copyright_text">{!! $data->website_copyright_text ?? null !!}</textarea>
                                    </div>

                                    {{-- <div class="form-group">
                                        <label class="col-md-12">Text area</label>
                                        <div class="col-md-12">
                                            <textarea class="form-control" rows="5"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-12">Input Select</label>
                                        <div class="col-sm-12">
                                            <select class="form-control">
                                                <option>1</option>
                                                <option>2</option>
                                                <option>3</option>
                                                <option>4</option>
                                                <option>5</option>
                                            </select>
                                        </div>
                                    </div> --}}

                                    <div class="form-group">
                                        <label>Website Header Logo</label>
                                        <input type="file" class="form-control mb-3" name="website_header_logo">
                                        <input type="hidden" value="{{ $data->website_header_logo ?? null }}"
                                            name="website_header_logo_old">
                                        <img width="150"
                                            src="{{ $data->get_logo ? asset($data->get_logo->file_url) : asset('frontEnd/images/no_image.png') }}"
                                            alt="Website Logo">
                                    </div>

                                    <div class="form-group">
                                        <label>Website Favicon</label>
                                        <input type="file" class="form-control mb-3" name="website_favicon">
                                        <input type="hidden" value="{{ $data->website_favicon ?? null }}"
                                            name="website_favicon_old">
                                        <img width="64" height="64"
                                            src="{{ $data->get_fav ? asset($data->get_fav->file_url) : asset('frontEnd/images/no_image.png') }}"
                                            alt="Website Logo">
                                    </div>

                                    <div class="form-group">
                                        <label for="website_linkedin">Currency Sign</label>
                                        <input type="text" class="form-control" name="currency_sign"
                                            id="currency_sign" value="{{ $data->currency_sign ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="delivery_info">Bkash Merchant Number</label>
                                        <input type="text" class="form-control" id="bkash_merchant_numb"
                                            name="bkash_merchant_numb" value="{{ $data->bkash_merchant_numb }}">
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-2">
                                <div class="card-header">
                                    <b>GTM</b>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="gtm_script_head">GTM Code in Head Tag</label>
                                        <textarea class="form-control form-control-textarea" id="gtm_script_head" name="gtm_script_head" rows="4">{{ $data->gtm_script_head }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="gtm_script_body">GTM Code in Body Tag</label>
                                        <textarea class="form-control form-control-textarea" name="gtm_script_body" id="gtm_script_body" rows="4">{!! $data->gtm_script_body ?? null !!}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-2">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="fb_pixel_id">Facebook Pixel ID</label>
                                        <input type="text" class="form-control" id="fb_pixel_id" name="fb_pixel_id"
                                            value="{{ $data->fb_pixel_id }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="fb_cpi_access_token">Conversion API Access Token</label>
                                        <textarea class="form-control form-control-textarea" name="fb_cpi_access_token" id="fb_cpi_access_token"
                                            rows="4">{!! $data->fb_cpi_access_token ?? null !!}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="fb_test_event_code">Facebook CAPI Test Event Code</label>
                                        <input type="text" class="form-control" id="fb_test_event_code"
                                            name="fb_test_event_code" value="{{ $data->fb_test_event_code }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-2">
                                <div class="card-header">
                                    Whatsapp API
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="wp_phone_number_id">Phone Number ID</label>
                                        <input type="text" class="form-control" id="wp_phone_number_id"
                                            name="wp_phone_number_id" value="{{ $data->wp_phone_number_id }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="wp_access_token">Access Token</label>
                                        <textarea class="form-control form-control-textarea" name="wp_access_token" id="wp_access_token" rows="4">{!! $data->wp_access_token ?? null !!}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-2">
                                <div class="card-header">
                                    <b>Order Defender (Fraud Prevention)</b>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-4">
                                        <input type="checkbox" class="form-check-input" id="is_order_defender_enabled"
                                            name="is_order_defender_enabled"
                                            {{ $data->is_order_defender_enabled ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_order_defender_enabled">Enable Order
                                            Defender</label>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="d-block mb-2"><strong>Orders per</strong></label>
                                        <div class="d-flex flex-wrap" style="gap: 12px;">
                                            <div>
                                                <label for="order_limit_per_minute" class="small text-muted">Minute</label>
                                                <input type="number" class="form-control" id="order_limit_per_minute"
                                                    name="order_limit_per_minute" min="0"
                                                    value="{{ $data->order_limit_per_minute }}"
                                                    placeholder="Skip"
                                                    style="max-width: 100px;">
                                            </div>
                                            <div>
                                                <label for="order_limit_per_hour" class="small text-muted">Hour</label>
                                                <input type="number" class="form-control" id="order_limit_per_hour"
                                                    name="order_limit_per_hour" min="0"
                                                    value="{{ $data->order_limit_per_hour }}"
                                                    placeholder="Skip"
                                                    style="max-width: 100px;">
                                            </div>
                                            <div>
                                                <label for="order_limit_per_day" class="small text-muted">Day</label>
                                                <input type="number" class="form-control" id="order_limit_per_day"
                                                    name="order_limit_per_day" min="0"
                                                    value="{{ $data->order_limit_per_day }}"
                                                    placeholder="Skip"
                                                    style="max-width: 100px;">
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Leave empty or 0 to skip that check. Applies to each selected identifier below.</small>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="d-block mb-2"><strong>Restrict by</strong></label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="order_defender_restrict_by_ip"
                                                name="order_defender_restrict_by_ip"
                                                {{ ($data->order_defender_restrict_by_ip ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="order_defender_restrict_by_ip">IP Address</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="order_defender_restrict_by_phone"
                                                name="order_defender_restrict_by_phone"
                                                {{ ($data->order_defender_restrict_by_phone ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="order_defender_restrict_by_phone">Phone Number</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="order_defender_restrict_by_user_agent"
                                                name="order_defender_restrict_by_user_agent"
                                                {{ ($data->order_defender_restrict_by_user_agent ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="order_defender_restrict_by_user_agent">User Agent (Device/Browser)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-2">
                                <div class="card-header">
                                    <b>Extra Special Discount (Checkout Offer)</b>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="extra_special_discount_amount">Discount Amount (BDT)</label>
                                        <input type="number" class="form-control" id="extra_special_discount_amount"
                                            name="extra_special_discount_amount" min="0" step="1"
                                            value="{{ $data->extra_special_discount_amount ?? 30 }}"
                                            placeholder="e.g. 30">
                                        <small class="form-text text-muted">
                                            This amount will be subtracted from the customer's order total when they win
                                            the extra special discount at checkout.
                                        </small>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label for="extra_special_discount_chance">Chance to Win Discount (%)</label>
                                        <input type="number" class="form-control" id="extra_special_discount_chance"
                                            name="extra_special_discount_chance" min="0" max="100" step="1"
                                            value="{{ $data->extra_special_discount_chance ?? 100 }}"
                                            placeholder="0–100">
                                        <small class="form-text text-muted">
                                            Set a percentage between 0 and 100. For example, 30 means roughly 30% of
                                            checkout close attempts will see the discount popup. Use 0 to disable and
                                            100 to always show it.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-success mt-4">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script src="{{ asset('backEnd/assets/vendor/summernote/js/summernote-bs4.js') }}"></script>
    <script>
        $('.summernote').summernote();
    </script>
@endsection

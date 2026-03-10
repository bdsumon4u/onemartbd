@extends('backEnd.admin.layouts.master')

@section('title')
    WhatsApp SMS Settings
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendor/summernote/css/summernote-bs4.css') }}">
    <style>
        .card {
            border-radius: 8px;
        }

        .card-header {
            background-color: #f8f9fa;
        }

        textarea.form-control {
            resize: none;
        }
    </style>
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
                            <h2 class="pageheader-title">WhatsApp SMS Settings</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">WhatsApp SMS Settings</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->
                <form action="{{ route('admin.settings.whatsapp.update') }}" method="POST">
                    @csrf

                    <!-- Global SMS Settings -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0 text-white">Global SMS Controls</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="d-block font-weight-bold mb-2">Global SMS Switch</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_sms_enabled"
                                                        name="is_sms_enabled" value="1"
                                                        {{ $webSettings && $webSettings->is_sms_enabled ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_sms_enabled">
                                                        Enable SMS Sending
                                                    </label>
                                                </div>
                                                <small class="text-muted">If disabled, no WhatsApp SMS will be sent
                                                    regardless of individual status settings</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="sms_start_time" class="font-weight-bold">SMS Start Time</label>
                                                <input type="time" class="form-control" id="sms_start_time"
                                                    name="sms_start_time" value="{{ $webSettings->sms_start_time ?? '' }}">
                                                <small class="text-muted">WhatsApp SMS will only be sent after this
                                                    time</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="sms_end_time" class="font-weight-bold">SMS End Time</label>
                                                <input type="time" class="form-control" id="sms_end_time"
                                                    name="sms_end_time" value="{{ $webSettings->sms_end_time ?? '' }}">
                                                <small class="text-muted">WhatsApp SMS will only be sent before this
                                                    time</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mt-3 mb-0">
                                        <strong>Note:</strong> If both start and end times are set, WhatsApp SMS will only
                                        be sent within that time range. Leave blank to allow SMS at any time.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Individual WhatsApp SMS Status Settings -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h5 class="mb-0">Individual Status WhatsApp Templates</h5>
                        </div>
                    </div>

                    <div class="row g-3">
                        @foreach ($smsSettings as $numeric => $setting)
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 p-2">
                                <div class="card border shadow-sm h-100">
                                    <div class="card-header py-2 text-center fw-semibold text-primary">
                                        {{ $setting->name }}
                                    </div>

                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="status_wp{{ $numeric }}"
                                                    name="smsSettings[{{ $setting->status }}][wp_active]" value="1"
                                                    {{ $setting->is_whatsapp ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_wp{{ $numeric }}">
                                                    Is Active?
                                                </label>
                                            </div>
                                        </div>

                                        <div>
                                            <input name="smsSettings[{{ $setting->status }}][template_name]"
                                                class="form-control form-control-sm" rows="3"
                                                placeholder="WhatsApp Template Name" value="{{ $setting->template_name }}">
                                        </div>
                                        <div class="mt-2">
                                            <small>
                                                @if (
                                                    $setting->status == 2 ||
                                                        $setting->status == 9 ||
                                                        $setting->status == 4 ||
                                                        $setting->status == 7 ||
                                                        $setting->status == 1)
                                                    Notes: <br> @{{ 1 }} -> Invoice ID <br>
                                                @elseif ($setting->status == 13)
                                                    Notes: <br> @{{ 1 }} -> Invoice ID <br>
                                                    @{{ 2 }} -> Product Name <br>
                                                    @{{ 3 }} -> Amount <br>
                                                @elseif ($setting->status == 6)
                                                    Notes: <br> @{{ 1 }} -> Invoice ID <br>
                                                    @{{ 2 }} -> Order Tracking Link <br>
                                                @endif
                                                ***Must be write template name same as in meta template
                                            </small>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2">Update SMS Settings</button>
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

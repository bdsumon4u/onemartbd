@extends('backEnd.admin.layouts.master')

@section('title')
    SMS Settings
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
                            <h2 class="pageheader-title">SMS Settings</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">SMS Settings</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->
                <form action="{{ route('admin.settings.sms.update') }}" method="POST">
                    @csrf

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
                                                    id="status_sms{{ $numeric }}"
                                                    name="smsSettings[{{ $setting->status }}][active]" value="1"
                                                    {{ $setting->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_sms{{ $numeric }}">
                                                    Is Active?
                                                </label>
                                            </div>

                                        </div>

                                        <div>
                                            <textarea name="smsSettings[{{ $setting->status }}][message]" class="form-control form-control-sm" rows="3"
                                                placeholder="Enter message...">{{ $setting->message }}</textarea>
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

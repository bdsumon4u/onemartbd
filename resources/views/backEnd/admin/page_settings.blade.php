@extends('backEnd.admin.layouts.master')

@section('title')
    Page Settings
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
                            <h2 class="pageheader-title">Page Settings</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Page Settings</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->
                <form action="{{ route('admin.settings.page.update') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="about_us">About Us</label>
                                        <textarea name="about_us" id="about_us" class="summernote" rows="15">{!! old('about_us', $data->about_us ?? '') !!}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="delivery_policy">Delivery Policy</label>
                                        <textarea name="delivery_policy" id="delivery_policy" class="summernote">{!! old('delivery_policy', $data->delivery_policy ?? '') !!}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="return_policy">Return Policy</label>
                                        <textarea name="return_policy" id="return_policy" class="summernote">{!! old('return_policy', $data->return_policy ?? '') !!}</textarea>
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
        $('.summernote').summernote({
            height: 150
        });
    </script>
@endsection

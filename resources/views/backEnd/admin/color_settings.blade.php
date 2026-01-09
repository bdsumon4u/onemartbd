@extends('backEnd.admin.layouts.master')

@section('title')
    Color Settings
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('backEnd/assets/vendor/summernote/css/summernote-bs4.css')}}">
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
                            <h2 class="pageheader-title">Color Settings</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Color Settings</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->
                <form action="{{route('admin.settings.color.update')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="primary_color">Primary Color</label>
                                        <input type="text" class="form-control" name="primary_color" id="primary_color" value="{{$data->primary_color ?? null}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="secondary_color">Secondary Color</label>
                                        <input type="text" class="form-control" name="secondary_color" id="secondary_color" value="{{$data->secondary_color ?? null}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="header_top_color">Header Top Color</label>
                                        <input type="text" class="form-control" name="header_top_color" id="header_top_color" value="{{$data->header_top_color ?? null}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="header_color">Header Color</label>
                                        <input type="text" class="form-control" name="header_color" id="header_color" value="{{$data->header_color ?? null}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="header_bottom_color">Header Bottom Color</label>
                                        <input type="text" class="form-control" name="header_bottom_color" id="header_bottom_color" value="{{$data->header_bottom_color ?? null}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="button_color">Button Color</label>
                                        <input type="text" class="form-control" name="button_color" id="button_color" value="{{$data->button_color ?? null}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="button_hover_color">Button Hover Color</label>
                                        <input type="text" class="form-control" name="button_hover_color" id="button_hover_color" value="{{$data->button_hover_color ?? null}}">
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
    <script src="{{asset('backEnd/assets/vendor/summernote/js/summernote-bs4.js')}}"></script>
    <script>
        $('.summernote').summernote();
    </script>

@endsection

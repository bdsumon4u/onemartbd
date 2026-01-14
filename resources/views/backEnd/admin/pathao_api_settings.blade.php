@extends('backEnd.admin.layouts.master')

@section('title')
    Pathao API Settings
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
                            <h2 class="pageheader-title">Pathao API Settings</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Pathao API Settings</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->

                <div class="row">
                    <div class="col-md-6 col-12">
                        <form action="{{ route('admin.settings.pathao.api.update') }}" method="post">
                            @csrf
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                            value="1"
                                            {{ old('is_active', (int) ($data->is_active ?? 0)) === 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Pathao API?</label>
                                    </div>
                                    <div class="form-group">
                                        <label for="store_id">Store ID</label>
                                        <input class="form-control" type="text" name="store_id" id="store_id"
                                            value="{{ $data->store_id ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="client_id">Client ID</label>
                                        <input class="form-control" type="text" name="client_id" id="client_id"
                                            value="{{ $data->client_id ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="client_secret">Client Secret</label>
                                        <input type="text" class="form-control" name="client_secret" id="client_secret"
                                            value="{{ $data->client_secret ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="username">Email</label>
                                        <input type="text" class="form-control" name="username" id="username"
                                            value="{{ $data->username ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="text" class="form-control" name="password" id="password"
                                            value="{{ $data->password ?? null }}">
                                    </div>
                                    <button type="submit" class="btn btn-success mt-4">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6 col-12">
                        <form action="{{ route('admin.settings.pathao.api.gen_access_token') }}" method="post">
                            @csrf
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="access_token">Access Token</label>
                                        <textarea class="form-control form-control-textarea" name="access_token" id="access_token" rows="5" disabled>{!! $data->access_token ?? null !!}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="refresh_token">Refresh Token</label>
                                        <textarea class="form-control form-control-textarea" name="refresh_token" id="refresh_token" rows="5" disabled>{!! $data->refresh_token ?? null !!}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-success mt-4">Generate New Access Token</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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

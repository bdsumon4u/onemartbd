@extends('backEnd.admin.layouts.master')

@section('title')
    CarryBee API Settings
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
                            <h2 class="pageheader-title">CarryBee API Settings</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">CarryBee API Settings</li>
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
                        <form action="{{ route('admin.settings.carrybee.api.update') }}" method="post">
                            @csrf
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                            value="1"
                                            {{ old('is_active', (int) ($data->is_active ?? 0)) === 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">CarryBee API?</label>
                                    </div>

                                    <div class="form-group">
                                        <label for="store_id">Store ID</label>
                                        <input type="text" class="form-control" name="store_id" id="store_id"
                                            value="{{ $data->store_id ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="client_id">Client ID</label>
                                        <input type="text" class="form-control" name="client_id" id="client_id"
                                            value="{{ $data->client_id ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="client_secret">Client Secret</label>
                                        <input type="text" class="form-control" name="client_secret" id="client_secret"
                                            value="{{ $data->client_secret ?? null }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="client_context">Client Context</label>
                                        <input type="text" class="form-control" name="client_context" id="client_context"
                                            value="{{ $data->client_context ?? null }}">
                                    </div>

                                    <button type="submit" class="btn btn-success mt-4">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6 col-12">
                        <form action="{{ route('admin.settings.carrybee.api.gen_access_token') }}" method="post">
                            @csrf
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="access_token">Authorization</label>
                                        <textarea class="form-control form-control-textarea" name="access_token" id="access_token" rows="5" disabled>CarryBee now uses Client-ID, Client-Secret and Client-Context headers for authorization. Access tokens are no longer required.</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-secondary mt-4">
                                        Acknowledge Header-based Authorization
                                    </button>
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

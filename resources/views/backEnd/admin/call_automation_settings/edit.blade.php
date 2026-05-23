@extends('backEnd.admin.layouts.master')

@section('title')
    Call Automation Settings
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <div class="page-header">
                    <h2 class="pageheader-title">Call Automation Settings</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"
                                        class="breadcrumb-link">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Call Automation Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.call-automation.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="enabled"
                                                name="enabled" value="1"
                                                {{ old('enabled', $settings->enabled ?? true) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="enabled">Enable auto call</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>API Key</label>
                                        <input name="api_key" class="form-control"
                                            value="{{ old('api_key', $settings->api_key ?? '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label>DID</label>
                                        <input name="did" class="form-control"
                                            value="{{ old('did', $settings->did ?? '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Main text</label>
                                        <textarea name="maintext" class="form-control" rows="3">{{ old('maintext', $settings->maintext ?? '') }}</textarea>
                                    </div>

                                    <div class="alert alert-light border">
                                        <div class="font-weight-bold mb-2">Available parameters</div>
                                        <div class="row">
                                            @foreach ($availableParameters as $parameter => $label)
                                                <div class="col-md-6 mb-2">
                                                    <code>{{ $parameter }}</code>
                                                    <span class="text-muted">- {{ $label }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Text 1</label>
                                        <input name="text1" class="form-control"
                                            value="{{ old('text1', $settings->text1 ?? '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Text 2</label>
                                        <input name="text2" class="form-control"
                                            value="{{ old('text2', $settings->text2 ?? '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Call URL</label>
                                        <input name="call_url" class="form-control"
                                            value="{{ old('call_url', $settings->call_url ?? '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Retry URL</label>
                                        <input name="retry_url" class="form-control"
                                            value="{{ old('retry_url', $settings->retry_url ?? '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Check Response URL</label>
                                        <input name="check_response_url" class="form-control"
                                            value="{{ old('check_response_url', $settings->check_response_url ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

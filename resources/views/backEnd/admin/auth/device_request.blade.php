@extends('backEnd.admin.layouts.master')

@section('title')
    Device Approval Required
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="container-fluid dashboard-content ">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h4>Device Approval Required</h4>
                        </div>
                        <div class="card-body">
                            <p>Your device is not yet approved for login. Please request approval below.</p>
                            <form method="post" action="{{ route($guard . '.device.request.submit') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Request Approval</button>
                            </form>
                            <hr>
                            <p class="text-muted">Device Info:</p>
                            <ul>
                                <li>Device Token: {{ $device->device_token ?? 'N/A' }}</li>
                                <li>User Agent: {{ $device->user_agent ?? request()->userAgent() }}</li>
                                <li>IP Address: {{ $device->ip_address ?? request()->ip() }}</li>
                            </ul>
                            @if (session('info'))
                                <div class="alert alert-info mt-3">{{ session('info') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('backEnd.admin.layouts.master')

@section('title')
    Device Approval Requests
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="container-fluid dashboard-content ">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h4>Pending Device Approval Requests</h4>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered text-center table-striped">
                                <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>User</th>
                                        <th>Device Token</th>
                                        <th>User Agent</th>
                                        <th>IP Address</th>
                                        <th>Requested At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($devices as $i => $device)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $device->user_type }} #{{ $device->user_id }}</td>
                                            <td>{{ $device->device_token }}</td>
                                            <td>{{ $device->user_agent }}</td>
                                            <td>{{ $device->ip_address }}</td>
                                            <td>{{ $device->requested_at }}</td>
                                            <td>
                                                @unless($device->approved)
                                                <form method="post"
                                                    action="{{ route($guard . '.device.approve', $device->id) }}"
                                                    style="display:inline-block;">
                                                    @csrf
                                                    <button class="btn btn-success btn-sm">Approve</button>
                                                </form>
                                                @endunless
                                                <form method="post"
                                                    action="{{ route($guard . '.device.reject', $device->id) }}"
                                                    style="display:inline-block;">
                                                    @csrf
                                                    <button class="btn btn-danger btn-sm">Reject</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($devices->isEmpty())
                                        <tr>
                                            <td colspan="7" class="text-center text-danger font-weight-bold">No pending
                                                requests found!</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

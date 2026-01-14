@extends('backEnd.admin.layouts.master')

@section('title')
    IP List
@endsection

@php
    $data = $data ?? [];
    $statusRoute = Auth::guard('admin')->check()
        ? 'admin.ip.status'
        : (Auth::guard('manager')->check() ? 'manager.ip.status' : null);
@endphp
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
                            <h2 class="pageheader-title">IP List</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">IP List</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->

                {{--<div class="row mb-3">
                    <div class="col-12">
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#add_customer">Add Customer</button>
                    </div>
                </div>--}}

                {{--<div class="row mb-2">
                    @if(Auth::guard('admin')->check())
                        <div class="col-md-9 col-12">
                            <form action="{{route('admin.customers.customer_export')}}" method="post" id="all_customers_export">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" id="all_ord_id" name="all_ord_id">
                                    <button type="button" id="customer_export" class="btn btn-info btn-sm ml-1">Export</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>--}}

                <div class="row">
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered text-center table-striped">
                                    <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>IP Address</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($i =1)
                                    @if($data->count() > 0)
                                        @foreach($data as $item)
                                            <tr id="tr_{{$item->id}}">
                                                <td>{{$i++}}</td>
                                                <td class="text-left">{{$item->ip_address}}</td>
                                                <td>
                                                    @if($statusRoute)
                                                        @if($item->status)
                                                            <a href="{{ route($statusRoute, [$item->id, 0]) }}"
                                                               onclick="return confirm('Are you sure to unblock this?')"
                                                               class="btn btn-success btn-sm">Unblock</a>
                                                        @else
                                                            <a href="{{ route($statusRoute, [$item->id, 1]) }}"
                                                               onclick="return confirm('Are you sure to block this?')"
                                                               class="btn btn-danger btn-sm">Block</a>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center text-danger font-weight-bold">No Data Found!</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{$data->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function () {

        });
    </script>
@endsection

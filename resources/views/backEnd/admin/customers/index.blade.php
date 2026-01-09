@extends('backEnd.admin.layouts.master')

@section('title')
    Customers
@endsection

@php
    $data = $data ?? [];
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
                            <h2 class="pageheader-title">Customers</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Customers</li>
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

                <div class="row mb-2">
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

                        <div class="col-md-3 col-12">
                            <form action="{{Auth::guard('admin')->check()?route('admin.customers'):(Auth::guard('manager')->check()?route('manager.customers'):"")}}"
                                  method="get" class="form-inline float-md-right">
                                <div class="form-group">
                                    <input type="text" class="form-control mr-2" placeholder="Search..." name="query" value="{{request()->query('query')}}">
                                    <button class="btn btn-dark btn-sm">Search</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered text-center table-striped">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="master"></th>
                                        <th>SL.</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($i =1)
                                    @if($data->count() > 0)
                                        @foreach($data as $item)
                                            <tr id="tr_{{$item->id}}">
                                                <td><input type="checkbox" class="sub_chk" data-id="{{$item->id}}">
                                                <td>{{$i++}}</td>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->phone}}</td>
                                                <td>{{$item->email}}</td>
                                                <td>{{$item->address}}</td>
                                                <td class="text-center">
                                                    @if($item->status == 1)
                                                        <a href="{{Auth::guard('admin')->check()?route('admin.customer.status',[$item->id,0]):(Auth::guard('manager')->check()?route('manager.customer.status',[$item->id,0]):"")}}"
                                                           onclick="return confirm('Are You Sure To Block This Customer?')" class="btn btn-success btn-sm">Unblocked</a>
                                                    @elseif($item->status == 0)
                                                        <a href="{{Auth::guard('admin')->check()?route('admin.customer.status',[$item->id,1]):(Auth::guard('manager')->check()?route('manager.customer.status',[$item->id,1]):"")}}"
                                                           onclick="return confirm('Are You Sure To Unblock This Customer?')" class="btn btn-danger btn-sm">Blocked</a>
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


            $('#master').on('click', function (e) {
                if ($(this).is(':checked', true)) {
                    $(".sub_chk").prop('checked', true);
                } else {
                    $(".sub_chk").prop('checked', false);
                }
            });

            $('#customer_export').on('click', function (e) {
                var allVals = [];
                $(".sub_chk:checked").each(function () {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    $('#all_ord_id').val(allVals);
                    $('#all_customers_export').submit();
                }
            });
        });
    </script>
@endsection

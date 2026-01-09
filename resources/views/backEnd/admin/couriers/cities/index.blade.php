@extends('backEnd.admin.layouts.master')

@section('title')
    Courier Cities
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
                            <h2 class="pageheader-title">Courier Cities</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : ""))}}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Courier Cities</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->

                <div class="row mb-3">
                    <div class="col-12">
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#add_courier_city">Add Courier City</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered text-center table-striped">
                                    <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>Courier Name</th>
                                        <th>City Name</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($i =1)
                                    @if($data->count() > 0)
                                        @foreach($data as $item)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$item->courier_name}}</td>
                                                <td>{{$item->city_name}}</td>
                                                <td>
                                                    @if($item->status ==1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="mr-1 edit_cour_city_btn" data-toggle="modal" data-target="#edit_courier_city"
                                                       data-id="{{$item->id}}"
                                                       data-courier_id="{{$item->courier_id}}"
                                                       data-city_name="{{$item->city_name}}"
                                                       data-status="{{$item->status}}"
                                                    >
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{Auth::guard('admin')->check() ? route('admin.courier.city.delete',$item->id) : (Auth::guard('manager')->check() ? route('manager.courier.city.delete',$item->id) : "")}}" onclick="return confirm('Are you sure to delete this?')"><i
                                                            class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center text-danger font-weight-bold">No Data Found!</td>
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
    </div>

    {{--add_courier--}}
    <div class="modal fade" id="add_courier_city" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Courier City</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{Auth::guard('admin')->check() ? route('admin.courier.city.store') : (Auth::guard('manager')->check() ? route('manager.courier.city.store') : "")}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="courier_id">Courier Name <span class="text-danger">*</span></label>
                            <select name="courier_id" id="courier_id" class="form-control select2">
                                <option value="">Select A Courier</option>
                                @foreach($couriers as $key => $item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="city_name">City Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="city_name" name="city_name" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-success" value="Add">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--edit courier--}}
    <div class="modal fade" id="edit_courier_city" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Courier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{Auth::guard('admin')->check() ? route('admin.courier.city.update') : (Auth::guard('manager')->check() ? route('manager.courier.city.update') : "")}}" method="post">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="courier_id_e">Courier Name <span class="text-danger">*</span></label>
                            <select name="courier_id" id="courier_id_e" class="form-control select2">
                                <option value="">Select A Courier</option>
                                @foreach($couriers as $key => $item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="city_name_e">City Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="city_name_e" name="city_name" required>
                        </div>

                        <div class="form-group">
                            <label for="status_e">Status</label>
                            <select name="status" id="status_e" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-success" value="Update">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
        $('.edit_cour_city_btn').on('click', function () {
            $('#id').val($(this).data('id'));
            $('#courier_id_e').val($(this).data('courier_id'));
            $('#city_name_e').val($(this).data('city_name'));
            $('#status_e').val($(this).data('status'));
        })
    </script>
@endsection

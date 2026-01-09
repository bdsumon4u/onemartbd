@extends('backEnd.admin.layouts.master')

@section('title')
    Couriers
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
                            <h2 class="pageheader-title">Couriers</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : ""))}}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Couriers</li>
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
                        {{-- <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#add_courier">Add Courier</button> --}}
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
                                        {{-- <th>City Available</th>
                                        <th>Zone Available</th> --}}
                                        <th>Courier Charge ISD</th>
                                        <th>Courier Charge OSD</th>
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
                                                {{-- <td>
                                                    @if($item->is_city == 1)
                                                        On
                                                    @else
                                                        Off
                                                    @endif
                                                </td>

                                                <td>
                                                    @if($item->is_zone == 1)
                                                        On
                                                    @else
                                                        Off
                                                    @endif
                                                </td> --}}
                                                <td>{{$web_settings->currency_sign}} {{ number_format($item->courier_charge_isd,2) }}</td>
                                                <td>{{$web_settings->currency_sign}} {{ number_format($item->courier_charge_osd,2) }}</td>
                                                <td>
                                                    @if($item->status ==1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="mr-1 edit_cour_btn" data-toggle="modal" data-target="#edit_courier"
                                                       data-id="{{$item->id}}"
                                                       data-courier_name="{{$item->courier_name}}"
                                                       data-courier_charge_isd="{{$item->courier_charge_isd}}"
                                                       data-courier_charge_osd="{{$item->courier_charge_osd}}"
                                                       data-status="{{$item->status}}"
                                                    >
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    {{-- <a href="{{Auth::guard('admin')->check() ? route('admin.courier.delete',$item->id) : (Auth::guard('manager')->check() ? route('manager.courier.delete',$item->id) : "")}}" onclick="return confirm('Are you sure to delete this?')"><i
                                                            class="fa fa-trash"></i></a> --}}
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
    <div class="modal fade" id="add_courier" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Courier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{Auth::guard('admin')->check() ? route('admin.courier.store') : (Auth::guard('manager')->check() ? route('manager.courier.store') : "")}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="courier_name">Courier Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="courier_name" name="courier_name" required>
                        </div>

                        <div class="form-group">
                            <label for="courier_charge">Courier Charge ISD <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="courier_charge_isd" name="courier_charge_isd" required>
                        </div>

                        <div class="form-group">
                            <label for="courier_charge">Courier Charge OSD <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="courier_charge_osd" name="courier_charge_osd" required>
                        </div>

                        {{-- <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="is_city" name="is_city">
                            <label for="is_city">City Available</label>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="is_zone" name="is_zone">
                            <label for="is_zone">Zone Available</label>
                        </div> --}}

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
    <div class="modal fade" id="edit_courier" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Courier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{Auth::guard('admin')->check() ? route('admin.courier.update') : (Auth::guard('manager')->check() ? route('manager.courier.update') : "")}}" method="post">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="courier_name_e">Courier Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="courier_name_e" name="courier_name" required>
                        </div>

                        <div class="form-group">
                            <label for="courier_charge_isd_e">Courier Charge ISD <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="courier_charge_isd_e" name="courier_charge_isd" required>
                        </div>
                        <div class="form-group">
                            <label for="courier_charge_osd_e">Courier Charge OSD <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="courier_charge_osd_e" name="courier_charge_osd" required>
                        </div>

                        {{-- <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="is_city_e" name="is_city">
                            <label for="is_city_e">City Available</label>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="is_zone_e" name="is_zone">
                            <label for="is_zone_e">Zone Available</label>
                        </div> --}}

                        <div class="form-group">
                            <label for="status">Status</label>
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
        $('.edit_cour_btn').on('click', function () {
            $('#id').val($(this).data('id'));
            $('#courier_name_e').val($(this).data('courier_name'));
            $('#courier_charge_isd_e').val($(this).data('courier_charge_isd'));
            $('#courier_charge_osd_e').val($(this).data('courier_charge_osd'));

            // if ($(this).data('is_city') == 1){
            //     $('#is_city_e').attr('checked',true);
            // }else {
            //     $('#is_city_e').attr('checked',false);
            // }

            // if ($(this).data('is_zone') == 1){
            //     $('#is_zone_e').attr('checked',true);
            // }else{
            //     $('#is_zone_e').attr('checked',false);
            // }

            $('#status_e').val($(this).data('status'));
        })
    </script>
@endsection

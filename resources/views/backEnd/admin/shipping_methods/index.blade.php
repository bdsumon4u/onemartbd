@extends('backEnd.admin.layouts.master')

@section('title')
    Shipping Methods
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
                            <h2 class="pageheader-title">Shipping Methods</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Shipping Methods</li>
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
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#add_shipp_meth">Add Shipping Method</button>
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
                                        <th>Shipping Method Type</th>
                                        <th>Shipping Method Text</th>
                                        <th>Shipping Method Amount</th>
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
                                                <td>{{$item->type}}</td>
                                                <td>{{$item->text}}</td>
                                                <td>{{$item->amount}}</td>
                                                <td>
                                                    @if($item->status ==1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="mr-1 edit_sm_btn" data-toggle="modal" data-target="#edit_shipp_meth"
                                                       data-id="{{$item->id}}"
                                                       data-type="{{$item->type}}"
                                                       data-text="{{$item->text}}"
                                                       data-amount="{{$item->amount}}"
                                                       data-status="{{$item->status}}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{route('admin.shipping_methods.delete',$item->id)}}" onclick="return confirm('Are you sure to delete this?')"><i class="fa fa-trash"></i></a>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--add shipping method--}}
    <div class="modal fade" id="add_shipp_meth" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Shipping Method</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.shipping_methods.store')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="type">Shipping Method Type</label>
                            <input type="text" class="form-control" id="type" name="type" placeholder="eg. ঢাকার ভিতরে" required>
                        </div>

                        <div class="form-group">
                            <label for="text">Shipping Method Text</label>
                            <input type="text" class="form-control" id="text" name="text" placeholder="eg. ঢাকায় ডেলিভারি খরচ" required>
                        </div>

                        <div class="form-group">
                            <label for="amount">Shipping Method Amount</label>
                            <input type="text" class="form-control" id="amount" name="amount" placeholder="eg. 60" required>
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

    {{--edit shipping method--}}
    <div class="modal fade" id="edit_shipp_meth" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Shipping Method</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.shipping_methods.update')}}" method="post">
                        @csrf
                        <input type="hidden" name="id" id="id_e">
                        <div class="form-group">
                            <label for="type_e">Shipping Method Type</label>
                            <input type="text" class="form-control" id="type_e" name="type" placeholder="eg. ঢাকার ভিতরে" required>
                        </div>

                        <div class="form-group">
                            <label for="text_e">Shipping Method Text</label>
                            <input type="text" class="form-control" id="text_e" name="text" placeholder="eg. ঢাকায় ডেলিভারি খরচ" required>
                        </div>

                        <div class="form-group">
                            <label for="amount_e">Shipping Method Amount</label>
                            <input type="text" class="form-control" id="amount_e" name="amount" placeholder="eg. 60" required>
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
        $('.edit_sm_btn').on('click', function () {
            $('#id_e').val($(this).data('id'));
            $('#type_e').val($(this).data('type'));
            $('#text_e').val($(this).data('text'));
            $('#amount_e').val($(this).data('amount'));
            $('#status_e').val($(this).data('status'));
        })
    </script>
@endsection

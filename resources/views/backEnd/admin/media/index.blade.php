@extends('backEnd.admin.layouts.master')

@section('title')
    Media
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
                            <h2 class="pageheader-title">Media</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Media</li>
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
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#add_img">Add Media</button>
                    </div>
                </div>
                {{--<div class="row">
                    @if($data->count() > 0)
                        @foreach($data as $item)
                            <div class="col-md-2 col-6">
                                <div class="card p-1 mb-3">
                                    <div class="media-img">
                                        <img src="{{uploaded_asset($item->id)}}" alt="">
                                    </div>
                                    <p class="mb-0 text-center" style="font-size: 13px;height: 40px;overflow: hidden">{{$item->file_original_name}}</p>

                                    <div class="card-footer p-1 text-center">
                                        <a href="javascript:void(0);" class="mr-1 edit_img_btn" data-toggle="modal" data-target="#edit_modal" data-id="{{$item->id}}"
                                           data-url="{{asset($item->file_url)}}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="{{route('admin.media.delete',$item->id)}}" onclick="return confirm('Are you sure do delete this?')"><i
                                                class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-md-2 col-4">
                            <h4 class="text-danger font-weight-bold text-center">No Media Available</h4>
                        </div>
                    @endif
                </div>--}}

                <div class="row">
                    <div class="col-12">
                        <div class="card table-responsive">
                            <div class="card-body">
                                <table class="table table-bordered text-center">
                                    <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>File</th>
                                        <th>File Name</th>
                                        <th>File Type</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @php($i=1)
                                    @if($data->count() > 0)
                                        @foreach($data as $item)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>
                                                    <img style="width: {{$item->type==1 ? "90px" : "120px"}}" src="{{asset($item->file_url)}}" alt="">
                                                </td>
                                                <td>{{$item->file_original_name}}</td>
                                                <td>
                                                    @if($item->type == 1)
                                                        Product Image (800x800)
                                                    @elseif($item->type ==2)
                                                        Product Thumbnail (180x180)
                                                    @elseif($item->type ==3)
                                                        Slider (1110x280)
                                                    @else
                                                        Normal
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="mr-1 edit_img_btn" data-toggle="modal" data-target="#edit_modal"
                                                       data-id="{{$item->id}}"
                                                       data-url="{{asset($item->file_url)}}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{route('admin.media.delete',$item->id)}}" onclick="return confirm('Are you sure do delete this?')"><i
                                                            class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center text-danger font-weight-bold">No Media Found!</td>
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

    {{--add modal--}}
    <div class="modal fade" id="add_img" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Media</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.media.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-success" value="Add">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--edit img--}}
    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Media</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.media.update')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="img_id" name="id">
                        <div class="form-group">
                            <div class="card w-50 m-auto">
                                <img id="img_append" src="" alt="">
                            </div>
                            <br>
                            <input type="file" class="form-control" id="file" name="file" required>
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
        $('.edit_img_btn').on('click', function () {
            $('#img_id').val($(this).data('id'));
            $('#img_append').attr('src', $(this).data('url'));
        })
    </script>
@endsection

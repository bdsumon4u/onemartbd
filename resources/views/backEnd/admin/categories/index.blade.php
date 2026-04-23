@extends('backEnd.admin.layouts.master')

@section('title')
    Categories
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
                            <h2 class="pageheader-title">Categories</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Categories</li>
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
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#add_cat">Add Category</button>
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
                                        <th class="text-left">Category & <br> Sub-category Name</th>
                                        <th>Image</th>
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
                                                <td class="text-left">
                                                    <b>{{$item->category_name}}</b>
                                                    <a href="javascript:void(0)" class="badge badge-success add_sub_cat_btn" data-id="{{$item->id}}" data-name="{{$item->category_name}}" type="button">Add</a>
                                                    @if(count($item->children)>0)
                                                        <div style="padding-left: 10px">
                                                            @include('backEnd.admin.categories.sub-category',['children'=> $item->children])
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <img src="{{ $item->image ? asset($item->image) : asset('frontEnd/images/no_image.png') }}"
                                                        alt="{{ $item->category_name }}" style="width:70px;height:50px;object-fit:cover;border-radius:4px;">
                                                </td>
                                                <td>
                                                    @if($item->status ==1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="mr-1 edit_cat_btn" data-toggle="modal" data-target="#edit_cat"
                                                       data-id="{{$item->id}}" data-name="{{$item->category_name}}"
                                                       data-status="{{$item->status}}" data-image="{{ $item->image ? asset($item->image) : asset('frontEnd/images/no_image.png') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{route('admin.category.delete',$item->id)}}" onclick="return confirm('Are you sure to delete this?')"><i class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center text-danger font-weight-bold">No Data Found!</td>
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

    {{--add modal--}}
    <div class="modal fade" id="add_cat" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.category.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="category_name">Parent Category</label>
                            <select name="parent" id="parent" class="form-control">
                                <option value="">Select Parent Category</option>
                                @foreach($data as $item)
                                    <option value="{{$item->id}}">{{$item->category_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="image">Category Image (Optional)</label>
                            <input type="file" class="form-control" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-success" value="Add">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{--add sub category modal--}}
    <div class="modal fade" id="add_sub_cat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Sub Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.category.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="category_name">Parent Category</label>
                            <input type="hidden" name="parent" id="sub_parent">
                            <input type="text" class="form-control" name="" id="parent_name" readonly>
                        </div>

                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sub_category_image">Category Image (Optional)</label>
                            <input type="file" class="form-control" id="sub_category_image" name="image" accept=".jpg,.jpeg,.png,.webp">
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
    <div class="modal fade" id="edit_cat" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.category.update')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="category_id_e">
                        <div class="form-group">
                            <label for="category_name_e">Category Name</label>
                            <input type="text" class="form-control" id="category_name_e" name="category_name" required>
                        </div>

                        <div class="form-group">
                            <label for="status_e">Status</label>
                            <select name="status" id="status_e" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="image_e">Category Image (Optional)</label>
                            <input type="file" class="form-control mb-2" id="image_e" name="image" accept=".jpg,.jpeg,.png,.webp">
                            <img id="category_image_preview" src="{{ asset('frontEnd/images/no_image.png') }}" alt="Category Image"
                                style="width:80px;height:60px;object-fit:cover;border-radius:4px;">
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
        $('.edit_cat_btn').on('click', function () {
            $('#category_id_e').val($(this).data('id'));
            $('#category_name_e').val($(this).data('name'));
            $('#status_e').val($(this).data('status'));
            $('#category_image_preview').attr('src', $(this).data('image'));
        });

        $('.add_sub_cat_btn').on('click', function () {
            $('#parent_name').val($(this).data('name'));
            $('#sub_parent').val($(this).data('id'));
            $('#add_sub_cat_modal').modal('show');
        })
    </script>
@endsection

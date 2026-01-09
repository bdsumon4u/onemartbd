@extends('backEnd.admin.layouts.master')

@section('title')
    Attribute Settings
@endsection

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
                            <h2 class="pageheader-title">Attribute Settings</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Attribute Settings</li>
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
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#add_modal">Add Attribute</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>Title</th>
                                        <th>Attribute Item(s)</th>
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
                                                <td>{{$item->title}}</td>
                                                <td>
                                                    @if($item->get_items->count()>0)
                                                        @foreach($item->get_items as $key => $value)
                                                            <a href="javascript:void(0)" class="edit_item_btn" data-attribute_item_id="{{$value->id}}"
                                                               data-attribute_item_title="{{$value->item_title}}">
                                                                <i class="fa fa-edit"></i>
                                                            </a>

                                                            <a href="{{route('admin.settings.attribute_item.delete',$value->id)}}"
                                                               onclick="return confirm('Are you sure to delete this?')" class="mr-1">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                            <span>{{$value->item_title}}</span>
                                                            <br>
                                                        @endforeach
                                                    @endif

                                                    <a href="javascript:void(0)" class="add_item_btn badge badge-primary" data-attribute_id="{{$item->id}}"
                                                       data-attribute_title="{{$item->title}}">
                                                        Add
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($item->status == 1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="mr-1 edit_btn"
                                                       data-id="{{$item->id}}"
                                                       data-title="{{$item->title}}"
                                                       data-status="{{$item->status}}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{route('admin.settings.attribute.delete',$item->id)}}"
                                                       onclick="return confirm('Are you sure to delete this?')"><i
                                                            class="fa fa-trash"></i></a>
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
    <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Attribute</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.settings.attribute.store')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
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

    {{--edit modal--}}
    <div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Attribute</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.settings.attribute.update')}}" method="post">
                        @csrf
                        <input type="hidden" name="id" id="id_e">
                        <div class="form-group">
                            <label for="title_e">Title</label>
                            <input type="text" class="form-control" id="title_e" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="status_e">Status</label>
                            <select name="status" id="status_e" class="form-control">
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

    {{--attribute item add--}}
    <div class="modal fade" id="item_add_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Attribute Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.settings.attribute_item.store')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="attribute_id">Attribute Title</label>
                            <select name="attribute_id" id="attribute_id" class="form-control"></select>
                        </div>

                        <div class="form-group">
                            <label for="item_title">Attribute Item Title</label>
                            <input type="text" class="form-control" id="item_title" name="item_title" required>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-success" value="Add">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--attribute item edit--}}
    <div class="modal fade" id="item_edit_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Attribute Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.settings.attribute_item.update')}}" method="post">
                        @csrf
                        <input type="hidden" id="attribute_item_id_e" name="id">
                        <div class="form-group">
                            <label for="item_title_e">Attribute Item Title</label>
                            <input type="text" class="form-control" id="item_title_e" name="item_title" required>
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
        $('.edit_btn').on('click', function () {
            $('#id_e').val($(this).data('id'));
            $('#title_e').val($(this).data('title'));
            $('#status_e').val($(this).data('status'));
            $('#edit_modal').modal('show');
        });

        $('.add_item_btn').on('click', function () {
            $('#attribute_id').append('<option value="' + $(this).data('attribute_id') + '">' + $(this).data('attribute_title') + '</option>');
            $('#item_add_modal').modal('show');
        });

        $('.edit_item_btn').on('click', function () {
            $('#attribute_item_id_e').val($(this).data('attribute_item_id'));
            $('#item_title_e').val($(this).data('attribute_item_title'));
            $('#item_edit_modal').modal('show');
        });
    </script>
@endsection

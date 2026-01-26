@extends('backEnd.admin.layouts.master')

@section('title')
    Products
@endsection

@section('css')
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
                            <h2 class="pageheader-title">Products</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{ Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : '')) }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Products</li>
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
                        <a href="{{ Auth::guard('admin')->check() ? route('admin.product.create') : (Auth::guard('manager')->check() ? route('manager.product.create') : '') }}"
                            class="btn btn-success btn-sm">Add
                            Product</a>
                    </div>
                </div>
                @if (Auth::guard('admin')->check())
                    <div class="row mb-2">
                        <div class="col-md-1 col-12">
                            <form action="{{ route('admin.product.bulk.delete') }}" method="post" id="bulk_delete_form">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" id="all_id" name="all_id">
                                    <button type="button" id="bulk_delete" class="btn btn-danger btn-sm">Delete</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-2 col-12">
                            <form action="{{ route('admin.product.bulk.status') }}" method="post" id="bulk_status_form">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" id="all_status_id" name="all_id">
                                    <select name="status" id="bulk_status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="1">Published</option>
                                        <option value="0">Unpublished</option>
                                    </select>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-9 col-12 mt-md-0 mt-2">
                            <form action="{{ route('admin.product') }}" method="get" class="form-inline float-md-right">
                                <div class="form-group">
                                    <input type="text" class="form-control mr-2" placeholder="Search Products"
                                        name="query" value="{{ $query }}">
                                    <button class="btn btn-dark btn-sm">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="master"></th>
                                            <td>SL.</td>
                                            <td>Image</td>
                                            <td>Product Name</td>
                                            <td>Category Name</td>
                                            <td width="10%">Position</td>
                                            <td>SKU</td>
                                            <td>Stock</td>
                                            <td>Purchase Price</td>
                                            <td>Price</td>
                                            <td>Sale Price</td>
                                            <td>Attributes</td>
                                            <td>Status</td>
                                            <td>Actions</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($i = 1)
                                        @if ($data->count() > 0)
                                            @foreach ($data as $item)
                                                <tr id="tr_{{ $item->id }}">
                                                    <td><input type="checkbox" class="sub_chk"
                                                            data-id="{{ $item->id }}">
                                                    <td>{{ $i++ }}</td>
                                                    <td>
                                                        <img width="30"
                                                            src="{{ $item->get_thumb ? asset($item->get_thumb->file_url) : asset('frontEnd/images/no_image.png') }}"
                                                            alt="">
                                                    </td>
                                                    <td>{{ $item->name }}</td>
                                                    <td>
                                                        @foreach ($item->get_categories as $key => $cat)
                                                            {{ $key != 0 ? ', ' : '' }}{{ $cat->category_name }}
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @if (Auth::guard('admin')->check())
                                                            <div class="d-flex align-items-center">
                                                                <input type="number"
                                                                    class="form-control form-control-sm mr-1"
                                                                    value="{{ $item->position }}" min="1"
                                                                    name="new_stock">
                                                                <i class="fa fa-check position_update_btn"
                                                                    style="cursor:pointer;"
                                                                    data-product_id="{{ $item->id }}"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->sku }}</td>
                                                    <td>{{ $item->stock }}</td>
                                                    <td>{{ $web_settings->currency_sign }} {{ $item->purchase_cost }}</td>
                                                    <td>{{ $web_settings->currency_sign }} {{ $item->price }}</td>
                                                    <td>{{ $web_settings->currency_sign }} {{ $item->sale_price }}</td>
                                                    <td class="text-left">
                                                        @foreach ($item->get_attributes as $attrb)
                                                            {{ $attrb->get_attribute->title }} -
                                                            {{ '(' }}
                                                            @foreach ($attrb->get_attribute_items as $key => $at_item)
                                                                {{ $key == 0 ? '' : ',' }}
                                                                {{ $at_item->get_attribute_item->item_title }}
                                                            @endforeach
                                                            {{ ')' }}
                                                            <br>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @if ($item->status == 1)
                                                            <span class="badge badge-success">Published</span>
                                                        @else
                                                            <span class="badge badge-danger">Unpublished</span>
                                                        @endif <br>

                                                        @if ($item->is_assigned)
                                                            <span class="badge badge-warning">Assigned</span> <a
                                                                href="{{ route('admin.user.product.delete', $item->id) }}"
                                                                onclick="return confirm('Are You Sure To Unassigned?')"><i
                                                                    class="fa fa-trash-alt text-danger"></i></a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ Auth::guard('admin')->check() ? route('admin.product.edit', $item->id) : (Auth::guard('manager')->check() ? route('manager.product.edit', $item->id) : '') }}"
                                                            class="">
                                                            <i class="fa fa-edit"></i>
                                                        </a> <br>
                                                        <a href="{{ Auth::guard('admin')->check() ? route('admin.product.delete', $item->id) : (Auth::guard('manager')->check() ? route('manager.product.delete', $item->id) : '') }}"
                                                            onclick="return confirm('Are You Sure To Delete This?')"><i
                                                                class="fa fa-trash"></i></a> <br>
                                                        @if (!$item->is_assigned)
                                                            <a href="javascript:void(0)"
                                                                data-product_id="{{ $item->id }}"
                                                                class="user_assign_btn"><i class="fa fa-user"></i></a>
                                                            <br>
                                                        @endif

                                                        <a
                                                            href="{{ Auth::guard('admin')->check() ? route('admin.product.duplicate', $item->id) : (Auth::guard('manager')->check() ? route('manager.product.duplicate', $item->id) : '') }}">
                                                            <i class="fa fa-copy"></i>
                                                        </a>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="11" class="text-center text-danger font-weight-bold">No
                                                    Data Found!</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $data->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- user assing modal --}}
    <div class="modal fade" id="user_assign" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Assign User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.user.product.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="product_id" id="product_id">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <select name="employee_id[]" id="employee_id" class="form-control select2" multiple
                                    required>
                                    <option value="">Select Employee</option>
                                    @foreach ($employees as $id => $item)
                                        <option value="{{ $id }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group text-center mt-2">
                            <input type="submit" class="btn btn-success" id="form_add_btn" value="Add">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            $('#employee_id').select2({
                width: '100%',
                dropdownParent: $('#user_assign'),
                placeholder: 'Select Employee',
            });

            $('#master').on('click', function(e) {
                if ($(this).is(':checked', true)) {
                    $(".sub_chk").prop('checked', true);
                } else {
                    $(".sub_chk").prop('checked', false);
                }
            });

            $('#bulk_delete').on('click', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    if (confirm('Are Your Sure To Delete?') == true) {
                        $('#all_id').val(allVals);
                        $('#bulk_delete_form').submit();
                    }
                }
            });

            $('#bulk_status').on('change', function(e) {
                var allVals = [];
                $(".sub_chk:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select row.");
                } else {
                    $('#all_status_id').val(allVals);
                    $('#bulk_status_form').submit();
                }
            });

            //assign user
            $('.user_assign_btn').click(function() {
                $('#product_id').val($(this).data('product_id'));
                $('#user_assign').modal('show');
            });

            $(document).on('click', '.position_update_btn', function() {
                var position = $(this).siblings().val();
                var product_id = $(this).data('product_id');

                var CSRF_TOKEN = `{{ csrf_token() }}`;
                $.ajax({
                    url: '{{ route('admin.product.position_update') }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        position: position,
                        product_id: product_id
                    },
                    success: function(data) {
                        if (data == 1) {
                            toastr.options = {
                                "positionClass": "toast-bottom-right"
                            };
                            toastr.success("Stock Updated Successfully");
                        } else {
                            toastr.options = {
                                "positionClass": "toast-bottom-right"
                            };
                            toastr.error("Something Went Wrong");
                        }
                    }
                });

            });
        });
    </script>
@endsection

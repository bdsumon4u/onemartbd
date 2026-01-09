@extends('backEnd.admin.layouts.master')

@section('title')
    Product Create
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendor/summernote/css/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('/') }}backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.css">
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
                            <h2 class="pageheader-title">Product Create</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : ""))}}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Product Create</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->

                <div class="row mb-2">
                    <div class="col-12">
                        <a href="{{route('admin.product')}}" class="btn btn-danger btn-sm">
                            <i class="fa fa-angle-double-left"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form
                            action="{{Auth::guard('admin')->check() ? route('admin.product.store') : (Auth::guard('manager')->check() ? route('manager.product.store') : "")}}"
                            method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="form-group  col-md-12 col-12">
                                    <label for="name">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-12">
                                    <label for="category_id">Product Category <span class="text-danger">*</span></label>
                                    <select name="category_id[]" id="category_id" class="form-control select2" multiple
                                            required>
                                        @foreach ($categories as $item)
                                            <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6 col-12">
                                    <label for="price">Regular Price <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="price" name="price" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4 col-12">
                                    <label for="purchase_cost">Purchase Price</label>
                                    <input type="text" class="form-control" id="purchase_cost" name="purchase_cost"
                                           min="0" value="0">
                                </div>
                                <div class="form-group col-md-4 col-12">
                                    <label for="sale_price">Sale Price</label>
                                    <input type="text" class="form-control" id="sale_price" name="sale_price"
                                           min="0" value="0">
                                </div>
                                <div class="form-group col-md-4 col-12">
                                    <label for="packaging_cost">Packaging Cost</label>
                                    <input type="text" class="form-control" id="packaging_cost" name="packaging_cost"
                                           min="0" value="0">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6 col-12">
                                    <label for="sku">SKU</label>
                                    <input type="text" class="form-control" id="sku" name="sku">
                                    <span class="text-danger" id="error_msg"></span>
                                </div>

                                <div class="form-group col-md-6 col-12">
                                    <label for="stock">Stock</label>
                                    <input type="number" name="stock" id="stock" class="form-control" min="0"
                                           value="0">
                                </div>

                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6 col-12">
                                    <label for="start_date">Start Date</label>
                                    <input type="text" class="form-control datetimepicker" id="start_date"
                                           name="start_date" placeholder="Start Date">
                                </div>
                                <div class="form-group
                            col-md-6 col-12">
                                    <label for="end_date">End Date</label>
                                    <input type="text" class="form-control datetimepicker" id="end_date" name="end_date"
                                           placeholder="End Date">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6 col-12">
                                    <label for="image">Feature Image</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                </div>
                                <div class="form-group col-md-6 col-12">
                                    <label for="gallery_image">Gallery Image</label>
                                    <input type="file" class="form-control" id="gallery_image" name="gallery_image[]"
                                           multiple>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-12"
                                     style="border: 1px solid #ddd;margin: 10px 0;border-radius: 5px">
                                    <h4 class="mb-1">Attributes</h4>
                                    <div class="form-row">
                                        @foreach ($attributes as $key => $attribute)
                                            <div class="form-group col-md-3 col-12">
                                                <label class="text-capitalize"
                                                       for="attribute_item_id{{ $key }}">{{ $attribute->title }}</label>
                                                <input type="checkbox" name="attribute_id[]" class="attribute_id"
                                                       value="{{ $attribute->id }}">
                                                <select name="attribute_item_id[]" id="attribute_item_id{{ $key }}"
                                                        class="form-control select2" disabled required multiple>
                                                    @foreach ($attribute->get_items as $att_item)
                                                        <option value="{{ $att_item->id }}">{{ $att_item->item_title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="description">Product Description</label>
                                    <textarea name="description" id="description" class="summernote"></textarea>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <label for="brand_name">Brand Name <span class="text-danger fw-bold">*</span></label>
                                            <input type="text" name="brand_name" id="brand_name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <label for="fb_description">FB Catalog Description <span class="text-danger fw-bold">*</span></label>
                                            <textarea name="fb_description" id="fb_description" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6 col-12">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">Published</option>
                                        <option value="0">Unpublished</option>
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
    </div>
@endsection
@section('js')
    <script src="{{ asset('backEnd/assets/vendor/summernote/js/summernote-bs4.js') }}"></script>
    <script src="{{ asset('/') }}backEnd/assets/vendor/datetimepicker/moment.min.js"></script>
    <script src="{{ asset('/') }}backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.datetimepicker').datetimepicker({
                icons: {
                    next: 'fa fa-angle-right',
                    previous: 'fa fa-angle-left'
                },
                format: 'DD-MM-YYYY',
                // defaultDate: new Date(),
            });

            $('.summernote').summernote({
                height: 300,
            });

            $('.select2').select2();

            //attribute
            $('.attribute_id').on('click', function() {
                if ($(this).is(':checked', true)) {
                    $(this).parent().find('select').prop('disabled', false);
                } else {
                    $(this).parent().find('select').prop('disabled', true);
                }
            });

        });
    </script>
@endsection

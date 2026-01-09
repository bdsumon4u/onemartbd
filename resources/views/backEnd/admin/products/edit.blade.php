@extends('backEnd.admin.layouts.master')

@section('title')
    Product Edit
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
                            <h2 class="pageheader-title">Product Edit</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{ Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : '')) }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Product Edit</li>
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
                        <a href="{{ route('admin.product') }}" class="btn btn-danger btn-sm">
                            <i class="fa fa-angle-double-left"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form
                            action="{{ Auth::guard('admin')->check() ? route('admin.product.update', $data->id) : (Auth::guard('manager')->check() ? route('manager.product.update', $data->id) : '') }}"
                            method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="thumb_old" value="{{ $data->thumb }}">
                            <input type="hidden" name="image_old" value="{{ $data->image }}">
                            <input type="hidden" name="gallery_images_old" value="{{ $data->gallery_images }}">
                            <div class="form-row">
                                <div class="form-group col-md-12 col-12">
                                    <label for="name_e">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name_e" name="name"
                                           value="{{ $data->name }}" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-12">
                                    @php
                                        $prod_cat = explode(',', $prod_cat);
                                    @endphp
                                    <label for="category_id_e">Product Category <span class="text-danger">*</span></label>
                                    <select name="category_id[]" id="category_id_e" class="form-control select2" multiple
                                            required>
                                        @foreach ($categories as $key => $item)
                                            <option value="{{ $key }}"
                                                {{ in_array($key, $prod_cat) ? 'selected' : '' }}>{{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6 col-12">
                                    <label for="price_e">Regular Price <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="price_e" name="price"
                                           value="{{ $data->price }}" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4 col-12">
                                    <label for="purchase_cost_e">Purchase Price</label>
                                    <input type="text" class="form-control" id="purchase_cost_e" name="purchase_cost"
                                           min="0" value="{{ $data->purchase_cost ?? 0 }}">
                                </div>

                                <div class="form-group col-md-4 col-12">
                                    <label for="sale_price_e">Sale Price</label>
                                    <input type="text" class="form-control" id="sale_price_e" name="sale_price"
                                           value="{{ $data->sale_price ?? 0 }}">
                                </div>
                                <div class="form-group col-md-4 col-12">
                                    <label for="packaging_cost_e">Packaging Cost</label>
                                    <input type="text" class="form-control" id="packaging_cost_e" name="packaging_cost"
                                           value="{{ $data->packaging_cost ?? 0 }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6 col-12">
                                    <label for="sku_e">SKU</label>
                                    <input type="text" class="form-control" id="sku_e" name="sku"
                                           value="{{ $data->sku }}">
                                </div>

                                <div class="form-group col-md-6 col-12">
                                    <label for="stock_e">Stock</label>
                                    <input type="number" name="stock" id="stock_e" class="form-control"
                                           value="{{ $data->stock ?? 0 }}">
                                </div>

                            </div>
                            <div class="form-row">
                                <div class="form-group
                                    col-md-6 col-12">
                                    <label for="start_date_e">Start Date</label>
                                    <input type="text" class="form-control datetimepicker" id="start_date_e"
                                           name="start_date"
                                           value="{{ $data->start_date ? date('d-m-Y', strtotime($data->start_date)) : '' }}">
                                </div>
                                <div class="form-group
                                    col-md-6 col-12">
                                    <label for="end_date_e">End Date</label>
                                    <input type="text" class="form-control datetimepicker" id="end_date_e"
                                           name="end_date"
                                           value="{{ $data->end_date ? date('d-m-Y', strtotime($data->end_date)) : '' }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6 col-12">
                                    <label for="image_e">Feature Image</label>

                                    <div class="mb-2">
                                        <img width="50"
                                             src="{{ $data->get_thumb ? asset($data->get_thumb->file_url) : asset('frontEnd/images/no_image.png') }}"
                                             alt="">
                                    </div>

                                    <input type="file" class="form-control" id="image_e" name="image">
                                </div>
                                <div class="form-group col-md-6 col-12">
                                    <label for="gallery_image_e">Gallery Image</label>
                                    <div class="mb-2">
                                        @foreach ($data->images as $photo)
                                            <img width="50"
                                                 src="{{ $photo ? asset($photo) : asset('frontEnd/images/no_image.png') }}"
                                                 alt="">
                                        @endforeach
                                    </div>

                                    <input type="file" class="form-control" id="gallery_image_e"
                                           name="gallery_image[]" multiple>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-12"
                                     style="border: 1px solid #ddd;margin: 10px 0;border-radius: 5px">
                                    <h4 class="mb-1">Attributes</h4>
                                    @php
                                        if ($prd_attr) {
                                            $prd_attr = explode(',', $prd_attr);
                                        } else {
                                            $prd_attr = [];
                                        }

                                        if ($prd_attr_item) {
                                            $prd_attr_item = explode(',', $prd_attr_item);
                                        } else {
                                            $prd_attr_item = [];
                                        }
                                    @endphp

                                    <div class="form-row">
                                        @foreach ($attributes as $key => $attribute)
                                            <div class="form-group col-md-3 col-12">
                                                <label class="text-capitalize"
                                                       for="attribute_item_id{{ $key }}">{{ $attribute->title }}</label>
                                                <input type="checkbox" name="attribute_id[]" class="attribute_id"
                                                       value="{{ $attribute->id }}"
                                                    {{ in_array($attribute->id, $prd_attr) ? 'checked' : '' }}>
                                                <select name="attribute_item_id[]"
                                                        id="attribute_item_id{{ $key }}"
                                                        class="form-control select2"
                                                        {{ in_array($attribute->id, $prd_attr) ? '' : 'disabled' }} multiple
                                                        required>
                                                    @foreach ($attribute->get_items as $att_item)
                                                        <option value="{{ $att_item->id }}"
                                                            {{ in_array($att_item->id, $prd_attr_item) ? 'selected' : '' }}>
                                                            {{ $att_item->item_title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="description_e">Product Description</label>
                                    <textarea name="description" id="description_e" class="summernote">
                                        {!! $data->description !!}
                                    </textarea>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <label for="brand_name">Brand Name <span class="text-danger fw-bold">*</span></label>
                                            <input type="text" name="brand_name" id="brand_name" class="form-control" required value="{{$data->brand_name}}">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-12">
                                            <label for="fb_description">FB Catalog Description <span class="text-danger fw-bold">*</span></label>
                                            <textarea name="fb_description" id="fb_description" class="form-control" required>{{$data->fb_description}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-12">
                                    <label for="status_e">Status</label>
                                    <select name="status" id="status_e" class="form-control">
                                        <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Published
                                        </option>
                                        <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Unpublished
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 text-center">
                                    <input type="submit" value="Update" id="form_update_btn" class="btn btn-success">
                                </div>
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
            $('.attribute_id').on('click', function () {
                if ($(this).is(':checked', true)) {
                    $(this).parent().find('select').prop('disabled', false);
                } else {
                    $(this).parent().find('select').prop('disabled', true);
                }
            });

            $('#is_sp_sm').on('click', function () {
                if ($(this).is(':checked', true)) {
                    $('#inside_dhaka').prop('disabled', false);
                    $('#outside_dhaka').prop('disabled', false);
                } else {
                    $('#inside_dhaka').prop('disabled', true);
                    $('#outside_dhaka').prop('disabled', true);
                }
            });
        });
    </script>
@endsection

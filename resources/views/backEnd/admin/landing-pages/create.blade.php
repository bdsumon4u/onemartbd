@extends('backEnd.admin.layouts.master')

@section('title')
    Create Landing Page
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendor/summernote/css/summernote-bs4.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content">
                <!-- pageheader -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Create Landing Page</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="{{ Auth::guard('admin')->check() ? route('admin.home') : '' }}"
                                                class="breadcrumb-link">Home</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('landing-pages.index') }}" class="breadcrumb-link">Landing
                                                Pages</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Create Landing Page</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end pageheader -->

                <div class="row mb-2">
                    <div class="col-12">
                        <a href="{{ route('landing-pages.index') }}" class="btn btn-danger btn-sm">
                            <i class="fa fa-angle-double-left"></i>
                            Back
                        </a>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('landing-pages.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-6 col-12">
                                    <label for="product_id">Select Product <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-control product-select" required>
                                        <option value="">Search and select product...</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 col-12">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-8 col-12">
                                    <label for="title">Landing Page Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ old('title') }}" required>
                                </div>
                                <div class="form-group col-md-4 col-12">
                                    <label for="subtitle">Subtitle</label>
                                    <input type="text" class="form-control" id="subtitle" name="subtitle"
                                        value="{{ old('subtitle') }}">
                                </div>
                            </div>

                            <!-- Banner Image Section -->
                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="banner_image">Banner/Thumbnail Image</label>
                                    <input type="file" class="form-control" id="banner_image" name="banner_image"
                                        accept="image/*">
                                    <small class="form-text text-muted">
                                        If no banner is uploaded, the product's default thumbnail will be used.
                                        Recommended size: 800x400px
                                    </small>
                                </div>
                            </div>

                            <!-- About Section -->
                            <div class="form-row">
                                <div class="form-group col-12"
                                    style="border: 1px solid #ddd; margin: 10px 0; border-radius: 5px; padding: 15px;">
                                    <h5 class="mb-3">About Section</h5>

                                    <div class="form-group">
                                        <label for="about_section_head">About Section Heading</label>
                                        <input type="text" class="form-control" id="about_section_head"
                                            name="about_section_head" value="{{ old('about_section_head') }}"
                                            placeholder="e.g., About This Product">
                                    </div>

                                    <div class="form-group">
                                        <label for="about_section_body">About Section Content</label>
                                        <textarea name="about_section_body" id="about_section_body" class="summernote">{{ old('about_section_body') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Images Section -->
                            <div class="form-row">
                                <div class="form-group col-12"
                                    style="border: 1px solid #ddd; margin: 10px 0; border-radius: 5px; padding: 15px;">
                                    <h5 class="mb-3">Gallery Section</h5>

                                    <div class="form-group">
                                        <label for="gallery_section_head">Gallery Section Heading</label>
                                        <input type="text" class="form-control" id="gallery_section_head"
                                            name="gallery_section_head"
                                            value="{{ old('gallery_section_head', 'প্রোডাক্ট এর ছবিগুলো দেখুন') }}"
                                            placeholder="e.g., Product Gallery">
                                    </div>

                                    <div class="form-group">
                                        <label for="gallery_images">Gallery Images</label>
                                        <input type="file" class="form-control" id="gallery_images"
                                            name="gallery_images[]" accept="image/*" multiple>
                                        <small class="form-text text-muted">
                                            If no gallery images are uploaded, the product's default gallery images will be
                                            used.
                                            You can select multiple images. Recommended size: 600x600px
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Why This Product Section -->
                            <div class="form-row">
                                <div class="form-group col-12"
                                    style="border: 1px solid #ddd; margin: 10px 0; border-radius: 5px; padding: 15px;">
                                    <h5 class="mb-3">Why This Product? (Features/Benefits)</h5>

                                    <div class="form-group">
                                        <label for="why_section_head">Why Section Heading</label>
                                        <input type="text" class="form-control" id="why_section_head"
                                            name="why_section_head" value="{{ old('why_section_head') }}"
                                            placeholder="e.g., Why Choose This Product?">
                                    </div>

                                    <div class="form-group">
                                        <label for="why_section_body">Why Section Content</label>
                                        <textarea name="why_section_body" id="why_section_body" class="summernote">{{ old('why_section_body') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-center mt-4">
                                <input type="submit" class="btn btn-success btn-lg" value="Create Landing Page">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Summernote editors
            $('.summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Initialize Select2 for product selection
            $('.product-select').select2({
                ajax: {
                    url: '{{ route('landing-pages.products.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: 'Search products by name or SKU...',
                allowClear: true
            });
        });
    </script>
@endsection

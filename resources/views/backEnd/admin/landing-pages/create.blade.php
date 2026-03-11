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
                                <div class="form-group col-12">
                                    <label for="title">Landing Page Title <span class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control" id="title" name="title" required>{{ old('title') }}</textarea>
                                </div>
                                <div class="form-group col-12">
                                    <label for="subtitle">Subtitle</label>
                                    <textarea type="text" class="form-control" id="subtitle" name="subtitle">
                                        {{ old('subtitle') }}
                                    </textarea>
                                </div>
                                <div class="form-group col-12">
                                    <label for="slug">Slug</label>
                                    <input type="text" class="form-control" id="slug" name="slug"
                                        value="{{ old('slug') }}" placeholder="Leave empty to auto-generate from title">
                                    <small class="form-text text-muted">
                                        Custom URL slug. Only lowercase letters, numbers, and hyphens allowed.
                                    </small>
                                </div>
                            </div>

                            <!-- Banner Image Section -->
                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="banner_image">Banner/Thumbnail Image or Video</label>
                                    <input type="file" class="form-control" id="banner_image" name="banner_image"
                                        accept="image/*,video/mp4,video/webm,video/quicktime">
                                    <small class="form-text text-muted">
                                        Upload an image or a short video (MP4, WebM, MOV). If a video is uploaded, it
                                        will autoplay on the landing page. Max 20MB.
                                        If nothing is uploaded, the product's default thumbnail will be used.
                                    </small>

                                    <div class="mt-2" id="autoplay-toggle" style="display:none;">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="banner_autoplay"
                                                name="banner_autoplay" value="1" checked>
                                            <label class="custom-control-label" for="banner_autoplay">
                                                <strong>Autoplay Video</strong>
                                                <small class="text-muted d-block">Video will autoplay with sound when the
                                                    landing page loads.</small>
                                            </label>
                                        </div>
                                    </div>
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

                            <!-- Product Reviews Section -->
                            <div class="form-row">
                                <div class="form-group col-12"
                                    style="border: 1px solid #ddd; margin: 10px 0; border-radius: 5px; padding: 15px;">
                                    <h5 class="mb-3">Product Reviews Section</h5>

                                    <div class="form-group">
                                        <label for="review_section_head">Review Section Heading</label>
                                        <input type="text" class="form-control" id="review_section_head"
                                            name="review_section_head"
                                            value="{{ old('review_section_head', 'আমাদের কাস্টমার রিভিউ') }}"
                                            placeholder="e.g., Customer Reviews">
                                    </div>

                                    <div class="form-group">
                                        <label for="review_images">Review Images</label>
                                        <input type="file" class="form-control" id="review_images"
                                            name="review_images[]" accept="image/*" multiple>
                                        <small class="form-text text-muted">
                                            Upload screenshot images of product reviews.
                                            You can select multiple images. Recommended size: 600x600px
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ Section (Optional) -->
                            <div class="form-row">
                                <div class="form-group col-12"
                                    style="border: 1px solid #ddd; margin: 10px 0; border-radius: 5px; padding: 15px;">
                                    <h5 class="mb-3">FAQ Section <small class="text-muted">(Optional)</small></h5>

                                    <div class="form-group">
                                        <label for="faq_section_head">FAQ Section Heading</label>
                                        <input type="text" class="form-control" id="faq_section_head"
                                            name="faq_section_head"
                                            value="{{ old('faq_section_head', 'সচরাচর জিজ্ঞাসা') }}"
                                            placeholder="e.g., Frequently Asked Questions">
                                    </div>

                                    <div id="faq-items">
                                        <div class="faq-item card mb-2">
                                            <div class="card-body py-2 px-3">
                                                <div class="form-group mb-2">
                                                    <label>Question</label>
                                                    <input type="text" class="form-control" name="faq_question[]"
                                                        value="{{ old('faq_question.0') }}" placeholder="প্রশ্ন লিখুন">
                                                </div>
                                                <div class="form-group mb-1">
                                                    <label>Answer</label>
                                                    <textarea class="form-control" name="faq_answer[]" rows="2"
                                                        placeholder="উত্তর লিখুন">{{ old('faq_answer.0') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-success mt-2" id="add-faq-btn">
                                        <i class="fa fa-plus"></i> Add FAQ
                                    </button>
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

            // Show/hide autoplay toggle based on file type
            $('#banner_image').on('change', function() {
                var file = this.files[0];
                if (file && file.type.startsWith('video/')) {
                    $('#autoplay-toggle').show();
                } else {
                    $('#autoplay-toggle').hide();
                }
            });

            // FAQ: Add new item
            $('#add-faq-btn').on('click', function() {
                var index = $('#faq-items .faq-item').length;
                var html = '<div class="faq-item card mb-2">' +
                    '<div class="card-body py-2 px-3">' +
                    '<div class="d-flex justify-content-end">' +
                    '<button type="button" class="btn btn-sm btn-outline-danger remove-faq-btn"><i class="fa fa-times"></i></button>' +
                    '</div>' +
                    '<div class="form-group mb-2">' +
                    '<label>Question</label>' +
                    '<input type="text" class="form-control" name="faq_question[]" placeholder="প্রশ্ন লিখুন">' +
                    '</div>' +
                    '<div class="form-group mb-1">' +
                    '<label>Answer</label>' +
                    '<textarea class="form-control" name="faq_answer[]" rows="2" placeholder="উত্তর লিখুন"></textarea>' +
                    '</div>' +
                    '</div></div>';
                $('#faq-items').append(html);
            });

            // FAQ: Remove item
            $(document).on('click', '.remove-faq-btn', function() {
                $(this).closest('.faq-item').remove();
            });
        });
    </script>
@endsection

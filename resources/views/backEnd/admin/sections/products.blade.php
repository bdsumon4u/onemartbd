@extends('backEnd.admin.layouts.master')

@section('title')
    {{ $section->name }} - Products
@endsection

@section('css')
    <style>
        .product-search-results {
            position: absolute;
            z-index: 1000;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 300px;
            overflow-y: auto;
            width: 100%;
            display: none;
        }
        .product-search-results .search-item {
            padding: 8px 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
        }
        .product-search-results .search-item:hover {
            background-color: #f5f5f5;
        }
        .product-search-results .search-item img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            margin-right: 10px;
            border-radius: 4px;
        }
    </style>
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">{{ $section->name }} - Manage Products</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('admin.sections') }}" class="breadcrumb-link">Sections</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $section->name }}</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Add Product --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><strong>Add Product</strong></div>
                            <div class="card-body">
                                <form action="{{ route('admin.sections.products.add', $section->id) }}" method="post" id="add_product_form">
                                    @csrf
                                    <input type="hidden" name="product_id" id="selected_product_id">
                                    <div class="form-group position-relative">
                                        <label for="product_search">Search Product</label>
                                        <input type="text" class="form-control" id="product_search" placeholder="Search by product name or SKU..." autocomplete="off">
                                        <div class="product-search-results" id="search_results"></div>
                                    </div>
                                    <div id="selected_product_info" class="mb-3" style="display: none;">
                                        <div class="alert alert-info d-flex align-items-center justify-content-between mb-0">
                                            <span id="selected_product_text"></span>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="clear_selection">&times;</button>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm" id="add_product_btn" disabled>
                                        <i class="fas fa-plus"></i> Add Product
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products List --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>Products ({{ $section->products->count() }})</strong>
                            </div>
                            <div class="card-body table-responsive">
                                <form action="{{ route('admin.sections.products.reorder', $section->id) }}" method="post" id="reorder_products_form">
                                    @csrf
                                    <table class="table table-bordered text-center table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%"><i class="fas fa-arrows-alt"></i></th>
                                                <th>SL.</th>
                                                <th>Image</th>
                                                <th>Product Name</th>
                                                <th>SKU</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sortable_products">
                                            @if($section->products->count() > 0)
                                                @foreach($section->products as $key => $product)
                                                    <tr data-id="{{ $product->id }}">
                                                        <td class="drag-handle" style="cursor: grab;"><i class="fas fa-grip-vertical"></i></td>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>
                                                            <img width="40" src="{{ $product->get_thumb ? asset($product->get_thumb->file_url) : asset('frontEnd/images/no_image.png') }}" alt="">
                                                        </td>
                                                        <td>{{ $product->name }}</td>
                                                        <td>{{ $product->sku }}</td>
                                                        <td>
                                                            @if($product->sale_price > 0)
                                                                <span style="text-decoration: line-through; color: #999;">{{ $product->price }}</span>
                                                                <br>{{ $product->sale_price }}
                                                            @else
                                                                {{ $product->price }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($product->status == 1)
                                                                <span class="badge badge-success">Active</span>
                                                            @else
                                                                <span class="badge badge-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.sections.products.remove', [$section->id, $product->id]) }}" onclick="return confirm('Remove this product from section?')" class="text-danger" title="Remove">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="8" class="text-center text-danger font-weight-bold">No Products Added!</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    @if($section->products->count() > 1)
                                        <button type="submit" class="btn btn-primary btn-sm" id="save_product_order_btn" style="display: none;">
                                            <i class="fas fa-save"></i> Save Order
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function () {
            var searchTimeout;

            // Product search
            $('#product_search').on('keyup', function () {
                clearTimeout(searchTimeout);
                var query = $(this).val();

                if (query.length < 2) {
                    $('#search_results').hide().empty();
                    return;
                }

                searchTimeout = setTimeout(function () {
                    $.ajax({
                        url: '{{ route("admin.sections.products.search") }}',
                        type: 'GET',
                        data: { q: query },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (data) {
                            var html = '';
                            if (data.length > 0) {
                                data.forEach(function (product) {
                                    var imgUrl = product.get_thumb
                                        ? '{{ asset("") }}' + product.get_thumb.file_url
                                        : '{{ asset("frontEnd/images/no_image.png") }}';
                                    var price = product.sale_price > 0 ? product.sale_price : product.price;
                                    html += '<div class="search-item" data-id="' + product.id + '" data-name="' + product.name + '">';
                                    html += '<img src="' + imgUrl + '" alt="">';
                                    html += '<div><strong>' + product.name + '</strong><br><small>SKU: ' + (product.sku || 'N/A') + ' | Price: ' + price + '</small></div>';
                                    html += '</div>';
                                });
                            } else {
                                html = '<div class="p-3 text-muted text-center">No products found</div>';
                            }
                            $('#search_results').html(html).show();
                        }
                    });
                }, 300);
            });

            // Select product from search results
            $(document).on('click', '.search-item', function () {
                var id = $(this).data('id');
                var name = $(this).data('name');
                $('#selected_product_id').val(id);
                $('#selected_product_text').text(name);
                $('#selected_product_info').show();
                $('#product_search').val('');
                $('#search_results').hide().empty();
                $('#add_product_btn').prop('disabled', false);
            });

            // Clear selection
            $('#clear_selection').on('click', function () {
                $('#selected_product_id').val('');
                $('#selected_product_info').hide();
                $('#add_product_btn').prop('disabled', true);
            });

            // Hide search results when clicking outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#product_search, #search_results').length) {
                    $('#search_results').hide();
                }
            });

            // Sortable products
            var el = document.getElementById('sortable_products');
            if (el && el.children.length > 1) {
                Sortable.create(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function () {
                        document.getElementById('save_product_order_btn').style.display = 'inline-block';
                    }
                });
            }

            var form = document.getElementById('reorder_products_form');
            form.addEventListener('submit', function () {
                var rows = document.querySelectorAll('#sortable_products tr[data-id]');
                rows.forEach(function (row) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'order[]';
                    input.value = row.dataset.id;
                    form.appendChild(input);
                });
            });
        });
    </script>
@endsection

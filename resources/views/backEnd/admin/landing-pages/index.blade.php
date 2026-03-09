@extends('backEnd.admin.layouts.master')

@section('title')
    Landing Pages
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content">
                <!-- pageheader -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Landing Pages</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="{{ Auth::guard('admin')->check() ? route('admin.home') : '' }}"
                                                class="breadcrumb-link">Home</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Landing Pages</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end pageheader -->

                <div class="row mb-2">
                    <div class="col-12">
                        <a href="{{ route('landing-pages.create') }}" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i>
                            Create Landing Page
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th>SL.</th>
                                            <th>Banner</th>
                                            <th>Title</th>
                                            <th>Product</th>
                                            <th>Slug</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($i = 1)
                                        @if ($landingPages->count() > 0)
                                            @foreach ($landingPages as $item)
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>
                                                        @if($item->is_banner_video)
                                                            <div style="width:50px;height:50px;display:inline-flex;align-items:center;justify-content:center;background:#f0f0f0;border:1px solid #dee2e6;border-radius:4px;">
                                                                <i class="fa fa-video" style="font-size:1.3rem;color:#555;"></i>
                                                            </div>
                                                        @else
                                                            <img width="50" src="{{ $item->display_banner }}" alt="Banner"
                                                                class="img-thumbnail">
                                                        @endif
                                                    </td>
                                                    <td class="text-left">
                                                        <strong>{{ $item->title }}</strong>
                                                        @if ($item->subtitle)
                                                            <br><small class="text-muted">{{ $item->subtitle }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-left">
                                                        {{ $item->product->name ?? 'N/A' }}
                                                        @if ($item->product)
                                                            <br><small class="text-muted">SKU:
                                                                {{ $item->product->sku }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('landing.page', $item->slug) }}" target="_blank"
                                                            class="text-primary">
                                                            {{ $item->slug }}
                                                            <i class="fas fa-external-link-alt ml-1"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if ($item->status)
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->created_at->format('d M Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('landing-pages.edit', $item) }}"
                                                            class="btn btn-primary btn-sm" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('landing-pages.duplicate', $item) }}"
                                                            class="btn btn-info btn-sm" title="Duplicate">
                                                            <i class="fa fa-copy"></i>
                                                        </a>
                                                        <a href="{{ route('landing-pages.destroy', $item) }}"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to delete this landing page?')"
                                                            title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8" class="text-center">No Landing Pages Found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                @if ($landingPages->hasPages())
                    <div class="row">
                        <div class="col-12 d-flex justify-content-center">
                            {{ $landingPages->links() }}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

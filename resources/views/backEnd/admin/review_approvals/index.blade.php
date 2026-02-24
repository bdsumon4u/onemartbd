@extends('backEnd.admin.layouts.master')

@section('title')
    Review Approvals
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Product Reviews</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="{{ Auth::guard('admin')->check() ? route('admin.home') : '#' }}"
                                                class="breadcrumb-link">Home</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Review Approvals</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Product Reviews</span>
                                <span class="badge badge-primary">{{ $reviews->total() }} total</span>
                            </div>
                            <div class="card-body table-responsive">
                                @if ($reviews->isEmpty())
                                    <p class="text-muted mb-0">There are no pending reviews at the moment.</p>
                                @else
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 22%;">Product</th>
                                                <th style="width: 12%;">Rating</th>
                                                <th style="width: 40%;">Review</th>
                                                <th style="width: 10%;">Status</th>
                                                <th style="width: 12%;">Submitted At</th>
                                                <th style="width: 4%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reviews as $review)
                                                @php
                                                    $overallRating = (int) optional($review->ratings->firstWhere('key', 'overall'))->value;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $review->reviewable?->name ?? 'N/A' }}</strong>
                                                    </td>
                                                    <td>
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i
                                                                class="fa {{ $i <= $overallRating ? 'fa-star text-warning' : 'fa-star-o text-muted' }}"></i>
                                                        @endfor
                                                    </td>
                                                    <td>{{ \Illuminate\Support\Str::limit($review->review, 150) }}</td>
                                                    <td>
                                                        @if ($review->approved)
                                                            <span class="badge badge-success">Approved</span>
                                                        @else
                                                            <span class="badge badge-warning">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $review->created_at?->format('d M Y, h:i A') }}</td>
                                                    <td>
                                                        @if (! $review->approved)
                                                            <form action="{{ route('admin.reviews.approve', $review->id) }}"
                                                                method="POST" class="d-inline-block mb-1">
                                                                @csrf
                                                                <button class="btn btn-success btn-sm">Approve</button>
                                                            </form>
                                                        @endif
                                                        <form action="{{ route('admin.reviews.reject', $review->id) }}"
                                                            method="POST" class="d-inline-block w-100">
                                                            @csrf
                                                            <button class="btn btn-danger btn-sm w-100">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                            @if ($reviews->hasPages())
                                <div class="card-footer">
                                    {{ $reviews->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

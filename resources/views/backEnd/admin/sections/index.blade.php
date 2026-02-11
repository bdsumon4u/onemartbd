@extends('backEnd.admin.layouts.master')

@section('title')
    Sections
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Sections</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Sections</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <a href="{{ route('admin.sections.create') }}" class="btn btn-success btn-sm">Add Section</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <form action="{{ route('admin.sections.reorder') }}" method="post" id="reorder_form">
                                    @csrf
                                    <table class="table table-bordered text-center table-striped" id="sections_table">
                                        <thead>
                                            <tr>
                                                <th width="5%"><i class="fas fa-arrows-alt"></i></th>
                                                <th>SL.</th>
                                                <th>Section Name</th>
                                                <th>Products</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sortable_sections">
                                            @if($sections->count() > 0)
                                                @foreach($sections as $key => $section)
                                                    <tr data-id="{{ $section->id }}">
                                                        <td class="drag-handle" style="cursor: grab;"><i class="fas fa-grip-vertical"></i></td>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $section->name }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.sections.products', $section->id) }}" class="badge badge-info">
                                                                {{ $section->products_count }} Products
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if($section->status == 1)
                                                                <span class="badge badge-success">Active</span>
                                                            @else
                                                                <span class="badge badge-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.sections.products', $section->id) }}" class="mr-1" title="Manage Products">
                                                                <i class="fas fa-box"></i>
                                                            </a>
                                                            <a href="{{ route('admin.sections.edit', $section->id) }}" class="mr-1" title="Edit">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <a href="{{ route('admin.sections.delete', $section->id) }}" onclick="return confirm('Are you sure to delete this section?')" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center text-danger font-weight-bold">No Sections Found!</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    @if($sections->count() > 1)
                                        <button type="submit" class="btn btn-primary btn-sm" id="save_order_btn" style="display: none;">
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
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('sortable_sections');
            if (el && el.children.length > 1) {
                Sortable.create(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function () {
                        document.getElementById('save_order_btn').style.display = 'inline-block';
                    }
                });
            }

            var form = document.getElementById('reorder_form');
            form.addEventListener('submit', function () {
                var rows = document.querySelectorAll('#sortable_sections tr[data-id]');
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

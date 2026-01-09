@extends('backEnd.admin.layouts.master')

@section('title')
    Incomplete Orders
@endsection
@section('css')
    <style>
        @media (max-width: 576px) {
            .form-inline .form-control {
                display: inline-block;
                width: auto;
                vertical-align: middle;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('/') }}backEnd/assets/vendor/datetimepicker/bootstrap-datetimepicker.min.css">
@endsection
@php

    @endphp
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
                            <h2 class="pageheader-title"> Incomplete Orders</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{ Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : '')) }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page"> Incomplete Orders</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 1%">SL.</th>
                                        <th style="width: 10%">Date</th>
                                        <th style="width: 20%">Customer Info</th>
                                        <th style="width: 60%">Products</th>
                                        <th style="width: 7%">Total</th>
                                        <th>Note</th>
                                        <th style="width: 2%">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($i = 1)
                                    @if (Auth::guard('admin')->check() || Auth::guard('manager')->check() || Auth::guard('employee')->check())
                                        @if ($data->count() > 0)
                                            @foreach ($data as $item)
                                                <tr id="tr_{{ $item->id }}">

                                                    <td>{{ $i++ }}</td>
                                                    <td>
                                                        {{ date('d M, Y', strtotime($item->created_at)) }}<br>
                                                        {{ date('h:i:s A', strtotime($item->created_at)) }}
                                                    </td>

                                                    <td>
                                                        <span><strong>Name: </strong>{{ $item->customer_name }}</span>
                                                        <br>
                                                        <a href="tel:{{ $item->customer_phone }}"><span><strong>Phone:
                                                                    </strong>{{ $item->customer_phone }}</span>
                                                        </a>
                                                        <br>
                                                        <span><strong>Address:
                                                                </strong>{{ $item->customer_address }}</span>
                                                    </td>

                                                    <td>
                                                        @foreach (json_decode($item->abandoned_item, true) as $key => $abandoned_item)
                                                            <?php
                                                            $product = \App\Product::with('get_thumb')->find($abandoned_item['product_id']);
                                                            ?>
                                                            <span
                                                                class="text-danger fw-bold">{{ $abandoned_item['qty'] }}</span>
                                                            x {{ $product->name }}
                                                            @if ($abandoned_item['attributes'])
                                                                <br>
                                                                <small class="fw-bold text-primary">
                                                                    @foreach (json_decode($abandoned_item['attributes'], true) as $variant => $variant_item)
                                                                        {{ $variant }} :
                                                                        {{ $variant_item }},
                                                                    @endforeach
                                                                </small>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $web_settings->currency_sign }}
                                                        {{ number_format($item->total, 2, '.', '') }}
                                                    </td>
                                                    <td>
                                                        <i class="fa fa-edit edit-note" data-note="{{$item->note}}" data-id="{{$item->id}}"></i>
                                                        {{$item->note}}
                                                    </td>
                                                    <td class="text-center">
                                                        @if (Auth::guard('admin')->check())
                                                            <a href="{{ route('admin.incomplete.order.create', $item->id) }}"
                                                               title="Create Order"
                                                               class="d-block mb-1 btn btn-success btn-sm">
                                                                Create Order</a>
                                                            </a>
                                                            <a href="{{ route('admin.incomplete.order.delete', $item->id) }}"
                                                               title="Delete Order"
                                                               class="d-block mb-1 btn btn-danger btn-sm"
                                                               onclick="return confirm('Are you sure to delete this?')">
                                                                Delete
                                                            </a>
                                                        @endif
                                                        @if (Auth::guard('manager')->check())
                                                            <a href="{{ route('manager.incomplete.order.create', $item->id) }}"
                                                               title="Create Order"
                                                               class="d-block mb-1 btn btn-success btn-sm">
                                                                Create Order
                                                            </a>
                                                        @endif
                                                        @if (Auth::guard('employee')->check())
                                                            <a href="{{ route('employee.incomplete.order.create', $item->id) }}"
                                                               title="Create Order"
                                                               class="d-block mb-1 btn btn-success btn-sm">
                                                                Create Order
                                                            </a>
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="12" class="text-center text-danger font-weight-bold">No
                                                    Data Found!
                                                </td>
                                            </tr>
                                        @endif
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

    <div class="modal fade" id="note_modal" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="note_modal_modalTitle">Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ Auth::guard('admin')->check() ? route('admin.incomplete.order.note.update') : (Auth::guard('manager')->check() ? route('manager.incomplete.order.note.update') : (Auth::guard('employee')->check() ? route('employee.incomplete.order.note.update') : '')) }}" method="post">
                        @csrf
                        <input type="hidden" name="id" id="id_e">
                        <div class="form-group">
                            <textarea name="note" id="note" class="form-control mb-2"></textarea>
                            <input type="submit" class="btn btn-success" value="Submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).on('click', '.edit-note', function () {
            $('#id_e').val($(this).data('id'));
            $('#note').text($(this).data('note'));
            $('#note_modal').modal('show')
        });
    </script>
@endsection

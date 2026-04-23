@extends('backEnd.admin.layouts.master')

@section('title')
    Incomplete Orders
@endsection
@section('css')
    <style>
        .order-card .card-body {
            padding: 10px 12px;
        }

        .order-card .card {
            transition: 0.2s ease;
        }

        .order-card .card.active-status-card {
            border-color: #007bff !important;
            box-shadow: 0 0 0 1px rgba(0, 123, 255, 0.2);
        }

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
    $paginate = $paginate ?? 10;
    $totalIncompleteCount = $totalIncompleteCount ?? 0;
    $activeIncompleteCount = $activeIncompleteCount ?? 0;
    $cancelledIncompleteCount = $cancelledIncompleteCount ?? 0;
    $statusFilter = $statusFilter ?? request()->query('status');
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
                        <div class="row mb-3 order-card">
                            <div class="col-xl-2 col-lg-3 col-md-6 col-sm-6 col-6 mb-2">
                                <a
                                    href="{{ Auth::guard('admin')->check() ? route('admin.incomplete.orders') : (Auth::guard('manager')->check() ? route('manager.incomplete.orders') : (Auth::guard('employee')->check() ? route('employee.incomplete.orders') : '')) }}">
                                    <div class="card border-3 {{ $statusFilter === null || $statusFilter === '' ? 'active-status-card' : '' }}">
                                        <div class="card-body">
                                            <div class="metric-value">
                                                <h2 class="mb-0">{{ $totalIncompleteCount }}</h2>
                                            </div>
                                            <h5 class="text-primary mb-0">Total</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-6 col-sm-6 col-6 mb-2">
                                <a
                                    href="{{ Auth::guard('admin')->check() ? route('admin.incomplete.orders', ['status' => 0]) : (Auth::guard('manager')->check() ? route('manager.incomplete.orders', ['status' => 0]) : (Auth::guard('employee')->check() ? route('employee.incomplete.orders', ['status' => 0]) : '')) }}">
                                    <div class="card border-3 {{ (string) $statusFilter === '0' ? 'active-status-card' : '' }}">
                                        <div class="card-body">
                                            <div class="metric-value">
                                                <h2 class="mb-0">{{ $activeIncompleteCount }}</h2>
                                            </div>
                                            <h5 class="text-success mb-0">Active</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-6 col-sm-6 col-6 mb-2">
                                <a
                                    href="{{ Auth::guard('admin')->check() ? route('admin.incomplete.orders', ['status' => 1]) : (Auth::guard('manager')->check() ? route('manager.incomplete.orders', ['status' => 1]) : (Auth::guard('employee')->check() ? route('employee.incomplete.orders', ['status' => 1]) : '')) }}">
                                    <div class="card border-3 {{ (string) $statusFilter === '1' ? 'active-status-card' : '' }}">
                                        <div class="card-body">
                                            <div class="metric-value">
                                                <h2 class="mb-0">{{ $cancelledIncompleteCount }}</h2>
                                            </div>
                                            <h5 class="text-danger mb-0">Cancelled</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-body table-responsive">
                                @if (Auth::guard('admin')->check() && $data->count() > 0)
                                    <div class="mt-2 d-flex flex-wrap align-items-center">
                                        <form action="" method="GET" id="paginate_form" class="form-inline mr-2 mb-2">
                                            <input type="hidden" name="status"
                                                value="{{ $statusFilter !== null ? $statusFilter : '' }}">
                                            <select name="paginate" id="paginate" class="form-control form-control-sm">
                                                @foreach ([10, 25, 50, 100, 200, 350, 500] as $pageLength)
                                                    <option value="{{ $pageLength }}"
                                                        {{ (int) request()->input('paginate', $paginate ?? 10) === $pageLength ? 'selected' : '' }}>
                                                        {{ $pageLength }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>

                                        <form id="bulk_assign_form" method="POST"
                                            action="{{ route('admin.incomplete.order.bulk-assign-employee') }}"
                                            class="form-inline mr-2 mb-2">
                                            @csrf
                                            <label class="mr-2 mb-0">Bulk assign to:</label>
                                            <select name="employee_id" class="form-control form-control-sm mr-2">
                                                <option value="">Not Assigned</option>
                                                @foreach ($employees as $employeeId => $employeeName)
                                                    <option value="{{ $employeeId }}">{{ $employeeName }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                Apply
                                            </button>
                                        </form>

                                        <form id="equal_assign_form" method="POST"
                                            action="{{ route('admin.incomplete.order.bulk-equal-assign') }}"
                                            class="mr-2 mb-2">
                                            @csrf
                                            <button type="button" id="equal_assign_btn" class="btn btn-primary btn-sm">
                                                Eq. Assign
                                            </button>
                                        </form>

                                        <form id="bulk_cancel_form" method="POST"
                                            action="{{ route('admin.incomplete.order.bulk-cancel') }}"
                                            class="mr-2 mb-2">
                                            @csrf
                                            <input type="hidden" name="reason" id="bulk_cancel_reason">
                                            <button type="button" id="bulk_cancel_btn" class="btn btn-warning btn-sm">
                                                Bulk Cancel
                                            </button>
                                        </form>

                                        <form id="bulk_delete_form" method="POST"
                                            action="{{ route('admin.incomplete.order.bulk-delete') }}" class="mb-2">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete selected incomplete orders?')">
                                                Bulk Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            @if (Auth::guard('admin')->check())
                                                <th style="width: 1%">
                                                    <input type="checkbox" id="select_all_orders">
                                                </th>
                                            @endif
                                            <th style="width: 1%">SL.</th>
                                            <th style="width: 8%">Date</th>
                                            <th style="width: 18%">Customer Info</th>
                                            <th style="width: 45%">Products</th>
                                            <th style="width: 6%">Total</th>
                                            <th style="width: 12%; white-space: nowrap;">Assigned To</th>
                                            <th style="width: 8%">Status</th>
                                            <th>Note</th>
                                            <th style="width: 3%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($i = 1)
                                        @if (Auth::guard('admin')->check() || Auth::guard('manager')->check() || Auth::guard('employee')->check())
                                            @if ($data->count() > 0)
                                                @foreach ($data as $item)
                                                    <tr id="tr_{{ $item->id }}">

                                                        @if (Auth::guard('admin')->check())
                                                            <td>
                                                                <input type="checkbox" class="order-checkbox"
                                                                    value="{{ $item->id }}"
                                                                    data-status="{{ $item->status }}">
                                                            </td>
                                                        @endif

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
                                                                $product = \App\Models\Product::with('get_thumb')->find($abandoned_item['product_id']);
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
                                                            <form method="POST"
                                                                action="{{ Auth::guard('admin')->check()
                                                                    ? route('admin.incomplete.order.assign-employee', $item->id)
                                                                    : (Auth::guard('manager')->check()
                                                                        ? route('manager.incomplete.order.assign-employee', $item->id)
                                                                        : (Auth::guard('employee')->check()
                                                                            ? route('employee.incomplete.order.assign-employee', $item->id)
                                                                            : '')) }}">
                                                                @csrf
                                                                <select name="employee_id"
                                                                    class="form-control form-control-sm"
                                                                    onchange="this.form.submit()">
                                                                    <option value="">Not Assigned</option>
                                                                    @foreach ($employees as $employeeId => $employeeName)
                                                                        <option value="{{ $employeeId }}"
                                                                            {{ optional($item->assignedEmployee)->id === $employeeId ? 'selected' : '' }}>
                                                                            {{ $employeeName }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </form>
                                                        </td>
                                                        <td>
                                                            @if ($item->status == 0)
                                                                <span class="badge badge-success">Active</span>
                                                            @else
                                                                <span class="badge badge-danger">Cancelled</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($item->status == 1 && $item->cancellation_reason)
                                                                <small class="text-danger"><strong>Reason:</strong>
                                                                    {{ $item->cancellation_reason }}</small>
                                                            @else
                                                                <i class="fa fa-edit edit-note"
                                                                    data-note="{{ $item->note }}"
                                                                    data-id="{{ $item->id }}"></i>
                                                                {{ $item->note }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if (Auth::guard('admin')->check())
                                                                <a href="{{ route('admin.incomplete.order.create', $item->id) }}"
                                                                    title="Create Order"
                                                                    class="d-block mb-1 btn btn-success btn-sm">
                                                                    Create Order</a>
                                                                @if($item->status == 0)
                                                                <a href="#" title="Cancel Order"
                                                                    class="d-block mb-1 btn btn-warning btn-sm cancel-btn"
                                                                    onclick="cancelOrder({{ $item->id }}, '{{ Auth::guard('admin')->check() ? route('admin.incomplete.order.cancel', $item->id) : '' }}')">
                                                                    Cancel
                                                                </a>
                                                                @endif
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
                                                                @if($item->status == 0)
                                                                <a href="#" title="Cancel Order"
                                                                    class="d-block mb-1 btn btn-warning btn-sm cancel-btn"
                                                                    onclick="cancelOrder({{ $item->id }}, '{{ Auth::guard('manager')->check() ? route('manager.incomplete.order.cancel', $item->id) : '' }}')">
                                                                    Cancel
                                                                </a>
                                                                @endif
                                                            @endif
                                                            @if (Auth::guard('employee')->check())
                                                                <a href="{{ route('employee.incomplete.order.create', $item->id) }}"
                                                                    title="Create Order"
                                                                    class="d-block mb-1 btn btn-success btn-sm">
                                                                    Create Order
                                                                </a>
                                                                @if($item->status == 0)
                                                                <a href="#" title="Cancel Order"
                                                                    class="d-block mb-1 btn btn-warning btn-sm cancel-btn"
                                                                    onclick="cancelOrder({{ $item->id }}, '{{ Auth::guard('employee')->check() ? route('employee.incomplete.order.cancel', $item->id) : '' }}')">
                                                                    Cancel
                                                                </a>
                                                                @endif
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

    <div class="modal fade" id="note_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="note_modal_modalTitle">Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form
                        action="{{ Auth::guard('admin')->check() ? route('admin.incomplete.order.note.update') : (Auth::guard('manager')->check() ? route('manager.incomplete.order.note.update') : (Auth::guard('employee')->check() ? route('employee.incomplete.order.note.update') : '')) }}"
                        method="post">
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

    <div class="modal fade" id="cancel_reason_modal" tabindex="-1" role="dialog" aria-labelledby="cancelReasonModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelReasonModalTitle">Cancel Order – Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label for="cancel_reason_text">Please provide a reason for cancellation (minimum 5 characters)</label>
                        <textarea id="cancel_reason_text" class="form-control" rows="4" placeholder="Enter cancellation reason..."></textarea>
                        <small id="cancel_reason_error" class="text-danger d-none">Reason must be at least 5 characters long.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="cancel_reason_confirm">Confirm Cancellation</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        let cancelOrderRoute = '';
        let cancelActionType = 'single';

        function cancelOrder(cartId, route) {
            cancelActionType = 'single';
            cancelOrderRoute = route;
            document.getElementById('cancel_reason_text').value = '';
            document.getElementById('cancel_reason_error').classList.add('d-none');
            $('#cancel_reason_modal').modal('show');
        }

        document.getElementById('cancel_reason_confirm').addEventListener('click', function() {
            const textarea = document.getElementById('cancel_reason_text');
            const errorEl = document.getElementById('cancel_reason_error');
            const reason = textarea.value.trim();

            if (reason.length < 5) {
                errorEl.classList.remove('d-none');
                return;
            }

            errorEl.classList.add('d-none');
            $('#cancel_reason_modal').modal('hide');

            if (cancelActionType === 'bulk') {
                var bulkCancelForm = document.getElementById('bulk_cancel_form');
                var ids = getSelectedOrderIds();

                if (!ids.length) {
                    alert('Please select at least one incomplete order.');
                    return;
                }

                bulkCancelForm.querySelectorAll('input[name="ids[]"]').forEach(function(input) {
                    input.parentNode.removeChild(input);
                });

                ids.forEach(function(id) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    bulkCancelForm.appendChild(input);
                });

                document.getElementById('bulk_cancel_reason').value = reason;
                bulkCancelForm.submit();
            } else {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = cancelOrderRoute;
                form.innerHTML = `@csrf`;
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = reason;
                form.appendChild(reasonInput);
                document.body.appendChild(form);
                form.submit();
            }
        });

        $('#cancel_reason_modal').on('hidden.bs.modal', function() {
            document.getElementById('cancel_reason_text').value = '';
            document.getElementById('cancel_reason_error').classList.add('d-none');
        });

        function getSelectedOrderIds() {
            var checkboxes = document.querySelectorAll('.order-checkbox:checked');
            var ids = [];

            checkboxes.forEach(function(checkbox) {
                ids.push(checkbox.value);
            });

            return ids;
        }

        var selectAllCheckbox = document.getElementById('select_all_orders');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                var checked = this.checked;
                var checkboxes = document.querySelectorAll('.order-checkbox');

                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = checked;
                });
            });
        }

        var paginateSelect = document.getElementById('paginate');
        if (paginateSelect) {
            paginateSelect.addEventListener('change', function() {
                document.getElementById('paginate_form').submit();
            });
        }

        var bulkAssignForm = document.getElementById('bulk_assign_form');
        if (bulkAssignForm) {
            bulkAssignForm.addEventListener('submit', function(event) {
                var ids = getSelectedOrderIds();

                if (!ids.length) {
                    event.preventDefault();
                    alert('Please select at least one incomplete order.');

                    return;
                }

                this.querySelectorAll('input[name="ids[]"]').forEach(function(input) {
                    input.parentNode.removeChild(input);
                });

                ids.forEach(function(id) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    bulkAssignForm.appendChild(input);
                });
            });
        }

        var bulkDeleteForm = document.getElementById('bulk_delete_form');
        if (bulkDeleteForm) {
            bulkDeleteForm.addEventListener('submit', function(event) {
                var ids = getSelectedOrderIds();

                if (!ids.length) {
                    event.preventDefault();
                    alert('Please select at least one incomplete order.');

                    return;
                }

                this.querySelectorAll('input[name="ids[]"]').forEach(function(input) {
                    input.parentNode.removeChild(input);
                });

                ids.forEach(function(id) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    bulkDeleteForm.appendChild(input);
                });
            });
        }

        var bulkCancelButton = document.getElementById('bulk_cancel_btn');
        if (bulkCancelButton) {
            bulkCancelButton.addEventListener('click', function() {
                var ids = getSelectedOrderIds();

                if (!ids.length) {
                    alert('Please select at least one incomplete order.');
                    return;
                }

                cancelActionType = 'bulk';
                document.getElementById('cancel_reason_text').value = '';
                document.getElementById('cancel_reason_error').classList.add('d-none');
                $('#cancel_reason_modal').modal('show');
            });
        }

        var equalAssignForm = document.getElementById('equal_assign_form');
        var equalAssignButton = document.getElementById('equal_assign_btn');
        if (equalAssignForm && equalAssignButton) {
            equalAssignButton.addEventListener('click', function() {
                var selectedActiveIds = [];
                var selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');

                selectedCheckboxes.forEach(function(checkbox) {
                    if (String(checkbox.dataset.status) === '0') {
                        selectedActiveIds.push(checkbox.value);
                    }
                });

                if (!selectedActiveIds.length) {
                    alert('Please select at least one active incomplete order.');
                    return;
                }

                equalAssignForm.querySelectorAll('input[name="ids[]"]').forEach(function(input) {
                    input.parentNode.removeChild(input);
                });

                selectedActiveIds.forEach(function(id) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    equalAssignForm.appendChild(input);
                });

                equalAssignForm.submit();
            });
        }

        $(document).on('click', '.edit-note', function() {
            $('#id_e').val($(this).data('id'));
            $('#note').text($(this).data('note'));
            $('#note_modal').modal('show')
        });
    </script>
@endsection

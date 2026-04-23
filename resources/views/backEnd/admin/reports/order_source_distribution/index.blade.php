@extends('backEnd.admin.layouts.master')

@section('title')
    Order Source Distribution
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <div class="row">
                    <div class="col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Order Source Distribution</h2>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <form method="GET"
                            action="{{ Auth::guard('admin')->check() ? route('admin.reports.order_source_distribution') : route('manager.reports.order_source_distribution') }}"
                            class="form-inline">
                            <label for="custom_range" class="mr-2 mb-2">Range</label>
                            <select id="custom_range" name="custom_range" class="form-control mr-2 mb-2">
                                <option value="today" {{ $customRange === 'today' ? 'selected' : '' }}>Today</option>
                                <option value="yesterday" {{ $customRange === 'yesterday' ? 'selected' : '' }}>Yesterday
                                </option>
                                <option value="last_7_days" {{ $customRange === 'last_7_days' ? 'selected' : '' }}>Last 7
                                    Days</option>
                                <option value="this_month" {{ $customRange === 'this_month' ? 'selected' : '' }}>This
                                    Month</option>
                                <option value="last_month" {{ $customRange === 'last_month' ? 'selected' : '' }}>Last
                                    Month</option>
                                <option value="last_6_months" {{ $customRange === 'last_6_months' ? 'selected' : '' }}>Last
                                    6 Months</option>
                                <option value="custom" {{ $customRange === 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                            <input type="date" id="start_date" name="start_date" class="form-control mr-2 mb-2"
                                value="{{ $startDate }}">
                            <input type="date" id="end_date" name="end_date" class="form-control mr-2 mb-2"
                                value="{{ $endDate }}">
                            <button type="submit" class="btn btn-primary btn-sm mr-2 mb-2">Filter</button>
                            <a href="{{ Auth::guard('admin')->check() ? route('admin.reports.order_source_distribution') : route('manager.reports.order_source_distribution') }}"
                                class="btn btn-secondary btn-sm mb-2">Reset</a>
                        </form>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-4 col-md-6 mb-2">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h6 class="mb-1">ORDER TYPES IN CHART</h6>
                                <h2 class="mb-0">{{ $sourcesInChart }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-2">
                        <div class="card text-white" style="background:#6f42c1;">
                            <div class="card-body">
                                <h6 class="mb-1">TOTAL ORDERS</h6>
                                <h2 class="mb-0">{{ $totalOrders }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                OrderType Distribution ({{ $rangeLabel }})
                            </div>
                            <div class="card-body">
                                <canvas id="orderSourceChart" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleCustomDateInputs() {
            const isCustom = document.getElementById('custom_range').value === 'custom';
            document.getElementById('start_date').disabled = !isCustom;
            document.getElementById('end_date').disabled = !isCustom;
        }

        document.getElementById('custom_range').addEventListener('change', toggleCustomDateInputs);
        toggleCustomDateInputs();

        const sourceLabels = @json($sourceRows->pluck('source_label')->values());
        const sourceValues = @json($sourceRows->pluck('total_orders')->map(fn($value) => (int) $value)->values());

        const ctx = document.getElementById('orderSourceChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: sourceLabels,
                datasets: [{
                    label: 'Orders',
                    data: sourceValues,
                    backgroundColor: '#ff7f0e',
                    borderColor: '#ff7f0e',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
@endsection

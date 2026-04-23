@extends('backEnd.admin.layouts.master')

@section('title')
    Product Distribution
@endsection

@section('css')
    <style>
        #productDistributionChart {
            min-height: 900px;
        }
    </style>
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <div class="row">
                    <div class="col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Product Distribution</h2>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <form method="GET"
                            action="{{ Auth::guard('admin')->check() ? route('admin.reports.product_distribution') : route('manager.reports.product_distribution') }}"
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
                            <a href="{{ Auth::guard('admin')->check() ? route('admin.reports.product_distribution') : route('manager.reports.product_distribution') }}"
                                class="btn btn-secondary btn-sm mb-2">Reset</a>
                        </form>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-4 col-md-6 mb-2">
                        <div class="card text-white" style="background:#1f6fe5;">
                            <div class="card-body">
                                <h6 class="mb-1">PRODUCTS IN CHART</h6>
                                <h2 class="mb-0">{{ $productsInChart }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-2">
                        <div class="card text-white" style="background:#1e8e5b;">
                            <div class="card-body">
                                <h6 class="mb-1">TOTAL ORDERED QUANTITY</h6>
                                <h2 class="mb-0">{{ $totalOrderedQuantity }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                Product Quantity Distribution ({{ $rangeLabel }})
                            </div>
                            <div class="card-body">
                                <canvas id="productDistributionChart"></canvas>
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

        const productLabels = @json($productRows->pluck('product_name')->values());
        const productValues = @json($productRows->pluck('total_qty')->map(fn($value) => (int) $value)->values());

        const ctx = document.getElementById('productDistributionChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: productLabels,
                datasets: [{
                    label: 'Quantity',
                    data: productValues,
                    backgroundColor: '#1f6fe5',
                    borderColor: '#1f6fe5',
                    borderWidth: 1,
                    barThickness: 14,
                    maxBarThickness: 18
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
@endsection

@extends('backEnd.admin.layouts.master')
@section('title', 'Monthly Payroll')
@section('body')
    <div class="dashboard-wrapper">
        <div class="container-fluid dashboard-content">
            <div class="page-header">
                <h2 class="pageheader-title">Monthly Payroll</h2>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <form method="GET" class="form-inline mb-2"><select name="month" class="form-control mr-2">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected((int) $month === $m)>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                @endfor
                            </select>
                            <input type="number" name="year" min="2020" max="2100" class="form-control mr-2"
                                value="{{ $year }}"><button class="btn btn-secondary mr-2">Filter</button>
                        </form>
                        <form method="POST" action="{{ route('admin.payroll.generate_all') }}" class="form-inline mb-2">
                            @csrf<input type="hidden" name="month" value="{{ $month }}"><input type="hidden"
                                name="year" value="{{ $year }}"><button class="btn btn-primary mr-2">Generate
                                All</button></form>
                        <form method="POST" action="{{ route('admin.payroll.generate_single') }}"
                            class="form-inline mb-2 ml-auto">@csrf<input type="hidden" name="month"
                                value="{{ $month }}"><input type="hidden" name="year"
                                value="{{ $year }}"><select name="user_id" class="form-control mr-2">
                                @foreach ($staffUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-info">Generate Selected</button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Monthly Salary</th>
                                    <th>Present</th>
                                    <th>Base Salary</th>
                                    <th>Off-day Bonus</th>
                                    <th>Overtime</th>
                                    <th>Hazira Bonus</th>
                                    <th>Special Bonus</th>
                                    <th>xSell Bonus</th>
                                    <th>Late Fee</th>
                                    <th>Penalty</th>
                                    <th>Advance</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payrolls as $index => $payroll)
                                    <tr>
                                        <td>{{ $payrolls->firstItem() + $index }}</td>
                                        <td>{{ $payroll->user?->name }}</td>
                                        <td>{{ number_format((float) ($payroll->user?->monthly_salary ?? 0), 2) }}</td>
                                        <td>{{ $payroll->present_days }}@if ($payroll->off_day_presents > 0)
                                                (OFF: {{ $payroll->off_day_presents }})
                                            @endif
                                        </td>
                                        <td class="text-success">{{ number_format((float) $payroll->base_salary, 2) }}</td>
                                        <td class="text-success">{{ number_format((float) $payroll->off_day_bonus, 2) }}</td>
                                        <td class="text-success">{{ number_format((float) $payroll->overtime_amount, 2) }}
                                        </td>
                                        <td class="text-success">
                                            {{ number_format((float) $payroll->hazira_bonus_amount, 2) }}</td>
                                        <td class="text-success">
                                            {{ number_format((float) $payroll->occasional_bonus_amount, 2) }}</td>
                                        <td class="text-success">{{ number_format((float) $payroll->xsell_bonus_amount, 2) }}
                                        </td>
                                        <td class="text-danger">{{ number_format((float) $payroll->late_deduction, 2) }}</td>
                                        <td class="text-danger">{{ number_format((float) $payroll->penalty_amount, 2) }}</td>
                                        <td class="text-danger">{{ number_format((float) $payroll->advance_deduction, 2) }}
                                        </td>
                                        <td><strong>{{ number_format((float) $payroll->net_salary, 2) }}</strong></td>
                                        <td><span
                                                class="badge badge-{{ $payroll->status === 'paid' ? 'success' : ($payroll->status === 'approved' ? 'primary' : 'secondary') }}">{{ strtoupper($payroll->status) }}</span>
                                        </td>
                                        <td><a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('admin.payroll.show', $payroll) }}">View</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>{{ $payrolls->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

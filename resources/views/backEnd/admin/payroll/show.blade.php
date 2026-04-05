@extends('backEnd.admin.layouts.master')
@section('title', 'Payroll Details: ' . ($payroll->staff?->name ?? ''))
@section('body')
    @php
        use Carbon\Carbon;
        use App\Enums\RoleType;

        $monthName = Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('F Y');
        $user = $payroll->staff;

        $roleLabel = match ((int) ($user?->role ?? 0)) {
            RoleType::Admin->value => 'Admin',
            RoleType::Manager->value => 'Manager',
            default => 'Employee',
        };

        $offDaysList = $user ? implode(', ', $user->getOffDaysArray()) : '';

        $totalOvertimeMinutes = $attendances->sum('overtime_minutes');
        $totalLateMinutes = $attendances->sum('late_minutes');
        $totalPenalty = $attendances->sum('penalty_amount');

        $overtimeUnitMin = max((int) $settings->overtime_unit_minutes, 1);
        $latetimeUnitMin = max((int) $settings->latetime_unit_minutes, 1);

        $statusBadge = match ($payroll->status) {
            'paid' => 'success',
            'approved' => 'primary',
            default => 'secondary',
        };

        $fmt = fn(float|string $v): string => '৳' . number_format((float) $v, 2);
    @endphp
    <div class="dashboard-wrapper">
        <div class="container-fluid dashboard-content">

            <div class="page-header">
                <h2 class="pageheader-title">Payroll Details: {{ $user?->name }}</h2>
                <p class="pageheader-text">{{ $monthName }} &ndash; Detailed Breakdown</p>
            </div>

            <div class="mb-3 d-flex" style="gap: 8px;">
                <a href="{{ route('admin.payroll.index') }}?month={{ $payroll->month }}&year={{ $payroll->year }}"
                    class="btn btn-outline-secondary btn-sm">
                    &larr; Back to Monthly Payroll
                </a>
                <a href="{{ route('admin.payroll.print', $payroll) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                    &#128438; Print Salary Sheet
                </a>
            </div>

            {{-- Top Info + Summary Cards --}}
            <div class="row">
                {{-- Employee Info --}}
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header text-white" style="background:#1565c0;">
                            <strong>Employee Info</strong>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th class="pl-3" style="width:40%">Name:</th>
                                    <td>{{ $user?->name }}</td>
                                </tr>
                                <tr>
                                    <th class="pl-3">Role:</th>
                                    <td>{{ $roleLabel }}</td>
                                </tr>
                                <tr>
                                    <th class="pl-3">Monthly Salary:</th>
                                    <td>{{ $fmt($user?->monthly_salary ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <th class="pl-3">Schedule:</th>
                                    <td>
                                        {{ $user?->start_time ?? '00:00:00' }}
                                        &ndash;
                                        {{ $user?->end_time ?? '23:59:59' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="pl-3">Off Days:</th>
                                    <td>{{ $offDaysList ?: 'None' }}</td>
                                </tr>
                                <tr>
                                    <th class="pl-3">Holidays:</th>
                                    <td>
                                        @if ($holidayRanges->isEmpty())
                                            None
                                        @else
                                            <ul class="mb-1 pl-3">
                                                @foreach ($holidayRanges as $holidayRange)
                                                    <li>
                                                        {{ $holidayRange['name'] }}
                                                        ({{ $holidayRange['from']->format('d M Y') }} -
                                                        {{ $holidayRange['to']->format('d M Y') }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <small><strong>Total:</strong> {{ $holidayTotalDays }} days</small>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Salary Summary --}}
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header text-white" style="background:#388e3c;">
                            <strong>Salary Summary</strong>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td class="pl-3">Present Days:</td>
                                    <td class="text-right pr-3">{{ $payroll->present_days }} days</td>
                                </tr>
                                <tr>
                                    <td class="pl-3">Off-day Work:</td>
                                    <td class="text-right pr-3">{{ $payroll->off_day_presents }} days</td>
                                </tr>
                                <tr>
                                    <td class="pl-3">Base Salary:</td>
                                    <td class="text-right pr-3">{{ $fmt($payroll->base_salary) }}</td>
                                </tr>
                                <tr>
                                    <td class="pl-3 text-success">+ Off-day Bonus:</td>
                                    <td class="text-right pr-3 text-success">{{ $fmt($payroll->off_day_bonus) }}</td>
                                </tr>
                                <tr>
                                    <td class="pl-3 text-success">+ Overtime:</td>
                                    <td class="text-right pr-3 text-success">{{ $fmt($payroll->overtime_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="pl-3 text-success">+ Hazira Bonus:</td>
                                    <td class="text-right pr-3 text-success">{{ $fmt($payroll->hazira_bonus_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="pl-3 text-success">+ Special Bonus:</td>
                                    <td class="text-right pr-3 text-success">{{ $fmt($payroll->occasional_bonus_amount) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pl-3 text-success">+ xSell Bonus:</td>
                                    <td class="text-right pr-3 text-success">{{ $fmt($payroll->xsell_bonus_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="pl-3 text-danger">- Late Fee:</td>
                                    <td class="text-right pr-3 text-danger">{{ $fmt($payroll->late_deduction) }}</td>
                                </tr>
                                <tr>
                                    <td class="pl-3 text-danger">- Penalties:</td>
                                    <td class="text-right pr-3 text-danger">{{ $fmt($payroll->penalty_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="pl-3 text-danger">- Advances:</td>
                                    <td class="text-right pr-3 text-danger">{{ $fmt($payroll->advance_deduction) }}</td>
                                </tr>
                                <tr style="background:#e3f2fd;">
                                    <td class="pl-3"><strong>Net Salary:</strong></td>
                                    <td class="text-right pr-3"><strong>{{ $fmt($payroll->net_salary) }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="pl-3">Status:</td>
                                    <td class="pr-3 text-right">
                                        <span
                                            class="badge badge-{{ $statusBadge }}">{{ ucfirst($payroll->status) }}</span>
                                        <form method="POST" action="{{ route('admin.payroll.status', $payroll) }}"
                                            class="form-inline d-inline-flex ml-2" style="gap:4px;">
                                            @csrf
                                            <select name="status" class="form-control form-control-sm"
                                                style="font-size:0.75rem;">
                                                <option value="draft" @selected($payroll->status === 'draft')>Draft</option>
                                                <option value="approved" @selected($payroll->status === 'approved')>Approved</option>
                                                <option value="paid" @selected($payroll->status === 'paid')>Paid</option>
                                            </select>
                                            <button class="btn btn-sm btn-primary" style="font-size:0.75rem;">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attendance Records --}}
            <div class="card mb-4">
                <div class="card-header"><strong>Attendance Records</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>DATE</th>
                                    <th>DAY</th>
                                    <th>STATUS</th>
                                    <th>CHECK IN</th>
                                    <th>CHECK OUT</th>
                                    <th class="text-center">OVER (MIN)</th>
                                    <th class="text-center">OVER ৳</th>
                                    <th class="text-center">LATE (MIN)</th>
                                    <th class="text-center">LATE ৳</th>
                                    <th class="text-center">PENALTY</th>
                                    <th>NOTES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendances as $att)
                                    @php
                                        $overtimeUnits =
                                            $overtimeUnitMin > 0 ? floor($att->overtime_minutes / $overtimeUnitMin) : 0;
                                        $overtimePay = $overtimeUnits * (float) $settings->overtime_rate;
                                        $lateUnits =
                                            $latetimeUnitMin > 0 ? floor($att->late_minutes / $latetimeUnitMin) : 0;
                                        $latePay = $lateUnits * (float) $settings->latetime_rate;
                                    @endphp
                                    <tr>
                                        <td>{{ $att->date->format('d M') }}</td>
                                        <td>{{ $att->date->format('l') }}</td>
                                        <td>
                                            <span
                                                class="badge badge-success">{{ ucfirst($att->status ?? 'present') }}</span>
                                            @if ($att->auto_checkout)
                                                <span class="badge badge-warning">Auto-out</span>
                                            @endif
                                            @if ($att->is_off_day)
                                                <span class="badge badge-info">Off-day</span>
                                            @endif
                                        </td>
                                        <td>{{ $att->check_in ? $att->check_in->format('h:i A') : '-' }}</td>
                                        <td>{{ $att->check_out ? $att->check_out->format('h:i A') : '-' }}</td>
                                        <td class="text-center">{{ $att->overtime_minutes ?? 0 }}</td>
                                        <td class="text-center {{ $overtimePay > 0 ? 'text-success' : '' }}">
                                            {{ $overtimePay > 0 ? $fmt($overtimePay) : '-' }}
                                        </td>
                                        <td class="text-center {{ ($att->late_minutes ?? 0) > 0 ? 'text-danger' : '' }}">
                                            {{ $att->late_minutes ?? 0 }}
                                        </td>
                                        <td class="text-center {{ $latePay > 0 ? 'text-danger' : '' }}">
                                            {{ $latePay > 0 ? $fmt($latePay) : '-' }}
                                        </td>
                                        <td
                                            class="text-center {{ (float) ($att->penalty_amount ?? 0) > 0 ? 'text-danger' : '' }}">
                                            {{ (float) ($att->penalty_amount ?? 0) > 0 ? $fmt($att->penalty_amount) : '-' }}
                                        </td>
                                        <td>{{ $att->note ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="5" class="text-right pr-3">Total:</td>
                                    <td class="text-center">{{ $totalOvertimeMinutes }} min</td>
                                    <td class="text-center text-success">{{ $fmt($payroll->overtime_amount) }}</td>
                                    <td class="text-center text-danger">{{ $totalLateMinutes }} min</td>
                                    <td class="text-center text-danger">{{ $fmt($payroll->late_deduction) }}</td>
                                    <td class="text-center text-danger">{{ $fmt($totalPenalty) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Salary Advances --}}
            @if ($advances->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header"><strong>Salary Advances</strong></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Approved By</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($advances as $i => $advance)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $advance->date->format('d M Y') }}</td>
                                            <td class="text-danger">{{ $fmt($advance->amount) }}</td>
                                            <td>{{ $advance->approver?->name ?? '-' }}</td>
                                            <td>{{ $advance->note ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="2" class="text-right pr-3">Total:</td>
                                        <td class="text-danger">{{ $fmt($advances->sum('amount')) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Special Bonuses --}}
            @if ($bonuses->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header"><strong>Special Bonuses</strong></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bonuses as $i => $bonus)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $bonus->name }}</td>
                                            <td class="text-success">{{ $fmt($bonus->amount) }}</td>
                                            <td>{{ $bonus->description ?? '-' }}</td>
                                            <td>{{ $bonus->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="2" class="text-right pr-3">Total:</td>
                                        <td class="text-success">{{ $fmt($bonuses->sum('amount')) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Salary Sheet</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 14px;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f1f1f;
            background: #fff;
            font-size: 12px;
            line-height: 1.25;
        }

        .actions {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }

        .btn {
            border: 1px solid #777;
            background: #efefef;
            color: #111;
            font-size: 12px;
            border-radius: 3px;
            padding: 5px 12px;
            cursor: pointer;
        }

        .btn-primary {
            background: #1e73e8;
            color: #fff;
            border-color: #1e73e8;
        }

        .btn-muted {
            background: #5f6a72;
            border-color: #5f6a72;
            color: #fff;
        }

        .sheet-title {
            text-align: center;
            font-size: 34px;
            font-weight: 700;
            line-height: 1;
            margin-top: 4px;
            margin-bottom: 0;
        }

        .sub-title {
            text-align: center;
            margin-top: 2px;
            margin-bottom: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .meta {
            text-align: center;
            color: #666;
            margin: 2px 0 8px;
            font-size: 11px;
        }

        .top-line {
            border-top: 3px solid #2f2f2f;
            margin-bottom: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }

        .box {
            border: 1px solid #c8c8c8;
            padding: 6px;
        }

        .box-title {
            font-weight: 700;
            margin-bottom: 6px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid #bdbdbd;
            padding: 3px 5px;
            vertical-align: middle;
        }

        th {
            text-align: left;
            background: #f5f5f5;
            font-weight: 700;
        }

        .muted {
            color: #777;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .green {
            color: #108a2c;
            font-weight: 700;
        }

        .red {
            color: #d92b2b;
            font-weight: 700;
        }

        .section-title {
            margin: 10px 0 6px;
            font-size: 13px;
            font-weight: 700;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .badge-green {
            background: #15aa34;
        }

        .badge-red {
            background: #e04545;
        }

        .badge-blue {
            background: #2c77d9;
        }

        .summary-wrap {
            display: grid;
            grid-template-columns: 1fr 260px;
            gap: 12px;
            margin-top: 10px;
        }

        .calc-table td {
            border: 0;
            border-bottom: 1px solid #d8d8d8;
            padding: 3px 0;
        }

        .calc-table tr:last-child td {
            border-bottom: 0;
        }

        .signature {
            margin-top: 34px;
            display: flex;
            justify-content: space-between;
        }

        .signature>div {
            width: 170px;
            text-align: center;
            font-size: 11px;
            border-top: 1px solid #222;
            padding-top: 3px;
        }

        .no-border td {
            border: 0;
            padding: 2px 0;
        }

        @media print {
            .actions {
                display: none;
            }

            body {
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
@php
    use App\Enums\RoleType;
    use Carbon\Carbon;

    $user = $payroll->user;
    $monthLabel = Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('F Y');
    $generatedAt = now()->format('d M Y h:i A');
    $roleLabel = match ((int) ($user?->role ?? 0)) {
        RoleType::Admin->value => 'Admin',
        RoleType::Manager->value => 'Manager',
        default => 'Employee',
    };
    $offDays = $user ? implode(', ', $user->getOffDaysArray()) : '';

    $totalOvertimeMinutes = $attendances->sum('overtime_minutes');
    $totalLateMinutes = $attendances->sum('late_minutes');
    $totalPenaltyAmount = $attendances->sum('penalty_amount');
    $overtimeUnitMin = max((int) $settings->overtime_unit_minutes, 1);
    $latetimeUnitMin = max((int) $settings->latetime_unit_minutes, 1);

    $formatMoney = fn(float|string $value): string => '৳' . number_format((float) $value, 2);
@endphp

<body>
    <div class="actions">
        <button type="button" class="btn btn-primary" onclick="window.print()">🖨 Print</button>
        <button type="button" class="btn btn-muted" onclick="window.close()">✕ Close</button>
    </div>

    <h1 class="sheet-title">Salary Sheet</h1>
    <h2 class="sub-title">{{ $user?->name }} — {{ $monthLabel }}</h2>
    <p class="meta">Generated: {{ $generatedAt }} | Status: {{ strtoupper($payroll->status) }}</p>
    <div class="top-line"></div>

    <div class="grid">
        <div class="box">
            <div class="box-title">Employee Information</div>
            <table class="no-border">
                <tr>
                    <td style="width:95px"><strong>Name:</strong></td>
                    <td>{{ $user?->name }}</td>
                </tr>
                <tr>
                    <td><strong>Role:</strong></td>
                    <td>{{ $roleLabel }}</td>
                </tr>
                <tr>
                    <td><strong>Monthly Salary:</strong></td>
                    <td>{{ $formatMoney($user?->monthly_salary ?? 0) }}</td>
                </tr>
                <tr>
                    <td><strong>Schedule:</strong></td>
                    <td>{{ $user?->start_time ?? '00:00:00' }} - {{ $user?->end_time ?? '23:59:59' }}</td>
                </tr>
                <tr>
                    <td><strong>Off Days:</strong></td>
                    <td>{{ $offDays ?: 'None' }}</td>
                </tr>
                <tr>
                    <td><strong>Holidays:</strong></td>
                    <td>
                        @if ($holidayRanges->isEmpty())
                            None
                        @else
                            @foreach ($holidayRanges as $holidayRange)
                                <div>
                                    {{ $holidayRange['name'] }}
                                    ({{ $holidayRange['from']->format('d M Y') }} -
                                    {{ $holidayRange['to']->format('d M Y') }})
                                </div>
                            @endforeach
                            <div><strong>Total:</strong> {{ $holidayTotalDays }} days</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="box">
            <div class="box-title">Attendance Summary</div>
            <table class="no-border">
                <tr>
                    <td style="width:95px"><strong>Total Days:</strong></td>
                    <td>{{ $payroll->total_days }} days</td>
                </tr>
                <tr>
                    <td><strong>Present Days:</strong></td>
                    <td>{{ $payroll->present_days }} days</td>
                </tr>
                <tr>
                    <td><strong>Off-day Work:</strong></td>
                    <td>{{ $payroll->off_day_presents }} days</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section-title">Attendance Records</div>
    <table>
        <thead>
            <tr>
                <th>DATE</th>
                <th>DAY</th>
                <th>STATUS</th>
                <th>CHECK IN</th>
                <th>CHECK OUT</th>
                <th>OVER (MIN)</th>
                <th>OVER ৳</th>
                <th>LATE (MIN)</th>
                <th>LATE ৳</th>
                <th>PENALTY</th>
                <th>NOTES</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                @php
                    $overtimeUnits = floor(($attendance->overtime_minutes ?? 0) / $overtimeUnitMin);
                    $overtimeAmount = $overtimeUnits * (float) $settings->overtime_rate;
                    $lateUnits = floor(($attendance->late_minutes ?? 0) / $latetimeUnitMin);
                    $lateAmount = $lateUnits * (float) $settings->latetime_rate;
                @endphp
                <tr>
                    <td>{{ $attendance->date->format('d M') }}</td>
                    <td>{{ $attendance->date->format('D') }}</td>
                    <td>
                        <span class="badge badge-green">{{ ucfirst($attendance->status ?? 'present') }}</span>
                        @if ($attendance->auto_checkout)
                            <span class="badge badge-red">Auto</span>
                        @endif
                        @if ($attendance->is_off_day)
                            <span class="badge badge-blue">Off</span>
                        @endif
                    </td>
                    <td>{{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '-' }}</td>
                    <td>{{ $attendance->check_out ? $attendance->check_out->format('h:i A') : '-' }}</td>
                    <td>{{ $attendance->overtime_minutes ?? 0 }}</td>
                    <td class="green">{{ $overtimeAmount > 0 ? $formatMoney($overtimeAmount) : '-' }}</td>
                    <td>{{ $attendance->late_minutes ?? 0 }}</td>
                    <td class="red">{{ $lateAmount > 0 ? $formatMoney($lateAmount) : '-' }}</td>
                    <td class="red">
                        {{ (float) ($attendance->penalty_amount ?? 0) > 0 ? $formatMoney($attendance->penalty_amount) : '-' }}
                    </td>
                    <td>{{ $attendance->note ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>Total:</strong></td>
                <td><strong>{{ $totalOvertimeMinutes }} min</strong></td>
                <td class="green"><strong>{{ $formatMoney($payroll->overtime_amount) }}</strong></td>
                <td><strong>{{ $totalLateMinutes }} min</strong></td>
                <td class="red"><strong>{{ $formatMoney($payroll->late_deduction) }}</strong></td>
                <td><strong>{{ $formatMoney($totalPenaltyAmount) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @if ($advances->isNotEmpty())
        <div class="section-title">Salary Advances</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Approved By</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($advances as $index => $advance)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $advance->date->format('d M Y') }}</td>
                        <td class="red">{{ $formatMoney($advance->amount) }}</td>
                        <td>{{ $advance->approver?->name ?? '-' }}</td>
                        <td>{{ $advance->note ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right"><strong>Total:</strong></td>
                    <td class="red"><strong>{{ $formatMoney($advances->sum('amount')) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($bonuses->isNotEmpty())
        <div class="section-title">Special Bonuses</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bonuses as $index => $bonus)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $bonus->name }}</td>
                        <td class="green">{{ $formatMoney($bonus->amount) }}</td>
                        <td>{{ $bonus->description ?? '-' }}</td>
                        <td>{{ $bonus->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right"><strong>Total:</strong></td>
                    <td class="green"><strong>{{ $formatMoney($bonuses->sum('amount')) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="summary-wrap">
        <div></div>
        <div>
            <div class="section-title" style="margin-top:0">Salary Calculation</div>
            <table class="calc-table" style="width:100%; font-size:12px;">
                <tr>
                    <td>Base Salary ({{ $payroll->present_days }} days × {{ $formatMoney($payroll->daily_salary) }})
                    </td>
                    <td class="text-right">{{ $formatMoney($payroll->base_salary) }}</td>
                </tr>
                <tr>
                    <td class="green">+ Off-day Bonus</td>
                    <td class="text-right green">{{ $formatMoney($payroll->off_day_bonus) }}</td>
                </tr>
                <tr>
                    <td class="green">+ Overtime Bonus</td>
                    <td class="text-right green">{{ $formatMoney($payroll->overtime_amount) }}</td>
                </tr>
                <tr>
                    <td class="green">+ Hazira Bonus</td>
                    <td class="text-right green">{{ $formatMoney($payroll->hazira_bonus_amount) }}</td>
                </tr>
                <tr>
                    <td class="green">+ Special Bonus</td>
                    <td class="text-right green">{{ $formatMoney($payroll->occasional_bonus_amount) }}</td>
                </tr>
                <tr>
                    <td class="green">+ xSell Bonus</td>
                    <td class="text-right green">{{ $formatMoney($payroll->xsell_bonus_amount) }}</td>
                </tr>
                <tr>
                    <td class="red">− Late Fee Deduction</td>
                    <td class="text-right red">{{ $formatMoney($payroll->late_deduction) }}</td>
                </tr>
                <tr>
                    <td class="red">− Penalty Deduction</td>
                    <td class="text-right red">{{ $formatMoney($payroll->penalty_amount) }}</td>
                </tr>
                <tr>
                    <td class="red">− Advance Deduction</td>
                    <td class="text-right red">{{ $formatMoney($payroll->advance_deduction) }}</td>
                </tr>
                <tr>
                    <td style="font-weight:700; border-top:2px solid #222;">NET SALARY</td>
                    <td class="text-right" style="font-weight:700; border-top:2px solid #222;">
                        {{ $formatMoney($payroll->net_salary) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="signature">
        <div>Employee Signature</div>
        <div>Authorized Signature</div>
    </div>
</body>

</html>

@extends('backEnd.admin.layouts.master')
@section('title', 'My Payroll')
@section('body')
<div class="dashboard-wrapper"><div class="container-fluid dashboard-content"><h2 class="pageheader-title">My Payroll</h2>
<table class="table table-bordered"><thead><tr><th>Month</th><th>Present</th><th>Base</th><th>Off-day Bonus</th><th>Overtime</th><th>Hazira</th><th>Special</th><th>xSell</th><th>Late</th><th>Penalty</th><th>Advance</th><th>Net</th><th>Status</th><th>Action</th></tr></thead><tbody>
@foreach($payrolls as $payroll)
<tr><td>{{ sprintf('%02d',$payroll->month) }}/{{ $payroll->year }}</td><td>{{ $payroll->present_days }}@if($payroll->off_day_presents>0) (OFF: {{ $payroll->off_day_presents }})@endif</td><td>{{ number_format((float)$payroll->base_salary,2) }}</td><td>{{ number_format((float)$payroll->off_day_bonus,2) }}</td><td>{{ number_format((float)$payroll->overtime_amount,2) }}</td><td>{{ number_format((float)$payroll->hazira_bonus_amount,2) }}</td><td>{{ number_format((float)$payroll->occasional_bonus_amount,2) }}</td><td>{{ number_format((float)$payroll->xsell_bonus_amount,2) }}</td><td>{{ number_format((float)$payroll->late_deduction,2) }}</td><td>{{ number_format((float)$payroll->penalty_amount,2) }}</td><td>{{ number_format((float)$payroll->advance_deduction,2) }}</td><td>{{ number_format((float)$payroll->net_salary,2) }}</td><td>{{ $payroll->status }}</td><td><a href="{{ route('admin.my_payroll.show',$payroll) }}" class="btn btn-sm btn-primary">View</a></td></tr>
@endforeach
</tbody></table>{{ $payrolls->links() }}</div></div>
@endsection

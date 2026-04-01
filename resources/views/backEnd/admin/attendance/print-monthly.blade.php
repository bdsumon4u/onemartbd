<!doctype html><html><head><meta charset="utf-8"><title>Monthly Attendance</title><style>body{font-family:Arial}table{width:100%;border-collapse:collapse}th,td{border:1px solid #000;padding:6px;text-align:left}</style></head><body onload="window.print()">
<h2>Monthly Attendance - {{ $user->name }} ({{ sprintf('%02d',$month) }}/{{ $year }})</h2>
<table><thead><tr><th>Date</th><th>Status</th><th>In</th><th>Out</th><th>OT</th><th>Extra OT</th><th>Late</th><th>Penalty</th></tr></thead><tbody>
@foreach($attendances as $attendance)
<tr><td>{{ $attendance->date->format('Y-m-d') }}</td><td>{{ $attendance->status }}</td><td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}</td><td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}</td><td>{{ $attendance->overtime_minutes }}</td><td>{{ $attendance->extra_overtime_minutes }}</td><td>{{ $attendance->late_minutes }}</td><td>{{ number_format((float)$attendance->penalty_amount,2) }}</td></tr>
@endforeach
</tbody></table>
<h4>Summary</h4>
<ul>
<li>Present: {{ $summary['present_days'] }}</li><li>Absent: {{ $summary['absent_days'] }}</li><li>Off-day Work: {{ $summary['off_day_work'] }}</li><li>Overtime Minutes: {{ $summary['overtime_minutes'] }}</li><li>Extra Overtime Minutes: {{ $summary['extra_overtime_minutes'] }}</li><li>Late Minutes: {{ $summary['late_minutes'] }}</li><li>Penalties: {{ number_format($summary['penalties'],2) }}</li>
</ul>
</body></html>

<!doctype html><html><head><meta charset="utf-8"><title>Daily Attendance</title><style>body{font-family:Arial}table{width:100%;border-collapse:collapse}th,td{border:1px solid #000;padding:6px;text-align:left}</style></head><body onload="window.print()">
<h2>Daily Attendance Sheet - {{ $date }}</h2>
<table><thead><tr><th>Name</th><th>Status</th><th>In</th><th>Out</th><th>OT</th><th>Late</th><th>Penalty</th></tr></thead><tbody>
@foreach($users as $user)
@php($row = $attendances[$user->id] ?? null)
<tr><td>{{ $user->name }}</td><td>{{ $row?->status ?? 'absent' }}</td><td>{{ $row?->check_in ? \Carbon\Carbon::parse($row->check_in)->format('h:i A') : '-' }}</td><td>{{ $row?->check_out ? \Carbon\Carbon::parse($row->check_out)->format('h:i A') : '-' }}</td><td>{{ $row?->overtime_minutes ?? 0 }}</td><td>{{ $row?->late_minutes ?? 0 }}</td><td>{{ number_format((float)($row?->penalty_amount ?? 0),2) }}</td></tr>
@endforeach
</tbody></table></body></html>

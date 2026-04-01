@extends('backEnd.employee.layouts.master')
@section('title', 'My Attendance')
@section('body')
<div class="dashboard-wrapper"><div class="container-fluid dashboard-content"><h2 class="pageheader-title">My Attendance</h2>
<button class="btn btn-primary mb-2" id="attendanceToggle">Toggle Check In/Out</button>
<div id="attendanceStatus" class="mb-2"></div>
<table class="table table-bordered"><thead><tr><th>Date</th><th>Status</th><th>In</th><th>Out</th><th>OT</th><th>Late</th></tr></thead><tbody>@foreach($attendances as $a)<tr><td>{{ $a->date->format('Y-m-d') }}</td><td>{{ $a->status }}</td><td>{{ $a->check_in ? \Carbon\Carbon::parse($a->check_in)->format('h:i A') : '-' }}</td><td>{{ $a->check_out ? \Carbon\Carbon::parse($a->check_out)->format('h:i A') : '-' }}</td><td>{{ $a->overtime_minutes + $a->extra_overtime_minutes }}</td><td>{{ $a->late_minutes }}</td></tr>@endforeach</tbody></table>
{{ $attendances->links() }}
</div></div>
@endsection
@section('js')<script>(function(){const s=document.getElementById('attendanceStatus');function r(){fetch("{{ route('employee.my_attendance.status') }}").then(x=>x.json()).then(d=>s.innerHTML=`Checked In: ${d.is_checked_in?'Yes':'No'} | Checked Out: ${d.is_checked_out?'Yes':'No'} | In: ${d.check_in??'-'} | Out: ${d.check_out??'-'}`)}document.getElementById('attendanceToggle').addEventListener('click',()=>{fetch("{{ route('employee.my_attendance.toggle') }}",{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>r())});r();})();</script>@endsection

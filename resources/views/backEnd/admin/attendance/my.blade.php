@extends('backEnd.admin.layouts.master')
@section('title', 'My Attendance')
@section('body')
<div class="dashboard-wrapper"><div class="container-fluid dashboard-content">
    <div class="page-header"><h2 class="pageheader-title">My Attendance</h2></div>
    <button class="btn btn-primary mb-3" id="attendanceToggle">Toggle Check In/Out</button>
    <div id="attendanceStatus" class="mb-3"></div>
    <div class="card"><div class="card-body">
    <form method="GET" class="form-inline mb-2"><input type="number" name="month" min="1" max="12" class="form-control mr-2" value="{{ $month }}"><input type="number" name="year" min="2020" max="2100" class="form-control mr-2" value="{{ $year }}"><button class="btn btn-secondary">Filter</button></form>
    <table class="table table-bordered"><thead><tr><th>Date</th><th>Status</th><th>In</th><th>Out</th><th>OT</th><th>Late</th></tr></thead><tbody>@foreach($attendances as $a)<tr><td>{{ $a->date->format('Y-m-d') }}</td><td>{{ $a->status }}</td><td>{{ $a->check_in ? \Carbon\Carbon::parse($a->check_in)->format('h:i A') : '-' }}</td><td>{{ $a->check_out ? \Carbon\Carbon::parse($a->check_out)->format('h:i A') : '-' }}</td><td>{{ $a->overtime_minutes + $a->extra_overtime_minutes }}</td><td>{{ $a->late_minutes }}</td></tr>@endforeach</tbody></table>
    {{ $attendances->links() }}
    </div></div>
</div></div>
@endsection
@section('js')
<script>
(function(){
const statusEl=document.getElementById('attendanceStatus');
function refresh(){fetch("{{ route('admin.my_attendance.status') }}").then(r=>r.json()).then(d=>{statusEl.innerHTML=`Checked In: ${d.is_checked_in?'Yes':'No'} | Checked Out: ${d.is_checked_out?'Yes':'No'} | In: ${d.check_in??'-'} | Out: ${d.check_out??'-'} | Self Checkout: ${d.allow_self_checkout?'Enabled':'Disabled'}`;});}
document.getElementById('attendanceToggle').addEventListener('click',()=>{fetch("{{ route('admin.my_attendance.toggle') }}",{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(r=>r.json()).then(()=>refresh());});
refresh();
})();
</script>
@endsection

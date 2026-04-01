@extends('backEnd.manager.layouts.master')
@section('title', 'My Advances')
@section('body')
<div class="dashboard-wrapper"><div class="container-fluid dashboard-content"><h2 class="pageheader-title">My Salary Advances</h2>
<table class="table table-bordered"><thead><tr><th>Date</th><th>Amount</th><th>Note</th></tr></thead><tbody>@foreach($advances as $a)<tr><td>{{ $a->date->format('Y-m-d') }}</td><td>{{ number_format((float)$a->amount,2) }}</td><td>{{ $a->note }}</td></tr>@endforeach</tbody></table>{{ $advances->links() }}
</div></div>
@endsection

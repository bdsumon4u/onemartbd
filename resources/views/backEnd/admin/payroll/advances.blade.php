@extends('backEnd.admin.layouts.master')
@section('title', 'Salary Advances')
@section('body')
    <div class="dashboard-wrapper">
        <div class="container-fluid dashboard-content">
            <div class="page-header">
                <h2 class="pageheader-title">Salary Advances</h2>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="form-inline mb-2"><select name="user_id" class="form-control mr-2">
                            <option value="">All Users</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="month" class="form-control mr-2">
                            <option value="">All Months</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @selected((int) request('month') === $m)>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endfor
                        </select>
                        <input type="number" name="year" class="form-control mr-2" placeholder="Year"
                            value="{{ request('year') }}"><button class="btn btn-secondary">Filter</button>
                    </form>
                    <form method="POST" action="{{ route('admin.salary_advances.store') }}" class="mb-3">@csrf<div
                            class="form-row align-items-center">
                            <div class="col"><select name="user_id" class="form-control">
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col"><input name="amount" class="form-control" placeholder="Amount"></div>
                            <div class="col"><input type="date" name="date" class="form-control"
                                    value="{{ old('date', now()->toDateString()) }}"></div>
                            <div class="col"><input name="note" class="form-control" placeholder="Note"></div>
                            <div class="col"><button class="btn btn-primary btn-block">Add</button></div>
                        </div>
                    </form>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($advances as $a)
                                <tr>
                                    <td>{{ $a->user?->name }}</td>
                                    <td>{{ number_format((float) $a->amount, 2) }}</td>
                                    <td>{{ $a->date->format('Y-m-d') }}</td>
                                    <td>{{ $a->note }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.salary_advances.delete', $a) }}">
                                            @csrf<button class="btn btn-sm btn-danger">Delete</button></form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>{{ $advances->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

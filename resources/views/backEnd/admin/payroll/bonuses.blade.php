@extends('backEnd.admin.layouts.master')
@section('title', 'User Bonuses')
@section('body')
    <div class="dashboard-wrapper">
        <div class="container-fluid dashboard-content">
            <div class="page-header">
                <h2 class="pageheader-title">User Bonuses</h2>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.user_bonuses.store') }}" class="mb-3">@csrf<div
                            class="form-row align-items-center">
                            <div class="col"><select class="form-control" name="user_id">
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col"><input class="form-control" name="name" placeholder="Name"></div>
                            <div class="col"><input class="form-control" name="amount" placeholder="Amount"></div>
                            <div class="col"><select class="form-control" name="month">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" @selected((int) old('month', now()->month) === $m)>
                                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col"><input class="form-control" name="year" placeholder="YYYY"
                                    value="{{ old('year', now()->format('Y')) }}"></div>
                            <div class="col"><button class="btn btn-primary btn-block">Add</button></div>
                        </div>
                    </form>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bonuses as $b)
                                <tr>
                                    <td>{{ $b->user?->name }}</td>
                                    <td>{{ $b->name }}</td>
                                    <td>{{ number_format((float) $b->amount, 2) }}</td>
                                    <td>{{ $b->month }}</td>
                                    <td>{{ $b->year }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.user_bonuses.delete', $b) }}">
                                            @csrf<button class="btn btn-sm btn-danger">Delete</button></form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>{{ $bonuses->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

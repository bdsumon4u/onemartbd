@extends('backEnd.admin.layouts.master')
@section('title', 'Holidays')
@section('body')
    <div class="dashboard-wrapper">
        <div class="container-fluid dashboard-content">
            <div class="page-header">
                <h2 class="pageheader-title">Holidays</h2>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="form-inline mb-2"><select name="month" class="form-control mr-2">
                            <option value="">All Months</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @selected(($selectedMonth ?? 0) === $m)>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endfor
                        </select>
                        <input type="number" name="year" class="form-control mr-2" placeholder="Year"
                            value="{{ $selectedYear ?? now()->year }}"><button class="btn btn-secondary">Filter</button>
                    </form>
                    <form method="POST" action="{{ route('admin.holidays.store') }}" class="mb-3">@csrf<div
                            class="form-row align-items-center">
                            <div class="col"><input name="name" class="form-control"
                                    placeholder="Holiday Name (e.g. Eid Vacation)" required></div>
                            <div class="col"><input type="date" name="from_date" class="form-control" required></div>
                            <div class="col"><input type="date" name="to_date" class="form-control" required></div>
                            <div class="col"><input name="note" class="form-control" placeholder="Note (optional)">
                            </div>
                            <div class="col"><button class="btn btn-primary btn-block">Add</button></div>
                        </div>
                    </form>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Days</th>
                                <th>Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($holidays as $holiday)
                                <tr>
                                    <form method="POST" action="{{ route('admin.holidays.update', $holiday) }}">@csrf<td>
                                            <input type="text" name="name" class="form-control form-control-sm"
                                                value="{{ $holiday->name }}" required></td>
                                        <td><input type="date" name="from_date" class="form-control form-control-sm"
                                                value="{{ $holiday->from_date->format('Y-m-d') }}" required></td>
                                        <td><input type="date" name="to_date" class="form-control form-control-sm"
                                                value="{{ $holiday->to_date->format('Y-m-d') }}" required></td>
                                        <td>{{ $holiday->from_date->diffInDays($holiday->to_date) + 1 }}</td>
                                        <td><input type="text" name="note" class="form-control form-control-sm"
                                                value="{{ $holiday->note }}" placeholder="-"></td>
                                        <td class="text-nowrap"><button class="btn btn-sm btn-primary mr-1">Update</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.holidays.delete', $holiday) }}"
                                        class="d-inline">@csrf<button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this holiday?')">Delete</button></form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>{{ $holidays->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

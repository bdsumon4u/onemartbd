@extends('backEnd.admin.layouts.master')
@section('title', 'Payroll Settings')
@section('body')
<div class="dashboard-wrapper"><div class="container-fluid dashboard-content"><div class="page-header"><h2 class="pageheader-title">Payroll Settings</h2></div>
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('admin.payroll.settings.update') }}">@csrf
<div class="row">
<div class="col-md-3 form-group"><label>Overtime Rate</label><input class="form-control" name="overtime_rate" value="{{ $settings->overtime_rate }}"></div>
<div class="col-md-3 form-group"><label>Overtime Unit Minutes</label><input class="form-control" name="overtime_unit_minutes" value="{{ $settings->overtime_unit_minutes }}"></div>
<div class="col-md-3 form-group"><label>Late Rate</label><input class="form-control" name="latetime_rate" value="{{ $settings->latetime_rate }}"></div>
<div class="col-md-3 form-group"><label>Late Unit Minutes</label><input class="form-control" name="latetime_unit_minutes" value="{{ $settings->latetime_unit_minutes }}"></div>
<div class="col-md-3 form-group"><label>Forgot Checkout Penalty</label><input class="form-control" name="forgot_checkout_penalty" value="{{ $settings->forgot_checkout_penalty }}"></div>
<div class="col-md-3 form-group"><label>Hazira Bonus</label><input class="form-control" name="hazira_bonus" value="{{ $settings->hazira_bonus }}"></div>
<div class="col-md-3 form-group"><label>xSell Bonus Rate</label><input class="form-control" name="xsell_bonus_rate" value="{{ $settings->xsell_bonus_rate }}"></div>
<div class="col-md-3 form-group"><label>Allow Self Checkout</label><select class="form-control" name="allow_self_checkout"><option value="1" @selected($settings->allow_self_checkout)>Yes</option><option value="0" @selected(!$settings->allow_self_checkout)>No</option></select></div>
</div>
<div class="alert alert-info">Overtime and late fee use separate rate/unit. Forgot-checkout penalty is applied by auto-checkout. Hazira bonus requires zero absence and zero late minutes. xSell bonus applies per qualified delivered order.</div>
<button class="btn btn-primary">Update Settings</button>
</form></div></div></div></div>
@endsection

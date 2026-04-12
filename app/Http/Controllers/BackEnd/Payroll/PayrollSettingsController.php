<?php

namespace App\Http\Controllers\BackEnd\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollSettingsController extends Controller
{
    public function index(): View
    {
        $settings = PayrollSetting::current();

        return view('backEnd.admin.payroll.settings', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'overtime_rate' => ['required', 'numeric', 'min:0'],
            'overtime_unit_minutes' => ['required', 'integer', 'min:1'],
            'latetime_rate' => ['required', 'numeric', 'min:0'],
            'latetime_unit_minutes' => ['required', 'integer', 'min:1'],
            'off_day_salary_boost' => ['required', 'numeric', 'between:1,3'],
            'forgot_checkout_penalty' => ['required', 'numeric', 'min:0'],
            'hazira_bonus' => ['required', 'numeric', 'min:0'],
            'xsell_bonus_rate' => ['required', 'numeric', 'min:0'],
            'allow_self_checkout' => ['required', 'boolean'],
        ]);

        PayrollSetting::current()->update($validated);

        return back()->with('success', 'Payroll settings updated successfully.');
    }
}

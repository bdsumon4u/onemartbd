<?php

namespace App\Http\Controllers\BackEnd\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HolidayController extends Controller
{
    public function index(Request $request): View
    {
        $query = Holiday::query()->latest('from_date');

        $selectedMonth = (int) $request->input('month', 0);
        $selectedYear = (int) $request->input('year', now()->year);

        if ($selectedMonth >= 1 && $selectedMonth <= 12) {
            $monthStart = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth()->toDateString();
            $monthEnd = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth()->toDateString();

            $query
                ->whereDate('from_date', '<=', $monthEnd)
                ->whereDate('to_date', '>=', $monthStart);
        }

        $holidays = $query->paginate(20)->withQueryString();

        return view('backEnd.admin.payroll.holidays', compact('holidays', 'selectedMonth', 'selectedYear'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        Holiday::query()->create($validated);

        return back()->with('success', 'Holiday added successfully.');
    }

    public function update(Request $request, Holiday $holiday): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $holiday->update($validated);

        return back()->with('success', 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return back()->with('success', 'Holiday deleted successfully.');
    }
}

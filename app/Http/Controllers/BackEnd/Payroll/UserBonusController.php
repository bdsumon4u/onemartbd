<?php

namespace App\Http\Controllers\BackEnd\Payroll;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserBonus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserBonusController extends Controller
{
    public function index(Request $request): View
    {
        $query = UserBonus::query()->with('user')->latest('id');

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }

        if ($request->filled('month')) {
            $query->where('month', str_pad((string) $request->month, 2, '0', STR_PAD_LEFT));
        }

        if ($request->filled('year')) {
            $query->where('year', (int) $request->year);
        }

        $bonuses = $query->paginate(25)->withQueryString();
        $users = User::query()->whereIn('role', [1, 2, 3])->where('status', 1)->orderBy('name')->get();

        return view('backEnd.admin.payroll.bonuses', compact('bonuses', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'year' => ['required', 'integer', 'between:2020,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'notes' => ['nullable', 'string'],
        ]);

        UserBonus::query()->create([
            ...$validated,
            'month' => str_pad((string) $validated['month'], 2, '0', STR_PAD_LEFT),
        ]);

        return back()->with('success', 'User bonus created.');
    }

    public function update(Request $request, UserBonus $userBonus): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'year' => ['required', 'integer', 'between:2020,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'notes' => ['nullable', 'string'],
        ]);

        $userBonus->update([
            ...$validated,
            'month' => str_pad((string) $validated['month'], 2, '0', STR_PAD_LEFT),
        ]);

        return back()->with('success', 'User bonus updated.');
    }

    public function destroy(UserBonus $userBonus): RedirectResponse
    {
        $userBonus->delete();

        return back()->with('success', 'User bonus deleted.');
    }
}

<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function change_pass()
    {
        return view('backEnd.admin.change_pass');
    }

    public function update_pass(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $user_id = Auth::guard('admin')->id();
            if (Hash::check($request->old_pass, Admin::find($user_id)->password)) {
                Admin::find($user_id)->update([
                    'password' => Hash::make($request->password),
                ]);

                $this->guard_admin()->logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return $this->loggedOut($request) ?: to_route('admin.home')->with('success', 'Password Changed Successfully');
            } else {
                return back()->with('error', 'Incorrect Old Password');
            }
        } elseif (Auth::guard('manager')->check()) {
            $user_id = Auth::guard('manager')->id();
            if (Hash::check($request->old_pass, Manager::find($user_id)->password)) {
                Manager::find($user_id)->update([
                    'password' => Hash::make($request->password),
                ]);

                $this->guard_manager()->logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return $this->loggedOut($request) ?: to_route('manager.home')->with('success', 'Password Changed Successfully');
            } else {
                return back()->with('error', 'Incorrect Old Password');
            }
        } elseif (Auth::guard('employee')->check()) {
            $user_id = Auth::guard('employee')->id();
            if (Hash::check($request->old_pass, Employee::find($user_id)->password)) {
                Employee::find($user_id)->update([
                    'password' => Hash::make($request->password),
                ]);

                $this->guard_employee()->logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return $this->loggedOut($request) ?: to_route('employee.home')->with('success', 'Password Changed Successfully');
            } else {
                return back()->with('error', 'Incorrect Old Password');
            }
        } else {
            return back()->with('warning', 'Something Went Wrong!');
        }
    }

    protected function loggedOut(Request $request) {}

    protected function guard_admin()
    {
        return Auth::guard('admin');
    }

    protected function guard_manager()
    {
        return Auth::guard('manager');
    }

    protected function guard_employee()
    {
        return Auth::guard('employee');
    }
}

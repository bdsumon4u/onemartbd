<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::ADMIN_HOME;

    public function showLoginForm()
    {
        return view('backEnd.admin.auth.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email','password');
        if (Auth::guard('admin')->attempt($credentials)){
            return redirect()->intended(route('admin.home'));
        }else{
            return redirect()->route('admin.login')->with('error','Please Enter Correct Email/Password');
        }

    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/admin-login');
    }


    public function __construct()
    {
        $this->middleware('admin.guest')->except('logout');
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }
}

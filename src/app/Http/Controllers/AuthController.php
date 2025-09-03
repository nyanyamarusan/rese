<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\AdminOrOwnerLoginRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        event(new Registered($user));
        return view('auth.thanks');
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate();

        return redirect(route('index'));
    }

    public function adminLoginView()
    {
        return view('auth.admin-login');
    }

    public function ownerLoginView()
    {
        return view('auth.owner-login');
    }

    public function adminLogin(AdminOrOwnerLoginRequest $request)
    {
        $request->authenticate();

        return redirect(route('admin-index'));
    }

    public function ownerLogin(AdminOrOwnerLoginRequest $request)
    {
        $request->authenticate();

        return redirect(route('owner-index'));
    }

    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
            $redirect = 'admin-login-view';
        }
        elseif (Auth::guard('owner')->check()) {
            Auth::guard('owner')->logout();
            $redirect = 'owner-login-view';
        }
        else {
            Auth::logout();
            $redirect = 'login';
        }

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect(route($redirect));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $isEmailValid = hash_equals(config('admin.email'), $validated['email']);
        $isPasswordValid = hash_equals(config('admin.password'), $validated['password']);

        if (! $isEmailValid || ! $isPasswordValid) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password admin salah.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        session([
            'admin_logged_in' => true,
            'admin_email' => $validated['email'],
        ]);

        return redirect()->intended(
            route('admin.dashboard')
        );
    }

    public function logout(Request $request)
    {
        $request->session()->forget([
            'admin_logged_in',
            'admin_email',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
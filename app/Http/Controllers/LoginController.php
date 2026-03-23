<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::user()) {
            return redirect()->route('admin.dashboard');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Roles allowed into the admin panel
            $bohRoles   = ['BOH-IT', 'BOH-Marketing', 'BOH-Sales', 'BOH-Support'];
            $adminRoles = ['Super-Admin', 'Admin', ...$bohRoles];

            // Block roles that have no business in the admin panel
            if (! $user->hasAnyRole($adminRoles)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'You do not have permission to access this area.',
                ])->withInput();
            }

            // BOH roles → BoH dashboard
            if ($user->hasAnyRole($bohRoles)) {
                return redirect()->route('admin.boh.dashboard');
            }

            // Super-Admin / Admin → main dashboard
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}


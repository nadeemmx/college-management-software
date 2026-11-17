<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'status' => 'Active',
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'LOGIN',
                'module' => 'Auth',
                'description' => 'User signed in: ' . Auth::user()->email,
                'ip_address' => $request->ip(),
            ]);

            return redirect()->intended($this->homeRouteForRole(Auth::user()));
        }

        return back()
            ->withErrors(['email' => 'Invalid email, password, or inactive account.'])
            ->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'LOGOUT',
                'module' => 'Auth',
                'description' => 'User signed out: ' . Auth::user()->email,
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been signed out securely.');
    }

    protected function homeRouteForRole(User $user): string
    {
        return match ($user->role?->slug) {
            'student' => route('portal.student'),
            'faculty' => route('portal.faculty'),
            'parent' => route('portal.parent'),
            default => route('dashboard'),
        };
    }

    protected function redirectByRole(User $user): RedirectResponse
    {
        return redirect()->to($this->homeRouteForRole($user));
    }
}

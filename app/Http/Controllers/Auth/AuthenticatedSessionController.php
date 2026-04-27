<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
   public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (!$request->user()->is_active) {
            auth()->logout();
            return back()->with('error', 'Your account has been deactivated. Please contact administrator.');
        }

        $user = $request->user();
        $role = strtoupper($user->role);

        // 1. Admins go to the main dashboard
        if ($role === 'ADMIN') {
            return redirect()->route('admin.dashboard');
        } 
        // 2. Marketers go strictly to their Leads page
        elseif ($role === 'MARKETER') {
        return redirect()->route('marketer.dashboard'); // <--- ADDED THIS    
    }

        // 3. Agents go to the agent dashboard
        return redirect()->route('agent.dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

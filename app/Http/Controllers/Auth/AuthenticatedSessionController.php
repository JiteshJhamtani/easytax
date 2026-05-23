<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
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

        if (! $request->user()->is_active) {
            auth()->logout();

            return back()->with('error', 'Your account has been deactivated. Please contact administrator.');
        }

        $user = $request->user();
        $role = strtoupper($user->role);

        // 1. Admins go to the main dashboard
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        // 2. Marketers go strictly to their Leads page
        elseif ($role === 'MARKETER') {
            return redirect()->route('marketer.dashboard');
        } elseif ($role === 'TEAM') {
            return redirect()->route('team.dashboard');
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

    public function crossServerLogin(Request $request)
    {
        if (! $request->has('token')) {
            return redirect()->route('login')->with('error', 'No authentication token provided.');
        }

        try {
            // 1. Decrypt the token using the shared secret
            $encrypter = new \Illuminate\Encryption\Encrypter(env('CROSS_SERVER_SECRET'), config('app.cipher'));
            $decrypted = $encrypter->decryptString($request->token);
            $payload = json_decode($decrypted, true);

            // 2. Security Check: Has the 60-second link expired?
            if (now()->timestamp > $payload['expires_at']) {
                return redirect()->route('login')->with('error', 'Login link expired for security. Please try again.');
            }

            // 3. Find the matching Admin user on THIS server
            $user = User::where('email', $payload['email'])
                ->whereIn('role', ['SUPER_ADMIN', 'ADMIN']) // Ensure only admins can do this
                ->first();

            if (! $user) {
                return redirect()->route('login')->with('error', 'Admin account not found on this server.');
            }

            // 4. Log the user in and bounce them straight to the dashboard!
            Auth::login($user);

            return redirect()->route('admin.dashboard')->with('success', 'Successfully switched portals!');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Invalid security token.');
        }
    }
}

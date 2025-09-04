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

        // Check if user is active
        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'You are not active. Please ask HR manager to activate your profile.');
        }

        // Check if user needs OTP verification
        if (!$user->email_verified_at) {
            // Generate and send OTP
            $otp = rand(100000, 999999);
            session(['otp' => $otp, 'otp_expiration' => now()->addMinutes(5)]);
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($otp));
            
            return redirect()->route('auth.otp.verify');
        }
        
        // Auto-start work session on login
        $this->autoStartWorkSession($user);
        
        return redirect()->intended(route('attendance.index', absolute: false));
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
    
    private function autoStartWorkSession($user)
    {
        // Check if user already has an active session today
        $activeSession = \App\Models\WorkSession::where('user_id', $user->id)
            ->whereNull('end_time')
            ->first();
            
        if (!$activeSession) {
            // Create new work session
            $session = \App\Models\WorkSession::create([
                'user_id' => $user->id,
                'start_time' => now()->setTimezone('Asia/Kolkata'),
                'end_reason' => ''
            ]);
            
            // Log the activity
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'timestamp' => now()->setTimezone('Asia/Kolkata'),
                'action' => 'Auto-Start',
                'description' => 'Work session auto-started on login'
            ]);
        }
    }
}

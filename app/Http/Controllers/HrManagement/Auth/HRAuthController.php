<?php

namespace App\Http\Controllers\HrManagement\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HR;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\HrOtpVerificationMail;

class HRAuthController extends Controller
{
    // Show dual login form
    public function showLoginForm() {
        return view('hr-management.auth.login');
    }

    // Handle dual login (password or OTP)
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'login_type' => 'required|in:password,otp'
        ]);

        $hr = HR::where('email', $request->email)->first();

        if (!$hr) {
            return back()->withErrors(['email' => 'No HR account found with this email.']);
        }

        if ($request->login_type === 'password') {
            $request->validate(['password' => 'required']);
            
            if (Hash::check($request->password, $hr->password)) {
                Auth::guard('hr')->login($hr, $request->boolean('remember'));
                $request->session()->regenerate();
                
                if (!$hr->email_verified_at) {
                    return redirect()->route('hr.otp.verify');
                }
                
                return redirect()->intended(route('hr.dashboard'));
            } else {
                return back()->withErrors(['password' => 'Invalid password.']);
            }
        } else {
            // OTP login
            $otp = rand(100000, 999999);
            session([
                'hr_login_otp' => $otp,
                'hr_login_otp_expiration' => now()->addMinutes(5),
                'hr_login_email' => $request->email
            ]);
            
            Mail::to($hr->email)->send(new HrOtpVerificationMail($otp));
            
            return redirect()->route('hr.login.otp.verify')->with('status', 'OTP sent to your email!');
        }
    }

    // Show login OTP verification
    public function showLoginOtpVerify() {
        return view('hr-management.auth.login-otp-verify');
    }

    // Verify login OTP
    public function verifyLoginOtp(Request $request) {
        $request->validate(['otp' => 'required|digits:6']);

        if ($request->otp == session('hr_login_otp') && 
            now()->lessThan(session('hr_login_otp_expiration')) &&
            session('hr_login_email')) {
            
            $hr = HR::where('email', session('hr_login_email'))->first();
            
            if ($hr) {
                Auth::guard('hr')->login($hr, $request->boolean('remember'));
                $request->session()->regenerate();
                
                // Clear OTP session data
                session()->forget(['hr_login_otp', 'hr_login_otp_expiration', 'hr_login_email']);
                
                if (!$hr->email_verified_at) {
                    return redirect()->route('hr.otp.verify');
                }
                
                return redirect()->intended(route('hr.dashboard'));
            }
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    // Show registration form
    public function showRegisterForm() {
        return view('hr-management.auth.register');
    }

    // Handle registration with OTP
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:hrs,email',
            'mobile' => 'required|string|max:15',
            'password' => 'required|min:6|confirmed',
        ]);

        // Generate employee ID
        $nameString = strtolower(preg_replace('/\s+/', '', $request->name));
        $shortName = substr($nameString, 0, 5);
        $count = HR::where('employee_id', 'like', 'HR-' . $shortName . '%')->count() + 1;
        $paddedNumber = str_pad($count, 3, '0', STR_PAD_LEFT);
        $employeeId = 'HR-' . $shortName . $paddedNumber;

        while (HR::where('employee_id', $employeeId)->exists()) {
            $count++;
            $paddedNumber = str_pad($count, 3, '0', STR_PAD_LEFT);
            $employeeId = 'HR-' . $shortName . $paddedNumber;
        }

        $hr = HR::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'employee_id' => $employeeId,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('hr')->login($hr);

        // Send OTP for email verification
        $otp = rand(100000, 999999);
        session([
            'hr_otp' => $otp,
            'hr_otp_expiration' => now()->addMinutes(5)
        ]);
        
        Mail::to($hr->email)->send(new HrOtpVerificationMail($otp));

        return redirect()->route('hr.otp.verify');
    }

    // Show OTP verification form
    public function showOtpVerify() {
        return view('hr-management.auth.verify-otp');
    }

    // Verify registration OTP
    public function verifyOtp(Request $request) {
        $request->validate(['otp' => 'required|digits:6']);

        if ($request->otp == session('hr_otp') && now()->lessThan(session('hr_otp_expiration'))) {
            $hr = Auth::guard('hr')->user();
            $hr->email_verified_at = now();
            $hr->save();

            session()->forget(['hr_otp', 'hr_otp_expiration']);

            return redirect()->route('hr.dashboard')->with('success', 'Email verified successfully!');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    // Resend OTP
    public function resendOtp(Request $request) {
        $hr = Auth::guard('hr')->user();
        if (!$hr) {
            return redirect()->route('hr.login')->withErrors(['error' => 'You need to be logged in to resend OTP']);
        }

        $otp = rand(100000, 999999);
        session([
            'hr_otp' => $otp,
            'hr_otp_expiration' => now()->addMinutes(5)
        ]);

        Mail::to($hr->email)->send(new HrOtpVerificationMail($otp));

        return back()->with('status', 'A new OTP has been sent to your email!');
    }

    // Logout
    public function logout(Request $request) {
        Auth::guard('hr')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('hr.login');
    }

    // Dashboard
    public function dashboard() {
        return view('hr-management.dashboard');
    }
}

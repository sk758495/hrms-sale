<?php

namespace App\Http\Controllers\API\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HR;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\HrOtpVerificationMail;
use Illuminate\Support\Facades\Auth;

class HRAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:hrs,email',
            'mobile' => 'required|string|max:15',
            'password' => 'required|min:6|confirmed',
        ]);

        $shortName = substr(strtolower(preg_replace('/\s+/', '', $request->name)), 0, 5);
        $count = HR::where('employee_id', 'like', "HR-$shortName%")->count() + 1;
        $employeeId = 'HR-' . $shortName . str_pad($count, 3, '0', STR_PAD_LEFT);

        while (HR::where('employee_id', $employeeId)->exists()) {
            $employeeId = 'HR-' . $shortName . str_pad(++$count, 3, '0', STR_PAD_LEFT);
        }

        $hr = HR::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'employee_id' => $employeeId,
            'password' => Hash::make($request->password),
        ]);

        $otp = rand(100000, 999999);
        $hr->otp = $otp;
        $hr->otp_expires_at = now()->addMinutes(5);
        $hr->save();

        Mail::to($hr->email)->send(new HrOtpVerificationMail($otp));

        return response()->json(['message' => 'Registered. OTP sent to email.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $hr = HR::where('email', $request->email)->first();
        if (!$hr || $hr->otp != $request->otp || now()->gt($hr->otp_expires_at)) {
            return response()->json(['error' => 'Invalid or expired OTP.'], 422);
        }

        $hr->email_verified_at = now();
        $hr->otp = null;
        $hr->otp_expires_at = null;
        $hr->save();

        return response()->json(['message' => 'Email verified successfully.']);
    }

    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $hr = HR::where('email', $request->email)->first();
        if (!$hr) return response()->json(['error' => 'HR not found.'], 404);

        $otp = rand(100000, 999999);
        $hr->otp = $otp;
        $hr->otp_expires_at = now()->addMinutes(5);
        $hr->save();

        Mail::to($hr->email)->send(new HrOtpVerificationMail($otp));

        return response()->json(['message' => 'OTP resent to email.']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'login_type' => 'required|in:password,otp'
        ]);

        $hr = HR::where('email', $request->email)->first();
        if (!$hr) return response()->json(['error' => 'No HR found with this email.'], 404);

        if ($request->login_type === 'password') {
            $request->validate(['password' => 'required']);
            if (!Hash::check($request->password, $hr->password)) {
                return response()->json(['error' => 'Invalid password.'], 401);
            }

            if (!$hr->hasVerifiedEmail()) {
                return response()->json(['error' => 'Email not verified.'], 403);
            }

            $token = $hr->createToken('hr_token')->plainTextToken;
            return response()->json(['token' => $token, 'user' => $hr]);
        }

        // Login with OTP
        $otp = rand(100000, 999999);
        $hr->otp = $otp;
        $hr->otp_expires_at = now()->addMinutes(5);
        $hr->save();

        Mail::to($hr->email)->send(new HrOtpVerificationMail($otp));
        return response()->json(['message' => 'OTP sent for login.']);
    }

    public function verifyLoginOtp(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required|digits:6']);

        $hr = HR::where('email', $request->email)->first();
        if (!$hr || $hr->otp !== $request->otp || now()->gt($hr->otp_expires_at)) {
            return response()->json(['error' => 'Invalid or expired OTP.'], 422);
        }

        if (!$hr->hasVerifiedEmail()) {
            return response()->json(['error' => 'Email not verified.'], 403);
        }

        $hr->otp = null;
        $hr->otp_expires_at = null;
        $hr->save();

        $token = $hr->createToken('hr_token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $hr]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function dashboard(Request $request)
    {
        return response()->json([
            'message' => 'Welcome to HR Dashboard',
            'user' => $request->user()
        ]);
    }
}

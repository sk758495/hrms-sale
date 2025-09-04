<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OtpVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required|digits:6']);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp !== $request->otp || now()->gt($user->otp_expires_at)) {
            return response()->json(['error' => 'Invalid or expired OTP'], 422);
        }

        $user->email_verified_at = now();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Email verified successfully.']);
    }

    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['error' => 'User not found'], 404);

        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($otp));

        return response()->json(['message' => 'OTP resent successfully.']);
    }
}

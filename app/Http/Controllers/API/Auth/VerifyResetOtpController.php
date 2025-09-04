<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class VerifyResetOtpController extends Controller
{
    public function verifyOtp(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required|digits:6']);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp !== $request->otp || now()->gt($user->otp_expires_at)) {
            return response()->json(['error' => 'Invalid or expired OTP'], 422);
        }

        return response()->json(['message' => 'OTP verified. Proceed to reset password.']);
    }
}

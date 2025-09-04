<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class ForgotPasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = rand(100000, 999999);
        $user = User::where('email', $request->email)->first();
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($otp));

        return response()->json(['message' => 'OTP sent to your email.']);
    }
}

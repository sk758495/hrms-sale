<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|string|max:15',
            'password' => 'required|confirmed|min:6',
        ]);

        $nameString = strtolower(preg_replace('/\s+/', '', $request->name));
        $shortName = substr($nameString, 0, 5);
        $count = User::where('employee_id', 'like', "JIS-$shortName%")->count() + 1;
        $employeeId = 'JIS-' . $shortName . str_pad($count, 3, '0', STR_PAD_LEFT);

        while (User::where('employee_id', $employeeId)->exists()) {
            $count++;
            $employeeId = 'JIS-' . $shortName . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'employee_id' => $employeeId,
            'password' => Hash::make($request->password),
        ]);

        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($otp));

        return response()->json(['message' => 'User registered. OTP sent to email.']);
    }
}

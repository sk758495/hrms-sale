<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        'mobile' => ['required', 'string', 'max:15'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // Use first 5 characters of name (no spaces), lowercase
    $nameString = strtolower(preg_replace('/\s+/', '', $request->name));
    $shortName = substr($nameString, 0, 5);

    // Get next sequential number starting from 001
    $count = User::count() + 1;
    $paddedNumber = str_pad($count, 3, '0', STR_PAD_LEFT);
    $employeeId = 'JIS-' . $shortName . $paddedNumber;

    // Ensure unique employee ID
    while (User::where('employee_id', $employeeId)->exists()) {
        $count++;
        $paddedNumber = str_pad($count, 3, '0', STR_PAD_LEFT);
        $employeeId = 'JIS-' . $shortName . $paddedNumber;
    }

    // Create the user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'mobile' => $request->mobile,
        'employee_id' => $employeeId,
        'password' => Hash::make($request->password),
    ]);

    Auth::login($user);
    event(new Registered($user));

    // Send OTP
    $otp = rand(100000, 999999);
    session(['otp' => $otp, 'otp_expiration' => now()->addMinutes(5)]);
    Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($otp));

    return redirect()->route('auth.otp.verify');
}

}

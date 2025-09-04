<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrEmailVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('hr')->check() && !Auth::guard('hr')->user()->email_verified_at) {
            return redirect()->route('hr.otp.verify');
        }

        return $next($request);
    }
}
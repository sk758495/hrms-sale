<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->email_verified_at) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Email not verified'], 403);
            }
            return redirect()->route('auth.otp.verify');
        }

        return $next($request);
    }
}
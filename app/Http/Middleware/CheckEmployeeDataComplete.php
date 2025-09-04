<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmployeeData;

class CheckEmployeeDataComplete
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $employeeData = EmployeeData::where('user_id', Auth::id())->first();
            
            if (!$employeeData && !$request->routeIs('user.employee-data.*')) {
                return redirect()->route('user.employee-data.create')
                    ->with('info', 'Please complete your employee data first.');
            }
        }

        return $next($request);
    }
}
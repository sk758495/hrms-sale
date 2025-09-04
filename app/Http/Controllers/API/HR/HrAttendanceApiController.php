<?php

namespace App\Http\Controllers\API\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;

class HrAttendanceApiController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $month = $request->get('month', now()->format('Y-m'));

        $todayAttendances = Attendance::with('user')
            ->where('date', $date)
            ->orderBy('check_in', 'desc')
            ->get();

        $monthlyAttendances = Attendance::with('user')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('user_id');

        $totalEmployees = User::whereHas('employeeData')->count();
        $presentToday = Attendance::where('date', $date)->where('status', '!=', 'absent')->count();
        $lateToday = Attendance::where('date', $date)->where('status', 'late')->count();

        return response()->json([
            'date' => $date,
            'month' => $month,
            'total_employees' => $totalEmployees,
            'present_today' => $presentToday,
            'late_today' => $lateToday,
            'today_attendances' => $todayAttendances,
            'monthly_attendances' => $monthlyAttendances,
        ]);
    }

    public function show(User $user, Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));

        $attendances = Attendance::where('user_id', $user->id)
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
            ->orderBy('date', 'desc')
            ->get();

        $stats = [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->where('status', 'present')->count(),
            'late_days' => $attendances->where('status', 'late')->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'total_hours' => $attendances->sum('working_hours'),
        ];

        return response()->json([
            'user' => $user,
            'month' => $month,
            'attendances' => $attendances,
            'stats' => $stats,
        ]);
    }
}

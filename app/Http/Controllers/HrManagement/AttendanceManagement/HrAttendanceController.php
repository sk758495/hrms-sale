<?php

namespace App\Http\Controllers\HrManagement\AttendanceManagement;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HrAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $month = $request->get('month', now()->format('Y-m'));
        
        // Today's attendance
        $todayAttendances = Attendance::with(['user'])
            ->where('date', $date)
            ->orderBy('check_in', 'desc')
            ->get();
        
        // Monthly summary
        $monthlyAttendances = Attendance::with(['user'])
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('user_id');
        
        // Statistics
        $totalEmployees = User::whereHas('employeeData')->count();
        $presentToday = Attendance::where('date', $date)->where('status', '!=', 'absent')->count();
        $lateToday = Attendance::where('date', $date)->where('status', 'late')->count();
        
        return view('hr-management.attendance.index', compact(
            'todayAttendances', 
            'monthlyAttendances', 
            'date', 
            'month',
            'totalEmployees',
            'presentToday',
            'lateToday'
        ));
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
            'total_hours' => $attendances->sum('working_hours')
        ];
        
        return view('hr-management.attendance.show', compact('user', 'attendances', 'stats', 'month'));
    }
}
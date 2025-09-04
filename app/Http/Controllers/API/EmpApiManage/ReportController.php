<?php

namespace App\Http\Controllers\API\EmpApiManage;

use App\Http\Controllers\Controller;
use App\Models\BreakSession;
use App\Models\WorkSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function daily(Request $req) {
        $user = $req->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $date = $req->query('date', now()->toDateString());
            
            $sessions = WorkSession::where('user_id', $user->id)
                ->whereDate('start_time', $date)
                ->orderBy('start_time')->get();

            $breaks = BreakSession::where('user_id', $user->id)
                ->whereDate('start_time', $date)
                ->orderBy('start_time')->get();

            // Calculate work time (sessions + meetings)
            $workSeconds = $sessions->sum('duration_seconds');
            $meetingSeconds = $breaks->where('type', 'meeting')->sum('duration_seconds');
            $totalWorkSeconds = $workSeconds + $meetingSeconds;
            
            // Calculate break time (lunch + short breaks only)
            $lunchSeconds = $breaks->where('type', 'lunch')->sum('duration_seconds');
            $shortSeconds = $breaks->where('type', 'short')->sum('duration_seconds');
            $totalBreakSeconds = $lunchSeconds + $shortSeconds;

            return response()->json([
                'date' => $date,
                'work_seconds' => $totalWorkSeconds,
                'work_hours' => $this->formatTime($totalWorkSeconds),
                'lunch_seconds' => $lunchSeconds,
                'lunch_hours' => $this->formatTime($lunchSeconds),
                'short_break_seconds' => $shortSeconds,
                'short_break_hours' => $this->formatTime($shortSeconds),
                'meeting_seconds' => $meetingSeconds,
                'meeting_hours' => $this->formatTime($meetingSeconds),
                'total_break_seconds' => $totalBreakSeconds,
                'total_break_hours' => $this->formatTime($totalBreakSeconds),
                'total_office_seconds' => $totalWorkSeconds + $totalBreakSeconds,
                'total_office_hours' => $this->formatTime($totalWorkSeconds + $totalBreakSeconds),
                'sessions' => $sessions,
                'breaks' => $breaks,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get daily report: ' . $e->getMessage()], 500);
        }
    }

    public function weekly(Request $req) {
        $user = $req->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $date = Carbon::parse($req->query('date', now()));
            $startOfWeek = $date->copy()->startOfWeek();
            $endOfWeek = $date->copy()->endOfWeek();
            
            $sessions = WorkSession::where('user_id', $user->id)
                ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
                ->get();

            $breaks = BreakSession::where('user_id', $user->id)
                ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
                ->get();

            $workSeconds = $sessions->sum('duration_seconds');
            $meetingSeconds = $breaks->where('type', 'meeting')->sum('duration_seconds');
            $totalWorkSeconds = $workSeconds + $meetingSeconds;
            
            $lunchSeconds = $breaks->where('type', 'lunch')->sum('duration_seconds');
            $shortSeconds = $breaks->where('type', 'short')->sum('duration_seconds');
            $totalBreakSeconds = $lunchSeconds + $shortSeconds;

            return response()->json([
                'week_start' => $startOfWeek->toDateString(),
                'week_end' => $endOfWeek->toDateString(),
                'work_seconds' => $totalWorkSeconds,
                'work_hours' => $this->formatTime($totalWorkSeconds),
                'lunch_hours' => $this->formatTime($lunchSeconds),
                'short_break_hours' => $this->formatTime($shortSeconds),
                'meeting_hours' => $this->formatTime($meetingSeconds),
                'total_break_hours' => $this->formatTime($totalBreakSeconds),
                'total_office_hours' => $this->formatTime($totalWorkSeconds + $totalBreakSeconds),
                'sessions_count' => $sessions->count(),
                'breaks_count' => $breaks->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get weekly report: ' . $e->getMessage()], 500);
        }
    }

    public function monthly(Request $req) {
        $user = $req->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $date = Carbon::parse($req->query('date', now()));
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $sessions = WorkSession::where('user_id', $user->id)
                ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                ->get();

            $breaks = BreakSession::where('user_id', $user->id)
                ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                ->get();

            $workSeconds = $sessions->sum('duration_seconds');
            $meetingSeconds = $breaks->where('type', 'meeting')->sum('duration_seconds');
            $totalWorkSeconds = $workSeconds + $meetingSeconds;
            
            $lunchSeconds = $breaks->where('type', 'lunch')->sum('duration_seconds');
            $shortSeconds = $breaks->where('type', 'short')->sum('duration_seconds');
            $totalBreakSeconds = $lunchSeconds + $shortSeconds;

            return response()->json([
                'month' => $date->format('Y-m'),
                'month_name' => $date->format('F Y'),
                'work_seconds' => $totalWorkSeconds,
                'work_hours' => $this->formatTime($totalWorkSeconds),
                'lunch_hours' => $this->formatTime($lunchSeconds),
                'short_break_hours' => $this->formatTime($shortSeconds),
                'meeting_hours' => $this->formatTime($meetingSeconds),
                'total_break_hours' => $this->formatTime($totalBreakSeconds),
                'total_office_hours' => $this->formatTime($totalWorkSeconds + $totalBreakSeconds),
                'working_days' => $sessions->groupBy(function($item) {
                    return $item->start_time->toDateString();
                })->count(),
                'sessions_count' => $sessions->count(),
                'breaks_count' => $breaks->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get monthly report: ' . $e->getMessage()], 500);
        }
    }

    private function formatTime($seconds) {
        if ($seconds == 0) return '0h 0m';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return $hours . 'h ' . $minutes . 'm';
    }
}
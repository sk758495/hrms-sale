<?php

namespace App\Http\Controllers\EmpManagement;

use App\Http\Controllers\Controller;
use App\Models\BreakSession;
use App\Models\WorkSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $user = auth()->user();
        $date = $request->query('date', now()->toDateString());
        
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
        
        // Total office time = work + breaks
        $totalOfficeSeconds = $totalWorkSeconds + $totalBreakSeconds;
        
        // Calculate missing time if there are gaps
        $missingSeconds = $this->calculateMissingTime($sessions, $date);

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
            'total_office_seconds' => $totalOfficeSeconds,
            'total_office_hours' => $this->formatTime($totalOfficeSeconds),
            'missing_seconds' => $missingSeconds,
            'missing_hours' => $this->formatTime($missingSeconds),
            'sessions' => $sessions,
            'breaks' => $breaks,
        ]);
    }

    public function weekly(Request $request)
    {
        $user = auth()->user();
        $date = Carbon::parse($request->query('date', now()));
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();
        
        $sessions = work_sessions::where('user_id', $user->id)
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->get();

        $breaks = breaks::where('user_id', $user->id)
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->get();

        $workSeconds = $sessions->sum('duration_seconds');
        $lunchSeconds = $breaks->where('type', 'lunch')->sum('duration_seconds');
        $shortSeconds = $breaks->where('type', 'short')->sum('duration_seconds');
        $meetingSeconds = $breaks->where('type', 'meeting')->sum('duration_seconds');
        $totalOfficeSeconds = $workSeconds + $lunchSeconds + $shortSeconds + $meetingSeconds;

        return response()->json([
            'week_start' => $startOfWeek->toDateString(),
            'week_end' => $endOfWeek->toDateString(),
            'work_seconds' => $workSeconds,
            'work_hours' => $this->formatTime($workSeconds),
            'lunch_hours' => $this->formatTime($lunchSeconds),
            'short_break_hours' => $this->formatTime($shortSeconds),
            'meeting_hours' => $this->formatTime($meetingSeconds),
            'total_break_hours' => $this->formatTime($lunchSeconds + $shortSeconds),
            'total_office_hours' => $this->formatTime($totalOfficeSeconds),
            'working_days' => $sessions->groupBy(function($item) {
                return $item->start_time->toDateString();
            })->count(),
            'sessions_count' => $sessions->count(),
            'breaks_count' => $breaks->count(),
        ]);
    }

    public function monthly(Request $request)
    {
        $user = auth()->user();
        $date = Carbon::parse($request->query('date', now()));
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        $sessions = work_sessions::where('user_id', $user->id)
            ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
            ->get();

        $breaks = breaks::where('user_id', $user->id)
            ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
            ->get();

        $workSeconds = $sessions->sum('duration_seconds');
        $lunchSeconds = $breaks->where('type', 'lunch')->sum('duration_seconds');
        $shortSeconds = $breaks->where('type', 'short')->sum('duration_seconds');
        $meetingSeconds = $breaks->where('type', 'meeting')->sum('duration_seconds');
        $totalOfficeSeconds = $workSeconds + $lunchSeconds + $shortSeconds + $meetingSeconds;

        return response()->json([
            'month' => $date->format('Y-m'),
            'month_name' => $date->format('F Y'),
            'work_seconds' => $workSeconds,
            'work_hours' => $this->formatTime($workSeconds),
            'lunch_hours' => $this->formatTime($lunchSeconds),
            'short_break_hours' => $this->formatTime($shortSeconds),
            'meeting_hours' => $this->formatTime($meetingSeconds),
            'total_break_hours' => $this->formatTime($lunchSeconds + $shortSeconds),
            'total_office_hours' => $this->formatTime($totalOfficeSeconds),
            'working_days' => $sessions->groupBy(function($item) {
                return $item->start_time->toDateString();
            })->count(),
            'sessions_count' => $sessions->count(),
            'breaks_count' => $breaks->count(),
        ]);
    }
    
    private function calculateMissingTime($sessions, $date)
    {
        if ($sessions->isEmpty()) {
            return 0;
        }
        
        // Sort sessions by start time
        $sortedSessions = $sessions->sortBy('start_time');
        $missingSeconds = 0;
        
        for ($i = 0; $i < $sortedSessions->count() - 1; $i++) {
            $currentSession = $sortedSessions->values()[$i];
            $nextSession = $sortedSessions->values()[$i + 1];
            
            if ($currentSession->end_time && $nextSession->start_time) {
                $gap = $nextSession->start_time->diffInSeconds($currentSession->end_time);
                // Only count gaps longer than 5 minutes as missing time
                if ($gap > 300) {
                    $missingSeconds += $gap;
                }
            }
        }
        
        return $missingSeconds;
    }

    private function formatTime($seconds)
    {
        if ($seconds == 0) return '0h 0m';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return $hours . 'h ' . $minutes . 'm';
    }
}
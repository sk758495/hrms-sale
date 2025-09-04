<?php

namespace App\Http\Controllers\EmpManagement;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BreakSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BreakController extends Controller
{
    public function start(Request $request)
    {
        $user = auth()->user();
        $type = $request->input('type');
        $at = Carbon::parse($request->input('started_at', now()))->setTimezone('Asia/Kolkata');
        $pauses = in_array($type, ['lunch', 'short']);
        
        $activeBreak = BreakSession::where('user_id', $user->id)
            ->whereNull('end_time')
            ->first();
            
        if ($activeBreak) {
            return response()->json([
                'error' => 'You already have an active break',
                'break_id' => $activeBreak->id
            ], 400);
        }
        
        $break = BreakSession::create([
            'user_id' => $user->id,
            'work_session_id' => $request->input('session_id'),
            'type' => $type,
            'pauses_timer' => $pauses,
            'start_time' => $at
        ]);
        
        $typeNames = [
            'lunch' => 'Lunch Break',
            'short' => 'Short Break',
            'meeting' => 'Meeting'
        ];
        
        ActivityLog::create([
            'user_id' => $user->id,
            'timestamp' => $at,
            'action' => 'Break Started',
            'description' => $typeNames[$type] . ' started'
        ]);
        
        return response()->json(['break_id' => $break->id]);
    }

    public function end(Request $request)
    {
        $user = auth()->user();
        $id = $request->input('break_id');
        $at = Carbon::parse($request->input('ended_at', now()))->setTimezone('Asia/Kolkata');

        $break = BreakSession::where('user_id', $user->id)->findOrFail($id);
        $break->end_time = $at;
        $break->duration_seconds = max(0, $break->start_time->diffInSeconds($break->end_time));
        $break->save();

        $typeNames = [
            'lunch' => 'Lunch Break',
            'short' => 'Short Break', 
            'meeting' => 'Meeting'
        ];

        ActivityLog::create([
            'user_id' => $user->id,
            'timestamp' => $at,
            'action' => 'Break Ended',
            'description' => $typeNames[$break->type] . ' ended (' . $this->formatTime($break->duration_seconds) . ')'
        ]);
        
        return response()->json([
            'success' => true,
            'duration_seconds' => $break->duration_seconds
        ]);
    }
    
    public function getActiveBreak()
    {
        $user = auth()->user();
        
        $activeBreak = BreakSession::where('user_id', $user->id)
            ->whereNull('end_time')
            ->latest()
            ->first();
            
        if ($activeBreak) {
            return response()->json([
                'active' => true,
                'break_id' => $activeBreak->id,
                'type' => $activeBreak->type,
                'start_time' => $activeBreak->start_time,
                'duration' => now()->diffInSeconds($activeBreak->start_time)
            ]);
        }
        
        return response()->json(['active' => false]);
    }
    
    private function formatTime($seconds)
    {
        if ($seconds == 0) return '0h 0m';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return $hours . 'h ' . $minutes . 'm';
    }
}
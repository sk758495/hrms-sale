<?php

namespace App\Http\Controllers\API\EmpApiManage;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BreakSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BreakController extends Controller
{
    public function start(Request $req) {
        $req->validate([
            'type' => 'required|in:lunch,short,meeting',
            'started_at' => 'nullable|date',
            'session_id' => 'nullable|integer'
        ]);
        
        $user = $req->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $type = $req->input('type');
            $at = Carbon::parse($req->input('started_at', now()))->setTimezone('Asia/Kolkata');
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
                'work_session_id' => $req->input('session_id'),
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
            
            // Auto-pause work session for lunch/short breaks (not meetings)
            if ($pauses && $req->input('session_id')) {
                $this->autoPauseWorkSession($user, $req->input('session_id'), $at, $typeNames[$type]);
            }
            
            return response()->json(['break_id' => $break->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to start break: ' . $e->getMessage()], 500);
        }
    }

    public function end(Request $req) {
        $req->validate([
            'break_id' => 'required|integer',
            'ended_at' => 'nullable|date'
        ]);
        
        $user = $req->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $id = $req->input('break_id');
            $at = Carbon::parse($req->input('ended_at', now()))->setTimezone('Asia/Kolkata');

            $b = BreakSession::where('user_id', $user->id)->findOrFail($id);
            $b->end_time = $at;
            $b->duration_seconds = max(0, $b->start_time->diffInSeconds($at));
            $b->save();

            $typeNames = [
                'lunch' => 'Lunch Break',
                'short' => 'Short Break', 
                'meeting' => 'Meeting'
            ];

            ActivityLog::create([
                'user_id' => $user->id,
                'timestamp' => $at,
                'action' => 'Break Ended',
                'description' => $typeNames[$b->type] . ' ended (' . $this->formatTime($b->duration_seconds) . ')'
            ]);
            
            return response()->json([
                'success' => true,
                'duration_seconds' => $b->duration_seconds
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to end break: ' . $e->getMessage()], 500);
        }
    }
    
    public function getActiveBreak(Request $req) {
        $user = $req->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get active break: ' . $e->getMessage()], 500);
        }
    }
    
    private function formatTime($seconds)
    {
        if ($seconds == 0) return '0h 0m';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return $hours . 'h ' . $minutes . 'm';
    }
    
    private function autoPauseWorkSession($user, $sessionId, $at, $breakType)
    {
        try {
            $session = \App\Models\WorkSession::where('user_id', $user->id)
                ->where('id', $sessionId)
                ->whereNull('end_time')
                ->first();
                
            if ($session) {
                $session->update([
                    'end_time' => $at,
                    'end_reason' => 'Auto-pause for ' . $breakType,
                    'duration_seconds' => max(0, $session->start_time->diffInSeconds($at))
                ]);
                
                ActivityLog::create([
                    'user_id' => $user->id,
                    'timestamp' => $at,
                    'action' => 'Timer Paused',
                    'description' => 'Auto-pause for ' . $breakType
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the break start
            \Log::error('Failed to auto-pause work session: ' . $e->getMessage());
        }
    }
}
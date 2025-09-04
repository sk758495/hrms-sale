<?php

namespace App\Http\Controllers\API\EmpApiManage;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\WorkSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SessionController extends Controller
{
    const INACTIVITY_TIMEOUT = 10; // minutes

    public function checkInactivity(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $lastActivity = Carbon::parse($request->input('last_activity'));
            
            // Find active sessions
            $activeSessions = WorkSession::where('user_id', $user->id)
                ->whereNull('end_time')
                ->get();
                
            foreach ($activeSessions as $session) {
                $inactiveTime = $lastActivity->diffInMinutes($session->start_time);
                
                if ($inactiveTime > self::INACTIVITY_TIMEOUT) {
                    // Auto-pause session
                    $pauseTime = $session->start_time->addMinutes(self::INACTIVITY_TIMEOUT);
                    
                    $session->update([
                        'end_time' => $pauseTime,
                        'end_reason' => 'Auto-pause due to inactivity',
                        'duration_seconds' => max(0, $pauseTime->diffInSeconds($session->start_time))
                    ]);
                    
                    ActivityLog::create([
                        'user_id' => $user->id,
                        'timestamp' => $pauseTime,
                        'action' => 'Auto-Pause',
                        'description' => 'Session paused due to ' . self::INACTIVITY_TIMEOUT . ' minutes of inactivity'
                    ]);
                    
                    // Calculate missing time
                    $missingSeconds = max(0, $lastActivity->diffInSeconds($pauseTime));
                    
                    return response()->json([
                        'session_paused' => true,
                        'missing_seconds' => $missingSeconds,
                        'missing_time' => $this->formatTime($missingSeconds)
                    ]);
                }
            }
            
            return response()->json(['session_paused' => false]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to check inactivity: ' . $e->getMessage()], 500);
        }
    }
    
    public function getActiveSession(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $activeSession = WorkSession::where('user_id', $user->id)
                ->whereNull('end_time')
                ->latest()
                ->first();
                
            if ($activeSession) {
                return response()->json([
                    'active' => true,
                    'session_id' => $activeSession->id,
                    'start_time' => $activeSession->start_time,
                    'duration' => now()->diffInSeconds($activeSession->start_time)
                ]);
            }
            
            return response()->json(['active' => false]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get active session: ' . $e->getMessage()], 500);
        }
    }
    
    private function formatTime($seconds)
    {
        if ($seconds == 0) return '0h 0m';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return $hours . 'h ' . $minutes . 'm';
    }
}
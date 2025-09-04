<?php

namespace App\Http\Controllers\EmpManagement;

use App\Http\Controllers\Controller;
use App\Models\activity_logs;
use App\Models\work_sessions;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SessionController extends Controller
{
    const INACTIVITY_TIMEOUT = 10; // minutes

    public function checkInactivity(Request $request)
    {
        $user = auth()->user();
        $lastActivity = Carbon::parse($request->input('last_activity'));
        
        // Find active sessions
        $activeSessions = work_sessions::where('user_id', $user->id)
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
                
                activity_logs::create([
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
    }
    
    public function getActiveSession()
    {
        $user = auth()->user();
        
        $activeSession = work_sessions::where('user_id', $user->id)
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
    }
    
    private function formatTime($seconds)
    {
        if ($seconds == 0) return '0h 0m';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return $hours . 'h ' . $minutes . 'm';
    }
}
<?php

namespace App\Http\Controllers\API\EmpApiManage;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\WorkSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimerController extends Controller
{
    public function start(Request $req) {
        $req->validate([
            'started_at' => 'nullable|date'
        ]);
        
        $user = $req->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $activeSession = WorkSession::where('user_id', $user->id)
                ->whereNull('end_time')
                ->first();
                
            if ($activeSession) {
                return response()->json([
                    'error' => 'You already have an active session',
                    'session_id' => $activeSession->id
                ], 400);
            }
            
            $startedAt = Carbon::parse($req->input('started_at', now()))->setTimezone('Asia/Kolkata');
            $session = WorkSession::create([
                'user_id' => $user->id,
                'start_time' => $startedAt,
                'end_reason' => '',
            ]);
            
            ActivityLog::create([
                'user_id' => $user->id,
                'timestamp' => $startedAt,
                'action' => 'Timer Started',
                'description' => 'Work session started'
            ]);
            
            return response()->json(['session_id' => $session->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to start timer: ' . $e->getMessage()], 500);
        }
    }

    public function pause(Request $req) {
        $req->validate([
            'session_id' => 'required|integer',
            'paused_at' => 'nullable|date',
            'reason' => 'nullable|string'
        ]);
        
        $user = $req->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $id = $req->input('session_id');
            $at = Carbon::parse($req->input('paused_at', now()))->setTimezone('Asia/Kolkata');
            $reason = $req->input('reason', 'Manual pause');

            $s = WorkSession::where('user_id', $user->id)->findOrFail($id);
            if (!$s->end_time) {
                $s->end_time = $at;
                $s->end_reason = $reason;
                $s->duration_seconds = max(0, $s->start_time->diffInSeconds($at));
                $s->save();
            }
            
            ActivityLog::create([
                'user_id' => $user->id,
                'timestamp' => $at,
                'action' => 'Timer Paused',
                'description' => $reason
            ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to pause timer: ' . $e->getMessage()], 500);
        }
    }

    public function resume(Request $req) {
        $req->validate([
            'resumed_at' => 'nullable|date'
        ]);
        
        $user = $req->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $activeSession = WorkSession::where('user_id', $user->id)
                ->whereNull('end_time')
                ->first();
                
            if ($activeSession) {
                return response()->json([
                    'error' => 'You already have an active session',
                    'session_id' => $activeSession->id
                ], 400);
            }
            
            $at = Carbon::parse($req->input('resumed_at', now()))->setTimezone('Asia/Kolkata');

            $s = WorkSession::create([
                'user_id' => $user->id,
                'start_time' => $at,
                'end_reason' => ''
            ]);
            
            ActivityLog::create([
                'user_id' => $user->id,
                'timestamp' => $at,
                'action' => 'Timer Resumed',
                'description' => 'Work session resumed after break/pause'
            ]);
            
            return response()->json(['session_id' => $s->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to resume timer: ' . $e->getMessage()], 500);
        }
    }

    public function stop(Request $req) {
        $req->validate([
            'session_id' => 'required|integer',
            'stopped_at' => 'nullable|date'
        ]);
        
        $user = $req->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $id = $req->input('session_id');
            $at = Carbon::parse($req->input('stopped_at', now()))->setTimezone('Asia/Kolkata');

            $s = WorkSession::where('user_id', $user->id)->findOrFail($id);
            if (!$s->end_time) {
                $s->end_time = $at;
                $s->end_reason = 'Check-out';
                $s->duration_seconds = max(0, $s->start_time->diffInSeconds($at));
                $s->save();
            }
            
            ActivityLog::create([
                'user_id' => $user->id,
                'timestamp' => $at,
                'action' => 'Timer Stopped',
                'description' => 'Work session ended'
            ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to stop timer: ' . $e->getMessage()], 500);
        }
    }

    public function manualAdd(Request $req) {
        $req->validate([
            'seconds' => 'required|integer|min:1',
            'at' => 'nullable|date',
            'note' => 'nullable|string'
        ]);
        
        $user = $req->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        try {
            $seconds = (int)$req->input('seconds', 0);
            $endAt = Carbon::parse($req->input('at', now()))->setTimezone('Asia/Kolkata');
            $startAt = $endAt->copy()->subSeconds($seconds);

            $s = WorkSession::create([
                'user_id' => $user->id,
                'start_time' => $startAt,
                'end_time' => $endAt,
                'duration_seconds' => max(0, $seconds),
                'end_reason' => 'Manual time'
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'timestamp' => $endAt,
                'action' => 'Manual Time Added',
                'description' => $req->input('note', '')
            ]);
            
            return response()->json(['session_id' => $s->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add manual time: ' . $e->getMessage()], 500);
        }
    }
    
    public function getActiveSession(Request $req) {
        $user = $req->user();
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
}
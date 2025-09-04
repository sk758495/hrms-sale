<?php

namespace App\Http\Controllers\EmpManagement;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\WorkSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimerController extends Controller
{
    public function index()
    {
        return view('emp-management.time-tracker.index');
    }

    public function start(Request $request)
    {
        $user = auth()->user();
        
        $activeSession = WorkSession::where('user_id', $user->id)
            ->whereNull('end_time')
            ->first();
            
        if ($activeSession) {
            return response()->json([
                'error' => 'You already have an active session',
                'session_id' => $activeSession->id
            ], 400);
        }
        
        $startedAt = Carbon::parse($request->input('started_at', now()))->setTimezone('Asia/Kolkata');
        
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
    }

    public function pause(Request $request)
    {
        $user = auth()->user();
        $id = $request->input('session_id');
        $at = Carbon::parse($request->input('paused_at', now()))->setTimezone('Asia/Kolkata');
        $reason = $request->input('reason', 'Manual pause');

        $session = WorkSession::where('user_id', $user->id)->findOrFail($id);
        if (!$session->end_time) {
            $session->end_time = $at;
            $session->end_reason = $reason;
            $session->duration_seconds = max(0, $session->start_time->diffInSeconds($at));
            $session->save();
        }
        
        ActivityLog::create([
            'user_id' => $user->id,
            'timestamp' => $at,
            'action' => 'Timer Paused',
            'description' => $reason
        ]);
        
        return response()->json(['success' => true]);
    }

    public function resume(Request $request)
    {
        $user = auth()->user();
        
        $activeSession = WorkSession::where('user_id', $user->id)
            ->whereNull('end_time')
            ->first();
            
        if ($activeSession) {
            return response()->json([
                'error' => 'You already have an active session',
                'session_id' => $activeSession->id
            ], 400);
        }
        
        $at = Carbon::parse($request->input('resumed_at', now()))->setTimezone('Asia/Kolkata');

        $session = WorkSession::create([
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
        
        return response()->json(['session_id' => $session->id]);
    }

    public function stop(Request $request)
    {
        $user = auth()->user();
        $id = $request->input('session_id');
        $at = Carbon::parse($request->input('stopped_at', now()))->setTimezone('Asia/Kolkata');

        $session = WorkSession::where('user_id', $user->id)->findOrFail($id);
        if (!$session->end_time) {
            $session->end_time = $at;
            $session->end_reason = 'Check-out';
            $session->duration_seconds = max(0, $session->start_time->diffInSeconds($at));
            $session->save();
        }
        
        ActivityLog::create([
            'user_id' => $user->id,
            'timestamp' => $at,
            'action' => 'Timer Stopped',
            'description' => 'Work session ended'
        ]);
        
        return response()->json(['success' => true]);
    }

    public function manualAdd(Request $request)
    {
        $user = auth()->user();
        $seconds = (int)$request->input('seconds', 0);
        $endAt = Carbon::parse($request->input('at', now()))->setTimezone('Asia/Kolkata');
        $startAt = $endAt->copy()->subSeconds($seconds);

        $session = WorkSession::create([
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
            'description' => $request->input('note', '')
        ]);
        
        return response()->json(['session_id' => $session->id]);
    }
    
    public function getActiveSession()
    {
        $user = auth()->user();
        
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
    }
}
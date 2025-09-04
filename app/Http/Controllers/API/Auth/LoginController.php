<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return response()->json(['error' => 'You are not active. Please ask HR manager to activate your profile.'], 403);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['error' => 'Email not verified'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Auto-start work session on login
        $sessionId = $this->autoStartWorkSession($user);

        return response()->json([
            'token' => $token, 
            'user' => $user,
            'session_id' => $sessionId,
            'auto_started' => $sessionId ? true : false
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }
    
    private function autoStartWorkSession($user)
    {
        // Check if user already has an active session
        $activeSession = \App\Models\WorkSession::where('user_id', $user->id)
            ->whereNull('end_time')
            ->first();
            
        if (!$activeSession) {
            // Create new work session
            $session = \App\Models\WorkSession::create([
                'user_id' => $user->id,
                'start_time' => now()->setTimezone('Asia/Kolkata'),
                'end_reason' => ''
            ]);
            
            // Log the activity
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'timestamp' => now()->setTimezone('Asia/Kolkata'),
                'action' => 'Auto-Start',
                'description' => 'Work session auto-started on API login'
            ]);
            
            return $session->id;
        }
        
        return $activeSession->id;
    }
}

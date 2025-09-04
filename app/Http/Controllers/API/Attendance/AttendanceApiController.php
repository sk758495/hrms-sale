<?php

namespace App\Http\Controllers\API\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\QrToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class AttendanceApiController extends Controller
{
    private $officeLatitude = 22.300602;
    private $officeLongitude = 73.238206;
    private $allowedRadius = 100;

    public function index()
    {
        $user = Auth::user();
        $currentMonth = now()->format('Y-m');

        $attendances = Attendance::where('user_id', $user->id)
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
            ->orderBy('date', 'desc')
            ->get();

        $today = Attendance::where('user_id', $user->id)->where('date', today())->first();

        return response()->json([
            'attendances' => $attendances,
            'today' => $today
        ]);
    }

    public function generateQr()
    {
        $user = Auth::user();
        $todayAttendance = Attendance::where('user_id', $user->id)->where('date', today())->first();

        if ($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out) {
            $oneHourAfter = $todayAttendance->check_in->addHour();
            if (now()->lessThan($oneHourAfter)) {
                return response()->json(['error' => 'Minimum 1 hour work not completed.'], 400);
            }
        }

        $qrToken = QrToken::generateToken($user->id);
        $qrUrl = route('mobile.scan', [
            'token' => $qrToken->token,
            'emp_id' => $user->employee_id,
            'emp_name' => $user->name
        ]);
        
        return response()->json([
            'qr_data' => $qrUrl,
            'expires_at' => $qrToken->expires_at->toTimeString()
        ]);
    }

    public function scan(Request $request, $token)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();
        $qrToken = QrToken::where('token', $token)->where('user_id', $user->id)->first();
        
        if (!$qrToken || !$qrToken->isValid()) {
            return response()->json(['error' => 'Invalid or expired QR code'], 400);
        }

        $distance = $this->calculateDistance($request->latitude, $request->longitude);
        if ($distance > $this->allowedRadius) {
            return response()->json(['error' => 'You must be in the office. Distance: ' . round($distance) . 'm'], 400);
        }

        $attendance = Attendance::firstOrCreate(['user_id' => $user->id, 'date' => today()], ['status' => 'absent']);

        if (!$attendance->check_in) {
            $status = now()->hour < 9 || (now()->hour == 9 && now()->minute <= 30) ? 'present' : 'late';
            $attendance->update([
                'check_in' => now(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => $status
            ]);
            $qrToken->update(['used' => true]);
            return response()->json(['success' => true, 'action' => 'check_in', 'status' => $status]);
        }

        if (!$attendance->check_out) {
            if (now()->lessThan($attendance->check_in->addHour())) {
                return response()->json(['error' => 'Check-out only after 1 hour'], 400);
            }

            $status = $attendance->status;
            $hour = now()->hour;
            $minute = now()->minute;
            if ($hour > 18 || ($hour == 18 && $minute >= 30)) $status = 'absent';
            elseif (($hour == 17 && $minute >= 30) || ($hour == 18 && $minute == 0)) $status = 'present';

            $attendance->update([
                'check_out' => now(),
                'status' => $status
            ]);
            $qrToken->update(['used' => true]);

            return response()->json([
                'success' => true,
                'action' => 'check_out',
                'status' => $status
            ]);
        }

        return response()->json(['error' => 'Already checked in and out'], 400);
    }

    public function mobileScan(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'employee_id' => 'required',
            'password' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = User::where('employee_id', $request->employee_id)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }

        $qrToken = QrToken::where('token', $request->token)->where('user_id', $user->id)->first();
        if (!$qrToken || !$qrToken->isValid()) {
            return response()->json(['error' => 'QR code not generated by this employee or expired'], 400);
        }

        $distance = $this->calculateDistance($request->latitude, $request->longitude);
        if ($distance > $this->allowedRadius) {
            return response()->json(['error' => 'You must be in the office. Distance: ' . round($distance) . 'm'], 400);
        }

        $attendance = Attendance::firstOrCreate(['user_id' => $user->id, 'date' => today()], ['status' => 'absent']);

        if (!$attendance->check_in) {
            $status = now()->hour < 9 || (now()->hour == 9 && now()->minute <= 30) ? 'present' : 'late';
            $attendance->update([
                'check_in' => now(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => $status
            ]);
            $qrToken->update(['used' => true]);
            return response()->json(['success' => true, 'action' => 'check_in', 'status' => $status]);
        }

        if (!$attendance->check_out) {
            if (now()->lessThan($attendance->check_in->addHour())) {
                return response()->json(['error' => 'Check-out only after 1 hour'], 400);
            }

            $status = $attendance->status;
            $hour = now()->hour;
            $minute = now()->minute;
            if ($hour > 18 || ($hour == 18 && $minute >= 30)) $status = 'absent';
            elseif (($hour == 17 && $minute >= 30) || ($hour == 18 && $minute == 0)) $status = 'present';

            $attendance->update([
                'check_out' => now(),
                'status' => $status
            ]);
            $qrToken->update(['used' => true]);

            return response()->json(['success' => true, 'action' => 'check_out', 'status' => $status]);
        }

        return response()->json(['error' => 'Already checked in and out'], 400);
    }

    private function calculateDistance($lat1, $lon1)
    {
        $lat2 = $this->officeLatitude;
        $lon2 = $this->officeLongitude;
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
}


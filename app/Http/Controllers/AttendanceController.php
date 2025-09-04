<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\QrToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class AttendanceController extends Controller
{
    private $officeLatitude = 22.300602; // this is real office latitude
    private $officeLongitude = 73.238206;
    private $allowedRadius = 100; // meters

    public function index()
    {
        // Mark incomplete attendance as absent before displaying
        $this->markIncompleteAttendanceAbsent();
        
        $user = Auth::user();
        $currentMonth = now()->format('Y-m');
        
        $attendances = Attendance::where('user_id', $user->id)
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
            ->orderBy('date', 'desc')
            ->get();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', today())
            ->first();
            
        // Update working hours from time management system
        $this->updateWorkingHoursFromTimeSystem($user, $attendances);
        if ($todayAttendance) {
            $this->updateWorkingHoursFromTimeSystem($user, collect([$todayAttendance]));
        }

        return view('attendance.index', compact('attendances', 'todayAttendance','user'));
    }
    
    private function markIncompleteAttendanceAbsent()
    {
        $yesterday = Carbon::yesterday();
        $tenHoursAgo = Carbon::now()->subHours(10);
        
        // Only mark as absent if:
        // 1. Have check_in but NO check_out
        // 2. Check_in was more than 10 hours ago OR it's from yesterday
        // 3. Don't touch records that already have both check_in and check_out
        Attendance::whereNotNull('check_in')
                  ->whereNull('check_out')
                  ->where(function($query) use ($yesterday, $tenHoursAgo) {
                      $query->where('date', $yesterday)
                            ->orWhere('check_in', '<=', $tenHoursAgo);
                  })
                  ->update(['status' => 'absent']);
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

            return response()->json(['success' => true, 'action' => 'check_out', 'status' => $status]);
        }

        return response()->json(['error' => 'Already checked in and out'], 400);
    }
    
    private function updateWorkingHoursFromTimeSystem($user, $attendances)
    {
        foreach ($attendances as $attendance) {
            $date = $attendance->date->format('Y-m-d');
            
            // Get work sessions for this date
            $sessions = \App\Models\WorkSession::where('user_id', $user->id)
                ->whereDate('start_time', $date)
                ->get();
                
            // Get breaks for this date
            $breaks = \App\Models\BreakSession::where('user_id', $user->id)
                ->whereDate('start_time', $date)
                ->get();
                
            // Calculate work time (sessions + meetings)
            $workSeconds = $sessions->sum('duration_seconds');
            $meetingSeconds = $breaks->where('type', 'meeting')->sum('duration_seconds');
            $totalWorkSeconds = $workSeconds + $meetingSeconds;
            
            // Convert to hours with 2 decimal places
            $workHours = round($totalWorkSeconds / 3600, 2);
            
            // Update attendance record
            $attendance->update(['working_hours' => $workHours]);
        }
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

    public function mobileScan(Request $request)
    {
        try {
            Log::info('Mobile scan request received', $request->all());
            
            $request->validate([
                'token' => 'required|string',
                'employee_id' => 'required|string',
                'password' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            // Authenticate user first
            $user = User::where('employee_id', $request->employee_id)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['error' => 'Invalid employee credentials'], 400);
            }

            // Verify token belongs to this user
            $qrToken = QrToken::where('token', $request->token)->where('user_id', $user->id)->first();
            if (!$qrToken || !$qrToken->isValid()) {
                return response()->json(['error' => 'QR code not generated by this employee or expired'], 400);
            }

            // Check location
            $distance = $this->calculateDistance(
                $request->latitude, 
                $request->longitude
            );

            if ($distance > $this->allowedRadius) {
                return response()->json(['error' => 'You must be at the office to scan. Distance: ' . round($distance) . 'm'], 400);
            }

            $today = today();
            $currentHour = now()->hour;

            $attendance = Attendance::firstOrCreate(
                ['user_id' => $user->id, 'date' => $today],
                ['status' => 'absent']
            );

            if (!$attendance->check_in) {
                // Check in - determine status based on time
                $checkInTime = now();
                $checkInHour = $checkInTime->hour;
                $checkInMinute = $checkInTime->minute;
                
                // Present: 9:00 AM - 9:30 AM, Late: after 9:30 AM
                $status = ($checkInHour < 9 || ($checkInHour == 9 && $checkInMinute <= 30)) ? 'present' : 'late';
                
                $attendance->update([
                    'check_in' => $checkInTime,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'status' => $status
                ]);
                
                $qrToken->update(['used' => true]);
                
                return response()->json([
                    'success' => true,
                    'action' => 'check_in',
                    'employee_name' => $user->name,
                    'time' => $checkInTime->format('H:i:s'),
                    'status' => $status
                ])->header('Content-Type', 'application/json');
            } elseif (!$attendance->check_out) {
                // Check if 1 hour has passed since check-in
                $oneHourAfterCheckIn = $attendance->check_in->addHour();
                if (now()->lessThan($oneHourAfterCheckIn)) {
                    $remainingMinutes = now()->diffInMinutes($oneHourAfterCheckIn);
                    return response()->json([
                        'error' => 'You can check out after completing 1 hour. Please wait ' . $remainingMinutes . ' more minutes.'
                    ], 400);
                }
                
                // Check out - determine final status based on checkout time
                $checkOutTime = now();
                $checkOutHour = $checkOutTime->hour;
                $checkOutMinute = $checkOutTime->minute;
                
                // Final status logic:
                // Present: checkout between 5:30 PM - 6:00 PM
                // Absent: checkout after 6:30 PM
                // Keep original status if checkout before 5:30 PM or between 6:00-6:30 PM
                $finalStatus = $attendance->status; // Keep original status by default
                
                if ($checkOutHour > 18 || ($checkOutHour == 18 && $checkOutMinute >= 30)) {
                    // After 6:30 PM - mark as absent
                    $finalStatus = 'absent';
                } elseif (($checkOutHour == 17 && $checkOutMinute >= 30) || ($checkOutHour == 18 && $checkOutMinute == 0)) {
                    // Between 5:30 PM - 6:00 PM - mark as present
                    $finalStatus = 'present';
                }
                
                $attendance->update([
                    'check_out' => $checkOutTime,
                    'status' => $finalStatus
                ]);
                
                $attendance->refresh();
                $qrToken->update(['used' => true]);
                
                return response()->json([
                    'success' => true,
                    'action' => 'check_out',
                    'employee_name' => $user->name,
                    'time' => $checkOutTime->format('H:i:s'),
                    'status' => $finalStatus,
                    'working_hours' => $attendance->working_hours
                ])->header('Content-Type', 'application/json');
            } else {
                return response()->json(['error' => 'Already checked in and out for today'], 400);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed: ' . implode(', ', Arr::flatten($e->errors()))], 400);
            // return response()->json(['error' => 'Validation failed: ' . implode(', ', array_flatten($e->errors()))], 400);
        } catch (\Exception $e) {
            Log::error('Mobile scan failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Scan failed: ' . $e->getMessage()], 500);
        }
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Carbon\Carbon;

class MarkIncompleteAttendanceAbsent extends Command
{
    protected $signature = 'attendance:mark-absent';
    protected $description = 'Mark employees as absent if they have not checked out within 10 hours';

    public function handle()
    {
        $yesterday = Carbon::yesterday();
        $tenHoursAgo = Carbon::now()->subHours(10);
        
        // Find attendance records that:
        // 1. Have check_in but no check_out
        // 2. Check_in was more than 10 hours ago OR it's from yesterday
        $incompleteAttendances = Attendance::where(function($query) use ($yesterday, $tenHoursAgo) {
            $query->whereNotNull('check_in')
                  ->whereNull('check_out')
                  ->where(function($subQuery) use ($yesterday, $tenHoursAgo) {
                      $subQuery->where('date', $yesterday)
                               ->orWhere('check_in', '<=', $tenHoursAgo);
                  });
        })->get();
        
        $count = 0;
        foreach ($incompleteAttendances as $attendance) {
            $attendance->update([
                'status' => 'absent',
                'check_out' => null // Ensure check_out remains null for absent status
            ]);
            $count++;
        }
        
        $this->info("Marked {$count} incomplete attendance records as absent.");
        return 0;
    }
}

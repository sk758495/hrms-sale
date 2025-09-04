<?php

use App\Http\Controllers\EmpManagement\TimerController;
use App\Http\Controllers\EmpManagement\BreakController;
use App\Http\Controllers\EmpManagement\ReportController;
use App\Http\Controllers\EmpManagement\SessionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'App\Http\Middleware\EnsureEmailIsVerified', 'App\Http\Middleware\CheckUserActive', 'check.employee.data'])->prefix('emp-management')->group(function () {
    
    // Time Tracker UI
    Route::get('/time-tracker', [TimerController::class, 'index'])->name('emp.time-tracker');
    
    // Timer Management
    Route::post('/timer/start', [TimerController::class, 'start'])->name('emp.timer.start');
    Route::post('/timer/pause', [TimerController::class, 'pause'])->name('emp.timer.pause');
    Route::post('/timer/resume', [TimerController::class, 'resume'])->name('emp.timer.resume');
    Route::post('/timer/stop', [TimerController::class, 'stop'])->name('emp.timer.stop');
    Route::post('/timer/manual', [TimerController::class, 'manualAdd'])->name('emp.timer.manual');
    Route::get('/timer/active', [TimerController::class, 'getActiveSession'])->name('emp.timer.active');
    
    // Break Management
    Route::post('/breaks/start', [BreakController::class, 'start'])->name('emp.breaks.start');
    Route::post('/breaks/end', [BreakController::class, 'end'])->name('emp.breaks.end');
    Route::get('/breaks/active', [BreakController::class, 'getActiveBreak'])->name('emp.breaks.active');
    
    // Session Management
    Route::post('/session/check-inactivity', [SessionController::class, 'checkInactivity'])->name('emp.session.check-inactivity');
    Route::get('/session/active', [SessionController::class, 'getActiveSession'])->name('emp.session.active');
    
    // Reports
    Route::get('/reports/daily', [ReportController::class, 'daily'])->name('emp.reports.daily');
    Route::get('/reports/weekly', [ReportController::class, 'weekly'])->name('emp.reports.weekly');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('emp.reports.monthly');
});
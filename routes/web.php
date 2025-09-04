<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



// Public scan routes for mobile access
Route::get('/scan/{token}', function ($token, Request $request) {
    $empId = $request->get('emp_id');
    $empName = $request->get('emp_name');
    return view('attendance.mobile-scan', compact('token', 'empId', 'empName'));
})->name('mobile.scan');

Route::post('/attendance/mobile-scan', [AttendanceController::class, 'mobileScan'])->name('attendance.mobile-scan');

Route::get('/dashboard', function () {
    return redirect()->route('attendance.index');
})->middleware(['auth', 'App\Http\Middleware\EnsureEmailIsVerified'])->name('dashboard');



// Redirect root to login if not authenticated
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('attendance.index');
    }
    return view('welcome');
});

Route::middleware(['auth', 'App\Http\Middleware\EnsureEmailIsVerified', 'App\Http\Middleware\CheckUserActive'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Attendance routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/generate-qr', [AttendanceController::class, 'generateQr'])->name('attendance.generate-qr');
    Route::post('/attendance/scan/{token}', [AttendanceController::class, 'scan'])->name('attendance.scan');

    // Employee Documents
    Route::prefix('my-documents')->name('emp.documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\EmpManagement\EmployeeDocumentController::class, 'index'])->name('index');
        Route::get('/appointment/{letter}/download', [\App\Http\Controllers\EmpManagement\EmployeeDocumentController::class, 'downloadAppointmentLetter'])->name('download.appointment');
        Route::get('/offer/{letter}/download', [\App\Http\Controllers\EmpManagement\EmployeeDocumentController::class, 'downloadOfferLetter'])->name('download.offer');
        Route::get('/nda/{nda}/download', [\App\Http\Controllers\EmpManagement\EmployeeDocumentController::class, 'downloadNda'])->name('download.nda');
        Route::get('/salary/{slip}/download', [\App\Http\Controllers\EmpManagement\EmployeeDocumentController::class, 'downloadSalarySlip'])->name('download.salary');
        Route::get('/file/{type}/download', [\App\Http\Controllers\EmpManagement\EmployeeDocumentController::class, 'downloadFile'])->name('download.file');
    });

    // Test route
    Route::get('/test-qr', function () {
        return response()->json(['message' => 'Test route works', 'user' => Auth::user()->name]);
    });
});

require __DIR__ . '/auth.php';
require __DIR__ . '/EmpManage/EmpDocument-route.php';
require __DIR__ . '/EmpManage/emp-time-management-route.php';
require __DIR__ . '/HrManage/auth-route.php';
require __DIR__ . '/HrManage/employee-route.php';

// HR Attendance Management Routes
// Route::middleware(['auth:hr'])->prefix('hr')->group(function () {
//     Route::get('/attendance', [\App\Http\Controllers\HrManagement\AttendanceManagement\HrAttendanceController::class, 'index'])->name('hr.attendance.index');
//     Route::get('/attendance/{user}', [\App\Http\Controllers\HrManagement\AttendanceManagement\HrAttendanceController::class, 'show'])->name('hr.attendance.show');
// });

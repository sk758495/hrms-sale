<?php

use App\Http\Controllers\HrManagement\EmployeeManagement\DepartmentController;
use App\Http\Controllers\HrManagement\EmployeeManagement\EmployeeDataController;
use App\Http\Controllers\HrManagement\EmployeeManagement\PositionController;
use App\Http\Controllers\HrManagement\DocumentManagement\AppointmentLetterController;
use App\Http\Controllers\HrManagement\DocumentManagement\OfferLetterController;
use App\Http\Controllers\HrManagement\DocumentManagement\SalarySlipController;
use App\Http\Controllers\HrManagement\DocumentManagement\NdaController;
use App\Http\Controllers\HrManagement\InterviewController;
use Illuminate\Support\Facades\Route;

// All employee management routes require HR authentication and email verification
Route::middleware(['auth:hr', 'App\Http\Middleware\HrEmailVerified'])->group(function () {
    
    // Employee Data Routes
    Route::get('/employee-data', [EmployeeDataController::class, 'index'])->name('employee-data.index');
    Route::get('/employee/create', [EmployeeDataController::class, 'create'])->name('employee.create');
    Route::post('/employee/store', [EmployeeDataController::class, 'store'])->name('employee.store');
    Route::put('/employee/toggle-status/{user}', [EmployeeDataController::class, 'toggleStatus'])->name('employee.toggleStatus');
    Route::get('/employee/edit/{employee}', [EmployeeDataController::class, 'edit'])->name('employee.edit');
    Route::put('/employee/update/{employee}', [EmployeeDataController::class, 'update'])->name('employee.update');
    Route::delete('/employee/delete/{employee}', [EmployeeDataController::class, 'destroy'])->name('employee.destroy');
    
    // AJAX route for positions by department
    Route::get('/positions/by-department/{id}', function ($id) {
        try {
            $positions = \App\Models\Position::where('department_id', $id)->get();
            return response()->json($positions);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    });
    
    // Department Routes
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('create');
        Route::post('/store', [DepartmentController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [DepartmentController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [DepartmentController::class, 'destroy'])->name('destroy');
    });
    
    // Position Routes
    Route::prefix('positions')->name('positions.')->group(function () {
        Route::get('/', [PositionController::class, 'index'])->name('index');
        Route::get('/create', [PositionController::class, 'create'])->name('create');
        Route::post('/store', [PositionController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PositionController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [PositionController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [PositionController::class, 'destroy'])->name('destroy');
    });
    
    // Appointment Letter Routes
    Route::prefix('appointment-letters')->name('appointment-letters.')->group(function () {
        Route::get('/', [AppointmentLetterController::class, 'index'])->name('index');
        Route::get('/create', [AppointmentLetterController::class, 'create'])->name('create');
        Route::post('/store', [AppointmentLetterController::class, 'store'])->name('store');
        Route::get('/{appointmentLetter}', [AppointmentLetterController::class, 'show'])->name('show');
        Route::delete('/{appointmentLetter}', [AppointmentLetterController::class, 'destroy'])->name('destroy');
    });
    
    // Offer Letter Routes
    Route::prefix('offer-letters')->name('offer-letters.')->group(function () {
        Route::get('/', [OfferLetterController::class, 'index'])->name('index');
        Route::get('/create', [OfferLetterController::class, 'create'])->name('create');
        Route::post('/store', [OfferLetterController::class, 'store'])->name('store');
        Route::get('/{offerLetter}', [OfferLetterController::class, 'show'])->name('show');
        Route::delete('/{offerLetter}', [OfferLetterController::class, 'destroy'])->name('destroy');
    });
    
    // Salary Slip Routes
    Route::prefix('salary-slips')->name('salary-slips.')->group(function () {
        Route::get('/', [SalarySlipController::class, 'index'])->name('index');
        Route::get('/create', [SalarySlipController::class, 'create'])->name('create');
        Route::post('/store', [SalarySlipController::class, 'store'])->name('store');
        Route::get('/setup', [SalarySlipController::class, 'setupSalaryStructure'])->name('setup');
        Route::post('/setup', [SalarySlipController::class, 'storeSalaryStructure'])->name('setup.store');
        Route::get('/employee-data', [SalarySlipController::class, 'getEmployeeData'])->name('employee-data');
        Route::get('/check-existing', [SalarySlipController::class, 'checkExisting'])->name('check-existing');
        Route::get('/{salarySlip}', [SalarySlipController::class, 'show'])->name('show');
    });
    
    // NDA Routes
    Route::prefix('ndas')->name('ndas.')->group(function () {
        Route::get('/', [NdaController::class, 'index'])->name('index');
        Route::get('/create', [NdaController::class, 'create'])->name('create');
        Route::post('/store', [NdaController::class, 'store'])->name('store');
        Route::get('/{nda}', [NdaController::class, 'show'])->name('show');
        Route::delete('/{nda}', [NdaController::class, 'destroy'])->name('destroy');
    });
    
    // Interview Routes
    Route::prefix('hr/interviews')->name('interviews.')->group(function () {
        Route::get('/', [InterviewController::class, 'index'])->name('index');
        Route::get('/create', [InterviewController::class, 'create'])->name('create');
        Route::post('/store', [InterviewController::class, 'store'])->name('store');
        Route::get('/completed', [InterviewController::class, 'completed'])->name('completed');
        Route::get('/rejected', [InterviewController::class, 'rejected'])->name('rejected');
        Route::get('/{interview}', [InterviewController::class, 'show'])->name('show');
        Route::get('/{interview}/edit', [InterviewController::class, 'edit'])->name('edit');
        Route::put('/{interview}', [InterviewController::class, 'update'])->name('update');
        Route::put('/{interview}/status', [InterviewController::class, 'updateStatus'])->name('update-status');
        Route::get('/{interview}/resume', [InterviewController::class, 'downloadResume'])->name('download-resume');
        Route::delete('/{interview}', [InterviewController::class, 'destroy'])->name('destroy');
        Route::get('/positions/{department_id}', [InterviewController::class, 'getPositions'])->name('positions');
    });
    
});

// HR Attendance Management Routes
Route::middleware(['auth:hr'])->prefix('hr')->group(function () {
    Route::get('/attendance', [\App\Http\Controllers\HrManagement\AttendanceManagement\HrAttendanceController::class, 'index'])->name('hr.attendance.index');
    Route::get('/attendance/{user}', [\App\Http\Controllers\HrManagement\AttendanceManagement\HrAttendanceController::class, 'show'])->name('hr.attendance.show');
});


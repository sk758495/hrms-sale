<?php

use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\OtpVerificationController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\Auth\VerifyResetOtpController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API Routes
Route::prefix('auth')->group(function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('otp/verify', [OtpVerificationController::class, 'verify']);
    Route::post('otp/resend', [OtpVerificationController::class, 'resend']);

    Route::post('login', [LoginController::class, 'login']);
    Route::middleware('auth:sanctum')->post('logout', [LoginController::class, 'logout']);

    Route::post('forgot-password', [ForgotPasswordController::class, 'sendOtp']);
    Route::post('verify-reset-otp', [VerifyResetOtpController::class, 'verifyOtp']);
    Route::post('reset-password', [ResetPasswordController::class, 'reset']);
});

use App\Http\Controllers\API\HR\HRAuthController;
// HR Authentication API Routes
Route::prefix('hr')->group(function () {
    Route::post('/register', [HRAuthController::class, 'register']);
    Route::post('/verify-otp', [HRAuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [HRAuthController::class, 'resendOtp']);
    
    Route::post('/login', [HRAuthController::class, 'login']);
    Route::post('/login/otp/verify', [HRAuthController::class, 'verifyLoginOtp']);

    Route::middleware(['auth:sanctum', 'App\Http\Middleware\HrEmailVerified'])->group(function () {
        Route::post('/logout', [HRAuthController::class, 'logout']);
        Route::get('/dashboard', [HRAuthController::class, 'dashboard']);
    });
});


use App\Http\Controllers\API\HR\HrAttendanceApiController;
// HR Attendance API Routes
Route::middleware(['auth:sanctum', 'App\Http\Middleware\HrEmailVerified'])->prefix('hr')->group(function () {
    Route::get('/attendance', [HrAttendanceApiController::class, 'index']);
    Route::get('/attendance/{user}', [HrAttendanceApiController::class, 'show']);
});

use App\Http\Controllers\API\HR\AppointmentLetterApiController;
// Appointment Letter API Routes
Route::middleware(['auth:sanctum', 'App\Http\Middleware\HrEmailVerified'])->prefix('hr')->group(function () {
    Route::get('/appointment-letters', [AppointmentLetterApiController::class, 'index']);
    Route::get('/appointment-letters/employees', [AppointmentLetterApiController::class, 'employees']);
    Route::post('/appointment-letters', [AppointmentLetterApiController::class, 'store']);
    Route::get('/appointment-letters/{id}', [AppointmentLetterApiController::class, 'show']);
    Route::delete('/appointment-letters/{id}', [AppointmentLetterApiController::class, 'destroy']);
});

use App\Http\Controllers\API\HR\NdaApiController;
// NDA API Routes
Route::middleware(['auth:sanctum', 'App\Http\Middleware\HrEmailVerified'])->prefix('hr')->group(function () {
    Route::get('/ndas', [NdaApiController::class, 'index']);
    Route::get('/ndas/employees', [NdaApiController::class, 'eligibleEmployees']);
    Route::post('/ndas', [NdaApiController::class, 'store']);
    Route::get('/ndas/{id}', [NdaApiController::class, 'show']);
    Route::delete('/ndas/{id}', [NdaApiController::class, 'destroy']);
});


use App\Http\Controllers\API\HR\OfferLetterApiController;
// Offer Letter API Routes
Route::middleware(['auth:sanctum', 'App\Http\Middleware\HrEmailVerified'])->prefix('hr')->group(function () {
    Route::get('/offer-letters', [OfferLetterApiController::class, 'index']);
    Route::get('/offer-letters/employees', [OfferLetterApiController::class, 'eligibleEmployees']);
    Route::post('/offer-letters', [OfferLetterApiController::class, 'store']);
    Route::get('/offer-letters/{id}', [OfferLetterApiController::class, 'show']);
    Route::delete('/offer-letters/{id}', [OfferLetterApiController::class, 'destroy']);
});

use App\Http\Controllers\API\HR\SalarySlipApiController;
// Salary Slip API Routes
Route::middleware(['auth:sanctum', 'App\Http\Middleware\HrEmailVerified'])->prefix('hr')->group(function () {
    Route::get('/salary-slips', [SalarySlipApiController::class, 'index']);
    Route::get('/salary-slips/check-existing', [SalarySlipApiController::class, 'checkExisting']);
    Route::get('/salary-slips/employee-data', [SalarySlipApiController::class, 'getEmployeeData']);
    Route::get('/salary-slips/{id}', [SalarySlipApiController::class, 'show']);
    Route::post('/salary-slips', [SalarySlipApiController::class, 'store']);

    Route::post('/salary-structure', [SalarySlipApiController::class, 'storeSalaryStructure']);
});

use App\Http\Controllers\API\HR\DepartmentApiController;
// Department API Routes
Route::middleware(['auth:sanctum', 'App\Http\Middleware\HrEmailVerified'])->prefix('hr')->group(function () {
    Route::get('/departments', [DepartmentApiController::class, 'index']);
    Route::post('/departments', [DepartmentApiController::class, 'store']);
    Route::get('/departments/{id}', [DepartmentApiController::class, 'show']);
    Route::put('/departments/{id}', [DepartmentApiController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentApiController::class, 'destroy']);
});


use App\Http\Controllers\API\HR\PositionApiController;
// Position API Routes
Route::middleware(['auth:sanctum', 'App\Http\Middleware\HrEmailVerified'])->prefix('hr')->group(function () {
    Route::get('/positions', [PositionApiController::class, 'index']);
    Route::post('/positions', [PositionApiController::class, 'store']);
    Route::get('/positions/{id}', [PositionApiController::class, 'show']);
    Route::put('/positions/{id}', [PositionApiController::class, 'update']);
    Route::delete('/positions/{id}', [PositionApiController::class, 'destroy']);
});

use App\Http\Controllers\API\HR\InterviewApiController;
// Interview API Routes
Route::middleware(['auth:sanctum', 'App\Http\Middleware\HrEmailVerified'])->prefix('hr')->group(function () {
    Route::get('/interviews', [InterviewApiController::class, 'index']);
    Route::get('/interviews/completed', [InterviewApiController::class, 'completed']);
    Route::get('/interviews/rejected', [InterviewApiController::class, 'rejected']);
    Route::get('/interviews/{id}', [InterviewApiController::class, 'show']);
    Route::post('/interviews', [InterviewApiController::class, 'store']);
    Route::put('/interviews/{id}', [InterviewApiController::class, 'update']);
    Route::put('/interviews/{id}/status', [InterviewApiController::class, 'updateStatus']);
    Route::delete('/interviews/{id}', [InterviewApiController::class, 'destroy']);

    Route::get('/interviews/{id}/resume', [InterviewApiController::class, 'downloadResume']);
    Route::get('/interviews/positions/{department_id}', [InterviewApiController::class, 'getPositions']);
});

use App\Http\Controllers\API\Attendance\AttendanceApiController;

// Attendance API Routes
Route::middleware('auth:sanctum')->prefix('attendance')->group(function () {
    Route::get('/', [AttendanceApiController::class, 'index']);
    Route::post('/generate-qr', [AttendanceApiController::class, 'generateQr']);
    Route::post('/scan/{token}', [AttendanceApiController::class, 'scan']);
});

// Public API for mobile scan via employee ID
Route::post('/attendance/mobile-scan', [AttendanceApiController::class, 'mobileScan']);

use App\Http\Controllers\API\HR\EmployeeDataController;

Route::middleware(['auth:sanctum', 'App\Http\Middleware\HrEmailVerified'])->prefix('hr')->group(function () {
    Route::get('/employees', [EmployeeDataController::class, 'index']);
    Route::get('/users', [EmployeeDataController::class, 'getAllUsers']);
    Route::post('/employees', [EmployeeDataController::class, 'store']);
    Route::get('/employees/{id}', [EmployeeDataController::class, 'show']);
    Route::put('/employees/{id}', [EmployeeDataController::class, 'update']);
    Route::delete('/employees/{id}', [EmployeeDataController::class, 'destroy']);
    Route::put('/employees/{user}/toggle-status', [EmployeeDataController::class, 'toggleStatus']);
});

// Employee Document API Routes
use App\Http\Controllers\API\EmpApiManage\EmployeeDocumentApiController;
use App\Http\Controllers\API\EmpApiManage\ReportController;
use App\Http\Controllers\API\EmpApiManage\TimerController;
use App\Http\Controllers\API\EmpApiManage\BreakController;
use App\Http\Controllers\API\EmpApiManage\SessionController;

Route::middleware(['auth:sanctum', 'App\Http\Middleware\EnsureEmailIsVerified'])->prefix('employee')->group(function () {
    // Document Management
    Route::prefix('documents')->group(function () {
        Route::get('/', [EmployeeDocumentApiController::class, 'index']);
        Route::get('/appointment/{id}', [EmployeeDocumentApiController::class, 'downloadAppointmentLetter']);
        Route::get('/offer/{id}', [EmployeeDocumentApiController::class, 'downloadOfferLetter']);
        Route::get('/nda/{id}', [EmployeeDocumentApiController::class, 'downloadNda']);
        Route::get('/salary-slip/{id}', [EmployeeDocumentApiController::class, 'downloadSalarySlip']);
        Route::get('/file/{type}', [EmployeeDocumentApiController::class, 'downloadFile']);
    });
    

});

Route::middleware(['auth:sanctum', 'App\Http\Middleware\EnsureEmailIsVerified'])->group(function () {
    // Time Management
    Route::prefix('timer')->group(function () {
        Route::post('/start', [TimerController::class, 'start']);
        Route::post('/pause', [TimerController::class, 'pause']);
        Route::post('/resume', [TimerController::class, 'resume']);
        Route::post('/stop', [TimerController::class, 'stop']);
        Route::post('/manual', [TimerController::class, 'manualAdd']);
        Route::get('/active', [TimerController::class, 'getActiveSession']);
    });
    
    // Break Management (lunch, short, meeting)
    Route::prefix('breaks')->group(function () {
        Route::post('/start', [BreakController::class, 'start']);
        Route::post('/end', [BreakController::class, 'end']);
        Route::get('/active', [BreakController::class, 'getActiveBreak']);
    });
    
    // Reports with enhanced time tracking
    Route::prefix('reports')->group(function () {
        Route::get('/daily', [ReportController::class, 'daily']); // ?date=YYYY-MM-DD
        Route::get('/weekly', [ReportController::class, 'weekly']); // ?date=YYYY-MM-DD
        Route::get('/monthly', [ReportController::class, 'monthly']); // ?date=YYYY-MM-DD
    });
    

});
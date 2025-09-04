<?php

use App\Http\Controllers\HrManagement\Auth\HRAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('hr')->group(function () {
    // Guest routes
    Route::middleware('guest:hr')->group(function () {
        Route::get('/login', [HRAuthController::class, 'showLoginForm'])->name('hr.login');
        Route::post('/login', [HRAuthController::class, 'login']);
        
        Route::get('/login/otp/verify', [HRAuthController::class, 'showLoginOtpVerify'])->name('hr.login.otp.verify');
        Route::post('/login/otp/verify', [HRAuthController::class, 'verifyLoginOtp']);
        
        Route::get('/register', [HRAuthController::class, 'showRegisterForm'])->name('hr.register');
        Route::post('/register', [HRAuthController::class, 'register']);
    });
    
    // Authenticated routes
    Route::middleware('auth:hr')->group(function () {
        // OTP verification for registration
        Route::get('/verify-otp', [HRAuthController::class, 'showOtpVerify'])->name('hr.otp.verify');
        Route::post('/verify-otp', [HRAuthController::class, 'verifyOtp']);
        Route::post('/resend-otp', [HRAuthController::class, 'resendOtp'])->name('hr.otp.resend');
        
        Route::post('/logout', [HRAuthController::class, 'logout'])->name('hr.logout');
        
        // Email verified routes
        Route::middleware('App\Http\Middleware\HrEmailVerified')->group(function () {
            Route::get('/dashboard', [HRAuthController::class, 'dashboard'])->name('hr.dashboard');
        });
    });
});

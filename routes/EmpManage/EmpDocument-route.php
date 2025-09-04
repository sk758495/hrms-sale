
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;




Route::middleware(['auth', 'App\Http\Middleware\EnsureEmailIsVerified'])->group(function () {

    // Employee Documents
    Route::prefix('my-documents')->name('emp.documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\EmpManagement\EmployeeDocumentController::class, 'index'])->name('index');
        Route::get('/appointment/{letter}/download', [\App\Http\Controllers\EmpManagement\EmployeeDocumentController::class, 'downloadAppointmentLetter'])->name('download.appointment');
        Route::get('/offer/{letter}/download', [\App\Http\Controllers\EmpManagement\EmployeeDocumentController::class, 'downloadOfferLetter'])->name('download.offer');
        Route::get('/nda/{nda}/download', [\App\Http\Controllers\EmpManagement\EmployeeDocumentController::class, 'downloadNda'])->name('download.nda');
        Route::get('/salary/{slip}/download', [\App\Http\Controllers\EmpManagement\EmployeeDocumentController::class, 'downloadSalarySlip'])->name('download.salary');
    });

});
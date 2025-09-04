<?php

namespace App\Http\Controllers\EmpManagement;

use App\Http\Controllers\Controller;
use App\Models\EmployeeData;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeDocumentController extends Controller
{
    public function index()
    {
        $employee = EmployeeData::with([
            'user', 'department', 'position',
            'appointmentLetters', 'offerLetters', 'ndas', 'salarySlips'
        ])->where('user_id', auth()->id())->first();

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Employee data not found');
        }

        return view('emp-management.documents.index', compact('employee'));
    }

    public function downloadAppointmentLetter($letterId)
    {
        $employee = EmployeeData::with('user', 'department', 'position')->where('user_id', auth()->id())->first();
        $letter = $employee->appointmentLetters()->findOrFail($letterId);

        $pdf = Pdf::loadView('hr-management.documents.appointment-letters.show', ['appointmentLetter' => $letter]);
        return $pdf->download('appointment-letter.pdf');
    }

    public function downloadOfferLetter($letterId)
    {
        $employee = EmployeeData::with('user', 'department', 'position')->where('user_id', auth()->id())->first();
        $letter = $employee->offerLetters()->findOrFail($letterId);

        $pdf = Pdf::loadView('hr-management.documents.offer-letters.show', ['offerLetter' => $letter]);
        return $pdf->download('offer-letter.pdf');
    }

    public function downloadNda($ndaId)
    {
        $employee = EmployeeData::with('user', 'department', 'position')->where('user_id', auth()->id())->first();
        $nda = $employee->ndas()->findOrFail($ndaId);

        $pdf = Pdf::loadView('hr-management.documents.ndas.show', ['nda' => $nda]);
        return $pdf->download('nda.pdf');
    }

    public function downloadSalarySlip($slipId)
    {
        $employee = EmployeeData::with('user', 'department', 'position')->where('user_id', auth()->id())->first();
        $salarySlip = $employee->salarySlips()->findOrFail($slipId);

        $pdf = Pdf::loadView('hr-management.documents.salary-slips.show', ['salarySlip' => $salarySlip]);
        return $pdf->download('salary-slip-' . $salarySlip->month_year . '.pdf');
    }

    public function downloadFile($type)
    {
        $employee = EmployeeData::where('user_id', auth()->id())->first();
        
        if (!$employee || !$employee->$type) {
            return redirect()->back()->with('error', 'File not found');
        }

        $filePath = storage_path('app/public/' . $employee->$type);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found');
        }

        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $originalFileName = pathinfo($employee->$type, PATHINFO_BASENAME);
        
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'zip' => 'application/zip'
        ];
        
        $mimeType = $mimeTypes[$fileExtension] ?? 'application/octet-stream';
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $originalFileName . '"'
        ]);
    }
}
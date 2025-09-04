<?php

namespace App\Http\Controllers\API\EmpApiManage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentApiController extends Controller
{
    public function index()
    {
        $employee = EmployeeData::with([
            'user', 'department', 'position',
            'appointmentLetters', 'offerLetters', 'ndas', 'salarySlips'
        ])->where('user_id', auth()->id())->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee data not found'], 404);
        }

        // Convert to array and add document URLs
        $employeeData = $employee->toArray();
        
        $documentFields = [
            'passport_photo', 'aadhar_doc', 'pan_doc', 'driving_license_doc', 'voter_id_doc',
            'passbook_image', 'resume', 'prev_offer_letter', 'prev_appointment_letter',
            'prev_salary_slips', 'prev_relieving_letter', 'form_16'
        ];

        $employeeData['document_urls'] = [];
        foreach ($documentFields as $field) {
            if ($employee->$field) {
                $employeeData['document_urls'][$field] = asset('storage/' . $employee->$field);
            }
        }

        return response()->json(['employee' => $employeeData]);
    }

    public function downloadAppointmentLetter($id)
    {
        $employee = EmployeeData::where('user_id', auth()->id())->firstOrFail();
        $letter = $employee->appointmentLetters()->findOrFail($id);

        $pdf = Pdf::loadView('hr-management.documents.appointment-letters.show', ['appointmentLetter' => $letter]);
        return $pdf->download('appointment-letter.pdf');
    }

    public function downloadOfferLetter($id)
    {
        $employee = EmployeeData::where('user_id', auth()->id())->firstOrFail();
        $letter = $employee->offerLetters()->findOrFail($id);

        $pdf = Pdf::loadView('hr-management.documents.offer-letters.show', ['offerLetter' => $letter]);
        return $pdf->download('offer-letter.pdf');
    }

    public function downloadNda($id)
    {
        $employee = EmployeeData::where('user_id', auth()->id())->firstOrFail();
        $nda = $employee->ndas()->findOrFail($id);

        $pdf = Pdf::loadView('hr-management.documents.ndas.show', ['nda' => $nda]);
        return $pdf->download('nda.pdf');
    }

    public function downloadSalarySlip($id)
    {
        $employee = EmployeeData::where('user_id', auth()->id())->firstOrFail();
        $slip = $employee->salarySlips()->findOrFail($id);

        $pdf = Pdf::loadView('hr-management.documents.salary-slips.show', ['salarySlip' => $slip]);
        return $pdf->download('salary-slip-' . $slip->month_year . '.pdf');
    }

    public function downloadFile($type)
    {
        $employee = EmployeeData::where('user_id', auth()->id())->first();

        if (!$employee || !$employee->$type) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $filePath = storage_path('app/public/' . $employee->$type);
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
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

<?php

namespace App\Http\Controllers\HrManagement;

use App\Http\Controllers\Controller;
use App\Models\EmployeeData;
use Illuminate\Http\Request;

class DocumentVerificationController extends Controller
{
    public function index()
    {
        $employees = EmployeeData::with(['user', 'department', 'position'])
            ->whereIn('overall_status', ['pending', 'under_review'])
            ->latest()
            ->get();
        
        return view('hr-management.document-verification.index', compact('employees'));
    }

    public function show(EmployeeData $employee)
    {
        $employee->load(['user', 'department', 'position']);
        $allDocumentsApproved = $this->areAllDocumentsApproved($employee);
        $pendingDocuments = $this->getPendingDocuments($employee);
        return view('hr-management.document-verification.show', compact('employee', 'allDocumentsApproved', 'pendingDocuments'));
    }

    public function verifyDocument(Request $request, EmployeeData $employee)
    {
        try {
            \Log::info('Document verification request received', [
                'employee_id' => $employee->id,
                'request_data' => $request->all()
            ]);

            $request->validate([
                'document_type' => 'required|string',
                'status' => 'required|in:approved,rejected',
                'remarks' => 'nullable|string'
            ]);

            $statusField = $request->document_type . '_status';
            $remarksField = $request->document_type . '_remarks';

            $updateData = [
                $statusField => $request->status,
                $remarksField => $request->remarks,
                'overall_status' => 'under_review'
            ];

            \Log::info('Updating employee data', $updateData);

            $employee->update($updateData);

            return response()->json(['success' => true, 'message' => 'Document status updated successfully']);
        } catch (\Exception $e) {
            \Log::error('Document verification error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function finalApproval(EmployeeData $employee)
    {
        $employee->update(['overall_status' => 'approved']);
        return redirect()->route('hr.document-verification.index')->with('success', 'Employee documents approved successfully!');
    }

    public function addCtc(Request $request, EmployeeData $employee)
    {
        if (!$this->areAllDocumentsApproved($employee)) {
            return redirect()->back()->with('error', 'All documents must be approved before adding CTC!');
        }

        $request->validate([
            'ctc' => 'required|numeric|min:0'
        ]);

        $employee->update(['ctc' => $request->ctc]);
        return redirect()->route('hr.document-verification.index')->with('success', 'CTC added successfully!');
    }

    public function downloadDocument(EmployeeData $employee, $document)
    {
        $filePath = $employee->$document;
        
        if (!$filePath || !file_exists(storage_path('app/public/' . $filePath))) {
            abort(404, 'Document not found');
        }

        return response()->download(storage_path('app/public/' . $filePath));
    }

    private function areAllDocumentsApproved(EmployeeData $employee)
    {
        $documentFields = [
            'passport_photo_status', 'aadhar_doc_status', 'pan_doc_status',
            'driving_license_doc_status', 'voter_id_doc_status', 'passbook_image_status',
            'resume_status'
        ];

        // Add experience documents only for experienced employees
        if ($employee->experience_type === 'experienced') {
            $documentFields = array_merge($documentFields, [
                'prev_offer_letter_status', 'prev_appointment_letter_status',
                'prev_salary_slips_status', 'prev_relieving_letter_status', 'form_16_status'
            ]);
        }

        foreach ($documentFields as $field) {
            if ($employee->$field !== 'approved') {
                return false;
            }
        }
        return true;
    }

    private function getPendingDocuments(EmployeeData $employee)
    {
        $documents = [
            'passport_photo' => 'Passport Photo',
            'aadhar_doc' => 'Aadhar Document',
            'pan_doc' => 'PAN Document',
            'driving_license_doc' => 'Driving License',
            'voter_id_doc' => 'Voter ID Document',
            'passbook_image' => 'Bank Passbook',
            'resume' => 'Resume'
        ];

        // Add experience documents only for experienced employees
        if ($employee->experience_type === 'experienced') {
            $documents = array_merge($documents, [
                'prev_offer_letter' => 'Previous Offer Letter',
                'prev_appointment_letter' => 'Previous Appointment Letter',
                'prev_salary_slips' => 'Previous Salary Slips',
                'prev_relieving_letter' => 'Previous Relieving Letter',
                'form_16' => 'Form 16'
            ]);
        }

        $pending = [];
        foreach ($documents as $field => $label) {
            $statusField = $field . '_status';
            if ($employee->$statusField !== 'approved') {
                $pending[] = $label;
            }
        }
        return $pending;
    }
}
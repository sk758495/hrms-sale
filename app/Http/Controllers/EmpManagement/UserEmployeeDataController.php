<?php

namespace App\Http\Controllers\EmpManagement;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Position;
use App\Models\EmployeeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserEmployeeDataController extends Controller
{
    public function index()
    {
        $employeeData = EmployeeData::where('user_id', Auth::id())->first();
        return view('emp-management.employee-data.index', compact('employeeData'));
    }

    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();
        return view('emp-management.employee-data.create', compact('departments', 'positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'current_address' => 'required|string',
            'extra_mobile' => 'nullable|string',
            'aadhar_number' => 'nullable|string',
            'aadhar_doc' => 'nullable|file',
            'pan_number' => 'nullable|string',
            'pan_doc' => 'nullable|file',
            'driving_license' => 'nullable|string',
            'driving_license_doc' => 'nullable|file',
            'voter_id' => 'nullable|string',
            'voter_id_doc' => 'nullable|file',
            'bank_ifsc' => 'nullable|string',
            'bank_account' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'experience_type' => 'required|in:fresher,experience',
            'passport_photo' => 'nullable|file',
            'passbook_image' => 'nullable|file',
            'resume' => 'nullable|file',
            'prev_offer_letter' => 'nullable|file',
            'prev_appointment_letter' => 'nullable|file',
            'prev_salary_slips' => 'nullable|file',
            'prev_relieving_letter' => 'nullable|file',
            'form_16' => 'nullable|file',
        ]);

        $data['user_id'] = Auth::id();

        $fileFields = [
            'passport_photo', 'aadhar_doc', 'pan_doc', 'driving_license_doc',
            'voter_id_doc', 'passbook_image', 'resume', 'prev_offer_letter',
            'prev_appointment_letter', 'prev_salary_slips', 'prev_relieving_letter', 'form_16'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('uploads/employee', 'public');
            }
        }

        EmployeeData::create($data);

        return redirect()->route('user.employee-data.index')->with('success', 'Employee data submitted successfully!');
    }

    public function edit()
    {
        $employeeData = EmployeeData::where('user_id', Auth::id())->firstOrFail();
        $departments = Department::all();
        $positions = Position::all();
        return view('emp-management.employee-data.edit', compact('employeeData', 'departments', 'positions'));
    }

    public function update(Request $request)
    {
        $employeeData = EmployeeData::where('user_id', Auth::id())->firstOrFail();

        $data = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'current_address' => 'required|string',
            'extra_mobile' => 'nullable|string',
            'aadhar_number' => 'nullable|string',
            'aadhar_doc' => 'nullable|file',
            'pan_number' => 'nullable|string',
            'pan_doc' => 'nullable|file',
            'driving_license' => 'nullable|string',
            'driving_license_doc' => 'nullable|file',
            'voter_id' => 'nullable|string',
            'voter_id_doc' => 'nullable|file',
            'bank_ifsc' => 'nullable|string',
            'bank_account' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'experience_type' => 'required|in:fresher,experience',
            'passport_photo' => 'nullable|file',
            'passbook_image' => 'nullable|file',
            'resume' => 'nullable|file',
            'prev_offer_letter' => 'nullable|file',
            'prev_appointment_letter' => 'nullable|file',
            'prev_salary_slips' => 'nullable|file',
            'prev_relieving_letter' => 'nullable|file',
            'form_16' => 'nullable|file',
        ]);

        $fileFields = [
            'passport_photo', 'aadhar_doc', 'pan_doc', 'driving_license_doc',
            'voter_id_doc', 'passbook_image', 'resume', 'prev_offer_letter',
            'prev_appointment_letter', 'prev_salary_slips', 'prev_relieving_letter', 'form_16'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('uploads/employee', 'public');
            }
        }

        $employeeData->update($data);

        return redirect()->route('user.employee-data.index')->with('success', 'Employee data updated successfully!');
    }

    public function getPositions($department_id)
    {
        $positions = Position::where('department_id', $department_id)->get();
        return response()->json($positions);
    }

    public function reuploadDocument(Request $request)
    {
        $employeeData = EmployeeData::where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'document_type' => 'required|string',
            'document' => 'required|file'
        ]);

        $documentType = $request->document_type;
        $filePath = $request->file('document')->store('uploads/employee', 'public');
        
        $updateData = [
            $documentType => $filePath,
            $documentType . '_status' => 'pending',
            $documentType . '_remarks' => null
        ];

        $employeeData->update($updateData);

        return redirect()->route('user.employee-data.index')->with('success', 'Document re-uploaded successfully!');
    }
}
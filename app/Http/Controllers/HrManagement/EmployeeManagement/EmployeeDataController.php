<?php

namespace App\Http\Controllers\HrManagement\EmployeeManagement;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\EmployeeData;
use App\Models\User;
use App\Models\Position;

class EmployeeDataController extends Controller
{
    public function index()
    {
        $employees = EmployeeData::with(['user', 'department', 'position'])->latest()->get();
        return view('hr-management.employee.index', compact('employees'));
    }


    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->route('employee-data.index')->with('success', 'Employee status updated successfully.');
    }

    public function create()
    {
        $users = User::whereDoesntHave('employeeData')->get();
        $departments = Department::all();
        $positions = Position::all();

        return view('hr-management.employee.create', compact('users', 'departments', 'positions'));
    }

    public function store(Request $request)
    {
        // Validate input
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
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
            'ctc' => 'nullable|string',
            'bank_ifsc' => 'nullable|string',
            'bank_account' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'experience_type' => 'required|in:fresher,experience',

            // File fields
            'passport_photo' => 'nullable|file',
            'passbook_image' => 'nullable|file',
            'resume' => 'nullable|file',
            'prev_offer_letter' => 'nullable|file',
            'prev_appointment_letter' => 'nullable|file',
            'prev_salary_slips' => 'nullable|file',
            'prev_relieving_letter' => 'nullable|file',
            'form_16' => 'nullable|file',
        ]);

        // Process file uploads
        $fileFields = [
            'passport_photo',
            'aadhar_doc',
            'pan_doc',
            'driving_license_doc',
            'voter_id_doc',
            'passbook_image',
            'resume',
            'prev_offer_letter',
            'prev_appointment_letter',
            'prev_salary_slips',
            'prev_relieving_letter',
            'form_16',
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('uploads/employee', 'public');
            }
        }

        // Create new employee data record
        EmployeeData::create($data);

        return redirect()->back()->with('success', 'Employee data submitted successfully!');
    }


    public function edit(EmployeeData $employee)
    {
        $users = User::all();
        $departments = Department::all();
        $positions = Position::all();
        return view('hr-management.employee.edit', compact('employee', 'users', 'departments', 'positions'));
    }

    public function update(Request $request, EmployeeData $employee)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'current_address' => 'required|string',
            'extra_mobile' => 'nullable|string',
            'aadhar_number' => 'nullable|string',
            'pan_number' => 'nullable|string',
            'driving_license' => 'nullable|string',
            'voter_id' => 'nullable|string',
            'ctc' => 'nullable|string',
            'bank_ifsc' => 'nullable|string',
            'bank_account' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'experience_type' => 'required|in:fresher,experience',
            // file fields
            'passport_photo' => 'nullable|file',
            'aadhar_doc' => 'nullable|file',
            'pan_doc' => 'nullable|file',
            'driving_license_doc' => 'nullable|file',
            'voter_id_doc' => 'nullable|file',
            'passbook_image' => 'nullable|file',
            'resume' => 'nullable|file',
            'prev_offer_letter' => 'nullable|file',
            'prev_appointment_letter' => 'nullable|file',
            'prev_salary_slips' => 'nullable|file',
            'prev_relieving_letter' => 'nullable|file',
            'form_16' => 'nullable|file',
        ]);

        // Process file uploads
        $fileFields = [
            'passport_photo',
            'aadhar_doc',
            'pan_doc',
            'driving_license_doc',
            'voter_id_doc',
            'passbook_image',
            'resume',
            'prev_offer_letter',
            'prev_appointment_letter',
            'prev_salary_slips',
            'prev_relieving_letter',
            'form_16',
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('uploads/employee', 'public');
            }
        }

        $employee->update($data);

        return redirect()->route('employee-data.index')->with('success', 'Employee data updated successfully!');
    }

    public function destroy(EmployeeData $employee)
    {
        $employee->delete();
        return redirect()->route('employee-data.index')->with('success', 'Employee data deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\API\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeData;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class EmployeeDataController extends Controller
{
    public function index()
    {
        $employees = EmployeeData::with(['user', 'department', 'position'])->latest()->get();
        return response()->json($employees);
    }

    public function getAllUsers()
    {
        $users = User::select('id', 'name', 'employee_id', 'email')->get();
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function show($id)
    {
        $employee = EmployeeData::with(['user', 'department', 'position'])->findOrFail($id);
        return response()->json($employee);
    }

    public function toggleStatus($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json(['message' => 'Employee status updated.', 'is_active' => $user->is_active]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        foreach ($this->fileFields() as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('uploads/employee', 'public');
            }
        }

        $employee = EmployeeData::create($data);

        return response()->json(['message' => 'Employee created', 'employee' => $employee]);
    }

    public function update(Request $request, $id)
    {
        $employee = EmployeeData::findOrFail($id);
        $data = $this->validateData($request);

        foreach ($this->fileFields() as $field) {
            if ($request->hasFile($field)) {
                // Optionally delete old file here
                $data[$field] = $request->file($field)->store('uploads/employee', 'public');
            }
        }

        $employee->update($data);

        return response()->json(['message' => 'Employee updated', 'employee' => $employee]);
    }

    public function destroy($id)
    {
        $employee = EmployeeData::findOrFail($id);
        $employee->delete();

        return response()->json(['message' => 'Employee deleted']);
    }

    private function validateData(Request $request)
    {
        return $request->validate([
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
            'passport_photo' => 'nullable|file',
            'passbook_image' => 'nullable|file',
            'resume' => 'nullable|file',
            'prev_offer_letter' => 'nullable|file',
            'prev_appointment_letter' => 'nullable|file',
            'prev_salary_slips' => 'nullable|file',
            'prev_relieving_letter' => 'nullable|file',
            'form_16' => 'nullable|file',
        ]);
    }

    private function fileFields()
    {
        return [
            'passport_photo', 'aadhar_doc', 'pan_doc', 'driving_license_doc', 'voter_id_doc',
            'passbook_image', 'resume', 'prev_offer_letter', 'prev_appointment_letter',
            'prev_salary_slips', 'prev_relieving_letter', 'form_16',
        ];
    }
}

<?php

namespace App\Http\Controllers\API\HR;

use App\Http\Controllers\Controller;
use App\Models\AppointmentLetter;
use App\Models\EmployeeData;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentLetterApiController extends Controller
{
    public function index(Request $request)
    {
        $query = AppointmentLetter::with(['user', 'employeeData.department', 'employeeData.position']);

        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }

        if ($request->filled('department')) {
            $query->whereHas('employeeData', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointmentLetters = $query->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $appointmentLetters
        ]);
    }

    public function employees()
    {
        $employees = EmployeeData::with(['user', 'department', 'position'])
            ->whereDoesntHave('appointmentLetters')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $employees
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_data_id' => 'required|exists:employee_data,id',
            'appointment_date' => 'required|date',
            'joining_date' => 'required|date',
            'terms_conditions' => 'nullable|string'
        ]);

        $existing = AppointmentLetter::where('employee_data_id', $request->employee_data_id)->first();
        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'Appointment letter already exists for this employee.'
            ], 422);
        }

        $employeeData = EmployeeData::findOrFail($request->employee_data_id);

        $letter = AppointmentLetter::create([
            'user_id' => $employeeData->user_id,
            'employee_data_id' => $request->employee_data_id,
            'hr_id' => Auth::guard('sanctum')->id(), // ensure HR uses Sanctum
            'appointment_date' => $request->appointment_date,
            'joining_date' => $request->joining_date,
            'terms_conditions' => $request->terms_conditions,
            'status' => 'draft'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Appointment letter created successfully.',
            'data' => $letter
        ]);
    }

    public function show($id)
    {
        $letter = AppointmentLetter::with(['user', 'employeeData.department', 'employeeData.position', 'hr'])
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $letter
        ]);
    }

    public function destroy($id)
    {
        $letter = AppointmentLetter::findOrFail($id);
        $letter->delete();

        return response()->json([
            'status' => true,
            'message' => 'Appointment letter deleted successfully.'
        ]);
    }
}

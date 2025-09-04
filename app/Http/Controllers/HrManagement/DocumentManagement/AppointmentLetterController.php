<?php

namespace App\Http\Controllers\HrManagement\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\AppointmentLetter;
use App\Models\EmployeeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentLetterController extends Controller
{
    public function index(Request $request)
    {
        $query = AppointmentLetter::with(['user', 'employeeData.department', 'employeeData.position']);
        
        // Apply filters
        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }
        
        if ($request->filled('department')) {
            $query->whereHas('employeeData', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $appointmentLetters = $query->latest()->get();
        
        // Get data for filters
        $employees = EmployeeData::with('user')->get();
        $departments = \App\Models\Department::all();
        
        return view('hr-management.documents.appointment-letters.index', compact('appointmentLetters', 'employees', 'departments'));
    }

    public function create()
    {
        $employees = EmployeeData::with(['user', 'department', 'position'])
            ->whereDoesntHave('appointmentLetters')
            ->get();
        return view('hr-management.documents.appointment-letters.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_data_id' => 'required|exists:employee_data,id',
            'appointment_date' => 'required|date',
            'joining_date' => 'required|date',
            'terms_conditions' => 'nullable|string'
        ]);

        // Check if appointment letter already exists for this employee
        $existingLetter = AppointmentLetter::where('employee_data_id', $request->employee_data_id)->first();
        
        if ($existingLetter) {
            return redirect()->back()->withErrors(['employee_data_id' => 'Appointment letter already exists for this employee.'])->withInput();
        }

        $employeeData = EmployeeData::findOrFail($request->employee_data_id);

        AppointmentLetter::create([
            'user_id' => $employeeData->user_id,
            'employee_data_id' => $request->employee_data_id,
            'hr_id' => Auth::guard('hr')->id(),
            'appointment_date' => $request->appointment_date,
            'joining_date' => $request->joining_date,
            'terms_conditions' => $request->terms_conditions,
            'status' => 'draft'
        ]);

        return redirect()->route('appointment-letters.index')->with('success', 'Appointment letter created successfully!');
    }

    public function show(AppointmentLetter $appointmentLetter)
    {
        $appointmentLetter->load(['user', 'employeeData.department', 'employeeData.position', 'hr']);
        return view('hr-management.documents.appointment-letters.show', compact('appointmentLetter'));
    }

    public function destroy(AppointmentLetter $appointmentLetter)
    {
        $appointmentLetter->delete();
        return redirect()->route('appointment-letters.index')->with('success', 'Appointment letter deleted successfully!');
    }
}
<?php

namespace App\Http\Controllers\HrManagement\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\OfferLetter;
use App\Models\EmployeeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferLetterController extends Controller
{
    public function index(Request $request)
    {
        $query = OfferLetter::with(['user', 'employeeData.department', 'employeeData.position']);
        
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
        
        $offerLetters = $query->latest()->get();
        
        // Get data for filters
        $employees = EmployeeData::with('user')->get();
        $departments = \App\Models\Department::all();
        
        return view('hr-management.documents.offer-letters.index', compact('offerLetters', 'employees', 'departments'));
    }

    public function create()
    {
        $employees = EmployeeData::with(['user', 'department', 'position', 'appointmentLetters'])
            ->whereDoesntHave('offerLetters')
            ->get();
        return view('hr-management.documents.offer-letters.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_data_id' => 'required|exists:employee_data,id',
            'offer_date' => 'required|date',
            'joining_date' => 'required|date',
            'offered_salary' => 'required|numeric',
            'probation_period' => 'required|string',
            'job_description' => 'nullable|string'
        ]);

        // Check if offer letter already exists for this employee
        $existingLetter = OfferLetter::where('employee_data_id', $request->employee_data_id)->first();
        
        if ($existingLetter) {
            return redirect()->back()->withErrors(['employee_data_id' => 'Offer letter already exists for this employee.'])->withInput();
        }

        $employeeData = EmployeeData::findOrFail($request->employee_data_id);

        $offerLetter = OfferLetter::create([
            'user_id' => $employeeData->user_id,
            'employee_data_id' => $request->employee_data_id,
            'hr_id' => Auth::guard('hr')->id(),
            'offer_date' => $request->offer_date,
            'joining_date' => $request->joining_date,
            'offered_salary' => $request->offered_salary,
            'probation_period' => $request->probation_period,
            'job_description' => $request->job_description,
            'status' => 'draft'
        ]);

        // Send email notification
        $offerLetter->load(['user', 'employeeData.department', 'employeeData.position', 'hr']);
        \Illuminate\Support\Facades\Mail::to($offerLetter->user->email)
            ->send(new \App\Mail\OfferLetterNotification($offerLetter));

        return redirect()->route('offer-letters.index')->with('success', 'Offer letter created and email sent successfully!');
    }

    public function show(OfferLetter $offerLetter)
    {
        $offerLetter->load(['user', 'employeeData.department', 'employeeData.position', 'hr']);
        return view('hr-management.documents.offer-letters.show', compact('offerLetter'));
    }

    public function destroy(OfferLetter $offerLetter)
    {
        $offerLetter->delete();
        return redirect()->route('offer-letters.index')->with('success', 'Offer letter deleted successfully!');
    }
}
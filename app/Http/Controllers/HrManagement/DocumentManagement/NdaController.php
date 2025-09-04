<?php

namespace App\Http\Controllers\HrManagement\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\Nda;
use App\Models\EmployeeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NdaController extends Controller
{
    public function index()
    {
        $ndas = Nda::with(['user', 'employeeData.department', 'employeeData.position'])
            ->latest()
            ->get();
        
        return view('hr-management.documents.ndas.index', compact('ndas'));
    }

    public function create()
    {
        $employees = EmployeeData::with(['user', 'department', 'position'])
            ->whereDoesntHave('ndas')
            ->get();
        return view('hr-management.documents.ndas.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_data_id' => 'required|exists:employee_data,id',
            'nda_date' => 'required|date',
            'confidentiality_terms' => 'required|string',
            'validity_until' => 'nullable|date'
        ]);

        // Check if NDA already exists for this employee
        $existingNda = Nda::where('employee_data_id', $request->employee_data_id)->first();
        
        if ($existingNda) {
            return redirect()->back()->withErrors(['employee_data_id' => 'NDA already exists for this employee.'])->withInput();
        }

        $employeeData = EmployeeData::findOrFail($request->employee_data_id);

        Nda::create([
            'user_id' => $employeeData->user_id,
            'employee_data_id' => $request->employee_data_id,
            'hr_id' => Auth::guard('hr')->id(),
            'nda_date' => $request->nda_date,
            'confidentiality_terms' => $request->confidentiality_terms,
            'validity_until' => $request->validity_until,
            'status' => 'draft'
        ]);

        return redirect()->route('ndas.index')->with('success', 'NDA created successfully!');
    }

    public function show(Nda $nda)
    {
        $nda->load(['user', 'employeeData.department', 'employeeData.position', 'hr']);
        return view('hr-management.documents.ndas.show', compact('nda'));
    }

    public function destroy(Nda $nda)
    {
        $nda->delete();
        return redirect()->route('ndas.index')->with('success', 'NDA deleted successfully!');
    }
}
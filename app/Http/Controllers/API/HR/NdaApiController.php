<?php

namespace App\Http\Controllers\API\HR;

use App\Http\Controllers\Controller;
use App\Models\Nda;
use App\Models\EmployeeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NdaApiController extends Controller
{
    public function index()
    {
        $ndas = Nda::with(['user', 'employeeData.department', 'employeeData.position'])
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $ndas
        ]);
    }

    public function eligibleEmployees()
    {
        $employees = EmployeeData::with(['user', 'department', 'position'])
            ->whereDoesntHave('ndas')
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
            'nda_date' => 'required|date',
            'confidentiality_terms' => 'required|string',
            'validity_until' => 'nullable|date'
        ]);

        if (Nda::where('employee_data_id', $request->employee_data_id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'NDA already exists for this employee.'
            ], 422);
        }

        $employeeData = EmployeeData::findOrFail($request->employee_data_id);

        $nda = Nda::create([
            'user_id' => $employeeData->user_id,
            'employee_data_id' => $request->employee_data_id,
            'hr_id' => Auth::guard('sanctum')->id(),
            'nda_date' => $request->nda_date,
            'confidentiality_terms' => $request->confidentiality_terms,
            'validity_until' => $request->validity_until,
            'status' => 'draft'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'NDA created successfully.',
            'data' => $nda
        ]);
    }

    public function show($id)
    {
        $nda = Nda::with(['user', 'employeeData.department', 'employeeData.position', 'hr'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $nda
        ]);
    }

    public function destroy($id)
    {
        $nda = Nda::findOrFail($id);
        $nda->delete();

        return response()->json([
            'status' => true,
            'message' => 'NDA deleted successfully.'
        ]);
    }
}

<?php

namespace App\Http\Controllers\API\HR;

use App\Http\Controllers\Controller;
use App\Models\OfferLetter;
use App\Models\EmployeeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferLetterApiController extends Controller
{
    public function index(Request $request)
    {
        $query = OfferLetter::with(['user', 'employeeData.department', 'employeeData.position']);

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

        $offerLetters = $query->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $offerLetters
        ]);
    }

    public function eligibleEmployees()
    {
        $employees = EmployeeData::with(['user', 'department', 'position', 'appointmentLetters'])
            ->whereDoesntHave('offerLetters')
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
            'offer_date' => 'required|date',
            'joining_date' => 'required|date',
            'offered_salary' => 'required|numeric',
            'probation_period' => 'required|string',
            'job_description' => 'nullable|string'
        ]);

        if (OfferLetter::where('employee_data_id', $request->employee_data_id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Offer letter already exists for this employee.'
            ], 422);
        }

        $employeeData = EmployeeData::findOrFail($request->employee_data_id);

        $letter = OfferLetter::create([
            'user_id' => $employeeData->user_id,
            'employee_data_id' => $request->employee_data_id,
            'hr_id' => Auth::guard('sanctum')->id(),
            'offer_date' => $request->offer_date,
            'joining_date' => $request->joining_date,
            'offered_salary' => $request->offered_salary,
            'probation_period' => $request->probation_period,
            'job_description' => $request->job_description,
            'status' => 'draft'
        ]);

        // Send email notification
        $letter->load(['user', 'employeeData.department', 'employeeData.position', 'hr']);
        \Illuminate\Support\Facades\Mail::to($letter->user->email)
            ->send(new \App\Mail\OfferLetterNotification($letter));

        return response()->json([
            'status' => true,
            'message' => 'Offer letter created and email sent successfully.',
            'data' => $letter
        ]);
    }

    public function show($id)
    {
        $offerLetter = OfferLetter::with(['user', 'employeeData.department', 'employeeData.position', 'hr'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $offerLetter
        ]);
    }

    public function destroy($id)
    {
        $offerLetter = OfferLetter::findOrFail($id);
        $offerLetter->delete();

        return response()->json([
            'status' => true,
            'message' => 'Offer letter deleted successfully.'
        ]);
    }
}

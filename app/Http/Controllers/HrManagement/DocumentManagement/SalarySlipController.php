<?php

namespace App\Http\Controllers\HrManagement\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\SalarySlip;
use App\Models\SalaryStructure;
use App\Models\EmployeeData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalarySlipController extends Controller
{
    public function index(Request $request)
    {
        $query = SalarySlip::with(['user', 'employeeData.department', 'employeeData.position']);
        
        // Apply filters
        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }
        
        if ($request->filled('department')) {
            $query->whereHas('employeeData', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }
        
        if ($request->filled('month_year')) {
            $monthYear = date('F Y', strtotime($request->month_year . '-01'));
            $query->where('month_year', $monthYear);
        }
        
        $salarySlips = $query->latest()->get();
        
        // Get data for filters
        $employees = EmployeeData::with('user')->get();
        $departments = \App\Models\Department::all();
        
        return view('hr-management.documents.salary-slips.index', compact('salarySlips', 'employees', 'departments'));
    }

    public function create()
    {
        $users = User::whereHas('employeeData')->with('employeeData')->get();
        return view('hr-management.documents.salary-slips.create', compact('users'));
    }

    public function getEmployeeData(Request $request)
    {
        $user = User::with(['employeeData.department', 'employeeData.position'])->find($request->user_id);
        
        // Remove commas from CTC and convert to number
        $ctc = $user->employeeData->ctc ?? 0;
        if (is_string($ctc)) {
            $ctc = (float) str_replace(',', '', $ctc);
        }
        
        return response()->json([
            'user' => $user,
            'ctc' => $ctc
        ]);
    }

    public function checkExisting(Request $request)
    {
        $exists = SalarySlip::where('user_id', $request->user_id)
            ->where('month_year', $request->month_year)
            ->exists();
        
        return response()->json(['exists' => $exists]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month_year' => 'required|string',
            'present_days' => 'required|integer',
            'leave_taken' => 'required|integer',
            'balance_leave' => 'required|integer',
            'basic_salary' => 'required|numeric',
            'hra' => 'required|numeric',
            'traveling_allowance' => 'required|numeric',
            'other_allowances' => 'required|numeric',
            'miscellaneous' => 'required|numeric',
            'professional_tax' => 'required|numeric',
            'advance_pay' => 'required|numeric',
            'arrears_deductions' => 'required|numeric',
            'payment_date' => 'required|date'
        ]);

        // Check if salary slip already exists for this month
        $existingSlip = SalarySlip::where('user_id', $request->user_id)
            ->where('month_year', $request->month_year)
            ->first();
        
        if ($existingSlip) {
            return redirect()->back()->withErrors(['month_year' => 'Salary slip for this month already exists for this employee.'])->withInput();
        }

        $user = User::with('employeeData')->findOrFail($request->user_id);
        
        $totalEarnings = $request->basic_salary + $request->hra + $request->traveling_allowance + 
                        $request->other_allowances + $request->miscellaneous;
        
        $totalDeductions = $request->professional_tax + $request->advance_pay + $request->arrears_deductions;
        
        $netSalary = $totalEarnings - $totalDeductions;

        SalarySlip::create([
            'user_id' => $request->user_id,
            'employee_data_id' => $user->employeeData->id,
            'hr_id' => Auth::guard('hr')->id(),
            'month_year' => $request->month_year,
            'joining_date' => $user->employeeData->created_at->toDateString(),
            'present_days' => $request->present_days,
            'leave_taken' => $request->leave_taken,
            'balance_leave' => $request->balance_leave,
            'basic_salary' => $request->basic_salary,
            'hra' => $request->hra,
            'traveling_allowance' => $request->traveling_allowance,
            'other_allowances' => $request->other_allowances,
            'miscellaneous' => $request->miscellaneous,
            'professional_tax' => $request->professional_tax,
            'advance_pay' => $request->advance_pay,
            'arrears_deductions' => $request->arrears_deductions,
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'payment_date' => $request->payment_date
        ]);

        return redirect()->route('salary-slips.index')->with('success', 'Salary slip created successfully!');
    }

    public function show(SalarySlip $salarySlip)
    {
        $salarySlip->load(['user', 'employeeData.department', 'employeeData.position', 'hr']);
        return view('hr-management.documents.salary-slips.show', compact('salarySlip'));
    }

    public function setupSalaryStructure()
    {
        $users = User::whereHas('employeeData')->with('employeeData')->get();
        return view('hr-management.documents.salary-slips.setup', compact('users'));
    }

    public function storeSalaryStructure(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'basic_salary' => 'required|numeric',
            'hra_percentage' => 'required|numeric',
            'traveling_allowance' => 'required|numeric',
            'other_allowances' => 'required|numeric',
            'professional_tax' => 'required|numeric'
        ]);

        $user = User::with('employeeData')->findOrFail($request->user_id);

        SalaryStructure::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'employee_data_id' => $user->employeeData->id,
                'basic_salary' => $request->basic_salary,
                'hra_percentage' => $request->hra_percentage,
                'traveling_allowance' => $request->traveling_allowance,
                'other_allowances' => $request->other_allowances,
                'professional_tax' => $request->professional_tax
            ]
        );

        return redirect()->route('salary-slips.setup')->with('success', 'Salary structure saved successfully!');
    }
}
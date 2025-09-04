<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    protected $fillable = [
        'user_id',
        'employee_data_id',
        'hr_id',
        'month_year',
        'joining_date',
        'present_days',
        'leave_taken',
        'balance_leave',
        'basic_salary',
        'hra',
        'traveling_allowance',
        'other_allowances',
        'miscellaneous',
        'professional_tax',
        'advance_pay',
        'arrears_deductions',
        'total_earnings',
        'total_deductions',
        'net_salary',
        'payment_date'
    ];

    protected $casts = [
        'joining_date' => 'date',
        'payment_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employeeData()
    {
        return $this->belongsTo(EmployeeData::class);
    }

    public function hr()
    {
        return $this->belongsTo(HR::class);
    }
}
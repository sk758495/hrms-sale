<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    protected $fillable = [
        'user_id',
        'employee_data_id',
        'basic_salary',
        'hra_percentage',
        'traveling_allowance',
        'other_allowances',
        'professional_tax'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employeeData()
    {
        return $this->belongsTo(EmployeeData::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentLetter extends Model
{
    protected $fillable = [
        'user_id',
        'employee_data_id',
        'hr_id',
        'appointment_date',
        'joining_date',
        'terms_conditions',
        'status'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'joining_date' => 'date',
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
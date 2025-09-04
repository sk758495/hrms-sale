<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferLetter extends Model
{
    protected $fillable = [
        'user_id',
        'employee_data_id',
        'hr_id',
        'offer_date',
        'joining_date',
        'offered_salary',
        'probation_period',
        'job_description',
        'status'
    ];

    protected $casts = [
        'offer_date' => 'date',
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
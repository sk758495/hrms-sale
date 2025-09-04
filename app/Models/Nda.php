<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nda extends Model
{
    protected $fillable = [
        'user_id',
        'employee_data_id',
        'hr_id',
        'nda_date',
        'confidentiality_terms',
        'validity_until',
        'status'
    ];

    protected $casts = [
        'nda_date' => 'date',
        'validity_until' => 'date',
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
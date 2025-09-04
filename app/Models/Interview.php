<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Interview extends Model
{
    protected $fillable = [
        'department_id',
        'position_id',
        'interviewer_name',
        'contact_number',
        'email',
        'resume',
        'employee_type',
        'current_salary',
        'expected_salary',
        'status',
        'remark_1',
        'remark_1_created_at',
        'remark_2',
        'remark_2_created_at',
        'remark_3',
        'remark_3_created_at',
        'interview_date'
    ];

    protected $casts = [
        'interview_date' => 'datetime',
        'remark_1_created_at' => 'datetime',
        'remark_2_created_at' => 'datetime',
        'remark_3_created_at' => 'datetime',
        'current_salary' => 'decimal:2',
        'expected_salary' => 'decimal:2'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    // Scope for sorting by closest future interview date
    public function scopeUpcoming($query)
    {
        return $query->where('interview_date', '>=', now())
                    ->orderBy('interview_date', 'asc');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('interview_date', today());
    }

    public function scopePast($query)
    {
        return $query->where('interview_date', '<', now())
                    ->orderBy('interview_date', 'desc');
    }
}

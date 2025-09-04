<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'latitude',
        'longitude',
        'status',
        'working_hours',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isCheckedIn()
    {
        return $this->check_in && !$this->check_out;
    }

    public function getOfficeHoursAttribute()
    {
        if ($this->check_in && $this->check_out) {
            $minutes = $this->check_in->diffInMinutes($this->check_out);
            return round($minutes / 60, 2);
        }
        return 0;
    }
}
<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmployeeData extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'position_id',
        'passport_photo',
        'current_address',
        'extra_mobile',
        'aadhar_number',
        'aadhar_doc',
        'pan_number',
        'pan_doc',
        'driving_license',
        'driving_license_doc',
        'voter_id',
        'voter_id_doc',
        'ctc',
        'bank_ifsc',
        'bank_account',
        'bank_name',
        'passbook_image',
        'resume',
        'experience_type',
        'prev_offer_letter',
        'prev_appointment_letter',
        'prev_salary_slips',
        'prev_relieving_letter',
        'form_16',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully!');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function appointmentLetters()
    {
        return $this->hasMany(AppointmentLetter::class);
    }

    public function offerLetters()
    {
        return $this->hasMany(OfferLetter::class);
    }

    public function ndas()
    {
        return $this->hasMany(Nda::class);
    }

    public function salarySlips()
    {
        return $this->hasMany(SalarySlip::class);
    }
}

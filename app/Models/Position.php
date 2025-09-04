<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['name', 'department_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
  public function employeeData()
{
    return $this->hasMany(EmployeeData::class);
}

}

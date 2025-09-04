<?php

namespace App\Http\Controllers\HrManagement\EmployeeManagement;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
     public function index()
    {
        $positions = Position::with('department')->latest()->get();
        return view('hr-management.employee.positions.index', compact('positions'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('hr-management.employee.positions.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string'
        ]);
        Position::create($request->only('department_id', 'name'));
        return redirect()->route('positions.index')->with('success', 'Position added!');
    }

    public function edit(Position $position)
    {
        $departments = Department::all();
        return view('hr-management.employee.positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string'
        ]);
        $position->update($request->only('department_id', 'name'));
        return redirect()->route('positions.index')->with('success', 'Position updated!');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()->back()->with('success', 'Position deleted!');
    }
}

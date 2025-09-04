<?php

namespace App\Http\Controllers\HrManagement\EmployeeManagement;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::latest()->get();
        return view('hr-management.employee.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('hr-management.employee.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:departments']);
        Department::create($request->only('name'));
        return redirect()->route('departments.index')->with('success', 'Department added successfully!');
    }

    public function edit($id)
{
    $department = Department::findOrFail($id);
    return view('hr-management.employee.departments.edit', compact('department'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $department = Department::findOrFail($id);
    $department->name = $request->name;
    $department->save();

    return redirect()->route('departments.index')->with('success', 'Department updated successfully!');
}

public function destroy($id)
{
    Department::findOrFail($id)->delete();
    return redirect()->route('departments.index')->with('success', 'Department deleted successfully!');
}

}

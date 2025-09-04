<?php

namespace App\Http\Controllers\API\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentApiController extends Controller
{
    public function index()
    {
        $departments = Department::latest()->get();
        return response()->json([
            'status' => true,
            'data' => $departments
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:departments'
        ]);

        $department = Department::create(['name' => $request->name]);

        return response()->json([
            'status' => true,
            'message' => 'Department added successfully.',
            'data' => $department
        ]);
    }

    public function show($id)
    {
        $department = Department::findOrFail($id);
        return response()->json([
            'status' => true,
            'data' => $department
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $department = Department::findOrFail($id);
        $department->update(['name' => $request->name]);

        return response()->json([
            'status' => true,
            'message' => 'Department updated successfully.',
            'data' => $department
        ]);
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json([
            'status' => true,
            'message' => 'Department deleted successfully.'
        ]);
    }
}

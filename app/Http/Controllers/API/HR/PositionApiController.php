<?php

namespace App\Http\Controllers\API\HR;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;

class PositionApiController extends Controller
{
    public function index()
    {
        $positions = Position::with('department')->latest()->get();
        return response()->json([
            'status' => true,
            'data' => $positions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255'
        ]);

        $position = Position::create($request->only('department_id', 'name'));

        return response()->json([
            'status' => true,
            'message' => 'Position added successfully.',
            'data' => $position
        ]);
    }

    public function show($id)
    {
        $position = Position::with('department')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $position
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255'
        ]);

        $position = Position::findOrFail($id);
        $position->update($request->only('department_id', 'name'));

        return response()->json([
            'status' => true,
            'message' => 'Position updated successfully.',
            'data' => $position
        ]);
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return response()->json([
            'status' => true,
            'message' => 'Position deleted successfully.'
        ]);
    }
}


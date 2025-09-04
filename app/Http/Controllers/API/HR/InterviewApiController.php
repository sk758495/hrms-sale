<?php

namespace App\Http\Controllers\API\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interview;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\InterviewConfirmationMail;
use App\Mail\InterviewRejectionMail;
use Illuminate\Support\Facades\Log;

class InterviewApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Interview::with(['department', 'position'])
            ->whereIn('status', ['Pending', 'Next Round']);

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('position')) {
            $query->where('position_id', $request->position);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_type')) {
            $query->where('employee_type', $request->employee_type);
        }

        return response()->json(['data' => $query->orderBy('interview_date', 'asc')->get()]);
    }

    public function rejected()
    {
        $rejectedInterviews = Interview::with(['department', 'position'])
            ->where(function($query) {
                $query->where('status', 'like', 'Rejection%')
                      ->orWhere('status', 'like', 'Reject%')
                      ->orWhere('status', 'Rejected');
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json(['data' => $rejectedInterviews]);
    }

    public function completed(Request $request)
    {
        $query = Interview::with(['department', 'position'])
            ->where('status', 'Confirm');

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('position')) {
            $query->where('position_id', $request->position);
        }

        if ($request->filled('employee_type')) {
            $query->where('employee_type', $request->employee_type);
        }

        return response()->json(['data' => $query->orderBy('interview_date', 'desc')->get()]);
    }

    public function show($id)
    {
        $interview = Interview::with(['department', 'position'])->findOrFail($id);
        return response()->json(['data' => $interview]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'interviewer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'employee_type' => 'required|in:Fresher,Experienced',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'interview_date' => 'required|date',
        ]);

        $data = $request->except(['resume']);

        if ($request->hasFile('resume')) {
            $data['resume'] = $request->file('resume')->store('resumes', 'public');
        }

        foreach (['remark_1', 'remark_2', 'remark_3'] as $field) {
            if ($request->filled($field)) {
                $data["{$field}_created_at"] = now();
            }
        }

        $interview = Interview::create($data);

        return response()->json(['status' => true, 'message' => 'Interview created.', 'data' => $interview]);
    }

    public function update(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'interviewer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'employee_type' => 'required|in:Fresher,Experienced',
            'status' => 'required',
            'interview_date' => 'required|date',
        ]);

        $data = $request->except(['resume']);

        if ($request->hasFile('resume')) {
            if ($interview->resume) {
                Storage::disk('public')->delete($interview->resume);
            }
            $data['resume'] = $request->file('resume')->store('resumes', 'public');
        }

        foreach (['remark_1', 'remark_2', 'remark_3'] as $field) {
            if ($request->filled($field) && $request->$field !== $interview->$field) {
                $data[$field] = $request->$field;
                $data["{$field}_created_at"] = now();
            }
        }

        $interview->update($data);

        return response()->json(['status' => true, 'message' => 'Interview updated.']);
    }

    public function updateStatus(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);
        $request->validate([
            'status' => 'required',
            'remark_1' => 'nullable|string',
            'remark_2' => 'nullable|string',
            'next_interview_date' => 'nullable|date|required_if:status,Next Round'
        ]);

        $data = ['status' => $request->status];

        foreach (['remark_1', 'remark_2'] as $field) {
            if ($request->filled($field) && $request->$field !== $interview->$field) {
                $data[$field] = $request->$field;
                $data["{$field}_created_at"] = now();
            }
        }

        if ($request->status === 'Next Round' && $request->filled('next_interview_date')) {
            $data['interview_date'] = $request->next_interview_date;
        }

        $interview->update($data);

        try {
            $hrName = Auth::guard('sanctum')->user()->name;
            if ($request->status === 'Confirm') {
                Mail::to($interview->email)->send(new InterviewConfirmationMail($interview, $hrName));
            } elseif (str_contains($request->status, 'Reject')) {
                $type = match ($request->status) {
                    'Rejection - No open Position' => 'no_position',
                    'Rejection - High CTC expectation' => 'high_ctc',
                    default => 'after_interview'
                };
                Mail::to($interview->email)->send(new InterviewRejectionMail($interview, $hrName, $type));
            }
        } catch (\Exception $e) {
            Log::error('Email failed: ' . $e->getMessage());
        }

        return response()->json(['status' => true, 'message' => 'Status updated.']);
    }

    public function destroy($id)
    {
        $interview = Interview::findOrFail($id);
        if ($interview->resume) {
            Storage::disk('public')->delete($interview->resume);
        }
        $interview->delete();

        return response()->json(['status' => true, 'message' => 'Interview deleted.']);
    }

    public function getPositions($department_id)
    {
        return response()->json(Position::where('department_id', $department_id)->get());
    }

    public function downloadResume($id)
    {
        try {
            $interview = Interview::findOrFail($id);
            
            if (!$interview->resume) {
                return response()->json(['error' => 'No resume uploaded for this interview.'], 404);
            }

            $filePath = storage_path("app/public/{$interview->resume}");
            
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'Resume file not found on server.'], 404);
            }

            $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $interview->interviewer_name);
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $fileName = $safeName . "_Resume." . $ext;

            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to download resume.'], 500);
        }
    }
}

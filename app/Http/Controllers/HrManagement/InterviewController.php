<?php

namespace App\Http\Controllers\HrManagement;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\InterviewConfirmationMail;
use App\Mail\InterviewRejectionMail;
use Illuminate\Support\Facades\Log;

class InterviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Interview::with(['department', 'position'])
            ->whereIn('status', ['Pending', 'Next Round']);

        // Apply filters
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

        // Get all interviews with simple sorting
        $interviews = $query->orderBy('interview_date', 'asc')->get();

        $departments = Department::all();
        $positions = Position::all();

        return view('hr-management.interviews.index', compact('interviews', 'departments', 'positions'));
    }

    public function completed(Request $request)
    {
        $query = Interview::with(['department', 'position'])
            ->where('status', 'Confirm');

        // Apply filters
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('position')) {
            $query->where('position_id', $request->position);
        }

        if ($request->filled('employee_type')) {
            $query->where('employee_type', $request->employee_type);
        }

        $interviews = $query->orderBy('interview_date', 'desc')->get();

        $departments = Department::all();
        $positions = Position::all();

        return view('hr-management.interviews.completed', compact('interviews', 'departments', 'positions'));
    }

    public function rejected(Request $request)
    {
        $query = Interview::with(['department', 'position'])
            ->where('status', 'like', 'Reject%');

        // Apply filters
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

        $interviews = $query->orderBy('interview_date', 'desc')->get();

        $departments = Department::all();
        $positions = Position::all();

        return view('hr-management.interviews.rejected', compact('interviews', 'departments', 'positions'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('hr-management.interviews.create', compact('departments'));
    }

    public function getPositions($department_id)
    {
        $positions = Position::where('department_id', $department_id)->get();
        return response()->json($positions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'interviewer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'employee_type' => 'required|in:Fresher,Experienced',
            'current_salary' => 'nullable|numeric|min:0',
            'expected_salary' => 'nullable|numeric|min:0',
            'interview_date' => 'required|date',
            'remark_1' => 'nullable|string',
            'remark_2' => 'nullable|string',
            'remark_3' => 'nullable|string'
        ]);

        $data = $request->all();

        // Handle file upload
        if ($request->hasFile('resume')) {
            $data['resume'] = $request->file('resume')->store('resumes', 'public');
        }

        // Set remark timestamps
        if ($request->filled('remark_1')) {
            $data['remark_1_created_at'] = now();
        }
        if ($request->filled('remark_2')) {
            $data['remark_2_created_at'] = now();
        }
        if ($request->filled('remark_3')) {
            $data['remark_3_created_at'] = now();
        }

        Interview::create($data);

        return redirect()->route('interviews.index')->with('success', 'Interview scheduled successfully!');
    }

    public function show(Interview $interview)
    {
        $interview->load(['department', 'position']);
        return view('hr-management.interviews.show', compact('interview'));
    }

    public function edit(Interview $interview)
    {
        $departments = Department::all();
        $positions = Position::where('department_id', $interview->department_id)->get();
        return view('hr-management.interviews.edit', compact('interview', 'departments', 'positions'));
    }

    public function update(Request $request, Interview $interview)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'interviewer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'employee_type' => 'required|in:Fresher,Experienced',
            'current_salary' => 'nullable|numeric|min:0',
            'expected_salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:Pending,Confirm,Next Round,Reject after interview,Rejection - No open Position,Rejection - High CTC expectation',
            'interview_date' => 'required|date',
            'remark_1' => 'nullable|string',
            'remark_2' => 'nullable|string',
            'remark_3' => 'nullable|string'
        ]);

        $data = $request->all();

        // Handle file upload
        if ($request->hasFile('resume')) {
            if ($interview->resume) {
                Storage::disk('public')->delete($interview->resume);
            }
            $data['resume'] = $request->file('resume')->store('resumes', 'public');
        }

        // Update remark timestamps only if remark content changed
        if ($request->filled('remark_1') && $request->remark_1 !== $interview->remark_1) {
            $data['remark_1_created_at'] = now();
        }
        if ($request->filled('remark_2') && $request->remark_2 !== $interview->remark_2) {
            $data['remark_2_created_at'] = now();
        }
        if ($request->filled('remark_3') && $request->remark_3 !== $interview->remark_3) {
            $data['remark_3_created_at'] = now();
        }

        $interview->update($data);

        return redirect()->route('interviews.index')->with('success', 'Interview updated successfully!');
    }

    public function updateStatus(Request $request, Interview $interview)
    {
        $request->validate([
            'status' => 'required|in:Pending,Confirm,Next Round,Reject after interview,Rejection - No open Position,Rejection - High CTC expectation',
            'remark_1' => 'nullable|string',
            'remark_2' => 'nullable|string',
            'next_interview_date' => 'nullable|date|required_if:status,Next Round'
        ]);

        $oldStatus = $interview->status;
        $newStatus = $request->status;

        $data = [
            'status' => $newStatus
        ];

        if ($request->filled('remark_1') && $request->remark_1 !== $interview->remark_1) {
            $data['remark_1'] = $request->remark_1;
            $data['remark_1_created_at'] = now();
        }

        if ($request->filled('remark_2') && $request->remark_2 !== $interview->remark_2) {
            $data['remark_2'] = $request->remark_2;
            $data['remark_2_created_at'] = now();
        }

        if ($newStatus === 'Next Round' && $request->filled('next_interview_date')) {
            $data['interview_date'] = $request->next_interview_date;
        }

        $interview->update($data);

        // Send email if status changed to final status
        if ($oldStatus !== $newStatus && in_array($newStatus, ['Confirm', 'Reject after interview', 'Rejection - No open Position', 'Rejection - High CTC expectation'])) {
            $hrName = Auth::guard('hr')->user()->name;

            try {
                if ($newStatus === 'Confirm') {
                    Mail::to($interview->email)->send(new InterviewConfirmationMail($interview, $hrName));
                } else {
                    $rejectionType = match ($newStatus) {
                        'Rejection - No open Position' => 'no_position',
                        'Rejection - High CTC expectation' => 'high_ctc',
                        'Reject after interview' => 'after_interview',
                        default => 'after_interview'
                    };
                    Mail::to($interview->email)->send(new InterviewRejectionMail($interview, $hrName, $rejectionType));
                }
            } catch (\Exception $e) {
                // Log error but don't fail the status update
                Log::error('Failed to send interview status email: ' . $e->getMessage());
            }
        }

        return redirect()->route('interviews.show', $interview)->with('success', 'Status updated successfully!');
    }

    public function downloadResume(Interview $interview)
    {
        if (!$interview->resume) {
            abort(404, 'Resume not found');
        }

        $filePath = storage_path('app/public/' . $interview->resume);

        if (!file_exists($filePath)) {
            abort(404, 'Resume file not found');
        }

        // Get the original extension of the file
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        // Sanitize interviewer name and create a clean file name
        $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $interview->interviewer_name);

        // Define download file name
        $downloadName = $safeName . '_Resume.' . $extension;

        // Return file with custom name
        return response()->download($filePath, $downloadName);
    }

    public function destroy(Interview $interview)
    {
        if ($interview->resume) {
            Storage::disk('public')->delete($interview->resume);
        }

        $interview->delete();
        return redirect()->route('interviews.index')->with('success', 'Interview deleted successfully!');
    }
}

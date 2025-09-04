@extends('layouts.hr-app')

@section('title', 'Interview Details')

@section('content')
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="bi bi-calendar-check"></i> Interview Details</h4>
                        <div>
                            <a href="{{ route('interviews.edit', $interview) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('interviews.index') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Basic Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Interviewer Name:</strong></td>
                                        <td>{{ $interview->interviewer_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Contact Number:</strong></td>
                                        <td>{{ $interview->contact_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $interview->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Department:</strong></td>
                                        <td>{{ $interview->department->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Position:</strong></td>
                                        <td>{{ $interview->position->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Employee Type:</strong></td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $interview->employee_type == 'Fresher' ? 'info' : 'success' }}">
                                                {{ $interview->employee_type }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $interview->status == 'Pending' ? 'warning' : ($interview->status == 'Completed' ? 'success' : ($interview->status == 'Cancelled' ? 'danger' : 'primary')) }}">
                                                {{ $interview->status }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Interview Details</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Interview Date:</strong></td>
                                        <td>{{ $interview->interview_date->format('d M Y, h:i A') }}</td>
                                    </tr>
                                    @if ($interview->employee_type == 'Experienced')
                                        <tr>
                                            <td><strong>Current Salary:</strong></td>
                                            <td>₹{{ number_format($interview->current_salary, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Expected Salary:</strong></td>
                                            <td>₹{{ number_format($interview->expected_salary, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($interview->resume)
                                        <tr>
                                            <td><strong>Resume:</strong></td>
                                            <td>
                                                <a href="{{ route('interviews.download-resume', $interview) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i> Download Resume
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <hr>
                        <h5>Status Update & Remarks</h5>

                        <form method="POST" action="{{ route('interviews.update-status', $interview) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Update Status</label>
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="Pending"
                                                {{ $interview->status == 'Pending' ? 'selected' : '' }}>Pending
                                            </option>
                                            <option value="Next Round"
                                                {{ $interview->status == 'Next Round' ? 'selected' : '' }}>Next Round
                                            </option>
                                            <option value="Confirm"
                                                {{ $interview->status == 'Confirm' ? 'selected' : '' }}>Confirm
                                            </option>
                                            <option value="Reject after interview"
                                                {{ $interview->status == 'Reject after interview' ? 'selected' : '' }}>
                                                Reject after interview</option>
                                            <option value="Rejection - No open Position"
                                                {{ $interview->status == 'Rejection - No open Position' ? 'selected' : '' }}>
                                                Rejection - No open Position</option>
                                            <option value="Rejection - High CTC expectation"
                                                {{ $interview->status == 'Rejection - High CTC expectation' ? 'selected' : '' }}>
                                                Rejection - High CTC expectation</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="remark_1" class="form-label">Remark 1</label>
                                        <textarea name="remark_1" id="remark_1" class="form-control" rows="3" placeholder="Add your remark here...">{{ $interview->remark_1 }}</textarea>
                                        @if ($interview->remark_1_created_at)
                                            <small class="text-muted">Last updated:
                                                {{ $interview->remark_1_created_at->format('d M Y, h:i A') }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Next Interview Date (shown when Next Round is selected) -->
                            <div id="next_interview_date_field" style="display: none;">
                                <div class="mb-3">
                                    <label for="next_interview_date" class="form-label">Next Interview Date &
                                        Time</label>
                                    <input type="datetime-local" name="next_interview_date" id="next_interview_date"
                                        class="form-control">
                                </div>
                            </div>

                            @if ($interview->status == 'Next Round')
                                <div class="mb-3">
                                    <label for="remark_2" class="form-label">Remark 2</label>
                                    <textarea name="remark_2" id="remark_2" class="form-control" rows="3" placeholder="Add remark for next round...">{{ $interview->remark_2 }}</textarea>
                                    @if ($interview->remark_2_created_at)
                                        <small class="text-muted">Last updated:
                                            {{ $interview->remark_2_created_at->format('d M Y, h:i A') }}</small>
                                    @endif
                                </div>
                            @endif

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Update Status & Remarks
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
<script>
    // Show/hide next interview date field based on status selection
    document.getElementById('status').addEventListener('change', function() {
        const nextDateField = document.getElementById('next_interview_date_field');
        if (this.value === 'Next Round') {
            nextDateField.style.display = 'block';
            document.getElementById('next_interview_date').required = true;
        } else {
            nextDateField.style.display = 'none';
            document.getElementById('next_interview_date').required = false;
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        if (statusSelect.value === 'Next Round') {
            document.getElementById('next_interview_date_field').style.display = 'block';
            document.getElementById('next_interview_date').required = true;
        }
    });
</script>
@endpush

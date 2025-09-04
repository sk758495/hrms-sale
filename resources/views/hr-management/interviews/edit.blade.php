<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Interview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('hr.dashboard') }}">🏢 HR System</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="bi bi-pencil"></i> Edit Interview</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('interviews.update', $interview) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="department_id" class="form-label">Department</label>
                                        <select name="department_id" id="department_id" class="form-select" required>
                                            <option value="">Select Department</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ $interview->department_id == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="position_id" class="form-label">Position</label>
                                        <select name="position_id" id="position_id" class="form-select" required>
                                            <option value="">Select Position</option>
                                            @foreach($positions as $position)
                                                <option value="{{ $position->id }}" {{ $interview->position_id == $position->id ? 'selected' : '' }}>
                                                    {{ $position->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('position_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="interviewer_name" class="form-label">Interviewer Name</label>
                                        <input type="text" name="interviewer_name" id="interviewer_name" class="form-control" value="{{ $interview->interviewer_name }}" required>
                                        @error('interviewer_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_number" class="form-label">Contact Number</label>
                                        <input type="text" name="contact_number" id="contact_number" class="form-control" value="{{ $interview->contact_number }}" required>
                                        @error('contact_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ $interview->email }}" required>
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="resume" class="form-label">Resume</label>
                                        <input type="file" name="resume" id="resume" class="form-control" accept=".pdf,.doc,.docx">
                                        @if($interview->resume)
                                            <small class="text-muted">Current: <a href="{{ route('interviews.download-resume', $interview) }}">View Resume</a></small>
                                        @endif
                                        @error('resume')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="employee_type" class="form-label">Employee Type</label>
                                        <select name="employee_type" id="employee_type" class="form-select" required>
                                            <option value="">Select Type</option>
                                            <option value="Fresher" {{ $interview->employee_type == 'Fresher' ? 'selected' : '' }}>Fresher</option>
                                            <option value="Experienced" {{ $interview->employee_type == 'Experienced' ? 'selected' : '' }}>Experienced</option>
                                        </select>
                                        @error('employee_type')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Salary fields -->
                            <div id="salary_fields" style="{{ $interview->employee_type == 'Experienced' ? 'display: block;' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="current_salary" class="form-label">Current Salary</label>
                                            <input type="number" name="current_salary" id="current_salary" class="form-control" step="0.01" value="{{ $interview->current_salary }}">
                                            @error('current_salary')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="expected_salary" class="form-label">Expected Salary</label>
                                            <input type="number" name="expected_salary" id="expected_salary" class="form-control" step="0.01" value="{{ $interview->expected_salary }}">
                                            @error('expected_salary')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="Pending" {{ $interview->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Scheduled" {{ $interview->status == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            <option value="Completed" {{ $interview->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="Cancelled" {{ $interview->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                        @error('status')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="interview_date" class="form-label">Interview Date & Time</label>
                                        <input type="datetime-local" name="interview_date" id="interview_date" class="form-control" value="{{ $interview->interview_date->format('Y-m-d\TH:i') }}" required>
                                        @error('interview_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Remarks Section -->
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <textarea name="remark_1" class="form-control" rows="3" placeholder="Remark 1">{{ $interview->remark_1 }}</textarea>
                                        @if($interview->remark_1_created_at)
                                            <small class="text-muted">Last updated: {{ $interview->remark_1_created_at->format('d M Y, h:i A') }}</small>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <textarea name="remark_2" class="form-control" rows="3" placeholder="Remark 2">{{ $interview->remark_2 }}</textarea>
                                        @if($interview->remark_2_created_at)
                                            <small class="text-muted">Last updated: {{ $interview->remark_2_created_at->format('d M Y, h:i A') }}</small>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <textarea name="remark_3" class="form-control" rows="3" placeholder="Remark 3">{{ $interview->remark_3 }}</textarea>
                                        @if($interview->remark_3_created_at)
                                            <small class="text-muted">Last updated: {{ $interview->remark_3_created_at->format('d M Y, h:i A') }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('interviews.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Interview</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Department-Position dependency
        document.getElementById('department_id').addEventListener('change', function() {
            const departmentId = this.value;
            const positionSelect = document.getElementById('position_id');
            
            positionSelect.innerHTML = '<option value="">Select Position</option>';
            
            if (departmentId) {
                fetch(`/hr/interviews/positions/${departmentId}`)
                    .then(response => response.json())
                    .then(positions => {
                        positions.forEach(position => {
                            const option = document.createElement('option');
                            option.value = position.id;
                            option.textContent = position.name;
                            if (position.id == {{ $interview->position_id }}) {
                                option.selected = true;
                            }
                            positionSelect.appendChild(option);
                        });
                    });
            }
        });

        // Employee type conditional logic
        document.getElementById('employee_type').addEventListener('change', function() {
            const salaryFields = document.getElementById('salary_fields');
            if (this.value === 'Experienced') {
                salaryFields.style.display = 'block';
            } else {
                salaryFields.style.display = 'none';
                document.getElementById('current_salary').value = '';
                document.getElementById('expected_salary').value = '';
            }
        });
    </script>
</body>
</html>
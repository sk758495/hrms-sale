<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Offer Letter</title>
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
                        <h4><i class="bi bi-file-earmark-check"></i> Create Offer Letter</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('offer-letters.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="employee_data_id" class="form-label">Select Employee</label>
                                @if($employees->count() > 0)
                                    <select name="employee_data_id" id="employee_data_id" class="form-control" required>
                                        <option value="">-- Select Employee --</option>
                                        @foreach($employees as $employee)
                                            @php
                                                $appointmentLetter = $employee->appointmentLetters->first();
                                                $joiningDate = $appointmentLetter ? $appointmentLetter->joining_date->format('Y-m-d') : '';
                                            @endphp
                                            <option value="{{ $employee->id }}" 
                                                data-department="{{ $employee->department->name ?? '' }}"
                                                data-position="{{ $employee->position->name ?? '' }}"
                                                data-ctc="{{ $employee->ctc ?? '' }}"
                                                data-joining-date="{{ $joiningDate }}">
                                                {{ $employee->user->name }} ({{ $employee->user->employee_id }})
                                                @if($appointmentLetter) - Joining: {{ $joiningDate }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> All employees already have offer letters.
                                    </div>
                                @endif
                                @error('employee_data_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="offer_date" class="form-label">Offer Date</label>
                                        <input type="date" name="offer_date" class="form-control" required>
                                        @error('offer_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="joining_date" class="form-label">Joining Date</label>
                                        <input type="date" name="joining_date" id="joining_date" class="form-control" readonly required>
                                        @error('joining_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="offered_salary" class="form-label">Offered Salary (Annual)</label>
                                        <input type="number" name="offered_salary" id="offered_salary" class="form-control" required>
                                        @error('offered_salary')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="probation_period" class="form-label">Probation Period</label>
                                        <input type="text" name="probation_period" class="form-control" value="6 months" required>
                                        @error('probation_period')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Department</label>
                                        <input type="text" id="department" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Position</label>
                                        <input type="text" id="position" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="job_description" class="form-label">Job Description</label>
                                <textarea name="job_description" id="job_description" class="form-control" rows="5" placeholder="Enter job description and responsibilities..."></textarea>
                                @error('job_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('offer-letters.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary" {{ $employees->count() == 0 ? 'disabled' : '' }}>Create Offer Letter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('employee_data_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            console.log('Selected option:', selectedOption);
            console.log('Joining date data:', selectedOption.dataset.joiningDate);
            
            if (selectedOption.value) {
                // Auto-populate fields from employee data
                document.getElementById('department').value = selectedOption.dataset.department || '';
                document.getElementById('position').value = selectedOption.dataset.position || '';
                document.getElementById('offered_salary').value = selectedOption.dataset.ctc || '';
                
                // Auto-populate joining date from appointment letter
                const joiningDate = selectedOption.dataset.joiningDate;
                console.log('Setting joining date to:', joiningDate);
                document.getElementById('joining_date').value = joiningDate || '';
            } else {
                // Clear fields when no employee selected
                document.getElementById('department').value = '';
                document.getElementById('position').value = '';
                document.getElementById('offered_salary').value = '';
                document.getElementById('joining_date').value = '';
            }
        });
    </script>
</body>
</html>
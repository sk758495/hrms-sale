<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Appointment Letter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('hr.dashboard') }}">🏢 HR System</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">{{ Auth::guard('hr')->user()->name }}</span>
                <form method="POST" action="{{ route('hr.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="bi bi-file-earmark-plus"></i> Create Appointment Letter</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('appointment-letters.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="employee_data_id" class="form-label">Select Employee</label>
                                @if($employees->count() > 0)
                                    <select name="employee_data_id" id="employee_data_id" class="form-control" required>
                                        <option value="">-- Select Employee --</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" data-name="{{ $employee->user->name }}" 
                                                    data-id="{{ $employee->user->employee_id }}" 
                                                    data-department="{{ $employee->department->name ?? 'N/A' }}" 
                                                    data-position="{{ $employee->position->name ?? 'N/A' }}" 
                                                    data-ctc="{{ $employee->ctc ?? 0 }}">
                                                {{ $employee->user->name }} ({{ $employee->user->employee_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> All employees already have appointment letters.
                                    </div>
                                @endif
                                @error('employee_data_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row" id="employee-details" style="display: none;">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Employee Name</label>
                                        <input type="text" class="form-control" id="emp-name" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Employee ID</label>
                                        <input type="text" class="form-control" id="emp-id" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Department</label>
                                        <input type="text" class="form-control" id="emp-department" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Position</label>
                                        <input type="text" class="form-control" id="emp-position" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">CTC</label>
                                        <input type="text" class="form-control" id="emp-ctc" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="appointment_date" class="form-label">Appointment Date</label>
                                        <input type="date" name="appointment_date" class="form-control" required>
                                        @error('appointment_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="joining_date" class="form-label">Joining Date</label>
                                        <input type="date" name="joining_date" class="form-control" required>
                                        @error('joining_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                                <textarea name="terms_conditions" class="form-control" rows="5" placeholder="Enter terms and conditions..."></textarea>
                                @error('terms_conditions')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('appointment-letters.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary" {{ $employees->count() == 0 ? 'disabled' : '' }}>Create Appointment Letter</button>
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
            const detailsDiv = document.getElementById('employee-details');
            
            if (selectedOption.value) {
                document.getElementById('emp-name').value = selectedOption.dataset.name;
                document.getElementById('emp-id').value = selectedOption.dataset.id;
                document.getElementById('emp-department').value = selectedOption.dataset.department;
                document.getElementById('emp-position').value = selectedOption.dataset.position;
                document.getElementById('emp-ctc').value = '₹' + new Intl.NumberFormat().format(selectedOption.dataset.ctc);
                detailsDiv.style.display = 'block';
            } else {
                detailsDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>
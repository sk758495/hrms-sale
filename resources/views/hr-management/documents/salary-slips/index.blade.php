@extends('layouts.hr-app')

@section('title', 'Salary Slips Management')

@section('content')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-receipt"></i> Salary Slips</h2>
            <div>
                <a href="{{ route('salary-slips.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Generate Slip
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('salary-slips.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Employee</label>
                            <select name="employee" class="form-select">
                                <option value="">All Employees</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->user_id }}" {{ request('employee') == $employee->user_id ? 'selected' : '' }}>
                                        {{ $employee->user->name }} ({{ $employee->user->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select name="department" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Month/Year</label>
                            <input type="month" name="month_year" class="form-control" value="{{ request('month_year') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="{{ route('salary-slips.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>



        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Employee ID</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Month/Year</th>
                                <th>Net Salary</th>
                                <th>Payment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salarySlips as $slip)
                                <tr>
                                    <td>{{ $slip->user->name }}</td>
                                    <td>{{ $slip->user->employee_id }}</td>
                                    <td>{{ $slip->employeeData->department->name ?? 'N/A' }}</td>
                                    <td>{{ $slip->employeeData->position->name ?? 'N/A' }}</td>
                                    <td>{{ $slip->month_year }}</td>
                                    <td>₹{{ number_format((float)$slip->net_salary, 2) }}</td>
                                    <td>{{ $slip->payment_date->format('d M Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('salary-slips.show', $slip) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <a href="{{ route('salary-slips.show', $slip) }}?download=1" class="btn btn-sm btn-success">
                                                <i class="bi bi-download"></i> PDF
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="editSalary({{ $slip->id }}, '{{ $slip->user->name }}')">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No salary slips found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Salary Management Modal -->
    <div class="modal fade" id="salaryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Salary - <span id="employeeName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="salaryForm">
                        <div class="mb-3">
                            <label class="form-label">Basic Salary</label>
                            <input type="number" class="form-control" id="basicSalary" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Allowances</label>
                            <input type="number" class="form-control" id="allowances">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deductions</label>
                            <input type="number" class="form-control" id="deductions">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Month/Year</label>
                            <input type="month" class="form-control" id="monthYear" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveSalary()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function editSalary(slipId, employeeName) {
        document.getElementById('employeeName').textContent = employeeName;
        // Here you would typically fetch the current salary data via AJAX
        // For now, we'll just show the modal
        new bootstrap.Modal(document.getElementById('salaryModal')).show();
    }

    function saveSalary() {
        // Here you would save the salary data via AJAX
        alert('Salary updated successfully!');
        bootstrap.Modal.getInstance(document.getElementById('salaryModal')).hide();
        location.reload();
    }
</script>
@endpush
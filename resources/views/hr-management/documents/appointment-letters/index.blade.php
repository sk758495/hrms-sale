@extends('layouts.hr-app')

@section('title', 'Appointment Letters')

@section('content')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-file-earmark-text"></i> Appointment Letters</h2>
            <a href="{{ route('appointment-letters.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Create New
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('appointment-letters.index') }}">
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
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="{{ route('appointment-letters.index') }}" class="btn btn-outline-secondary">
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
                                <th>CTC</th>
                                <th>Appointment Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointmentLetters as $letter)
                                <tr>
                                    <td>{{ $letter->user->name }}</td>
                                    <td>{{ $letter->user->employee_id }}</td>
                                    <td>{{ $letter->employeeData->department->name ?? 'N/A' }}</td>
                                    <td>{{ $letter->employeeData->position->name ?? 'N/A' }}</td>
                                    <td>₹{{ number_format((float)str_replace(',', '', $letter->employeeData->ctc ?? 0)) }}</td>
                                    <td>{{ $letter->appointment_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $letter->status === 'draft' ? 'secondary' : ($letter->status === 'sent' ? 'warning' : 'success') }}">
                                            {{ ucfirst($letter->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('appointment-letters.show', $letter) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('appointment-letters.destroy', $letter) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No appointment letters found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection
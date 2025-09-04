@extends('layouts.hr-app')

@section('title', 'Completed Interviews')

@section('content')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-check-circle"></i> Completed Interviews</h2>
            <div>
                <a href="{{ route('interviews.rejected') }}" class="btn btn-outline-danger me-2">
                    <i class="bi bi-x-circle"></i> Rejected Interviews
                </a>
                <a href="{{ route('interviews.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Active Interviews
                </a>
            </div>
        </div>



        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('interviews.completed') }}">
                    <div class="row g-3">
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
                        <div class="col-md-3">
                            <label class="form-label">Position</label>
                            <select name="position" class="form-select">
                                <option value="">All Positions</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ request('position') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-2">
                            <label class="form-label">Type</label>
                            <select name="employee_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="Fresher" {{ request('employee_type') == 'Fresher' ? 'selected' : '' }}>Fresher</option>
                                <option value="Experienced" {{ request('employee_type') == 'Experienced' ? 'selected' : '' }}>Experienced</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="{{ route('interviews.completed') }}" class="btn btn-outline-secondary">
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
                                <th>Interviewer</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Type</th>
                                <th>Interview Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($interviews as $interview)
                                <tr>
                                    <td>{{ $interview->interviewer_name }}</td>
                                    <td>{{ $interview->contact_number }}</td>
                                    <td>{{ $interview->email }}</td>
                                    <td>{{ $interview->department->name }}</td>
                                    <td>{{ $interview->position->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $interview->employee_type == 'Fresher' ? 'info' : 'success' }}">
                                            {{ $interview->employee_type }}
                                        </span>
                                    </td>
                                    <td>{{ $interview->interview_date->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $interview->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('interviews.show', $interview) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No completed interviews found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection
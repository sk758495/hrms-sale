@extends('layouts.hr-app')

@section('title', 'Interviews Management')

@section('content')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-calendar-check"></i> Active Interviews</h2>
            <div>
                <a href="{{ route('interviews.completed') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-check-circle"></i> Completed Interviews
                </a>
                <a href="{{ route('interviews.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Schedule Interview
                </a>
            </div>
        </div>



        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('interviews.index') }}">
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
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Next Round" {{ request('status') == 'Next Round' ? 'selected' : '' }}>Next Round</option>
                            </select>
                        </div>
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
                                <a href="{{ route('interviews.index') }}" class="btn btn-outline-secondary">
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
                                <th>Resume</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($interviews as $interview)
                                <tr class="{{ $interview->interview_date->isToday() ? 'table-warning' : ($interview->interview_date->isPast() ? 'table-light' : '') }}">
                                    <td>
                                        {{ $interview->interviewer_name }}
                                        @if($interview->interview_date->isToday())
                                            <span class="badge bg-warning ms-1">Today</span>
                                        @endif
                                    </td>
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
                                                <a href="{{ route('interviews.download-resume', $interview) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i> Download Resume
                                                </a>
                                            </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $interview->status == 'Pending' ? 'warning' : 
                                            ($interview->status == 'Confirm' ? 'success' : 
                                            ($interview->status == 'Next Round' ? 'info' : 
                                            (str_contains($interview->status, 'Reject') ? 'danger' : 'primary'))) 
                                        }}">
                                            {{ $interview->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('interviews.show', $interview) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('interviews.edit', $interview) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('interviews.destroy', $interview) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No interviews found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection
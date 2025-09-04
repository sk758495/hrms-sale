<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejected Interviews</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-x-circle"></i> Rejected Interviews</h2>
            <div>
                <a href="{{ route('interviews.completed') }}" class="btn btn-outline-success me-2">
                    <i class="bi bi-check-circle"></i> Completed Interviews
                </a>
                <a href="{{ route('interviews.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Active Interviews
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('interviews.rejected') }}">
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
                            <label class="form-label">Rejection Type</label>
                            <select name="status" class="form-select">
                                <option value="">All Rejections</option>
                                <option value="Reject after interview" {{ request('status') == 'Reject after interview' ? 'selected' : '' }}>Reject after interview</option>
                                <option value="Rejection - No open Position" {{ request('status') == 'Rejection - No open Position' ? 'selected' : '' }}>Rejection - No open Position</option>
                                <option value="Rejection - High CTC expectation" {{ request('status') == 'Rejection - High CTC expectation' ? 'selected' : '' }}>Rejection - High CTC expectation</option>
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
                                <a href="{{ route('interviews.rejected') }}" class="btn btn-outline-secondary">
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
                                <th>Rejection Reason</th>
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
                                        <span class="badge bg-danger">
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
                                    <td colspan="9" class="text-center">No rejected interviews found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
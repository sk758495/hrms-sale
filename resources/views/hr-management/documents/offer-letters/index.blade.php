<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letters</title>
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
            <h2><i class="bi bi-file-earmark-check"></i> Offer Letters</h2>
            <a href="{{ route('offer-letters.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Create New
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('offer-letters.index') }}">
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
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="{{ route('offer-letters.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

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
                                <th>Offered Salary</th>
                                <th>Probation Period</th>
                                <th>Offer Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($offerLetters as $letter)
                                <tr>
                                    <td>{{ $letter->user->name }}</td>
                                    <td>{{ $letter->user->employee_id }}</td>
                                    <td>{{ $letter->employeeData->department->name ?? 'N/A' }}</td>
                                    <td>{{ $letter->employeeData->position->name ?? 'N/A' }}</td>
                                    <td>₹{{ number_format((float)$letter->offered_salary) }}</td>
                                    <td>{{ $letter->probation_period }}</td>
                                    <td>{{ $letter->offer_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $letter->status === 'draft' ? 'secondary' : ($letter->status === 'sent' ? 'warning' : ($letter->status === 'accepted' ? 'success' : 'danger')) }}">
                                            {{ ucfirst($letter->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('offer-letters.show', $letter) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('offer-letters.destroy', $letter) }}" class="d-inline">
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
                                    <td colspan="9" class="text-center">No offer letters found</td>
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
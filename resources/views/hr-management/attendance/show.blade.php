<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR - {{ $user->name }} Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('hr.dashboard') }}">🏢 HR Portal</a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('hr.attendance.index') }}" class="btn btn-outline-light btn-sm me-2">Back to Attendance</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Employee Info -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h4>{{ $user->name }} - Attendance Details</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Employee ID:</strong> {{ $user->employee_id }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Department:</strong> {{ $user->employeeData->department->name ?? 'N/A' }}</p>
                        <p><strong>Position:</strong> {{ $user->employeeData->position->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5>Total Days</h5>
                        <h3>{{ $stats['total_days'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5>Present</h5>
                        <h3>{{ $stats['present_days'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h5>Late</h5>
                        <h3>{{ $stats['late_days'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h5>Absent</h5>
                        <h3>{{ $stats['absent_days'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h5>Total Working Hours</h5>
                        <h3>{{ $stats['total_hours'] }} hrs</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Month Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Month</label>
                        <input type="month" name="month" class="form-control" value="{{ $month }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Detailed Attendance -->
        <div class="card">
            <div class="card-header">
                <h5>Detailed Attendance - {{ \Carbon\Carbon::parse($month)->format('F Y') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Working Hours</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i:s') : '-' }}</td>
                                    <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i:s') : '-' }}</td>
                                    <td>{{ $attendance->working_hours }} hours</td>
                                    <td>
                                        @if($attendance->latitude && $attendance->longitude)
                                            <small class="text-muted">{{ number_format((float)$attendance->latitude, 4) }}, {{ number_format((float)$attendance->longitude, 4) }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No attendance records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
@extends('layouts.hr-app')

@section('title', 'Attendance Management')

@push('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        color: white;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .stat-card.primary { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
    .stat-card.success { background: linear-gradient(135deg, #059669 0%, #047857 100%); }
    .stat-card.warning { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); }
    .stat-card.danger { background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); }
    
    .stat-card h2 {
        font-size: 3rem;
        font-weight: 700;
        margin: 0;
    }
    
    .stat-card h5 {
        font-weight: 500;
        margin-bottom: 1rem;
        opacity: 0.9;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clock"></i> Attendance Management</h2>
</div>
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card primary">
            <h5><i class="bi bi-people"></i> Total Employees</h5>
            <h2>{{ $totalEmployees }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <h5><i class="bi bi-check-circle"></i> Present Today</h5>
            <h2>{{ $presentToday }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <h5><i class="bi bi-clock"></i> Late Today</h5>
            <h2>{{ $lateToday }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card danger">
            <h5><i class="bi bi-x-circle"></i> Absent Today</h5>
            <h2>{{ $totalEmployees - $presentToday }}</h2>
        </div>
    </div>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="bi bi-funnel"></i> Filter Attendance</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-calendar-date"></i> Date</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-calendar-month"></i> Month</label>
                <input type="month" name="month" class="form-control" value="{{ $month }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Today's Attendance -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="bi bi-calendar-check"></i> Today's Attendance - {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h5>
    </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Working Hours</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayAttendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->user->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : ($attendance->status == 'late' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i:s') : '-' }}</td>
                                    <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i:s') : '-' }}</td>
                                    <td>{{ $attendance->working_hours }} hours</td>
                                    <td>
                                        <a href="{{ route('hr.attendance.show', $attendance->user) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
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

<!-- Monthly Summary -->
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-bar-chart"></i> Monthly Summary - {{ \Carbon\Carbon::parse($month)->format('F Y') }}</h5>
    </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Total Days</th>
                                <th>Present</th>
                                <th>Late</th>
                                <th>Absent</th>
                                <th>Total Hours</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyAttendances as $userId => $attendances)
                                @php
                                    $user = $attendances->first()->user;
                                    $totalDays = $attendances->count();
                                    $presentDays = $attendances->where('status', 'present')->count();
                                    $lateDays = $attendances->where('status', 'late')->count();
                                    $absentDays = $attendances->where('status', 'absent')->count();
                                    $totalHours = $attendances->sum('working_hours');
                                @endphp
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $totalDays }}</td>
                                    <td><span class="badge bg-success">{{ $presentDays }}</span></td>
                                    <td><span class="badge bg-warning">{{ $lateDays }}</span></td>
                                    <td><span class="badge bg-danger">{{ $absentDays }}</span></td>
                                    <td>{{ $totalHours }} hours</td>
                                    <td>
                                        <a href="{{ route('hr.attendance.show', $user) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No attendance records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection
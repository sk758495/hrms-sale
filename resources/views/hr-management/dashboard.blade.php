@extends('layouts.hr-app')

@section('title', 'HR Dashboard')

@push('styles')
<style>
    .dashboard-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 1rem;
        overflow: hidden;
    }
    
    .dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .stat-card.info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-card.success { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); }
    .stat-card.warning { background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); }
    
    .action-btn {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        padding: 2rem 1rem;
        text-decoration: none;
        color: #4a5568;
        display: block;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .action-btn:hover {
        border-color: #2563eb;
        color: #2563eb;
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
    }
    
    .action-btn i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        display: block;
    }
    
    .section-title {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 3px solid #2563eb;
        display: inline-block;
    }
</style>
@endpush

@section('content')

<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card dashboard-card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-speedometer2"></i> Welcome to HR Dashboard</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card info dashboard-card">
                            <i class="bi bi-person-badge fs-1"></i>
                            <h5>Employee ID</h5>
                            <p class="mb-0 fs-5 fw-bold">{{ Auth::guard('hr')->user()->employee_id }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card success dashboard-card">
                            <i class="bi bi-envelope fs-1"></i>
                            <h5>Email</h5>
                            <p class="mb-0 fs-6">{{ Auth::guard('hr')->user()->email }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card warning dashboard-card">
                            <i class="bi bi-phone fs-1"></i>
                            <h5>Mobile</h5>
                            <p class="mb-0 fs-6">{{ Auth::guard('hr')->user()->mobile ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee Management Section -->
<div class="row mb-4">
    <div class="col-12">
        <h3 class="section-title"><i class="bi bi-people"></i> Employee Management</h3>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="{{ route('departments.index') }}" class="action-btn dashboard-card">
                    <i class="bi bi-building"></i>
                    <strong>Departments</strong>
                    <small class="d-block text-muted">Manage departments</small>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="{{ route('positions.index') }}" class="action-btn dashboard-card">
                    <i class="bi bi-briefcase"></i>
                    <strong>Positions</strong>
                    <small class="d-block text-muted">Manage positions</small>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="{{ route('employee.create') }}" class="action-btn dashboard-card">
                    <i class="bi bi-person-plus"></i>
                    <strong>Add Employee</strong>
                    <small class="d-block text-muted">Register new employee</small>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="{{ route('employee-data.index') }}" class="action-btn dashboard-card">
                    <i class="bi bi-people"></i>
                    <strong>View Employees</strong>
                    <small class="d-block text-muted">All employee records</small>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="row mb-4">
    <div class="col-12">
        <h3 class="section-title"><i class="bi bi-lightning"></i> Quick Actions</h3>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <a href="{{ route('interviews.index') }}" class="action-btn dashboard-card">
                    <i class="bi bi-calendar-check"></i>
                    <strong>Interviews</strong>
                    <small class="d-block text-muted">Schedule & manage interviews</small>
                </a>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <a href="{{ route('hr.attendance.index') }}" class="action-btn dashboard-card">
                    <i class="bi bi-clock"></i>
                    <strong>Attendance</strong>
                    <small class="d-block text-muted">Track employee attendance</small>
                </a>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <a href="{{ route('hr.document-verification.index') }}" class="action-btn dashboard-card">
                    <i class="bi bi-clock"></i>
                    <strong>Document Verification</strong>
                    <small class="d-block text-muted">Track employee Document Verification</small>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Document Management Section -->
<div class="row mb-4">
    <div class="col-12">
        <h3 class="section-title"><i class="bi bi-file-earmark-text"></i> Document Management</h3>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="{{ route('appointment-letters.index') }}" class="action-btn dashboard-card">
                    <i class="bi bi-file-earmark-text"></i>
                    <strong>Appointment Letters</strong>
                    <small class="d-block text-muted">Generate & manage</small>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="{{ route('offer-letters.index') }}" class="action-btn dashboard-card">
                    <i class="bi bi-file-earmark-check"></i>
                    <strong>Offer Letters</strong>
                    <small class="d-block text-muted">Create offer letters</small>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="{{ route('ndas.index') }}" class="action-btn dashboard-card">
                    <i class="bi bi-shield-lock"></i>
                    <strong>NDAs</strong>
                    <small class="d-block text-muted">Non-disclosure agreements</small>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="{{ route('salary-slips.index') }}" class="action-btn dashboard-card">
                    <i class="bi bi-receipt"></i>
                    <strong>Salary Slips</strong>
                    <small class="d-block text-muted">Generate salary slips</small>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- System Status Section -->
<div class="row">
    <div class="col-12">
        <h3 class="section-title"><i class="bi bi-shield-check"></i> System Status</h3>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card dashboard-card border-0">
                    <div class="card-body text-center">
                        <div class="text-success mb-3">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                        <h5 class="text-success">Email Verified</h5>
                        <p class="text-muted mb-0">Your email is verified and active</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card dashboard-card border-0">
                    <div class="card-body text-center">
                        <div class="text-primary mb-3">
                            <i class="bi bi-shield-check fs-1"></i>
                        </div>
                        <h5 class="text-primary">Account Active</h5>
                        <p class="text-muted mb-0">Your HR account is active and ready</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
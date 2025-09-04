<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HR Management System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
        }

        body {
            background-color: var(--light-color);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            box-shadow: 0 2px 10px rgba(37, 99, 235, 0.2);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white !important;
        }

        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 0.75rem;
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: var(--light-color);
            color: var(--primary-color);
        }

        .main-content {
            min-height: calc(100vh - 80px);
            padding: 2rem 0;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            color: white;
            border-radius: 1rem 1rem 0 0 !important;
            padding: 1.5rem;
            border: none;
        }

        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .alert {
            border: none;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
        }

        .table {
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--dark-color);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            margin-right: 1rem;
        }

        @media (max-width: 768px) {
            .navbar-nav {
                margin-top: 1rem;
            }
            
            .main-content {
                padding: 1rem 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('hr.dashboard') }}">
                <i class="bi bi-building"></i> HR Management
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}" href="{{ route('hr.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-people"></i> Employees
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('employee-data.index') }}"><i class="bi bi-list"></i> View All</a></li>
                            <li><a class="dropdown-item" href="{{ route('employee.create') }}"><i class="bi bi-person-plus"></i> Add New</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('departments.index') }}"><i class="bi bi-building"></i> Departments</a></li>
                            <li><a class="dropdown-item" href="{{ route('positions.index') }}"><i class="bi bi-briefcase"></i> Positions</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('interviews.*') ? 'active' : '' }}" href="{{ route('interviews.index') }}">
                            <i class="bi bi-calendar-check"></i> Interviews
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('hr.attendance.*') ? 'active' : '' }}" href="{{ route('hr.attendance.index') }}">
                            <i class="bi bi-clock"></i> Attendance
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-file-earmark-text"></i> Documents
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('appointment-letters.index') }}"><i class="bi bi-file-earmark-text"></i> Appointment Letters</a></li>
                            <li><a class="dropdown-item" href="{{ route('offer-letters.index') }}"><i class="bi bi-file-earmark-check"></i> Offer Letters</a></li>
                            <li><a class="dropdown-item" href="{{ route('ndas.index') }}"><i class="bi bi-shield-lock"></i> NDAs</a></li>
                            <li><a class="dropdown-item" href="{{ route('salary-slips.index') }}"><i class="bi bi-receipt"></i> Salary Slips</a></li>
                        </ul>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <div class="user-info">
                        <i class="bi bi-person-circle"></i>
                        <span>{{ Auth::guard('hr')->user()->name }}</span>
                        <small class="d-block">ID: {{ Auth::guard('hr')->user()->employee_id }}</small>
                    </div>
                    
                    <form method="POST" action="{{ route('hr.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
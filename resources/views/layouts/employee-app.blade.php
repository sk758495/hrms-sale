<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Employee Portal')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-person-circle"></i> Employee Portal
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
                            <i class="bi bi-clock"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('emp.time-tracker') ? 'active' : '' }}" href="{{ route('emp.time-tracker') }}">
                            <i class="bi bi-stopwatch"></i> Time Tracker
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('emp.documents.*') ? 'active' : '' }}" href="{{ route('emp.documents.index') }}">
                            <i class="bi bi-file-earmark-text"></i> My Documents
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person-gear"></i> Profile
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <span class="navbar-text me-3">
                        <i class="bi bi-person"></i> {{ Auth::user()->employee_id }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Check if work session was auto-started
        $(document).ready(function() {
            $.get('/emp-management/timer/active').done(function(response) {
                if (response.active) {
                    // Show notification that timer is running
                    if (!sessionStorage.getItem('timer_notification_shown')) {
                        $('<div class="alert alert-info alert-dismissible fade show position-fixed" style="top: 80px; right: 20px; z-index: 9999; width: 300px;">' +
                          '<i class="bi bi-play-circle"></i> Work timer started automatically!' +
                          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                          '</div>').appendTo('body').delay(5000).fadeOut();
                        sessionStorage.setItem('timer_notification_shown', 'true');
                    }
                }
            });
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
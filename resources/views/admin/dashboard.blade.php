<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Seller Sathi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            background-color: #343a40;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 60px;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #495057;
            color: #fff;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="d-flex">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="#"><i class="bi bi-box-seam"></i> Products</a>
        <a href="#"><i class="bi bi-people"></i> Users</a>
        <a href="#"><i class="bi bi-gear"></i> Settings</a>
    </div>

    <!-- Main Content -->
    <div class="main-content mt-6 my-5">
        <h2>Welcome, Admin</h2>
        <p class="text-muted">You are logged in to the admin dashboard.</p>

        <div class="row">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <p class="card-text display-6">125</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Active Users</h5>
                        <p class="card-text display-6">78</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Orders Today</h5>
                        <p class="card-text display-6">42</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

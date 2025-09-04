<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NDAs</title>
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
            <h2><i class="bi bi-shield-lock"></i> Non-Disclosure Agreements</h2>
            <a href="{{ route('ndas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Create New
            </a>
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
                                <th>NDA Date</th>
                                <th>Valid Until</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ndas as $nda)
                                <tr>
                                    <td>{{ $nda->user->name }}</td>
                                    <td>{{ $nda->user->employee_id }}</td>
                                    <td>{{ $nda->employeeData->department->name ?? 'N/A' }}</td>
                                    <td>{{ $nda->employeeData->position->name ?? 'N/A' }}</td>
                                    <td>{{ $nda->nda_date->format('d M Y') }}</td>
                                    <td>{{ $nda->validity_until ? $nda->validity_until->format('d M Y') : 'Indefinite' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $nda->status === 'draft' ? 'secondary' : ($nda->status === 'sent' ? 'warning' : 'success') }}">
                                            {{ ucfirst($nda->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('ndas.show', $nda) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('ndas.destroy', $nda) }}" class="d-inline">
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
                                    <td colspan="8" class="text-center">No NDAs found</td>
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
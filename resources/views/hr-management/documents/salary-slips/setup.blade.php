<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Structure Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('hr.dashboard') }}">🏢 HR System</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="bi bi-gear"></i> One-Time Salary Structure Setup</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ route('salary-slips.setup.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="user_id" class="form-label">Select Employee</label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->employee_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="basic_salary" class="form-label">Basic Salary</label>
                                        <input type="number" name="basic_salary" class="form-control" required>
                                        @error('basic_salary')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="hra_percentage" class="form-label">HRA Percentage</label>
                                        <input type="number" name="hra_percentage" class="form-control" value="40" step="0.01" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="traveling_allowance" class="form-label">Traveling Allowance</label>
                                        <input type="number" name="traveling_allowance" class="form-control" value="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="other_allowances" class="form-label">Other Allowances</label>
                                        <input type="number" name="other_allowances" class="form-control" value="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="professional_tax" class="form-label">Professional Tax</label>
                                <input type="number" name="professional_tax" class="form-control" value="200" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('hr.dashboard') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Salary Structure</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
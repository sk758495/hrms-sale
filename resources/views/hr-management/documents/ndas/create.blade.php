<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create NDA</title>
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
                        <h4><i class="bi bi-shield-lock"></i> Create Non-Disclosure Agreement</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('ndas.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="employee_data_id" class="form-label">Select Employee</label>
                                @if($employees->count() > 0)
                                    <select name="employee_data_id" id="employee_data_id" class="form-control" required>
                                        <option value="">-- Select Employee --</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->user->name }} ({{ $employee->user->employee_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> All employees already have NDAs.
                                    </div>
                                @endif
                                @error('employee_data_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nda_date" class="form-label">NDA Date</label>
                                        <input type="date" name="nda_date" class="form-control" required>
                                        @error('nda_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validity_until" class="form-label">Valid Until (Optional)</label>
                                        <input type="date" name="validity_until" class="form-control">
                                        @error('validity_until')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="confidentiality_terms" class="form-label">Confidentiality Terms</label>
                                <textarea name="confidentiality_terms" class="form-control" rows="10" required placeholder="Enter confidentiality terms and conditions...">The Employee agrees to maintain strict confidentiality regarding:

1. All proprietary information, trade secrets, and confidential data
2. Client information and business relationships
3. Technical specifications and processes
4. Financial information and business strategies
5. Any other information marked as confidential

This agreement remains in effect during employment and continues thereafter.</textarea>
                                @error('confidentiality_terms')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('ndas.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary" {{ $employees->count() == 0 ? 'disabled' : '' }}>Create NDA</button>
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
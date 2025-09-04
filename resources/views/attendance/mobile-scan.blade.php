<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mobile Attendance Scan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4><i class="bi bi-qr-code-scan"></i> Attendance Scanner</h4>
                    </div>
                    <div class="card-body text-center">
                        @if(isset($empId) && isset($empName))
                        <div class="alert alert-info mb-3">
                            <strong>QR Code Generated For:</strong><br>
                            <i class="bi bi-person"></i> {{ $empName }} ({{ $empId }})
                        </div>
                        @endif
                        
                        <div id="login-section">
                            <h5>Enter Your Employee Credentials</h5>
                            <div class="mb-3">
                                <input type="text" id="employee-id" class="form-control" placeholder="Employee ID" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" id="employee-password" class="form-control" placeholder="Password" required>
                            </div>
                            <button id="login-scan" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Login & Scan Attendance
                            </button>
                        </div>
                        
                        <div id="result-section" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const token = '{{ $token }}';
        const expectedEmpId = '{{ $empId ?? "" }}';
        const expectedEmpName = '{{ $empName ?? "" }}';
        
        document.getElementById('login-scan').addEventListener('click', function() {
            const employeeId = document.getElementById('employee-id').value;
            const password = document.getElementById('employee-password').value;
            
            if (!employeeId || !password) {
                alert('Please enter both Employee ID and Password');
                return;
            }
            
            // Check if entered ID matches expected ID
            if (expectedEmpId && employeeId !== expectedEmpId) {
                document.getElementById('result-section').innerHTML = `
                    <div class="alert alert-warning">
                        <h5><i class="bi bi-exclamation-triangle"></i> Incorrect Employee ID</h5>
                        <p>Please use the correct Employee ID: <strong>${expectedEmpId}</strong></p>
                        <p>This QR code was generated for: <strong>${expectedEmpName}</strong></p>
                    </div>
                `;
                return;
            }
            
            // Show loading
            document.getElementById('result-section').innerHTML = '<div class="alert alert-info">Logging in and scanning...</div>';
            
            // Get location
            if (!navigator.geolocation) {
                document.getElementById('result-section').innerHTML = '<div class="alert alert-danger">Geolocation not supported</div>';
                return;
            }
            
            navigator.geolocation.getCurrentPosition(function(position) {
                // Login and scan
                fetch('/attendance/mobile-scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        token: token,
                        employee_id: employeeId,
                        password: password,
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const action = data.action === 'check_in' ? 'Checked In' : 'Checked Out';
                        const status = data.status ? ` (${data.status})` : '';
                        const hours = data.working_hours ? ` - ${data.working_hours} hours worked` : '';
                        
                        document.getElementById('result-section').innerHTML = `
                            <div class="alert alert-success">
                                <h5><i class="bi bi-check-circle"></i> ${action} Successfully!</h5>
                                <p><strong>Employee:</strong> ${data.employee_name}</p>
                                <p><strong>Time:</strong> ${data.time}${status}${hours}</p>
                            </div>
                        `;
                    } else {
                        document.getElementById('result-section').innerHTML = `
                            <div class="alert alert-danger">
                                <h5><i class="bi bi-x-circle"></i> Scan Failed</h5>
                                <p>${data.error}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('result-section').innerHTML = `
                        <div class="alert alert-danger">
                            <h5><i class="bi bi-x-circle"></i> Error</h5>
                            <p>Failed to scan: ${error.message}</p>
                        </div>
                    `;
                });
            }, function(error) {
                document.getElementById('result-section').innerHTML = '<div class="alert alert-danger">Please enable location access</div>';
            });
        });
    </script>
</body>
</html>
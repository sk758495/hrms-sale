<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Login - HR Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 480px;
            padding: 2rem;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-header h4 {
            color: #1e293b;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #64748b;
            font-size: 1rem;
        }
        
        .login-type-tabs {
            display: flex;
            margin-bottom: 2rem;
            border-radius: 1rem;
            overflow: hidden;
            background: #f1f5f9;
            padding: 0.25rem;
        }
        
        .login-type-tab {
            flex: 1;
            padding: 1rem;
            text-align: center;
            background: transparent;
            cursor: pointer;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            color: #64748b;
            transition: all 0.3s ease;
        }
        
        .login-type-tab.active {
            background: #2563eb;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }
        
        .form-control {
            border-radius: 0.75rem;
            border: 2px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.75rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
        }
        
        .password-field {
            display: none;
        }
        
        .password-field.active {
            display: block;
        }
        
        .input-group .btn {
            border-radius: 0 0.75rem 0.75rem 0;
            border: 2px solid #e2e8f0;
            border-left: none;
        }
        
        .input-group .form-control {
            border-radius: 0.75rem 0 0 0.75rem;
        }
        
        .alert {
            border-radius: 0.75rem;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .form-check-input:checked {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        
        .register-link {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .register-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h4><i class="bi bi-building"></i> HR Management</h4>
                <p>Sign in to access your HR dashboard</p>
            </div>

        @if (session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        @if (session('status'))
            <div class="alert alert-info mb-3">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ url('/hr/login') }}" id="loginForm">
            @csrf

            <!-- Login Type Selection -->
            <div class="login-type-tabs">
                <button type="button" class="login-type-tab active" data-type="password">
                    <i class="bi bi-key"></i> Password
                </button>
                <button type="button" class="login-type-tab" data-type="otp">
                    <i class="bi bi-envelope"></i> Email OTP
                </button>
            </div>

            <input type="hidden" name="login_type" id="login_type" value="password">

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field (shown by default) -->
            <div class="mb-3 password-field active" id="passwordField">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                <label class="form-check-label" for="remember_me">Remember me</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">
                <i class="bi bi-box-arrow-in-right"></i> Login with Password
            </button>
        </form>

            <div class="text-center">
                <a href="{{ route('hr.register') }}" class="register-link">
                    Don't have an account? Register here
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Login type switching
        document.querySelectorAll('.login-type-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.login-type-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const type = this.dataset.type;
                document.getElementById('login_type').value = type;
                
                const passwordField = document.getElementById('passwordField');
                const submitBtn = document.getElementById('submitBtn');
                
                if (type === 'password') {
                    passwordField.classList.add('active');
                    submitBtn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Login with Password';
                    document.getElementById('password').required = true;
                } else {
                    passwordField.classList.remove('active');
                    submitBtn.innerHTML = '<i class="bi bi-envelope"></i> Send Login OTP';
                    document.getElementById('password').required = false;
                }
            });
        });

        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("eyeIcon");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            }
        }
    </script>
</body>
</html>
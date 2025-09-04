<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Register - HR Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        .register-card {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            border-radius: 16px;
            padding: 40px 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="register-card">
        <h4 class="text-center mb-4 fw-bold">🏢 HR Registration</h4>

        @if (session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ url('/hr/register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mobile -->
            <div class="mb-3">
                <label for="mobile" class="form-label">Mobile Number</label>
                <input id="mobile" type="text" name="mobile" value="{{ old('mobile') }}"
                       class="form-control @error('mobile') is-invalid @enderror" required>
                @error('mobile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', 'eyeIcon1')">
                        <i class="bi bi-eye-slash" id="eyeIcon1"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation', 'eyeIcon2')">
                        <i class="bi bi-eye-slash" id="eyeIcon2"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="bi bi-person-plus"></i> Register
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('hr.login') }}" class="text-decoration-none">
                Already have an account? Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
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
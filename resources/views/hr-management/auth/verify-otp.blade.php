<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Email Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        .otp-container {
            max-width: 420px;
            margin: 80px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .otp-input {
            font-size: 1.2rem;
            text-align: center;
            letter-spacing: 8px;
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <h4 class="text-center mb-4">🔐 HR Email Verification</h4>
        <p class="text-center text-muted mb-4">We've sent an OTP to your email. Please enter it below to verify your account.</p>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ url('/hr/verify-otp') }}">
            @csrf
            <div class="mb-3">
                <label for="otp" class="form-label">Enter OTP</label>
                <input type="text" id="otp" name="otp" maxlength="6" required
                       class="form-control otp-input @error('otp') is-invalid @enderror"
                       placeholder="______" autofocus>
                @error('otp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">✅ Verify Email</button>
        </form>

        <form method="POST" action="{{ url('/hr/resend-otp') }}" class="text-center">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">
                🔁 Resend OTP
            </button>
        </form>

        <div class="text-center mt-3">
            <form method="POST" action="{{ url('/hr/logout') }}">
                @csrf
                <button type="submit" class="btn btn-link text-muted">
                    🚪 Logout
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
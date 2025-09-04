<!DOCTYPE html>
<html>
<head>
    <title>HR System - OTP Verification</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #333; margin-bottom: 30px; }
        .otp-box { background-color: #007bff; color: white; padding: 20px; text-align: center; border-radius: 8px; font-size: 24px; font-weight: bold; letter-spacing: 3px; margin: 20px 0; }
        .footer { text-align: center; color: #666; font-size: 14px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 HR System Email Verification</h1>
        </div>
        
        <p>Hello,</p>
        <p>Your OTP for HR system email verification is:</p>
        
        <div class="otp-box">{{ $otp }}</div>
        
        <p>This OTP will expire in 5 minutes. Please enter this code on the verification page to complete your registration.</p>
        
        <p>If you didn't request this verification, please ignore this email.</p>
        
        <div class="footer">
            <p>Thank you,<br>HR Management System</p>
        </div>
    </div>
</body>
</html>
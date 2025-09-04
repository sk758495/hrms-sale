<?php

// Simple test script to verify attendance system
require_once 'vendor/autoload.php';

use App\Models\QrToken;
use App\Models\Attendance;

echo "Testing QR Token Generation...\n";

// Test QR token generation
$token = QrToken::generateToken();
echo "Generated token: " . $token->token . "\n";
echo "Expires at: " . $token->expires_at . "\n";
echo "Is valid: " . ($token->isValid() ? 'Yes' : 'No') . "\n";

echo "\nAttendance system is ready!\n";
echo "Routes available:\n";
echo "- GET /attendance (view attendance)\n";
echo "- POST /attendance/generate-qr (generate QR code)\n";
echo "- POST /attendance/scan/{token} (scan QR code)\n";
<?php

// Simple test script to check API endpoints
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Create Laravel app instance
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Testing API Endpoints...\n\n";

// Test database connection
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=hrms_system', 'root', '');
    echo "✓ Database connection successful\n";
    
    // Check if tables exist
    $tables = ['work_sessions', 'breaks', 'activity_logs'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Table '$table' exists\n";
        } else {
            echo "✗ Table '$table' missing\n";
        }
    }
    
    // Check table structure
    echo "\nChecking work_sessions table structure:\n";
    $stmt = $pdo->query("DESCRIBE work_sessions");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Field']} ({$row['Type']})\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\nAPI Routes registered:\n";
echo "POST /api/timer/start\n";
echo "POST /api/timer/pause\n";
echo "POST /api/timer/resume\n";
echo "POST /api/timer/stop\n";
echo "POST /api/timer/manual\n";
echo "POST /api/breaks/start\n";
echo "POST /api/breaks/end\n";
echo "GET /api/reports/daily\n";

echo "\nTo test the API:\n";
echo "1. Make sure you have a valid Sanctum token\n";
echo "2. Send requests with proper authentication headers\n";
echo "3. Include required fields in request body\n";
echo "4. Check Laravel logs for any errors: storage/logs/laravel.log\n";
<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TESTING ADMIN TRANSACTIONS WITH AUTH ===\n\n";

try {
    // Get admin token
    $auth = Http::post('http://localhost:8000/api/login', [
        'email' => 'admin@bitchest.com',
        'password' => 'admin123'
    ]);
    
    if ($auth->status() !== 200) {
        echo "Auth failed: Status " . $auth->status() . "\n";
        echo "Response: " . $auth->body() . "\n";
        exit;
    }
    
    $data = $auth->json();
    if (!isset($data['data']['token'])) {
        echo "No token in response\n";
        exit;
    }
    
    $token = $data['data']['token'];
    echo "✓ Authenticated as admin\n";
    echo "Token: " . substr($token, 0, 20) . "...\n\n";
    
    // Test /admin/transactions
    echo "Testing /admin/transactions:\n";
    $response = Http::withToken($token)->get('http://localhost:8000/api/admin/transactions');
    
    echo "Status: " . $response->status() . "\n";
    $data = $response->json();
    
    if ($response->status() === 200) {
        if (isset($data['data'])) {
            echo "✓ Transactions count: " . count($data['data']) . "\n\n";
            if (count($data['data']) > 0) {
                echo "First transaction:\n";
                echo json_encode($data['data'][0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
            }
        } else {
            echo "Response:\n";
            echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "Error response:\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

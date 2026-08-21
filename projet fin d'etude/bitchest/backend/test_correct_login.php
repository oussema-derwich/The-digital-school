<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TESTING LOGIN WITH CORRECT CREDENTIALS ===\n\n";

$tests = [
    ['email' => 'user@bitchest.com', 'password' => 'user123', 'desc' => 'User Account'],
    ['email' => 'admin@bitchest.com', 'password' => 'admin123', 'desc' => 'Admin Account'],
];

foreach ($tests as $test) {
    echo "Testing: {$test['desc']}\n";
    echo "Email: {$test['email']}\n";
    echo "Password: {$test['password']}\n";
    
    try {
        $response = Http::post('http://localhost:8000/api/auth/login', [
            'email' => $test['email'],
            'password' => $test['password']
        ]);
        
        echo "Status: " . $response->status() . "\n";
        echo "Response: " . json_encode($response->json(), JSON_PRETTY_PRINT) . "\n";
        
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['data']['token'])) {
                echo "✓ TOKEN: " . $data['data']['token'] . "\n";
            }
        }
    } catch (\Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n---\n\n";
}

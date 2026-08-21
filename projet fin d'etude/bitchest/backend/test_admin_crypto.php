<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TESTING ADMIN CRYPTO API ===\n\n";

try {
    // Authenticate first
    $auth = Http::post('http://localhost:8000/api/login', [
        'email' => 'admin@bitchest.com',
        'password' => 'admin123'
    ]);
    
    echo "Auth Status: " . $auth->status() . "\n";
    echo "Auth Body: " . $auth->body() . "\n";
    
    $authData = $auth->json();
    if (!$authData || !isset($authData['data']['token'])) {
        echo "Auth response: " . json_encode($authData) . "\n";
        exit;
    }
    
    $token = $authData['data']['token'];
    echo "✓ Authenticated as admin\n\n";
    
    // Get admin cryptos
    $response = Http::withToken($token)->get('http://localhost:8000/api/admin/cryptos');
    
    echo "Status: " . $response->status() . "\n";
    echo "Admin Cryptos:\n";
    echo json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

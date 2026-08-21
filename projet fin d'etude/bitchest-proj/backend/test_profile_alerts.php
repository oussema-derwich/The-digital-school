<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TESTING PROFILE AND ALERTS ENDPOINTS ===\n\n";

// Get token from user
$user = \App\Models\User::find(1); // user@bitchest.com
$token = $user->createToken('test')->plainTextToken;

echo "User: {$user->email}\n";
echo "Token: $token\n\n";

// Test 1: Get profile
echo "1. GET /api/auth/profile\n";
try {
    $response = Http::withToken($token)->get('http://localhost:8000/api/auth/profile');
    echo "   Status: " . $response->status() . "\n";
    if ($response->status() === 200) {
        $data = $response->json();
        $profile = $data['data'] ?? $data;
        echo "   ✓ Profile: " . $profile['email'] . " - " . $profile['name'] . "\n";
        echo "   ✓ Avatar: " . ($profile['avatar'] ?? 'null') . "\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: Get alerts
echo "\n2. GET /api/alerts\n";
try {
    $response = Http::withToken($token)->get('http://localhost:8000/api/alerts');
    echo "   Status: " . $response->status() . "\n";
    $data = $response->json();
    $alerts = is_array($data) ? $data : ($data['data'] ?? []);
    echo "   ✓ Alerts count: " . count($alerts) . "\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Create alert
echo "\n3. POST /api/alerts (create new alert)\n";
try {
    $response = Http::withToken($token)->post('http://localhost:8000/api/alerts', [
        'crypto_id' => 1,
        'price_threshold' => 50000,
        'type' => 'above'
    ]);
    echo "   Status: " . $response->status() . "\n";
    if ($response->status() === 201 || $response->status() === 200) {
        $data = $response->json();
        echo "   ✓ Alert created: " . $data['alert']['id'] . "\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 4: Update profile
echo "\n4. PUT /api/auth/profile (update profile)\n";
try {
    $response = Http::withToken($token)->put('http://localhost:8000/api/auth/profile', [
        'name' => 'User Updated',
        'email' => $user->email
    ]);
    echo "   Status: " . $response->status() . "\n";
    if ($response->status() === 200) {
        $data = $response->json();
        echo "   ✓ Profile updated\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== ALL TESTS COMPLETED ===\n";

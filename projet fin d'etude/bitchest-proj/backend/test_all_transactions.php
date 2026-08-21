<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TESTING ALL TRANSACTION ENDPOINTS ===\n\n";

// Get token from user
$user = \App\Models\User::find(1); // user@bitchest.com
$token = $user->createToken('test')->plainTextToken;

echo "User: {$user->email}\n";
echo "Token: $token\n\n";

// Test 1: Get transactions
echo "1. GET /api/transactions\n";
try {
    $response = Http::withToken($token)->get('http://localhost:8000/api/transactions');
    echo "   Status: " . $response->status() . "\n";
    if ($response->status() === 200) {
        $data = $response->json();
        echo "   ✓ Transactions loaded: " . count($data['data']) . " transaction(s)\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: Get first transaction
echo "\n2. GET /api/transactions/{id}/proof\n";
try {
    $txId = 5; // The transaction we know exists
    $response = Http::withToken($token)->get('http://localhost:8000/api/transactions/' . $txId . '/proof');
    echo "   Status: " . $response->status() . "\n";
    if ($response->status() === 200) {
        echo "   ✓ Proof downloaded: " . strlen($response->body()) . " bytes\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Export CSV
echo "\n3. GET /api/transactions/export/csv\n";
try {
    $response = Http::withToken($token)->get('http://localhost:8000/api/transactions/export/csv');
    echo "   Status: " . $response->status() . "\n";
    if ($response->status() === 200) {
        echo "   ✓ CSV Export: " . strlen($response->body()) . " bytes\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 4: Export PDF
echo "\n4. GET /api/transactions/export/pdf\n";
try {
    $response = Http::withToken($token)->get('http://localhost:8000/api/transactions/export/pdf');
    echo "   Status: " . $response->status() . "\n";
    if ($response->status() === 200) {
        echo "   ✓ PDF Export: " . strlen($response->body()) . " bytes\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== ALL TESTS COMPLETED ===\n";

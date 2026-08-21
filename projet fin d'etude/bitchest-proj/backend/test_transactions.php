<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TESTING TRANSACTIONS ENDPOINT ===\n\n";

// Get token from user
$user = \App\Models\User::find(1); // user@bitchest.com
$token = $user->createToken('test')->plainTextToken;

echo "Token: $token\n\n";

// Test transactions endpoint
try {
    $response = Http::withToken($token)->get('http://localhost:8000/api/transactions');
    
    echo "Status: " . $response->status() . "\n";
    $data = $response->json();
    
    if ($response->status() === 200) {
        echo "✓ Transactions loaded successfully\n";
        echo "Number of transactions: " . count($data['data']) . "\n";
        
        if (count($data['data']) > 0) {
            echo "\nFirst transaction structure:\n";
            echo json_encode($data['data'][0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
        }
        
        echo "\nStatistics:\n";
        echo json_encode($data['statistiques'], JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "✗ Error: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== TESTING CSV EXPORT ===\n";
try {
    $response = Http::withToken($token)->get('http://localhost:8000/api/transactions/export/csv');
    
    echo "Status: " . $response->status() . "\n";
    if ($response->status() === 200) {
        echo "✓ CSV Export successful\n";
        echo "Response length: " . strlen($response->body()) . " bytes\n";
        echo "First 200 chars:\n";
        echo substr($response->body(), 0, 200) . "\n";
    }
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== TESTING PDF EXPORT ===\n";
try {
    $response = Http::withToken($token)->get('http://localhost:8000/api/transactions/export/pdf');
    
    echo "Status: " . $response->status() . "\n";
    if ($response->status() === 200) {
        echo "✓ PDF Export successful\n";
        echo "Response length: " . strlen($response->body()) . " bytes\n";
    }
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

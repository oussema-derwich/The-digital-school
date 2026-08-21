<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== TESTING ADMIN ENDPOINTS ===\n\n";

try {
    // Test /admin/transactions
    echo "Testing /admin/transactions:\n";
    $response = Http::get('http://localhost:8000/api/admin/transactions');
    
    echo "Status: " . $response->status() . "\n";
    $data = $response->json();
    
    if (isset($data['data'])) {
        echo "Transactions count: " . count($data['data']) . "\n";
        if (count($data['data']) > 0) {
            echo "First transaction:\n";
            echo json_encode($data['data'][0], JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "Full response:\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

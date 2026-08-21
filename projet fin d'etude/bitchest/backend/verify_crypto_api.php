<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== VERIFYING CRYPTO API RESPONSE ===\n\n";

try {
    $response = Http::get('http://localhost:8000/api/cryptocurrencies');
    
    echo "Status: " . $response->status() . "\n\n";
    
    $json = $response->json();
    echo "Full Response:\n";
    echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    
    if (isset($json['data']) && is_array($json['data']) && count($json['data']) > 0) {
        echo "\n✓ Found " . count($json['data']) . " cryptocurrencies\n";
        echo "\nFirst Crypto Structure:\n";
        echo json_encode($json['data'][0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } else {
        echo "\n✗ No cryptocurrency data found in response\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

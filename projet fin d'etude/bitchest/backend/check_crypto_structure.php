<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== CHECKING CRYPTOCURRENCY DATA STRUCTURE ===\n\n";

try {
    $response = Http::get('http://localhost:8000/api/cryptocurrencies');
    
    echo "Status: " . $response->status() . "\n";
    $data = $response->json();
    
    echo "\nFirst cryptocurrency structure:\n";
    if (is_array($data)) {
        echo json_encode($data[0] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } else {
        echo json_encode($data[0] ?? $data['data'][0] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

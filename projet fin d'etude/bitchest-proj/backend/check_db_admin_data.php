<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING DATABASE FOR ADMIN DATA ===\n\n";

try {
    // Check users
    $users = DB::table('users')->select('id', 'name', 'email', 'role')->get();
    echo "Users in database: " . count($users) . "\n";
    foreach ($users as $user) {
        echo "  - {$user->email} (Role: {$user->role})\n";
    }
    
    // Check cryptos
    echo "\nCryptocurrencies in database:\n";
    $cryptos = DB::table('cryptocurrencies')->select('id', 'name', 'symbol', 'current_price', 'logo_url', 'image')->get();
    echo "Total: " . count($cryptos) . "\n";
    foreach ($cryptos as $crypto) {
        echo "  - {$crypto->symbol} ({$crypto->name}) @ {$crypto->current_price} TND\n";
        echo "    Logo: " . ($crypto->logo_url ? 'Yes' : 'No') . "\n";
        echo "    Image: " . ($crypto->image ? 'Yes' : 'No') . "\n";
    }
    
    // Check transactions
    echo "\nTransactions count: " . DB::table('transactions')->count() . "\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

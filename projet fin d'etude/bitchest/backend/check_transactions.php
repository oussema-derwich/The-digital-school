<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

echo "=== CHECKING TRANSACTIONS ===\n\n";

try {
    // Check transactions table structure
    $columns = DB::select("SHOW COLUMNS FROM transactions");
    echo "Transactions table columns:\n";
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
    
    // Check actual transactions
    echo "\nTransactions count: " . Transaction::count() . "\n";
    
    if (Transaction::count() > 0) {
        $tx = Transaction::first();
        echo "\nFirst transaction:\n";
        echo json_encode($tx->toArray(), JSON_PRETTY_PRINT) . "\n";
        
        // Try to load with relationship
        $txWithCrypto = Transaction::with('cryptocurrency')->first();
        echo "\nWith cryptocurrency relation:\n";
        if ($txWithCrypto->cryptocurrency) {
            echo json_encode($txWithCrypto->toArray(), JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "✗ No cryptocurrency loaded\n";
        }
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

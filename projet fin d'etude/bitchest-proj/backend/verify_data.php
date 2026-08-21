<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\User;

echo "=== Database Verification ===\n\n";

// Check users
$users = User::get();
echo "Users count: " . $users->count() . "\n";
foreach ($users->take(2) as $user) {
  echo "- ID: " . $user->id . " | Name: " . $user->name . " | Email: " . $user->email . "\n";
}

echo "\n";

// Check transactions
$transactions = Transaction::with(['user', 'cryptocurrency'])->get();
echo "Transactions count: " . $transactions->count() . "\n";

if ($transactions->count() > 0) {
  foreach ($transactions->take(2) as $tx) {
    echo "- TX ID: " . $tx->id . "\n";
    echo "  User: " . ($tx->user ? $tx->user->name : 'NULL') . "\n";
    echo "  Crypto: " . ($tx->cryptocurrency ? $tx->cryptocurrency->symbol : 'NULL') . "\n";
    echo "  Type: " . $tx->type . "\n";
    echo "  Status: " . $tx->status . "\n";
    echo "\n";
  }
}
?>

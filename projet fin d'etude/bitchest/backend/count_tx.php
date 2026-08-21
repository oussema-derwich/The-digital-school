<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

$count = Transaction::count();
echo "Transactions: " . $count . "\n";
$tx = Transaction::first();
if ($tx) {
  echo "First TX ID: " . $tx->id . "\n";
  echo "User: " . $tx->user_id . "\n";
  echo "Crypto: " . $tx->cryptocurrency_id . "\n";
}
?>

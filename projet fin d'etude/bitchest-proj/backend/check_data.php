<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Cryptocurrencies ===\n";
$cryptos = DB::table('cryptocurrencies')->get();
echo "Total: " . count($cryptos) . "\n";
foreach ($cryptos as $c) {
    echo "ID: {$c->id}, Symbol: {$c->symbol}, Name: {$c->name}\n";
}

echo "\n=== Users ===\n";
$users = DB::table('users')->where('email', 'admin@bitchest.com')->first();
if ($users) {
    echo "Found user: {$users->email}, Balance: {$users->balance_eur}\n";
} else {
    echo "No user found\n";
}

echo "\n=== Wallets ===\n";
$wallets = DB::table('wallets')->get();
echo "Total wallets: " . count($wallets) . "\n";

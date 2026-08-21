<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Update admin user balance to 1,000,000 EUR
$user = User::where('email', 'admin@bitchest.com')->first();
if ($user) {
    $user->balance_eur = 1000000; // 1 million EUR
    $user->save();
    echo "✓ Admin user balance updated to 1,000,000 EUR\n";
} else {
    echo "✗ Admin user not found\n";
}

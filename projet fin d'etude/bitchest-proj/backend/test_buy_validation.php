<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;

// Find admin user
$user = User::where('email', 'admin@bitchest.com')->first();
if (!$user) {
    echo "User not found\n";
    exit(1);
}

// Create token
$token = $user->createToken('test-token')->plainTextToken;

echo "=== Testing /api/wallets/buy ===\n";
echo "Token: " . substr($token, 0, 20) . "...\n\n";

// Simulate request
$request = new Request();
$request->setMethod('POST');
$request->setUserResolver(function () use ($user) {
    return $user;
});

// Test with proper data
$request->replace([
    'cryptocurrency_id' => 1,
    'quantity' => 0.1
]);

echo "Request data: " . json_encode($request->all()) . "\n\n";

// Validate
$validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
    'cryptocurrency_id' => 'required|integer|exists:cryptocurrencies,id',
    'quantity' => 'required|numeric|min:0.00000001'
]);

if ($validator->fails()) {
    echo "Validation errors:\n";
    print_r($validator->errors()->all());
} else {
    echo "✓ Validation passed\n";
    echo "  - cryptocurrency_id: {$request->cryptocurrency_id}\n";
    echo "  - quantity: {$request->quantity}\n";
}

// Check user balance
$crypto = DB::table('cryptocurrencies')->find(1);
$total_cost = 0.1 * $crypto->current_price;
$user_balance = $user->balance_eur;

echo "\nBalance check:\n";
echo "  - User balance: $user_balance EUR\n";
echo "  - Crypto current_price: {$crypto->current_price}\n";
echo "  - Total cost for 0.1: $total_cost EUR\n";
echo "  - Can buy: " . ($user_balance >= $total_cost ? "YES" : "NO") . "\n";

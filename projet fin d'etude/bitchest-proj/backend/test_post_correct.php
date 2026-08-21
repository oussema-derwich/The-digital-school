<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Http;

// Get user with wallet
$user = User::where('email', 'user@bitchest.com')->first();
if (!$user) {
  echo "User not found\n";
  exit(1);
}

echo "User found: " . $user->name . "\n";
echo "Current balance: " . $user->balance_eur . " EUR\n\n";

// Create token
$token = $user->createToken('test-token')->plainTextToken;

// Test POST /transactions with type = sell
$response = Http::withToken($token)->post('http://localhost:8000/api/transactions', [
  'type' => 'sell',
  'cryptocurrency_id' => 1,
  'quantity' => 0.001
]);

echo "POST /transactions Response Status: " . $response->status() . "\n";
echo "Response:\n";
echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n";
?>

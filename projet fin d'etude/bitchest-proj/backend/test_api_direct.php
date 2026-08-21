<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Http;

// Get admin user
$admin = User::where('email', 'admin@bitchest.com')->first();
if (!$admin) {
  echo "Admin user not found\n";
  exit(1);
}

echo "Admin user found: " . $admin->name . " (ID: " . $admin->id . ")\n\n";

// Create token for admin
$token = $admin->createToken('test-token')->plainTextToken;
echo "Token created\n\n";

// Test API endpoint with token
$response = Http::withToken($token)->get('http://localhost:8000/api/admin/transactions');

echo "Response Status: " . $response->status() . "\n";
echo "Response:\n";
echo json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
?>

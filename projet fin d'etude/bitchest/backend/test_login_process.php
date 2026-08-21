<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== VERIFICATION USERS ET LOGIN ===\n\n";

// Lister tous les users
$users = User::all();
echo "Total users in database: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "User ID: {$user->id}\n";
    echo "  Email: {$user->email}\n";
    echo "  Name: {$user->name}\n";
    echo "  Password hash: " . substr($user->password, 0, 20) . "...\n";
    echo "  Role: {$user->role}\n";
    echo "  Is Active: {$user->is_active}\n";
    
    // Tester avec password 'admin123'
    if (Hash::check('admin123', $user->password)) {
        echo "  ✓ Password 'admin123' MATCHES\n";
    } else {
        echo "  ✗ Password 'admin123' does NOT match\n";
    }
    echo "\n";
}

// Tester le login process
echo "\n=== TESTING LOGIN PROCESS ===\n";
$testEmail = 'admin@bitchest.com';
$testPassword = 'admin123';

$user = User::where('email', $testEmail)->first();
if (!$user) {
    echo "✗ User not found\n";
} else {
    echo "✓ User found\n";
    
    if (!Hash::check($testPassword, $user->password)) {
        echo "✗ Password does not match\n";
    } else {
        echo "✓ Password matches\n";
        
        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;
        echo "✓ Token created: " . substr($token, 0, 20) . "...\n";
    }
}

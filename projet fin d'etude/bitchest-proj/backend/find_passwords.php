<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== FINDING CORRECT PASSWORDS ===\n\n";

// Trouver tous les users
$users = User::all();

foreach ($users as $user) {
    echo "User: {$user->email} (ID: {$user->id})\n";
    echo "Name: {$user->name}\n";
    
    // Essayer des passwords communs
    $testPasswords = [
        'test123',
        'admin123',
        'password',
        '123456',
        'test',
        'user123'
    ];
    
    $found = false;
    foreach ($testPasswords as $pwd) {
        if (\Illuminate\Support\Facades\Hash::check($pwd, $user->password)) {
            echo "✓ Password works: '$pwd'\n";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "✗ No common password found\n";
    }
    echo "\n";
}

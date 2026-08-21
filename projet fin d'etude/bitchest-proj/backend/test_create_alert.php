<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Alert;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Set the user
$user = User::find(1);
Auth::login($user);

echo "Creating alert for user: " . Auth::user()->email . "\n";

try {
    $alert = Alert::create([
        'user_id' => Auth::id(),
        'cryptocurrency_id' => 1,
        'price_threshold' => 50000,
        'type' => 'above',
        'is_active' => true
    ]);
    
    echo "✓ Alert created successfully!\n";
    echo "Alert ID: " . $alert->id . "\n";
    echo "Cryptocurrency ID: " . $alert->cryptocurrency_id . "\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

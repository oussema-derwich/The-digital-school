<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Testing Favorite model...\n";
    $count = \App\Models\Favorite::count();
    echo "✓ Favorites count: $count\n";
    
    echo "Testing with user...\n";
    $user = \App\Models\User::first();
    if ($user) {
        $favs = \App\Models\Favorite::where('user_id', $user->id)->get();
        echo "✓ User {$user->id} has " . $favs->count() . " favorites\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

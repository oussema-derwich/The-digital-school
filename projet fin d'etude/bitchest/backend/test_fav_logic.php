<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $db = \DB::table('favorites')->get();
    echo "✓ Favorites table exists with " . count($db) . " records\n";
    
    // Now test the API endpoint logic
    $user = \App\Models\User::find(1);
    if ($user) {
        echo "✓ User found: {$user->email}\n";
        
        $favorites = \App\Models\Favorite::where('user_id', $user->id)
            ->with('cryptocurrency')
            ->get()
            ->map(function ($favorite) {
                return [
                    'id' => $favorite->cryptocurrency->id,
                    'name' => $favorite->cryptocurrency->name,
                    'symbol' => $favorite->cryptocurrency->symbol,
                    'price' => (float)$favorite->cryptocurrency->current_price,
                ];
            });
        echo "✓ Favorites loaded: " . $favorites->count() . " items\n";
        
        // Test response
        $response = [
            'status' => 'success',
            'data' => $favorites->values()->toArray()
        ];
        echo "✓ Response:\n";
        echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "✗ User not found\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
}

<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Cryptocurrency;
use App\Models\PriceHistory;
use Carbon\Carbon;

echo "=== Génération des données de prix historiques ===\n";

$cryptos = Cryptocurrency::all();

if ($cryptos->count() === 0) {
    echo "Aucune cryptomonnaie trouvée! Lancez d'abord le seeder des cryptos.\n";
    exit(1);
}

foreach ($cryptos as $crypto) {
    echo "\nGénération pour {$crypto->name} ({$crypto->symbol})...\n";
    
    // Clear existing history
    PriceHistory::where('cryptocurrency_id', $crypto->id)->delete();
    
    $basePrice = $crypto->current_price;
    $now = now();
    $count = 0;

    for ($i = 309; $i >= 0; $i--) {
        // Variation de prix aléatoire entre -2% et +2%
        $variation = (rand(-200, 200) / 10000); // -2% à +2%
        $price = $basePrice * (1 + $variation);
        
        // Ajouter une tendance progressive
        $trend = ($i / 310) * 0.05; // Tendance sur 5% du prix
        $price = $price * (1 + $trend);

        PriceHistory::create([
            'cryptocurrency_id' => $crypto->id,
            'price' => round($price, 8),
            'created_at' => $now->copy()->subHours($i),
            'updated_at' => $now->copy()->subHours($i),
        ]);
        
        $count++;
    }
    
    echo "  ✓ {$count} entrées créées\n";
}

echo "\n=== Total: " . PriceHistory::count() . " records ===\n";

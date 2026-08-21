<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Cryptocurrency;
use App\Models\PriceHistory;
use Carbon\Carbon;

echo "=== Création des cryptomonnaies ===\n";

$cryptos = [
    [
        'name' => 'Bitcoin',
        'symbol' => 'BTC',
        'current_price' => 42500.00,
        'logo_url' => '/assets/bitcoin.png',
        'description' => 'La première et la plus célèbre cryptomonnaie',
        'is_active' => true,
        'price_change_24h' => 3.72
    ],
    [
        'name' => 'Ethereum',
        'symbol' => 'ETH',
        'current_price' => 2250.00,
        'logo_url' => '/assets/ethereum.png',
        'description' => 'Plateforme de contrats intelligents',
        'is_active' => true,
        'price_change_24h' => -1.12
    ],
    [
        'name' => 'Cardano',
        'symbol' => 'ADA',
        'current_price' => 0.85,
        'logo_url' => '/assets/cardano.png',
        'description' => 'Plateforme blockchain peer-reviewed',
        'is_active' => true,
        'price_change_24h' => 2.45
    ],
    [
        'name' => 'Solana',
        'symbol' => 'SOL',
        'current_price' => 150.00,
        'logo_url' => '/assets/stellar.png',
        'description' => 'Blockchain haute performance',
        'is_active' => true,
        'price_change_24h' => 5.82
    ],
];

foreach ($cryptos as $cryptoData) {
    try {
        $crypto = Cryptocurrency::updateOrCreate(
            ['symbol' => $cryptoData['symbol']],
            $cryptoData
        );
        echo "✓ {$crypto->name} créée (id: {$crypto->id})\n";
    } catch (\Exception $e) {
        echo "✗ Erreur pour {$cryptoData['name']}: {$e->getMessage()}\n";
    }
}

echo "\nTotal: " . Cryptocurrency::count() . " cryptomonnaies\n";

echo "\n=== Création des prix historiques ===\n";

$allCryptos = Cryptocurrency::all();

foreach ($allCryptos as $crypto) {
    echo "\nGénération pour {$crypto->name}...\n";
    
    PriceHistory::where('cryptocurrency_id', $crypto->id)->delete();
    
    $basePrice = $crypto->current_price;
    $now = now();

    for ($i = 309; $i >= 0; $i--) {
        $variation = (rand(-200, 200) / 10000);
        $price = $basePrice * (1 + $variation);
        $trend = ($i / 310) * 0.05;
        $price = $price * (1 + $trend);

        PriceHistory::create([
            'cryptocurrency_id' => $crypto->id,
            'price' => round($price, 8),
            'created_at' => $now->copy()->subHours($i),
            'updated_at' => $now->copy()->subHours($i),
        ]);
    }
    
    $count = PriceHistory::where('cryptocurrency_id', $crypto->id)->count();
    echo "✓ {$count} entrées créées\n";
}

echo "\n✓ TOTAL: " . PriceHistory::count() . " price history records\n";

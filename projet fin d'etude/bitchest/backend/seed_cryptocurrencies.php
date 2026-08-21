<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Cryptocurrency;

echo "=== CREATING CRYPTOCURRENCIES ===\n\n";

$cryptos = [
    [
        'name' => 'Bitcoin',
        'symbol' => 'BTC',
        'current_price' => 42500,
        'image' => '/assets/bitcoin.png',
        'logo_url' => 'https://cryptoicons.org/api/v2/color/bitcoin/32.png',
        'description' => 'Bitcoin est la première et la plus grande cryptomonnaie.',
        'in_stock' => true
    ],
    [
        'name' => 'Ethereum',
        'symbol' => 'ETH',
        'current_price' => 2300,
        'image' => '/assets/ethereum.png',
        'logo_url' => 'https://cryptoicons.org/api/v2/color/ethereum/32.png',
        'description' => 'Ethereum est une plateforme de contrats intelligents.',
        'in_stock' => true
    ],
    [
        'name' => 'Cardano',
        'symbol' => 'ADA',
        'current_price' => 0.98,
        'image' => '/assets/cardano.png',
        'logo_url' => 'https://cryptoicons.org/api/v2/color/cardano/32.png',
        'description' => 'Cardano est une plateforme blockchain de troisième génération.',
        'in_stock' => true
    ],
    [
        'name' => 'Solana',
        'symbol' => 'SOL',
        'current_price' => 180,
        'image' => '/assets/solana.png',
        'logo_url' => 'https://cryptoicons.org/api/v2/color/solana/32.png',
        'description' => 'Solana est une blockchain haute performance.',
        'in_stock' => true
    ]
];

foreach ($cryptos as $crypto) {
    try {
        $exists = Cryptocurrency::where('symbol', $crypto['symbol'])->first();
        if (!$exists) {
            Cryptocurrency::create($crypto);
            echo "✓ {$crypto['symbol']} créé\n";
        } else {
            echo "⚠ {$crypto['symbol']} existe déjà\n";
        }
    } catch (\Exception $e) {
        echo "✗ Erreur pour {$crypto['symbol']}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== CRYPTOCURRENCIES CREATED ===\n";

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Cryptocurrency;

echo "=== UPDATING CRYPTOCURRENCIES ===\n\n";

$updates = [
    'BTC' => [
        'image' => '/assets/bitcoin.png',
        'logo_url' => 'https://cryptoicons.org/api/v2/color/bitcoin/32.png',
        'description' => 'Bitcoin est la première et la plus grande cryptomonnaie.'
    ],
    'ETH' => [
        'image' => '/assets/ethereum.png',
        'logo_url' => 'https://cryptoicons.org/api/v2/color/ethereum/32.png',
        'description' => 'Ethereum est une plateforme de contrats intelligents.'
    ],
    'ADA' => [
        'image' => '/assets/cardano.png',
        'logo_url' => 'https://cryptoicons.org/api/v2/color/cardano/32.png',
        'description' => 'Cardano est une plateforme blockchain de troisième génération.'
    ],
    'SOL' => [
        'image' => '/assets/solana.png',
        'logo_url' => 'https://cryptoicons.org/api/v2/color/solana/32.png',
        'description' => 'Solana est une blockchain haute performance.'
    ]
];

foreach ($updates as $symbol => $data) {
    try {
        $crypto = Cryptocurrency::where('symbol', $symbol)->first();
        if ($crypto) {
            $crypto->update($data);
            echo "✓ {$symbol} updated\n";
        } else {
            echo "✗ {$symbol} not found\n";
        }
    } catch (\Exception $e) {
        echo "✗ Error for {$symbol}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== UPDATE COMPLETE ===\n";

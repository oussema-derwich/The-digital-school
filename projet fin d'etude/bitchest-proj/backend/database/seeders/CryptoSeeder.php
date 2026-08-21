<?php

namespace Database\Seeders;

use App\Models\Cryptocurrency;
use Illuminate\Database\Seeder;

class CryptoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cryptos = [
            [
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'current_price' => 42500.00,
                'logo_url' => '/assets/bitcoin.png',
                'image' => '/assets/bitcoin.png',
                'description' => 'La première et la plus célèbre cryptomonnaie',
                'in_stock' => true,
            ],
            [
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'current_price' => 2250.00,
                'logo_url' => '/assets/ethereum.png',
                'image' => '/assets/ethereum.png',
                'description' => 'Plateforme de contrats intelligents',
                'in_stock' => true,
            ],
            [
                'name' => 'Cardano',
                'symbol' => 'ADA',
                'current_price' => 0.85,
                'logo_url' => '/assets/cardano.png',
                'image' => '/assets/cardano.png',
                'description' => 'Plateforme blockchain peer-reviewed',
                'in_stock' => true,
            ],
            [
                'name' => 'Solana',
                'symbol' => 'SOL',
                'current_price' => 150.00,
                'logo_url' => '/assets/solana.png',
                'image' => '/assets/solana.png',
                'description' => 'Blockchain haute performance',
                'in_stock' => true,
            ],
            [
                'name' => 'Polkadot',
                'symbol' => 'DOT',
                'current_price' => 8.50,
                'logo_url' => '/assets/polkadot.png',
                'image' => '/assets/polkadot.png',
                'description' => 'Réseau interopérable de blockchains',
                'in_stock' => true,
            ],
            [
                'name' => 'Ripple',
                'symbol' => 'XRP',
                'current_price' => 2.10,
                'logo_url' => '/assets/ripple.png',
                'image' => '/assets/ripple.png',
                'description' => 'Protocole de paiement global',
                'in_stock' => true,
            ],
            [
                'name' => 'Litecoin',
                'symbol' => 'LTC',
                'current_price' => 180.00,
                'logo_url' => '/assets/litecoin.png',
                'image' => '/assets/litecoin.png',
                'description' => 'Argent numérique pair-à-pair',
                'in_stock' => true,
            ],
            [
                'name' => 'Chainlink',
                'symbol' => 'LINK',
                'current_price' => 28.00,
                'logo_url' => '/assets/chainlink.png',
                'image' => '/assets/chainlink.png',
                'description' => 'Réseau d\'oracles décentralisés',
                'in_stock' => true,
            ],
            [
                'name' => 'Stellar',
                'symbol' => 'XLM',
                'current_price' => 0.12,
                'logo_url' => '/assets/stellar.png',
                'image' => '/assets/stellar.png',
                'description' => 'Protocole de paiement ouvert',
                'in_stock' => true,
            ],
            [
                'name' => 'Dogecoin',
                'symbol' => 'DOGE',
                'current_price' => 0.32,
                'logo_url' => '/assets/dogecoin.png',
                'image' => '/assets/dogecoin.png',
                'description' => 'La cryptomonnaie du mème',
                'in_stock' => true,
            ],
        ];

        foreach ($cryptos as $crypto) {
            try {
                Cryptocurrency::updateOrCreate(
                    ['symbol' => $crypto['symbol']],
                    $crypto
                );
            } catch (\Exception $e) {
                \Log::error('Error creating cryptocurrency: ' . $e->getMessage());
            }
        }
    }
}

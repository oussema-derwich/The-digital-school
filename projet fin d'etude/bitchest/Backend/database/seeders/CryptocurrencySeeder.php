<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cryptocurrency;
use App\Models\PriceHistory;
use Carbon\Carbon;

class CryptocurrencySeeder extends Seeder
{
    public function run()
    {
        $cryptos = [
            ['name' => 'Bitcoin', 'image' => 'bitcoin.png'],
            ['name' => 'Ethereum', 'image' => 'ethereum.png'],
            ['name' => 'Ripple', 'image' => 'ripple.png'],
            ['name' => 'Bitcoin Cash', 'image' => 'bitcoin_cash.png'],
            ['name' => 'Cardano', 'image' => 'cardano.png'],
            ['name' => 'Litecoin', 'image' => 'litecoin.png'],
            ['name' => 'NEM', 'image' => 'nem.png'],
            ['name' => 'Stellar', 'image' => 'stellar.png'],
            ['name' => 'IOTA', 'image' => 'iota.png'],
            ['name' => 'Dash', 'image' => 'dash.png'],
        ];

        foreach ($cryptos as $cryptoData) {
            // Générer un prix initial réaliste
            $initialPrice =  self::getFirstCotation($cryptoData['name']) * rand(100, 500) + rand(1, 100);

            $crypto = Cryptocurrency::create([
                'name' => $cryptoData['name'],
                'image' => $cryptoData['image'],
                'currentPrice' => $initialPrice,
                'inStock' => round(mt_rand(5000, 20000) / 100, 2), // Stock entre 50.00 et 200.00
            ]);

            // Générer l'historique des prix sur 30 jours
            $this->generatePriceHistory($crypto);
        }
    }

    // Génère un prix initial basé sur le nom de la crypto
    private function getFirstCotation($cryptoname)
    {
        return ord(substr($cryptoname, 0, 1)) + rand(0, 10);
    }

    // Génère un historique des prix sur 30 jours
    private function generatePriceHistory($crypto)
    {
        $currentPrice = $crypto->currentPrice;
        $date = Carbon::now()->subDays(30);

        for ($i = 0; $i < 30; $i++) {
            $variation =  self::getCotationFor($crypto->name);
            $currentPrice = max(0.01, $currentPrice + $variation); // Éviter les prix négatifs

            PriceHistory::create([
                'cryptocurrency_id' => $crypto->id,
                'value' => $currentPrice,
                'date' => $date->copy(),
            ]);

            $date->addDay();
        }
    }

    // Génère une variation de prix aléatoire
    private function getCotationFor($cryptoname)
    {
        return ((rand(0, 99) > 40 ? 1 : -1) * (rand(0, 99) > 49 ? ord(substr($cryptoname, 0, 1)) : ord(substr($cryptoname, -1))) * (rand(1, 10) * 0.01));
    }
}

<?php

namespace Database\Seeders;

use App\Models\Cryptocurrency;
use App\Models\PriceHistory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PriceHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cryptos = Cryptocurrency::all();

        foreach ($cryptos as $crypto) {
            // Générer 310 points de prix historiques (environ 13 jours de données horaires)
            $basePrice = $crypto->current_price;
            $now = now();

            for ($i = 309; $i >= 0; $i--) {
                // Variation de prix aléatoire entre -2% et +2%
                $variation = (rand(-200, 200) / 10000); // -2% à +2%
                $price = $basePrice * (1 + $variation);
                
                // Ajouter une tendance progressive
                $trend = ($i / 310) * 0.05; // Tendance sur 5% du prix
                $price = $price * (1 + $trend);

                PriceHistory::create([
                    'cryptocurrency_id' => $crypto->id,
                    'value' => round($price, 8),
                    'created_at' => $now->copy()->subHours($i),
                    'updated_at' => $now->copy()->subHours($i),
                ]);
            }
        }
    }
}

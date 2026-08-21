<?php

namespace App\Jobs;

use App\Models\Crypto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateCryptoPrices implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Récupérer toutes les cryptos de la base de données
            $cryptos = Crypto::all();
            
            // Pour chaque crypto, faire une requête à l'API pour obtenir le prix actuel
            foreach ($cryptos as $crypto) {
                try {
                    // Exemple avec CoinGecko API (à adapter selon l'API choisie)
                    $response = Http::get("https://api.coingecko.com/api/v3/simple/price", [
                        'ids' => strtolower($crypto->name),
                        'vs_currencies' => 'eur'
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        $price = $data[strtolower($crypto->name)]['eur'] ?? null;

                        if ($price !== null) {
                            $crypto->current_price = $price;
                            $crypto->save();
                            
                            Log::info("Prix mis à jour pour {$crypto->name}: {$price} EUR");
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Erreur lors de la mise à jour du prix de {$crypto->name}: " . $e->getMessage());
                }

                // Attendre un peu entre chaque requête pour respecter les limites de l'API
                sleep(1);
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour des prix: " . $e->getMessage());
        }
    }
}

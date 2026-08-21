<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Crypto;
use App\Events\CryptoPriceAlert;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CryptoMonitoringService
{
    public function checkAlerts()
    {
        $alerts = Alert::with(['user', 'crypto'])->where('is_active', true)->get();
        
        foreach ($alerts as $alert) {
            try {
                $currentPrice = $this->getCurrentPrice($alert->crypto);
                
                if ($this->shouldTriggerAlert($alert, $currentPrice)) {
                    event(new CryptoPriceAlert($alert, $alert->crypto, $currentPrice));
                    
                    // Désactiver l'alerte après déclenchement
                    $alert->is_active = false;
                    $alert->save();
                }
            } catch (\Exception $e) {
                Log::error("Error checking alert {$alert->id}: " . $e->getMessage());
            }
        }
    }

    private function getCurrentPrice(Crypto $crypto)
    {
        // Utiliser le cache pour éviter trop de requêtes API
        return Cache::remember("crypto_price_{$crypto->id}", 60, function () use ($crypto) {
            // Ici, vous pouvez intégrer l'appel à une API externe comme CoinGecko
            return $crypto->current_price;
        });
    }

    private function shouldTriggerAlert(Alert $alert, $currentPrice)
    {
        switch ($alert->condition) {
            case 'above':
                return $currentPrice >= $alert->threshold;
            case 'below':
                return $currentPrice <= $alert->threshold;
            default:
                return false;
        }
    }
}
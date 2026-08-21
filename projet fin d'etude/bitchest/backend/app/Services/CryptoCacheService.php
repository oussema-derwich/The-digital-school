<?php

namespace App\Services;

use App\Models\Crypto;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CryptoCacheService
{
    const CACHE_DURATION = 300; // 5 minutes

    public function getCryptoData($cryptoId)
    {
        return Cache::remember("crypto_{$cryptoId}", self::CACHE_DURATION, function () use ($cryptoId) {
            return Crypto::with(['transactions' => function ($query) {
                $query->latest()->limit(100);
            }])->findOrFail($cryptoId);
        });
    }

    public function getMarketOverview()
    {
        return Cache::remember('market_overview', self::CACHE_DURATION, function () {
            return Crypto::select(['id', 'name', 'symbol', 'current_price', 'price_change_24h', 'market_cap'])
                ->where('is_active', true)
                ->get()
                ->map(function ($crypto) {
                    $crypto->price_formatted = number_format($crypto->current_price, 2);
                    $crypto->change_percentage = number_format($crypto->price_change_24h, 2);
                    return $crypto;
                });
        });
    }

    public function invalidateCryptoCache($cryptoId)
    {
        Cache::forget("crypto_{$cryptoId}");
        Cache::forget('market_overview');
        Log::info("Cache invalidated for crypto ID: {$cryptoId}");
    }

    public function warmupCache()
    {
        try {
            // Précharger les données fréquemment utilisées
            $this->getMarketOverview();
            
            $cryptoIds = Crypto::pluck('id');
            foreach ($cryptoIds as $cryptoId) {
                $this->getCryptoData($cryptoId);
            }
            
            Log::info('Cache warmup completed successfully');
            return true;
        } catch (\Exception $e) {
            Log::error('Cache warmup failed: ' . $e->getMessage());
            return false;
        }
    }
}
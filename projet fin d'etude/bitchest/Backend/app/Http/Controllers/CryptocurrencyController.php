<?php

namespace App\Http\Controllers;

use App\Http\Services\CryptocurrencyService;
use Illuminate\Http\Request;

class CryptocurrencyController extends Controller
{
    protected $cryptocurrencyService;

    public function __construct(CryptocurrencyService $cryptocurrencyService)
    {
        $this->cryptocurrencyService = $cryptocurrencyService;
    }

    // Route pour récupérer les prix actuels
    public function getCurrentPrices()
    {
        $cryptosData = $this->cryptocurrencyService->getCurrentPrices();
        return response()->json($cryptosData);
    }

    // Route pour récupérer l'historique des prix sur 30 jours
    public function getPriceHistory($cryptoName)
    {
        $priceHistory = $this->cryptocurrencyService->getPriceHistory($cryptoName);

        if (!$priceHistory) {
            return response()->json(['error' => 'Cryptocurrency not found'], 404);
        }

        return response()->json([
            'name' => $cryptoName,
            'price_history' => $priceHistory
        ]);
    }
}

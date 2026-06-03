<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\PriceHistoryService;

class PriceHistoryController extends Controller
{
    protected $priceHistoryService;

    public function __construct(PriceHistoryService $priceHistoryService)
    {
        $this->priceHistoryService = $priceHistoryService;
    }

    public function getPriceHistory($cryptoId, Request $request)
    {
        $days = $request->query('days', 30); // Par défaut 30 jours si non fourni
        $history = $this->priceHistoryService->getHistoryForCrypto($cryptoId, $days);

        return response()->json($history);
    }
}

<?php

namespace App\Http\Services;

use App\Models\Cryptocurrency;
use App\Models\PriceHistory;
use Carbon\Carbon;

class PriceHistoryService
{
    public function getHistoryForCrypto($cryptoId, $days)
    {
        $history = PriceHistory::where('cryptocurrency_id', $cryptoId)
            ->orderBy('date', 'desc')
            ->limit($days)
            ->get()
            ->sortBy('date')
            ->values();

        $crypto = Cryptocurrency::select('name', 'image')->find($cryptoId);

        return response()->json([
            'crypto' => $crypto,
            'history' => $history,
        ]);
    }
}

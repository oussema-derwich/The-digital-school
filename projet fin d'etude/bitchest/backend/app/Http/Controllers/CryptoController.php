<?php

namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use App\Models\PriceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CryptoController extends Controller
{
    public function index(): JsonResponse
    {
        $cryptos = Cryptocurrency::all(['id', 'name', 'symbol', 'current_price', 'image', 'logo_url', 'description'])->map(function ($crypto) {
            return [
                'id' => $crypto->id,
                'name' => $crypto->name,
                'symbol' => $crypto->symbol,
                'price' => (float)$crypto->current_price,
                'current_price' => (float)$crypto->current_price,
                'image' => $crypto->image,
                'logo_url' => $crypto->logo_url,
                'description' => $crypto->description,
                'logo' => $crypto->logo_url
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $cryptos
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $crypto = Cryptocurrency::find($id);

        if (!$crypto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cryptomonnaie non trouvée'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $crypto->id,
                'name' => $crypto->name,
                'symbol' => $crypto->symbol,
                'price' => (float)$crypto->current_price,
                'current_price' => (float)$crypto->current_price
            ]
        ]);
    }

    public function history(int $id): JsonResponse
    {
        $crypto = Cryptocurrency::find($id);

        if (!$crypto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cryptomonnaie non trouvée'
            ], 404);
        }

        $history = PriceHistory::where('cryptocurrency_id', $id)
            ->orderBy('created_at', 'asc')
            ->limit(310)
            ->get(['value', 'created_at'])
            ->map(function ($record) {
                return [
                    'price' => (float)$record->value,
                    'timestamp' => $record->created_at->getTimestamp(),
                    'date' => $record->created_at->format('Y-m-d H:i:s')
                ];
            })
            ->values()
            ->toArray();

        return response()->json([
            'status' => 'success',
            'data' => [
                'cryptocurrency_id' => $id,
                'name' => $crypto->name,
                'symbol' => $crypto->symbol,
                'history' => $history
            ]
        ]);
    }

    public function market(): JsonResponse
    {
        $cryptos = Cryptocurrency::all(['id', 'name', 'symbol', 'current_price'])
            ->map(function ($crypto) {
                return [
                    'id' => $crypto->id,
                    'name' => $crypto->name,
                    'symbol' => $crypto->symbol,
                    'price' => (float)$crypto->current_price,
                    'current_price' => (float)$crypto->current_price
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $cryptos
        ]);
    }

    public function count(): JsonResponse
    {
        $count = Cryptocurrency::count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'count' => $count
            ]
        ]);
    }

    public function historyByDays(int $id, int $days): JsonResponse
    {
        $crypto = Cryptocurrency::find($id);

        if (!$crypto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cryptomonnaie non trouvée'
            ], 404);
        }

        $startDate = now()->subDays($days);

        $history = PriceHistory::where('cryptocurrency_id', $id)
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'asc')
            ->get(['value', 'created_at'])
            ->map(function ($record) {
                return [
                    'price' => (float)$record->value,
                    'timestamp' => $record->created_at->getTimestamp(),
                    'date' => $record->created_at->format('Y-m-d H:i:s')
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'cryptocurrency_id' => $id,
                'name' => $crypto->name,
                'symbol' => $crypto->symbol,
                'period_days' => $days,
                'history' => $history
            ]
        ]);
    }
}

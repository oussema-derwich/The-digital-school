<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PriceHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    /**
     * Create a new PortfolioController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum,api');
    }

    /**
     * Get portfolio history for a given period
     */
    public function history(Request $request): JsonResponse
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        
        if (!$wallet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wallet not found'
            ], 404);
        }

        $period = $request->query('period', '7J');

        // Calculate date range
        $now = now();
        $startDate = match($period) {
            '7J' => $now->copy()->subDays(7),
            '30J' => $now->copy()->subDays(30),
            '90J' => $now->copy()->subDays(90),
            '1Y' => $now->copy()->subYear(),
            default => $now->copy()->subDays(7)
        };

        $wallet = $user->wallet;
        if (!$wallet) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }
        
        $walletCryptoIds = $wallet->walletCryptos()->pluck('id');

        // Get all transactions for the user within the period
        $transactions = Transaction::whereIn('wallet_crypto_id', $walletCryptoIds)
            ->whereBetween('created_at', [$startDate, $now])
            ->with('walletCrypto.cryptocurrency')
            ->orderBy('created_at', 'asc')
            ->get();

        // Calculate portfolio value at each point in time
        $portfolioHistory = [];
        $currentHoldings = [];

        // Get all transactions (not just in period) to calculate current holdings
        $allTransactions = Transaction::whereIn('wallet_crypto_id', $walletCryptoIds)
            ->with('walletCrypto.cryptocurrency')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($allTransactions as $transaction) {
            $cryptoId = $transaction->walletCrypto->cryptocurrency_id;

            if (!isset($currentHoldings[$cryptoId])) {
                $currentHoldings[$cryptoId] = [
                    'quantity' => 0,
                    'cost' => 0
                ];
            }

            if ($transaction->type === 'buy') {
                $currentHoldings[$cryptoId]['quantity'] += $transaction->quantity;
                $currentHoldings[$cryptoId]['cost'] += $transaction->total_price;
            } else {
                $currentHoldings[$cryptoId]['quantity'] -= $transaction->quantity;
                $currentHoldings[$cryptoId]['cost'] -= $transaction->total_price;
            }

            // If in the period, calculate portfolio value
            if ($transaction->created_at >= $startDate) {
                $totalValue = (float)$wallet->balance;

                // Add current holdings value
                foreach ($currentHoldings as $id => $holding) {
                    $crypto = \App\Models\Cryptocurrency::find($id);
                    if ($crypto && $holding['quantity'] > 0) {
                        $totalValue += $holding['quantity'] * (float)$crypto->current_price;
                    }
                }

                $portfolioHistory[] = [
                    'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'timestamp' => $transaction->created_at->getTimestamp(),
                    'value' => $totalValue
                ];
            }
        }

        // If no transactions in period, return current portfolio value at start and end
        if (empty($portfolioHistory)) {
            $totalValue = (float)$wallet->balance;
            foreach ($currentHoldings as $id => $holding) {
                $crypto = \App\Models\Cryptocurrency::find($id);
                if ($crypto && $holding['quantity'] > 0) {
                    $totalValue += $holding['quantity'] * (float)$crypto->current_price;
                }
            }

            $portfolioHistory = [
                [
                    'date' => $startDate->format('Y-m-d H:i:s'),
                    'timestamp' => $startDate->getTimestamp(),
                    'value' => $totalValue
                ],
                [
                    'date' => $now->format('Y-m-d H:i:s'),
                    'timestamp' => $now->getTimestamp(),
                    'value' => $totalValue
                ]
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => $period,
                'history' => $portfolioHistory
            ]
        ]);
    }

    /**
     * Get portfolio summary
     */
    public function summary(): JsonResponse
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        // Create wallet if it doesn't exist
        if (!$wallet) {
            $wallet = \App\Models\Wallet::create([
                'user_id' => $user->id
            ]);
        }

        $holdings = $wallet->walletCryptos()->with('cryptocurrency')->get();
        $totalCryptoValue = $holdings->sum(function ($holding) {
            return $holding->getCurrentValue();
        });

        $totalProfitLoss = $holdings->sum(function ($holding) {
            return $holding->getProfitLoss();
        });

        $formattedHoldings = $holdings->map(function ($holding) {
            return [
                'crypto_id' => $holding->cryptocurrency_id,
                'quantity' => (float)$holding->quantity,
                'average_buy_price' => (float)$holding->average_buy_price,
                'current_value' => (float)$holding->getCurrentValue(),
                'profit_loss' => (float)$holding->getProfitLoss(),
                'crypto' => [
                    'id' => $holding->cryptocurrency->id,
                    'name' => $holding->cryptocurrency->name,
                    'symbol' => $holding->cryptocurrency->symbol,
                    'price' => (float)$holding->cryptocurrency->current_price,
                    'price_change_24h' => 0,
                    'logo_url' => $holding->cryptocurrency->logo_url
                ]
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'balance_eur' => (float)$wallet->balance,
                'total_crypto_value' => (float)$totalCryptoValue,
                'total_portfolio_value' => (float)$wallet->balance + $totalCryptoValue,
                'total_profit_loss' => (float)$totalProfitLoss,
                'holdings_count' => $holdings->count(),
                'holdings' => $formattedHoldings
            ]
        ]);
    }
}

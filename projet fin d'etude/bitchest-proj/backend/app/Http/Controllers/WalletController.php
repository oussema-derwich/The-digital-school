<?php

namespace App\Http\Controllers;

use App\Models\WalletCrypto;
use App\Models\Transaction;
use App\Models\Cryptocurrency;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    /**
     * Create a new WalletController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum,api');
    }

    /**
     * Display user's wallet with all holdings.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Portefeuille non trouvé'
            ], 404);
        }

        $holdings = $wallet->walletCryptos()->with('cryptocurrency')->get()->map(function ($holding) {
            return [
                'id' => $holding->id,
                'cryptocurrency_id' => $holding->cryptocurrency_id,
                'symbol' => $holding->cryptocurrency->symbol,
                'name' => $holding->cryptocurrency->name,
                'quantity' => $holding->quantity,
                'avg_buy_price' => $holding->average_buy_price,
                'current_price' => $holding->cryptocurrency->current_price,
                'current_value' => $holding->getCurrentValue(),
                'profit_loss' => $holding->getProfitLoss(),
                'profit_loss_percentage' => $holding->getProfitLossPercentage()
            ];
        });

        $total_crypto_value = $holdings->sum('current_value');

        return response()->json([
            'status' => 'success',
            'data' => [
                'balance_eur' => (float)$wallet->balance,
                'total_crypto_value' => (float)$total_crypto_value,
                'total_portfolio_value' => (float)$wallet->balance + $total_crypto_value,
                'holdings' => $holdings
            ]
        ]);
    }

    /**
     * Buy cryptocurrency.
     */
    public function buy(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cryptocurrency_id' => 'required|integer|exists:cryptocurrencies,id',
            'quantity' => 'required|numeric|min:0.00000001'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $wallet = $user->wallet;
        $quantity = (float)$request->quantity;
        
        // Récupérer le prix courant de la crypto
        $crypto = Cryptocurrency::find($request->cryptocurrency_id);
        if (!$crypto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Crypto-monnaie non trouvée'
            ], 404);
        }
        
        $price = (float)$crypto->current_price;
        $total_cost = $quantity * $price;
        $wallet_balance = (float)$wallet->balance;

        // Vérifier le solde
        if ($wallet_balance < $total_cost) {
            return response()->json([
                'status' => 'error',
                'message' => "Solde insuffisant pour cet achat",
                'data' => [
                    'solde_disponible' => $wallet_balance,
                    'cout_total' => $total_cost,
                    'manque' => $total_cost - $wallet_balance
                ]
            ], 400);
        }

        try {
            // Débiter le portefeuille
            $wallet->balance -= $total_cost;
            $wallet->save();

            // Trouver ou créer le holding
            $holding = WalletCrypto::firstOrCreate(
                ['wallet_id' => $wallet->id, 'cryptocurrency_id' => $request->cryptocurrency_id],
                ['quantity' => 0, 'average_buy_price' => 0]
            );

            // Calculer le nouveau prix moyen
            $old_quantity = (float)$holding->quantity;
            $old_total_cost = $old_quantity * (float)$holding->average_buy_price;
            $new_quantity = $old_quantity + $quantity;
            $new_avg_price = $new_quantity > 0 ? ($old_total_cost + $total_cost) / $new_quantity : $price;

            $holding->update([
                'quantity' => $new_quantity,
                'average_buy_price' => $new_avg_price
            ]);

            // Créer la transaction
            $transaction = Transaction::create([
                'wallet_crypto_id' => $holding->id,
                'type' => 'buy',
                'quantity' => $quantity,
                'unit_price' => $price,
                'total_price' => $total_cost,
                'status' => 'completed'
            ]);

            // Créer notification d'achat
            if ($crypto) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'message' => "Achat réussi - {$crypto->symbol}: {$quantity} unités à {$price}€ (Total: {$total_cost}€)"
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Achat réussi',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'crypto' => [
                        'id' => $crypto->id,
                        'symbol' => $crypto->symbol,
                        'name' => $crypto->name,
                        'price_achat' => $price,
                        'quantite_achetee' => $quantity
                    ],
                    'achat' => [
                        'type' => 'buy',
                        'quantite' => $quantity,
                        'prix_unitaire' => $price,
                        'cout_total' => $total_cost,
                        'date' => $transaction->created_at->format('Y-m-d H:i:s')
                    ],
                    'holding' => [
                        'quantite_totale' => $new_quantity,
                        'prix_moyen' => $new_avg_price,
                        'valeur_portefeuille' => $new_quantity * $crypto->current_price,
                        'profit_loss' => ($crypto->current_price - $new_avg_price) * $new_quantity
                    ],
                    'portefeuille' => [
                        'solde_eur' => (float)$wallet->balance,
                        'ancien_solde' => $wallet_balance
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'achat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sell cryptocurrency.
     */
    public function sell(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cryptocurrency_id' => 'required|integer|exists:cryptocurrencies,id',
            'quantity' => 'required|numeric|min:0.00000001'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $wallet = $user->wallet;
        $quantity = (float)$request->quantity;
        
        // Récupérer le prix courant de la crypto
        $crypto = Cryptocurrency::find($request->cryptocurrency_id);
        if (!$crypto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Crypto-monnaie non trouvée'
            ], 404);
        }
        
        $price = (float)$crypto->current_price;
        $total_revenue = $quantity * $price;

        // Vérifier le holding
        $holding = WalletCrypto::where('wallet_id', $wallet->id)
            ->where('cryptocurrency_id', $request->cryptocurrency_id)
            ->first();

        if (!$holding || (float)$holding->quantity < $quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quantité insuffisante pour cette vente',
                'data' => [
                    'quantite_possedee' => $holding ? (float)$holding->quantity : 0,
                    'quantite_demandee' => $quantity
                ]
            ], 400);
        }

        try {
            $old_balance = (float)$wallet->balance;
            $old_avg_price = (float)$holding->average_buy_price;
            
            // Créditer le portefeuille
            $wallet->balance += $total_revenue;
            $wallet->save();

            // Réduire le holding
            $new_quantity = (float)$holding->quantity - $quantity;

            if ($new_quantity <= 0) {
                $holding->delete();
            } else {
                $holding->update(['quantity' => $new_quantity]);
            }

            // Créer la transaction
            $transaction = Transaction::create([
                'wallet_crypto_id' => $holding->id,
                'type' => 'sell',
                'quantity' => $quantity,
                'unit_price' => $price,
                'total_price' => $total_revenue,
                'status' => 'completed'
            ]);

            // Calculer le profit/loss
            $profit_loss = ($price - $old_avg_price) * $quantity;
            $profit_loss_percentage = $old_avg_price > 0 ? (($price - $old_avg_price) / $old_avg_price) * 100 : 0;

            // Créer notification de vente
            if ($crypto) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'message' => "Vente réussie - {$crypto->symbol}: {$quantity} unités à {$price}€ (Total: {$total_revenue}€)"
                ]);
            }

            // Notifier si solde trop bas
            if ($wallet->balance < 100) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'message' => "Solde faible: Votre solde EUR est maintenant de {$wallet->balance}€"
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Vente réussie',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'crypto' => [
                        'id' => $crypto->id,
                        'symbol' => $crypto->symbol,
                        'name' => $crypto->name,
                        'prix_vente' => $price,
                        'quantite_vendue' => $quantity
                    ],
                    'vente' => [
                        'type' => 'sell',
                        'quantite' => $quantity,
                        'prix_unitaire' => $price,
                        'revenu_total' => $total_revenue,
                        'date' => $transaction->created_at->format('Y-m-d H:i:s')
                    ],
                    'profit_loss' => [
                        'montant' => $profit_loss,
                        'pourcentage' => $profit_loss_percentage,
                        'prix_achat_moyen' => $old_avg_price,
                        'prix_vente' => $price
                    ],
                    'holding' => [
                        'quantite_restante' => $new_quantity,
                        'status' => $new_quantity > 0 ? 'actif' : 'liquidé'
                    ],
                    'portefeuille' => [
                        'ancien_solde' => $old_balance,
                        'nouveau_solde' => (float)$wallet->balance
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la vente: ' . $e->getMessage()
            ], 500);
        }
    }
}


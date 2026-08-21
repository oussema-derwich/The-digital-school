<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum,api');
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        
        if (!$wallet) {
            return response()->json([
                'status' => 'success',
                'data' => [],
                'statistiques' => [
                    'total_achats' => ['montant' => 0, 'nombre' => 0],
                    'total_ventes' => ['montant' => 0, 'nombre' => 0],
                    'total_transactions' => 0
                ]
            ]);
        }
        
        $walletCryptoIds = $wallet->walletCryptos()->pluck('id');
        
        $transactions = Transaction::whereIn('wallet_crypto_id', $walletCryptoIds)
            ->with('walletCrypto.cryptocurrency')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'crypto' => [
                        'id' => $transaction->walletCrypto->cryptocurrency->id,
                        'symbol' => $transaction->walletCrypto->cryptocurrency->symbol,
                        'name' => $transaction->walletCrypto->cryptocurrency->name,
                        'price_courant' => (float)$transaction->walletCrypto->cryptocurrency->current_price
                    ],
                    'transaction' => [
                        'quantite' => (float)$transaction->quantity,
                        'prix_unitaire' => (float)$transaction->unit_price,
                        'montant_eur' => (float)$transaction->total_price,
                        'type' => $transaction->type === 'buy' ? 'Achat' : 'Vente'
                    ],
                    'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'timestamp' => $transaction->created_at->timestamp
                ];
            });

        // Calculer les statistiques
        $total_buy = Transaction::whereIn('wallet_crypto_id', $walletCryptoIds)
            ->where('type', 'buy')
            ->sum('total_price');
        
        $total_sell = Transaction::whereIn('wallet_crypto_id', $walletCryptoIds)
            ->where('type', 'sell')
            ->sum('total_price');

        $count_buy = Transaction::whereIn('wallet_crypto_id', $walletCryptoIds)->where('type', 'buy')->count();
        $count_sell = Transaction::whereIn('wallet_crypto_id', $walletCryptoIds)->where('type', 'sell')->count();

        return response()->json([
            'status' => 'success',
            'data' => $transactions,
            'statistiques' => [
                'total_achats' => [
                    'montant' => (float)$total_buy,
                    'nombre' => $count_buy
                ],
                'total_ventes' => [
                    'montant' => (float)$total_sell,
                    'nombre' => $count_sell
                ],
                'total_transactions' => $count_buy + $count_sell
            ]
        ]);
    }

    public function exportCSV(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        
        if (!$wallet) {
            abort(404, 'Wallet not found');
        }
        
        $walletCryptoIds = $wallet->walletCryptos()->pluck('id');
        $transactions = Transaction::whereIn('wallet_crypto_id', $walletCryptoIds)
            ->with('walletCrypto.cryptocurrency')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="transactions_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Date', 'Type', 'Crypto', 'Quantité', 'Prix Unitaire €', 'Montant Total €'], ';');
            
            // Data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->type === 'buy' ? 'Achat' : 'Vente',
                    $transaction->walletCrypto->cryptocurrency->symbol,
                    $transaction->quantity,
                    $transaction->unit_price,
                    $transaction->total_price
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPDF(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        
        if (!$wallet) {
            abort(404, 'Wallet not found');
        }
        
        $walletCryptoIds = $wallet->walletCryptos()->pluck('id');
        $transactions = Transaction::whereIn('wallet_crypto_id', $walletCryptoIds)
            ->with('walletCrypto.cryptocurrency')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'type' => $transaction->type === 'buy' ? 'Achat' : 'Vente',
                    'crypto' => $transaction->walletCrypto->cryptocurrency->symbol,
                    'quantity' => $transaction->quantity,
                    'unit_price' => $transaction->unit_price,
                    'total_price' => $transaction->total_price
                ];
            });

        // Generate simple HTML for PDF
        $html = '<html><head><meta charset="utf-8"><style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style></head><body>';
        
        $html .= '<h1>Transactions - ' . $user->email . '</h1>';
        $html .= '<p>Généré le ' . now()->format('d/m/Y H:i:s') . '</p>';
        $html .= '<table>';
        $html .= '<tr><th>Date</th><th>Type</th><th>Crypto</th><th>Quantité</th><th>Prix Unitaire €</th><th>Montant €</th></tr>';
        
        foreach ($transactions as $t) {
            $html .= '<tr>';
            $html .= '<td>' . $t['date'] . '</td>';
            $html .= '<td>' . $t['type'] . '</td>';
            $html .= '<td>' . $t['crypto'] . '</td>';
            $html .= '<td>' . $t['quantity'] . '</td>';
            $html .= '<td>' . number_format($t['unit_price'], 2, ',', ' ') . '</td>';
            $html .= '<td>' . number_format($t['total_price'], 2, ',', ' ') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table></body></html>';

        // Save HTML to temp file
        $filename = 'transactions_' . date('Y-m-d') . '.html';
        $path = storage_path('app/temp/' . $filename);
        
        // Create directory if not exists
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        file_put_contents($path, $html);
        
        return response()->download($path, $filename, [
            'Content-Type' => 'text/html; charset=utf-8'
        ])->deleteFileAfterSend();
    }

    public function proof($id): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $user = Auth::user();
        $transaction = Transaction::where('user_id', $user->id)
            ->where('id', $id)
            ->with('cryptocurrency')
            ->firstOrFail();

        // Generate proof HTML
        $html = '<html><head><meta charset="utf-8"><style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .proof-container { border: 2px solid #333; padding: 20px; }
            .row { display: flex; justify-content: space-between; margin: 10px 0; }
            .label { font-weight: bold; }
            .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
        </style></head><body>';
        
        $html .= '<div class="header"><h1>Preuve de Transaction</h1></div>';
        $html .= '<div class="proof-container">';
        $html .= '<div class="row"><span class="label">ID Transaction:</span><span>' . $transaction->id . '</span></div>';
        $html .= '<div class="row"><span class="label">Date:</span><span>' . $transaction->created_at->format('d/m/Y H:i:s') . '</span></div>';
        $html .= '<div class="row"><span class="label">Utilisateur:</span><span>' . $user->email . '</span></div>';
        $html .= '<div class="row"><span class="label">Type:</span><span>' . ($transaction->type === 'buy' ? 'Achat' : 'Vente') . '</span></div>';
        $html .= '<div class="row"><span class="label">Crypto:</span><span>' . $transaction->cryptocurrency->symbol . ' (' . $transaction->cryptocurrency->name . ')</span></div>';
        $html .= '<div class="row"><span class="label">Quantité:</span><span>' . $transaction->quantity . '</span></div>';
        $html .= '<div class="row"><span class="label">Prix Unitaire:</span><span>€ ' . number_format($transaction->unit_price, 2, ',', ' ') . '</span></div>';
        $html .= '<div class="row"><span class="label">Montant Total:</span><span>€ ' . number_format($transaction->total_price, 2, ',', ' ') . '</span></div>';
        $html .= '</div>';
        $html .= '<div class="footer"><p>Généré le ' . now()->format('d/m/Y à H:i:s') . '</p>';
        $html .= '<p>Cette preuve certifie la transaction effectuée sur BitChest</p></div>';
        $html .= '</body></html>';

        // Save to temp
        $filename = 'proof_transaction_' . $transaction->id . '.html';
        $path = storage_path('app/temp/' . $filename);
        
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        file_put_contents($path, $html);
        
        return response()->download($path, $filename, [
            'Content-Type' => 'text/html; charset=utf-8'
        ])->deleteFileAfterSend();
    }
}

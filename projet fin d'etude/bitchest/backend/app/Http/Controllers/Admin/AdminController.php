<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Crypto;
use App\Models\Transaction;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\AuditService;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Build a compatibility `stats` object expected by the frontend
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $newUsers = User::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        $transactionVolume = Transaction::sum('amount');
        $transactionCount = Transaction::count();
        $todayTransactions = Transaction::whereDate('created_at', Carbon::today())->count();

        $activeAlerts = Alert::where('is_active', true)->count();
        $totalTraded = $transactionVolume;

        // recent transactions (limit 10)
        $recentTransactions = Transaction::with(['user', 'crypto'])->orderBy('created_at', 'desc')->limit(10)->get();

        // Convert avatar paths to full URLs
        $recentTransactions->each(function($transaction) {
            if ($transaction->user && $transaction->user->avatar) {
                $transaction->user->avatar = str_starts_with($transaction->user->avatar, 'http') 
                    ? $transaction->user->avatar 
                    : asset('storage/' . $transaction->user->avatar);
            }
        });

        $stats = [
            'activeUsers' => $activeUsers,
            'userGrowth' => $newUsers,
            'transactionVolume' => $transactionVolume,
            'transactionCount' => $transactionCount,
            'activeAlerts' => $activeAlerts,
            'totalTraded' => $totalTraded,
            'marketData' => $this->getMarketEvolution(),
            'recentTransactions' => $recentTransactions
        ];

        return response()->json(['stats' => $stats]);
    }

    public function approveUser(User $user)
    {
        $user->is_active = true;
        $user->save();

        try { app(AuditService::class)->logAction('approve', $user); } catch (\Exception $e) {}

        return response()->json(['message' => 'Utilisateur approuvé', 'user' => $user]);
    }

    public function suspendUser(User $user)
    {
        $user->is_active = false;
        $user->save();

        try { app(AuditService::class)->logAction('suspend', $user); } catch (\Exception $e) {}

        return response()->json(['message' => 'Utilisateur suspendu', 'user' => $user]);
    }

    public function activateUser(User $user)
    {
        $user->is_active = true;
        $user->save();

        try { app(AuditService::class)->logAction('activate', $user); } catch (\Exception $e) {}

        return response()->json(['message' => 'Utilisateur réactivé', 'user' => $user]);
    }

    public function exportTransactions(Request $request)
    {
        $transactions = Transaction::with(['user', 'crypto'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Create CSV content (simple, Excel-friendly)
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions_export.csv"'
        ];

        $callback = function() use ($transactions) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Utilisateur', 'Email', 'Type', 'Crypto', 'Quantité', 'Prix', 'Montant']);
            foreach ($transactions as $tx) {
                fputcsv($out, [
                    $tx->created_at->toDateTimeString(),
                    $tx->user->name ?? '',
                    $tx->user->email ?? '',
                    $tx->type,
                    $tx->crypto->symbol ?? $tx->crypto->name ?? '',
                    $tx->quantity,
                    $tx->price,
                    $tx->amount
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateAlert(Request $request, Alert $alert)
    {
        $validated = $request->validate([
            'is_active' => 'sometimes|boolean',
            'price_threshold' => 'sometimes|numeric|min:0'
        ]);

        $original = $alert->getOriginal();
        $alert->update($validated);

        try { app(AuditService::class)->logAction('update', $alert, ['before' => $original, 'after' => $alert->getAttributes()]); } catch (\Exception $e) {}

        return response()->json(['message' => 'Alerte mise à jour', 'alert' => $alert]);
    }

    public function users()
    {
        $users = User::withCount(['transactions', 'wallets'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Convert avatar paths to full URLs
        $users->getCollection()->transform(function($user) {
            if ($user->avatar) {
                $user->avatar = str_starts_with($user->avatar, 'http') 
                    ? $user->avatar 
                    : asset('storage/' . $user->avatar);
            }
            return $user;
        });

        return response()->json($users);
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'is_active' => 'sometimes|boolean',
            'role' => 'sometimes|in:user,admin'
        ]);

        $original = $user->getOriginal();
        $user->update($validated);

        // Audit log
        try {
            app(AuditService::class)->logAction('update', $user, ['before' => $original, 'after' => $user->getAttributes()]);
        } catch (\Exception $e) {
            // ignore audit errors
        }

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user
        ]);
    }

    public function deleteUser(User $user)
    {
        // Soft delete or deactivate to preserve history
        $user->is_active = false;
        $user->deleted_at = now();
        $user->save();

        try {
            app(AuditService::class)->logAction('delete', $user, [], []);
        } catch (\Exception $e) {}

        return response()->json(['message' => 'Utilisateur désactivé (historique conservé)']);
    }

    public function cryptos()
    {
        $cryptos = Crypto::withCount('transactions')
            ->orderBy('name')
            ->get();

        return response()->json($cryptos);
    }

    public function updateCrypto(Request $request, Crypto $crypto)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'symbol' => 'sometimes|string|max:10',
            'current_price' => 'sometimes|numeric|min:0'
        ]);

        $original = $crypto->getOriginal();
        $crypto->update($validated);

        try {
            app(AuditService::class)->logAction('update', $crypto, ['before' => $original, 'after' => $crypto->getAttributes()]);
        } catch (\Exception $e) {}

        return response()->json([
            'message' => 'Cryptomonnaie mise à jour avec succès',
            'crypto' => $crypto
        ]);
    }

    public function transactions()
    {
        $transactions = Transaction::with(['user', 'crypto'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Convert avatar paths to full URLs
        $transactions->getCollection()->transform(function($transaction) {
            if ($transaction->user && $transaction->user->avatar) {
                $transaction->user->avatar = str_starts_with($transaction->user->avatar, 'http') 
                    ? $transaction->user->avatar 
                    : asset('storage/' . $transaction->user->avatar);
            }
            return $transaction;
        });

        return response()->json($transactions);
    }

    public function alerts()
    {
        $alerts = Alert::with(['user', 'crypto'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($alerts);
    }

    private function getMarketEvolution()
    {
        return Crypto::select('id', 'name', 'symbol')
            ->withCount(['transactions as total_volume' => function($query) {
                $query->select(DB::raw('sum(amount)'));
            }])
            ->get()
            ->map(function($crypto) {
                $prices = $crypto->priceHistory()
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->orderBy('created_at')
                    ->get(['price', 'created_at'])
                    ->map(function($history) {
                        return [
                            'price' => $history->price,
                            'date' => $history->created_at->format('Y-m-d')
                        ];
                    });

                return [
                    'id' => $crypto->id,
                    'name' => $crypto->name,
                    'symbol' => $crypto->symbol,
                    'volume' => $crypto->total_volume,
                    'price_history' => $prices
                ];
            });
    }
}
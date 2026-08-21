<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Events\SuspiciousActivity;

class SecurityMonitoringService
{
    const SUSPICIOUS_AMOUNT_THRESHOLD = 50000; // 50,000 en devise locale
    const MAX_TRANSACTIONS_PER_HOUR = 20;
    const UNUSUAL_PRICE_CHANGE_THRESHOLD = 0.15; // 15%

    public function checkTransaction(Transaction $transaction)
    {
        $flags = [];

        // Vérifier le montant inhabituel
        if ($transaction->amount > self::SUSPICIOUS_AMOUNT_THRESHOLD) {
            $flags[] = 'high_amount';
        }

        // Vérifier la fréquence des transactions
        $recentTransactions = $this->getRecentTransactions($transaction->user_id);
        if ($recentTransactions > self::MAX_TRANSACTIONS_PER_HOUR) {
            $flags[] = 'high_frequency';
        }

        // Vérifier les changements de prix inhabituels
        if ($this->hasUnusualPriceChange($transaction)) {
            $flags[] = 'unusual_price';
        }

        // Si des drapeaux sont levés, logger et notifier
        if (!empty($flags)) {
            $this->handleSuspiciousActivity($transaction, $flags);
        }

        return $flags;
    }

    private function getRecentTransactions($userId)
    {
        $wallet = \App\Models\User::find($userId)->wallet;
        if (!$wallet) return 0;
        
        $walletCryptoIds = $wallet->walletCryptos()->pluck('id');
        return Transaction::whereIn('wallet_crypto_id', $walletCryptoIds)
            ->where('created_at', '>=', now()->subHour())
            ->count();
    }

    private function hasUnusualPriceChange($transaction)
    {
        $crypto = $transaction->crypto;
        $priceChange = abs(($transaction->price - $crypto->current_price) / $crypto->current_price);
        return $priceChange > self::UNUSUAL_PRICE_CHANGE_THRESHOLD;
    }

    private function handleSuspiciousActivity($transaction, $flags)
    {
        $logData = [
            'transaction_id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'amount' => $transaction->amount,
            'flags' => $flags,
            'timestamp' => now()
        ];

        // Logger l'activité suspecte
        Log::channel('security')->warning('Suspicious activity detected', $logData);

        // Émettre un événement pour notification en temps réel
        event(new SuspiciousActivity($logData));

        // Si nécessaire, bloquer temporairement l'utilisateur
        if (count($flags) >= 2) {
            $this->restrictUserAccess($transaction->user_id);
        }
    }

    private function restrictUserAccess($userId)
    {
        $user = User::find($userId);
        $user->is_restricted = true;
        $user->restricted_until = now()->addHours(24);
        $user->save();

        Log::channel('security')->alert("User {$userId} access restricted due to suspicious activity");
    }
}
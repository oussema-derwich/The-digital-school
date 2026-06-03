<?php
namespace App\Http\Services;

use App\Models\Transaction;
use App\Models\CryptoWallet;
use Illuminate\Support\Facades\Auth;

class CryptoWalletService
{
    public function getCryptoPurchases($cryptoId)
    {
        $userId = Auth::id(); // Get the current logged-in user's ID

        // Check if the user has a wallet containing this cryptocurrency
        $cryptoWallet = CryptoWallet::whereHas('wallet', function ($query) use ($userId) {
            $query->where('idUser', $userId);
        })->where('idCrypto', $cryptoId)->first();

        if (!$cryptoWallet) {
            return ['error' => 'No wallet found for this cryptocurrency.'];
        }

        // Get all 'buy' transactions related to this wallet
        $purchases = Transaction::where('idCryptoWallet', $cryptoWallet->id)
            ->where('type', 'buy') // Filter only purchases
            ->orderBy('date', 'desc') // Sort by date, most recent first
            ->get();

        if ($purchases->isEmpty()) {
            return ['error' => 'No purchases found for this cryptocurrency.'];
        }

        return $purchases;
    }
}

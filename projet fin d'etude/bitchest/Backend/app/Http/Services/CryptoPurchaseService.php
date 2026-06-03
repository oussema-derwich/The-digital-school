<?php
namespace App\Http\Services;

use App\Models\Wallet;
use App\Models\CryptoWallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CryptoPurchaseService
{
    public function buyCrypto($cryptoId, $quantity, $price)
    {
        $user = Auth::user();
        $totalPrice = $quantity * $price;

        DB::beginTransaction();
        try {
            // Check if the user has a wallet
            $wallet = Wallet::where('idUser', $user->id)->first();
            if (!$wallet) {
                return ['error' => 'Wallet not found.'];
            }

            // Check if the balance is sufficient
            if ($wallet->balance < $totalPrice) {
                return ['error' => 'Insufficient balance.'];
            }

            // Check if the cryptocurrency exists and if there's sufficient stock
            $cryptoWallet = CryptoWallet::where('idCrypto', $cryptoId)->first();
            if (!$cryptoWallet) {
                return ['error' => 'Cryptocurrency not found.'];
            }

            // Assuming you have a field `maxQuantity` in the cryptoWallet model that represents the max available stock
            $availableQuantity = $cryptoWallet->quantity;
            if ($quantity > $availableQuantity) {
                return ['error' => 'Insufficient stock for the requested quantity.'];
            }

            // Update or create the CryptoWallet entry
            $userCryptoWallet = CryptoWallet::where('idWallet', $wallet->id)
                ->where('idCrypto', $cryptoId)
                ->first();

            if ($userCryptoWallet) {
                $userCryptoWallet->quantity += $quantity;
                $userCryptoWallet->save();
            } else {
                $userCryptoWallet = CryptoWallet::create([
                    'idWallet' => $wallet->id,
                    'idCrypto' => $cryptoId,
                    'quantity' => $quantity,
                    'status' => 'active',
                ]);
            }

            // Debit the wallet balance
            $wallet->balance -= $totalPrice;
            $wallet->save();

            // Register the transaction
            Transaction::create([
                'idCryptoWallet' => $userCryptoWallet->id,
                'quantity' => $quantity,
                'unitPrice' => $price,
                'totalPrice' => $totalPrice,
                'operationStatus' => 'completed',
                'date' => now(),
                'type' => 'buy',
            ]);

            DB::commit();
            return ['message' => 'Crypto purchase successful!'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['error' => 'Purchase failed.', 'details' => $e->getMessage()];
        }
    }

    public function getUserWallet()
    {
        $user = Auth::user();

        // Get the user's wallet
        $wallet = Wallet::where('idUser', $user->id)->first();
        if (!$wallet) {
            return ['error' => 'Wallet not found.'];
        }

        // Get the cryptocurrencies owned by the user
        $cryptoWallets = CryptoWallet::where('idWallet', $wallet->id)
            ->with('cryptocurrency')
            ->get();

        $cryptoWalletDetails = $cryptoWallets->map(function ($cryptoWallet) {
            $latestTransaction = $cryptoWallet->transactions()->latest('date')->first();

            return [
                'id' => $cryptoWallet->cryptocurrency->id,
                'image' => $cryptoWallet->cryptocurrency->image,
                'name' => $cryptoWallet->cryptocurrency->name,
                'quantity' => (float) $cryptoWallet->quantity,
                'unitPrice' => (float) ($latestTransaction->unitPrice ?? 0),
                'purchaseDate' => $latestTransaction->date ?? null,
                'totalPrice' => (float) ($cryptoWallet->quantity * ($latestTransaction->unitPrice ?? 0)),
            ];
        });

        return $cryptoWalletDetails;
    }

    public function getUserTransactions($role)
    {
        $user = Auth::user();

        if ($role === 'admin') {
            $transactions = Transaction::with(['cryptoWallet.cryptocurrency', 'cryptoWallet.wallet.user'])
                ->orderBy('date', 'desc')
                ->get();
        } else {
            $wallet = Wallet::where('idUser', $user->id)->first();
            if (!$wallet) {
                return ['error' => 'Wallet not found.'];
            }

            $transactions = Transaction::whereHas('cryptoWallet', function ($query) use ($wallet) {
                $query->where('idWallet', $wallet->id);
            })
                ->with(['cryptoWallet.cryptocurrency', 'cryptoWallet.wallet.user'])
                ->orderBy('date', 'desc')
                ->get();
        }

        return $transactions->map(function ($transaction) {
            return [
                'userId' => $transaction->cryptoWallet->wallet->user->id ?? null,
                'userName' => $transaction->cryptoWallet->wallet->user->name ?? 'Unknown',
                'cryptoName' => $transaction->cryptoWallet->cryptocurrency->name ?? 'Unknown',
                'cryptoImage' => $transaction->cryptoWallet->cryptocurrency->image ?? null,
                'quantity' => $transaction->quantity,
                'unitPrice' => $transaction->unitPrice,
                'totalPrice' => $transaction->totalPrice,
                'operationStatus' => $transaction->operationStatus,
                'transactionDate' => $transaction->date,
                'type' => $transaction->type,
            ];
        });
    }
// getTotalCryptoPurchases
public function getTotalCryptoPurchases()
{
    $user = Auth::user();

    // Retrieve the wallet for the user
    $wallet = Wallet::where('idUser', $user->id)->first();

    // Check if the wallet exists
    if (!$wallet) {
        return ['error' => 'Wallet not found.'];
    }

    // Get the total purchase amount for the user
    $totalPurchases = Transaction::whereHas('cryptoWallet', function ($query) use ($wallet) {
        $query->where('idWallet', $wallet->id);
    })
    ->where('type', 'buy')
    ->sum('totalPrice');

    return ['totalCryptoPurchase' => (float) $totalPurchases];
}



}

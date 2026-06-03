<?php

namespace App\Http\Services;

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class WalletService
{
    // Créer un wallet pour l'utilisateur si il n'en a pas
    public function createWallet()
    {
        $user = Auth::user();  // Récupérer l'utilisateur authentifié

        // Vérifier si l'utilisateur a déjà un wallet
        $wallet = Wallet::where('idUser', $user->id)->first();
        if (!$wallet) {
            // Si aucun wallet n'est trouvé, créer un nouveau wallet
            $wallet = Wallet::create([
                'idUser' => $user->id,
                'balance' => 0, // Solde initial
            ]);
        }

        return $wallet;
    }

    // Récupérer les informations du wallet de l'utilisateur
    public function getWalletInfo()
    {
        $user = Auth::user();

        // Récupérer le wallet de l'utilisateur
        $wallet = Wallet::where('idUser', $user->id)->first();

        // Vérifier si le wallet existe
        if (!$wallet) {
            return null;
        }

        return $wallet;
    }
}

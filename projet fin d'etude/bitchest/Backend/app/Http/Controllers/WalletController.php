<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\WalletService;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->middleware('auth:sanctum');  // Vérifie si l'utilisateur est authentifié
        $this->walletService = $walletService;  // Injection du service
    }

    // Créer un wallet si l'utilisateur n'en a pas encore
    public function createWallet(Request $request)
    {
        $wallet = $this->walletService->createWallet();  // Appel à la méthode du service

        return response()->json(['wallet' => $wallet], 201);
    }

    // Obtenir les informations du wallet de l'utilisateur
    public function getWalletInfo()
    {
        $wallet = $this->walletService->getWalletInfo();  // Appel à la méthode du service

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found.'], 404);
        }

        return response()->json([
            'balance' => $wallet->balance,
            'public_address' => $wallet->public_address,
            'private_address' => $wallet->private_address, // ⚠️ Peut-être à masquer pour la sécurité
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Services\StatsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    protected $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Vérifie que l'ID dans la requête correspond à l'utilisateur authentifié
     */
    protected function validateUserId(Request $request): ?JsonResponse
    {
        $userId = $request->input('user_id');
        $authenticatedUserId = auth()->id();

        if ($userId != $authenticatedUserId) {
            return response()->json([
                'message' => 'Unauthorized: You can only access your own data.'
            ], 403);
        }

        return null;
    }
    /**
     * Classement des utilisateurs qui font le plus d'achats
     */
    public function getTopBuyers(): JsonResponse
    {
        return response()->json($this->statsService->getTopBuyers());
    }

    /**
     * Classement des utilisateurs avec le plus grand portefeuille
     */
    public function getTopWallets(): JsonResponse
    {
        return response()->json($this->statsService->getTopWallets());
    }

    /**
     * Cryptos les plus populaires
     */
    public function getMostPopularCryptos(): JsonResponse
    {
        return response()->json($this->statsService->getMostPopularCryptos());
    }

    /**
     * Volume total des transactions
     */
    public function getTotalTransactionVolume(): JsonResponse
    {
        return response()->json($this->statsService->getTotalTransactionVolume());
    }

    /**
     * Activité récente des utilisateurs
     */
    public function getRecentActivity(Request $request): JsonResponse
    {
        $limit = $request->input('limit');
        return response()->json($this->statsService->getRecentActivity($limit));
    }

    /**
     * Utilisateurs inactifs
     */
    public function getInactiveUsers(): JsonResponse
    {
        return response()->json($this->statsService->getInactiveUsers());
    }
    /**
     * Valeur totale des cryptos sur la plateforme
     */
    public function getPlatformTotalValue(): JsonResponse
    {
        return response()->json($this->statsService->getPlatformTotalValue());
    }

    /**
     * Valeur par crypto sur la plateforme
     */
    public function getPlatformCryptoDetails(): JsonResponse
    {
        return response()->json($this->statsService->getPlatformCryptoDetails());
    }

    /**
     * Top 5 des cryptos les plus échangées (en volume)
     */
    public function getTopCryptos(): JsonResponse
    {
        return response()->json($this->statsService->getTopCryptos());
    }
    /**
     * Top 5 des cryptos les plus échangées (en revenus)
     */
    public function getTopCryptosByRevenue(): JsonResponse
    {
        return response()->json($this->statsService->getTopCryptosByRevenue());
    }

    /**
     * Valeur totale du portefeuille de l'utilisateur
     */
    public function getUserPortfolio(Request $request): JsonResponse
    {
        $validationResponse = $this->validateUserId($request);
        if ($validationResponse) {
            return $validationResponse;
        }

        $userId = $request->input('user_id');
        return response()->json($this->statsService->getUserPortfolio($userId));
    }

    /**
     * Détails par crypto pour l'utilisateur
     */
    public function getUserCryptoDetails(Request $request): JsonResponse
    {
        $validationResponse = $this->validateUserId($request);
        if ($validationResponse) {
            return $validationResponse;
        }

        $userId = $request->input('user_id');
        return response()->json($this->statsService->getUserCryptoDetails($userId));
    }

    /**
     * Valeur des investissements par utilisateur et par crypto
     */
    public function getUserInvestments(Request $request): JsonResponse
    {
        $validationResponse = $this->validateUserId($request);
        if ($validationResponse) {
            return $validationResponse;
        }

        $userId = $request->input('user_id');
        return response()->json($this->statsService->getUserInvestments($userId));
    }

    /**
     * Comparaison entre la valeur actuelle du portefeuille et la valeur d'achat
     */
    public function comparePortfolioValue(Request $request): JsonResponse
    {
        $validationResponse = $this->validateUserId($request);
        if ($validationResponse) {
            return $validationResponse;
        }

        $userId = $request->input('user_id');
        return response()->json($this->statsService->comparePortfolioValue($userId));
    }
    /**
     * Afficher l'evulution du portefeuille
     */
    public function getPortfolioEvolution(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $days = $request->query('days', 30);

        $data = $this->statsService->getPortfolioEvolution($userId, $days);
        return response()->json(['data' => $data]);
    }

    /**
     * Plus-value actuelle pour chaque crypto
     */
    public function getCryptoProfitOrLoss(Request $request): JsonResponse
    {
        $validationResponse = $this->validateUserId($request);
        if ($validationResponse) {
            return $validationResponse;
        }

        $userId = $request->input('user_id');
        return response()->json($this->statsService->getCryptoProfitOrLoss($userId));
    }
}

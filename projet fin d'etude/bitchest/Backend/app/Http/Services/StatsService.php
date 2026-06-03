<?php

namespace App\Http\Services;

use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsService
{

    /**
     **** ------------- Admin Stats ------------------****
     */

    /**
     * Classement des utilisateurs qui font le plus d'achats
     */
    public function getTopBuyers()
    {
        return DB::table('transactions as t')
            ->join('crypto_wallets as cw', 't.idCryptoWallet', '=', 'cw.id')
            ->join('wallets as w', 'cw.idWallet', '=', 'w.id')
            ->join('users as u', 'w.idUser', '=', 'u.id')
            ->where('t.type', 'buy')
            ->select(
                'u.id as user_id',
                'u.name as user_name',
                DB::raw('SUM(t.totalPrice) as total_spent')
            )
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('total_spent')
            ->limit(10) // Top 10 acheteurs
            ->get();
    }

    /**
     * Classement des utilisateurs avec le plus grand portefeuille
     */
    public function getTopWallets()
    {
        return DB::table('crypto_wallets as cw')
            ->join('wallets as w', 'cw.idWallet', '=', 'w.id')
            ->join('users as u', 'w.idUser', '=', 'u.id')
            ->join('cryptocurrencies as c', 'cw.idCrypto', '=', 'c.id')
            ->select(
                'u.id as user_id',
                'u.name as user_name',
                DB::raw('SUM(cw.quantity * c.currentPrice) as total_value')
            )
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('total_value')
            ->limit(10) // Top 10 portefeuilles
            ->get();
    }

    /**
     * Volume total des transactions (achats et ventes)
     */
    public function getTotalTransactionVolume()
    {
        return DB::table('transactions')
            ->select(
                DB::raw('SUM(CASE WHEN type = "buy" THEN totalPrice ELSE 0 END) as total_buy'),
                DB::raw('SUM(CASE WHEN type = "sell" THEN totalPrice ELSE 0 END) as total_sell')
            )
            ->first();
    }

    /**
     * Activité récente des utilisateurs
     */
    public function getRecentActivity($limit)
    {
        return DB::table('transactions as t')
            ->join('crypto_wallets as cw', 't.idCryptoWallet', '=', 'cw.id')
            ->join('wallets as w', 'cw.idWallet', '=', 'w.id')
            ->join('users as u', 'w.idUser', '=', 'u.id')
            ->join('cryptocurrencies as c', 'cw.idCrypto', '=', 'c.id')
            ->select(
                'u.name as user_name',
                'c.name as crypto_name',
                't.type',
                't.quantity',
                't.unitPrice',
                't.totalPrice',
                't.date'
            )
            ->orderByDesc('t.date')
            ->limit($limit)
            ->get();
    }

    /**
     * Utilisateurs inactifs (aucune transaction depuis 30 jours)
     */
    public function getInactiveUsers()
    {
        return DB::table('users as u')
            ->leftJoin('wallets as w', 'u.id', '=', 'w.idUser')
            ->leftJoin('crypto_wallets as cw', 'w.id', '=', 'cw.idWallet')
            ->leftJoin('transactions as t', 'cw.id', '=', 't.idCryptoWallet')
            ->where('u.role', '!=', 'admin') // Exclure les administrateurs
            ->select(
                'u.id as user_id',
                'u.name as user_name',
                'u.email as user_email',
                DB::raw('MAX(t.date) as last_transaction_date')
            )
            ->groupBy('u.id', 'u.name')
            ->havingRaw('last_transaction_date IS NULL OR last_transaction_date < DATE_SUB(NOW(), INTERVAL 30 DAY)')
            ->get();
    }

    /**
     * Valeur totale des cryptos sur la plateforme
     */
    public function getPlatformTotalValue()
    {
        return DB::table('cryptocurrencies as c')
            ->leftJoin('crypto_wallets as cw', 'cw.idCrypto', '=', 'c.id')
            ->select(
                DB::raw('SUM(c.inStock * c.currentPrice) + COALESCE(SUM(cw.quantity * c.currentPrice), 0) AS totalValue')
            )
            ->first();
    }

    /**
     * Valeur par crypto sur la plateforme
     */
    public function getPlatformCryptoDetails()
    {
        return DB::table('cryptocurrencies as c')
            ->leftJoin('crypto_wallets as cw', 'cw.idCrypto', '=', 'c.id')
            ->select(
                'c.name',
                'c.currentPrice',
                DB::raw('c.inStock + COALESCE(SUM(cw.quantity), 0) as totalQuantity'),
                DB::raw('(c.inStock + COALESCE(SUM(cw.quantity), 0)) * c.currentPrice as totalValue')
            )
            ->groupBy('c.id', 'c.name', 'c.currentPrice')
            ->get()
            ->map(function ($crypto) {
                return [
                    'name' => $crypto->name,
                    'currentPrice' => (float) $crypto->currentPrice,
                    'totalQuantity' => (float) $crypto->totalQuantity,
                    'totalValue' => (float) $crypto->totalValue,
                ];
            });
    }

    /**
     * Top 5 des cryptos les plus échangées (en volume)
     */
    public function getTopCryptos()
    {
        return DB::table('transactions as t')
            ->join('cryptocurrencies as c', 't.idCryptoWallet', '=', 'c.id')
            ->select(
                'c.name',
                DB::raw('SUM(t.quantity) as totalVolume')
            )
            ->groupBy('c.name')
            ->orderByDesc('totalVolume')
            ->limit(5)
            ->get();
    }
    /**
     * Top 5 des cryptos en fonction des revenus de vente
     */
    public function getTopCryptosByRevenue()
    {
        return DB::table('transactions as t')
            ->join('cryptocurrencies as c', 't.idCryptoWallet', '=', 'c.id')
            ->where('t.type', 'sell') // Ne prendre en compte que les ventes
            ->select(
                'c.name',
                DB::raw('SUM(t.totalPrice) as totalRevenue') // Somme des revenus de vente
            )
            ->groupBy('c.name')
            ->orderByDesc('totalRevenue') // Classer par revenu décroissant
            ->limit(5) // Limiter aux 5 premières cryptos
            ->get();
    }


    /**
     **** ------------- Client Stats ------------------****
     */

    /**
     * Valeur totale du portefeuille de l'utilisateur
     */
    public function getUserPortfolio($userId)
    {
        return DB::table('wallets as w')
            ->join('crypto_wallets as cw', 'cw.idWallet', '=', 'w.id')
            ->join('cryptocurrencies as c', 'cw.idCrypto', '=', 'c.id')
            ->where('w.idUser', $userId)
            ->select(
                DB::raw('SUM(cw.quantity * c.currentPrice) as totalValue')
            )
            ->first();
    }

    /**
     * Détails par crypto pour l'utilisateur
     */
    public function getUserCryptoDetails($userId)
    {
        return DB::table('wallets as w')
            ->join('crypto_wallets as cw', 'cw.idWallet', '=', 'w.id')
            ->join('cryptocurrencies as c', 'cw.idCrypto', '=', 'c.id')
            ->where('w.idUser', $userId)
            ->select(
                'c.name as label',
                'c.currentPrice',
                'cw.quantity',
                DB::raw('(cw.quantity * c.currentPrice) as value')
            )
            ->get()
            ->map(function ($crypto) {
                return [
                    'label' => $crypto->label,
                    'currentPrice' => (float) $crypto->currentPrice,
                    'totalQuantity' => (float) $crypto->quantity,
                    'totalValue' => (float) $crypto->value,
                ];
            });
    }


    /**
     * Valeur des investissements par utilisateur et par crypto
     */
    public function getUserInvestments($userId)
    {
        // Récupérer les transactions d'achat de l'utilisateur
        $transactions = DB::table('transactions as t')
            ->join('crypto_wallets as cw', 't.idCryptoWallet', '=', 'cw.id')
            ->join('wallets as w', 'cw.idWallet', '=', 'w.id')
            ->join('cryptocurrencies as c', 'cw.idCrypto', '=', 'c.id')
            ->where('w.idUser', $userId)
            ->where('t.type', 'buy') // On ne prend que les achats
            ->select(
                'c.name as crypto',
                't.quantity',
                't.unitPrice',
                DB::raw('t.quantity * t.unitPrice as totalCost')
            )
            ->get();

        // Grouper les transactions par crypto
        $investments = [];
        foreach ($transactions as $transaction) {
            if (!isset($investments[$transaction->crypto])) {
                $investments[$transaction->crypto] = [
                    'totalQuantity' => 0,
                    'totalCost' => 0,
                ];
            }
            $investments[$transaction->crypto]['totalQuantity'] += $transaction->quantity;
            $investments[$transaction->crypto]['totalCost'] += $transaction->totalCost;
        }

        // Calculer la valeur d'achat moyenne et la plus-value
        $result = [];
        foreach ($investments as $crypto => $data) {
            $currentPrice = DB::table('cryptocurrencies')
                ->where('name', $crypto)
                ->value('currentPrice');

            $totalQuantity = $data['totalQuantity'];
            $totalCost = $data['totalCost'];
            $averagePurchasePrice = $totalCost / $totalQuantity;

            $currentValue = $totalQuantity * $currentPrice;
            $profitOrLoss = $currentValue - $totalCost;
            $percentage = ($profitOrLoss / $totalCost) * 100;

            $result[] = [
                'crypto' => $crypto,
                'totalQuantity' => $totalQuantity,
                'totalCost' => $totalCost,
                'averagePurchasePrice' => $averagePurchasePrice,
                'currentPrice' => $currentPrice,
                'currentValue' => $currentValue,
                'profitOrLoss' => $profitOrLoss,
                'percentage' => $percentage,
            ];
        }

        return $result;
    }

    /**
     * Comparaison entre la valeur actuelle du portefeuille et la valeur d'achat
     */
    public function comparePortfolioValue($userId)
    {
        // Récupérer les investissements de l'utilisateur
        $investments = $this->getUserInvestments($userId);

        // Récupérer le solde du wallet de l'utilisateur
        $walletBalance = DB::table('wallets')
            ->where('idUser', $userId)
            ->value('balance');

        $totalInvestmentValue = 0;
        $totalCurrentValue = 0;

        foreach ($investments as $investment) {
            $totalInvestmentValue += $investment['totalCost'];
            $totalCurrentValue += $investment['currentValue'];
        }

        // Calcul du profit ou de la perte
        $profitOrLoss = $totalCurrentValue - $totalInvestmentValue;
        $percentage = $totalInvestmentValue > 0 ? ($profitOrLoss / $totalInvestmentValue) * 100 : 0;

        return [
            'walletBalance' => (float) $walletBalance,
            'totalInvestmentValue' => (float) $totalInvestmentValue,
            'totalCurrentValue' => (float) $totalCurrentValue,
            'profitOrLoss' => (float) $profitOrLoss,
            'percentage' => (float) $percentage,
        ];
    }

    /**
     *  Afficher l'évolution du portefeuille
     */
    public function getPortfolioEvolution($userId, $days = 30)
    {
        // Récupérer les cryptos détenues par l'utilisateur
        $userCryptos = DB::table('wallets as w')
            ->join('crypto_wallets as cw', 'cw.idWallet', '=', 'w.id')
            ->join('cryptocurrencies as c', 'cw.idCrypto', '=', 'c.id')
            ->where('w.idUser', $userId)
            ->select('cw.idCrypto', 'cw.quantity')
            ->get();

        // Récupérer l'historique des prix pour chaque crypto détenue
        $history = DB::table('price_histories')
            ->whereIn('cryptocurrency_id', $userCryptos->pluck('idCrypto'))
            ->where('date', '>=', Carbon::now()->subDays($days))
            ->orderBy('date', 'asc')
            ->get();

        // Calculer la valeur totale du portefeuille chaque jour
        $portfolioEvolution = [];
        foreach ($history->groupBy('date') as $date => $prices) {
            $totalValue = 0;
            foreach ($prices as $price) {
                $crypto = $userCryptos->firstWhere('idCrypto', $price->cryptocurrency_id);
                if ($crypto) {
                    $totalValue += $crypto->quantity * $price->value;
                }
            }
            $portfolioEvolution[] = [
                'date' => $date,
                'totalValue' => round($totalValue, 2)
            ];
        }

        return $portfolioEvolution;
    }
    /**
     * Plus-value actuelle pour chaque crypto
     */
    public function getCryptoProfitOrLoss($userId)
    {
        $investments = $this->getUserInvestments($userId);

        $result = [];
        foreach ($investments as $investment) {
            $result[] = [
                'crypto' => $investment['crypto'],
                'profitOrLoss' => $investment['profitOrLoss'],
                'percentage' => $investment['percentage'],
            ];
        }

        return $result;
    }

    /**
     * Cryptos les plus populaires (les plus achetées)
     */
    public function getMostPopularCryptos()
    {
        return DB::table('transactions as t')
            ->join('crypto_wallets as cw', 't.idCryptoWallet', '=', 'cw.id')
            ->join('cryptocurrencies as c', 'cw.idCrypto', '=', 'c.id')
            ->where('t.type', 'buy')
            ->select(
                'c.id as crypto_id',
                'c.name as crypto_name',
                DB::raw('SUM(t.quantity) as total_quantity')
            )
            ->groupBy('c.name')
            ->orderByDesc('total_quantity')
            ->limit(5) // Top 5 cryptos
            ->get();
    }
}

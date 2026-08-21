<?php

/**
 * Configuration des Services et Événements
 * 
 * Centralise la configuration des services métier et des événements
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Services Applicatifs
    |--------------------------------------------------------------------------
    |
    | Enregistre et configure tous les services métier
    |
    */

    'services' => [
        'auth' => [
            'class' => 'App\Services\AuthService',
            'methods' => ['login', 'register', 'logout', 'refreshToken', 'validateToken'],
        ],
        'wallet' => [
            'class' => 'App\Services\WalletService',
            'methods' => ['create', 'getBalance', 'updateBalance', 'getTransactions'],
        ],
        'cryptocurrency' => [
            'class' => 'App\Services\CryptoService',
            'methods' => ['getAll', 'getById', 'getPriceHistory', 'updatePrices', 'syncFromAPI'],
        ],
        'transaction' => [
            'class' => 'App\Services\TransactionService',
            'methods' => ['buy', 'sell', 'transfer', 'getHistory', 'calculateProfit'],
        ],
        'alert' => [
            'class' => 'App\Services\AlertService',
            'methods' => ['create', 'check', 'trigger', 'delete', 'getActive'],
        ],
        'notification' => [
            'class' => 'App\Services\NotificationService',
            'methods' => ['send', 'notify', 'getUnread', 'markAsRead'],
        ],
        'portfolio' => [
            'class' => 'App\Services\PortfolioService',
            'methods' => ['getOverview', 'calculateValue', 'getPerformance', 'getDistribution'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Événements
    |--------------------------------------------------------------------------
    |
    | Enregistre les événements et leurs listeners
    |
    */

    'events' => [
        'UserRegistered' => [
            'class' => 'App\Events\UserRegistered',
            'listeners' => [
                'App\Listeners\SendWelcomeEmail',
                'App\Listeners\CreateDefaultWallet',
            ],
        ],
        'UserLoggedIn' => [
            'class' => 'App\Events\UserLoggedIn',
            'listeners' => [
                'App\Listeners\UpdateLastLogin',
                'App\Listeners\LogLoginActivity',
            ],
        ],
        'CryptoPriceUpdated' => [
            'class' => 'App\Events\CryptoPriceUpdated',
            'listeners' => [
                'App\Listeners\CheckAlerts',
                'App\Listeners\RecordPriceHistory',
            ],
        ],
        'TransactionCompleted' => [
            'class' => 'App\Events\TransactionCompleted',
            'listeners' => [
                'App\Listeners\UpdateWalletBalance',
                'App\Listeners\NotifyUser',
                'App\Listeners\RecordTransaction',
            ],
        ],
        'AlertTriggered' => [
            'class' => 'App\Events\AlertTriggered',
            'listeners' => [
                'App\Listeners\NotifyUserAlert',
                'App\Listeners\CreateNotification',
            ],
        ],
        'WalletUpdated' => [
            'class' => 'App\Events\WalletUpdated',
            'listeners' => [
                'App\Listeners\NotifyWalletChange',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Jobs / Queues
    |--------------------------------------------------------------------------
    |
    | Configuration des jobs asynchrones
    |
    */

    'jobs' => [
        'UpdateCryptoPrices' => [
            'class' => 'App\Jobs\UpdateCryptoPrices',
            'schedule' => 'everyMinute', // */1 * * * *
            'timeout' => 300,
            'description' => 'Met à jour les prix des cryptomonnaies toutes les minutes',
        ],
        'CheckPriceAlerts' => [
            'class' => 'App\Jobs\CheckPriceAlerts',
            'schedule' => 'everyTwoMinutes', // */2 * * * *
            'timeout' => 60,
            'description' => 'Vérifie les alertes de prix',
        ],
        'SendNotifications' => [
            'class' => 'App\Jobs\SendNotifications',
            'schedule' => 'everyFiveMinutes', // */5 * * * *
            'timeout' => 120,
            'description' => 'Envoie les notifications en attente',
        ],
        'CleanupNotifications' => [
            'class' => 'App\Jobs\CleanupNotifications',
            'schedule' => 'daily', // 0 0 * * *
            'timeout' => 600,
            'description' => 'Nettoie les vieilles notifications',
        ],
        'CalculatePortfolioValue' => [
            'class' => 'App\Jobs\CalculatePortfolioValue',
            'schedule' => 'everyHour', // 0 * * * *
            'timeout' => 300,
            'description' => 'Calcule la valeur du portefeuille',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Observateurs
    |--------------------------------------------------------------------------
    |
    | Observateurs pour les événements du modèle Eloquent
    |
    */

    'observers' => [
        'User' => 'App\Observers\UserObserver',
        'Wallet' => 'App\Observers\WalletObserver',
        'Transaction' => 'App\Observers\TransactionObserver',
        'Alert' => 'App\Observers\AlertObserver',
        'Notification' => 'App\Observers\NotificationObserver',
        'Cryptocurrency' => 'App\Observers\CryptoObserver',
    ],

    /*
    |--------------------------------------------------------------------------
    | Caches
    |--------------------------------------------------------------------------
    |
    | Configuration du cache pour les données fréquemment accédées
    |
    */

    'caches' => [
        'cryptocurrencies' => [
            'key' => 'crypto:all',
            'ttl' => 300, // 5 minutes
        ],
        'cryptocurrency_price' => [
            'key' => 'crypto:price:{id}',
            'ttl' => 60, // 1 minute
        ],
        'user_portfolio' => [
            'key' => 'portfolio:{user_id}',
            'ttl' => 120, // 2 minutes
        ],
        'wallet_balance' => [
            'key' => 'wallet:{wallet_id}',
            'ttl' => 60, // 1 minute
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Configuration des types de notifications
    |
    */

    'notification_types' => [
        'alert' => [
            'channel' => ['database', 'mail'],
            'template' => 'notifications.alert',
        ],
        'transaction' => [
            'channel' => ['database'],
            'template' => 'notifications.transaction',
        ],
        'welcome' => [
            'channel' => ['mail', 'database'],
            'template' => 'notifications.welcome',
        ],
        'system' => [
            'channel' => ['database'],
            'template' => 'notifications.system',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions et Rôles
    |--------------------------------------------------------------------------
    |
    | Définit les permissions pour les rôles
    |
    */

    'permissions' => [
        'user' => [
            'view_own_profile' => true,
            'edit_own_profile' => true,
            'view_wallet' => true,
            'manage_wallet' => true,
            'view_transactions' => true,
            'create_transaction' => true,
            'manage_alerts' => true,
            'view_notifications' => true,
        ],
        'admin' => [
            'view_all_users' => true,
            'manage_users' => true,
            'view_all_transactions' => true,
            'manage_system' => true,
            'view_analytics' => true,
            'manage_cryptocurrencies' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des API Externes
    |--------------------------------------------------------------------------
    |
    | URLs et clés d'API pour les services externes
    |
    */

    'external_apis' => [
        'coingecko' => [
            'base_url' => 'https://api.coingecko.com/api/v3',
            'timeout' => 30,
            'endpoints' => [
                'markets' => '/coins/markets',
                'price' => '/simple/price',
                'chart' => '/coins/{id}/market_chart',
            ],
        ],
        'coinmarketcap' => [
            'base_url' => 'https://pro-api.coinmarketcap.com/v1',
            'api_key' => env('COINMARKETCAP_API_KEY'),
            'timeout' => 30,
        ],
    ],

];

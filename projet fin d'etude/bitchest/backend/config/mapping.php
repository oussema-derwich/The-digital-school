<?php

/**
 * Configuration centralisée des mappings et des relations
 * 
 * Ce fichier contient tous les mappings nécessaires pour l'application
 * incluant les relations entre modèles, les transformations de données,
 * et les configurations métier.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Mappings des Modèles
    |--------------------------------------------------------------------------
    |
    | Définit les relations entre les modèles et leurs associations
    |
    */

    'models' => [
        'user' => [
            'class' => 'App\Models\User',
            'table' => 'users',
            'relations' => [
                'wallets' => [
                    'type' => 'hasMany',
                    'model' => 'App\Models\Wallet',
                    'foreign_key' => 'user_id',
                    'local_key' => 'id',
                ],
                'transactions' => [
                    'type' => 'hasMany',
                    'model' => 'App\Models\Transaction',
                    'foreign_key' => 'user_id',
                    'local_key' => 'id',
                ],
                'alerts' => [
                    'type' => 'hasMany',
                    'model' => 'App\Models\Alert',
                    'foreign_key' => 'user_id',
                    'local_key' => 'id',
                ],
                'notifications' => [
                    'type' => 'hasMany',
                    'model' => 'App\Models\Notification',
                    'foreign_key' => 'user_id',
                    'local_key' => 'id',
                ],
            ],
            'attributes' => [
                'fillable' => ['name', 'email', 'password', 'role', 'is_active', 'photo', 'avatar'],
                'hidden' => ['password', 'remember_token'],
                'casts' => [
                    'email_verified_at' => 'datetime',
                    'is_active' => 'boolean',
                ],
            ],
        ],

        'wallet' => [
            'class' => 'App\Models\Wallet',
            'table' => 'wallets',
            'relations' => [
                'user' => [
                    'type' => 'belongsTo',
                    'model' => 'App\Models\User',
                    'foreign_key' => 'user_id',
                    'owner_key' => 'id',
                ],
                'cryptocurrencies' => [
                    'type' => 'belongsToMany',
                    'model' => 'App\Models\Cryptocurrency',
                    'pivot_table' => 'wallet_cryptocurrencies',
                    'foreign_key' => 'wallet_id',
                    'related_key' => 'cryptocurrency_id',
                ],
                'transactions' => [
                    'type' => 'hasMany',
                    'model' => 'App\Models\Transaction',
                    'foreign_key' => 'wallet_id',
                    'local_key' => 'id',
                ],
            ],
            'attributes' => [
                'fillable' => ['user_id', 'balance', 'public_address', 'private_address'],
                'casts' => [
                    'balance' => 'decimal:2',
                ],
            ],
        ],

        'cryptocurrency' => [
            'class' => 'App\Models\Cryptocurrency',
            'table' => 'cryptocurrencies',
            'relations' => [
                'priceHistories' => [
                    'type' => 'hasMany',
                    'model' => 'App\Models\PriceHistory',
                    'foreign_key' => 'cryptocurrency_id',
                    'local_key' => 'id',
                ],
                'wallets' => [
                    'type' => 'belongsToMany',
                    'model' => 'App\Models\Wallet',
                    'pivot_table' => 'wallet_cryptocurrencies',
                    'foreign_key' => 'cryptocurrency_id',
                    'related_key' => 'wallet_id',
                ],
            ],
            'attributes' => [
                'fillable' => ['symbol', 'name', 'current_price', 'market_cap', 'volume_24h'],
                'casts' => [
                    'current_price' => 'decimal:8',
                    'market_cap' => 'decimal:2',
                    'volume_24h' => 'decimal:2',
                ],
            ],
        ],

        'transaction' => [
            'class' => 'App\Models\Transaction',
            'table' => 'transactions',
            'relations' => [
                'user' => [
                    'type' => 'belongsTo',
                    'model' => 'App\Models\User',
                    'foreign_key' => 'user_id',
                    'owner_key' => 'id',
                ],
                'wallet' => [
                    'type' => 'belongsTo',
                    'model' => 'App\Models\Wallet',
                    'foreign_key' => 'wallet_id',
                    'owner_key' => 'id',
                ],
                'cryptocurrency' => [
                    'type' => 'belongsTo',
                    'model' => 'App\Models\Cryptocurrency',
                    'foreign_key' => 'cryptocurrency_id',
                    'owner_key' => 'id',
                ],
            ],
            'attributes' => [
                'fillable' => ['user_id', 'wallet_id', 'cryptocurrency_id', 'type', 'amount', 'price_at_transaction'],
                'casts' => [
                    'amount' => 'decimal:8',
                    'price_at_transaction' => 'decimal:8',
                    'created_at' => 'datetime',
                ],
            ],
        ],

        'alert' => [
            'class' => 'App\Models\Alert',
            'table' => 'alerts',
            'relations' => [
                'user' => [
                    'type' => 'belongsTo',
                    'model' => 'App\Models\User',
                    'foreign_key' => 'user_id',
                    'owner_key' => 'id',
                ],
                'cryptocurrency' => [
                    'type' => 'belongsTo',
                    'model' => 'App\Models\Cryptocurrency',
                    'foreign_key' => 'cryptocurrency_id',
                    'owner_key' => 'id',
                ],
            ],
            'attributes' => [
                'fillable' => ['user_id', 'cryptocurrency_id', 'price_target', 'condition', 'is_triggered'],
                'casts' => [
                    'price_target' => 'decimal:8',
                    'is_triggered' => 'boolean',
                ],
            ],
        ],

        'notification' => [
            'class' => 'App\Models\Notification',
            'table' => 'notifications',
            'relations' => [
                'user' => [
                    'type' => 'belongsTo',
                    'model' => 'App\Models\User',
                    'foreign_key' => 'user_id',
                    'owner_key' => 'id',
                ],
            ],
            'attributes' => [
                'fillable' => ['user_id', 'title', 'message', 'type', 'is_read'],
                'casts' => [
                    'is_read' => 'boolean',
                    'created_at' => 'datetime',
                ],
            ],
        ],

        'price_history' => [
            'class' => 'App\Models\PriceHistory',
            'table' => 'price_histories',
            'relations' => [
                'cryptocurrency' => [
                    'type' => 'belongsTo',
                    'model' => 'App\Models\Cryptocurrency',
                    'foreign_key' => 'cryptocurrency_id',
                    'owner_key' => 'id',
                ],
            ],
            'attributes' => [
                'fillable' => ['cryptocurrency_id', 'price', 'timestamp'],
                'casts' => [
                    'price' => 'decimal:8',
                    'timestamp' => 'datetime',
                ],
            ],
        ],

        'wallet_crypto' => [
            'class' => 'App\Models\WalletCrypto',
            'table' => 'wallet_cryptocurrencies',
            'relations' => [
                'wallet' => [
                    'type' => 'belongsTo',
                    'model' => 'App\Models\Wallet',
                    'foreign_key' => 'wallet_id',
                    'owner_key' => 'id',
                ],
                'cryptocurrency' => [
                    'type' => 'belongsTo',
                    'model' => 'App\Models\Cryptocurrency',
                    'foreign_key' => 'cryptocurrency_id',
                    'owner_key' => 'id',
                ],
            ],
            'attributes' => [
                'fillable' => ['wallet_id', 'cryptocurrency_id', 'quantity', 'total_value'],
                'casts' => [
                    'quantity' => 'decimal:8',
                    'total_value' => 'decimal:8',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mappings des Contrôleurs
    |--------------------------------------------------------------------------
    |
    | Associe les routes aux contrôleurs et leurs méthodes
    |
    */

    'controllers' => [
        'auth' => [
            'class' => 'App\Http\Controllers\Auth\AuthController',
            'actions' => [
                'login' => ['method' => 'POST', 'route' => 'auth/login', 'auth' => false],
                'register' => ['method' => 'POST', 'route' => 'auth/register', 'auth' => false],
                'logout' => ['method' => 'POST', 'route' => 'auth/logout', 'auth' => true],
                'profile' => ['method' => 'GET', 'route' => 'auth/profile', 'auth' => true],
                'updateProfile' => ['method' => 'PUT', 'route' => 'auth/profile', 'auth' => true],
            ],
        ],
        'wallet' => [
            'class' => 'App\Http\Controllers\WalletController',
            'actions' => [
                'index' => ['method' => 'GET', 'route' => 'wallet', 'auth' => true],
                'show' => ['method' => 'GET', 'route' => 'wallet/{id}', 'auth' => true],
                'buy' => ['method' => 'POST', 'route' => 'buy', 'auth' => true],
                'sell' => ['method' => 'POST', 'route' => 'sell', 'auth' => true],
            ],
        ],
        'crypto' => [
            'class' => 'App\Http\Controllers\CryptoController',
            'actions' => [
                'index' => ['method' => 'GET', 'route' => 'cryptocurrencies', 'auth' => false],
                'show' => ['method' => 'GET', 'route' => 'cryptocurrencies/{id}', 'auth' => false],
                'history' => ['method' => 'GET', 'route' => 'cryptocurrencies/{id}/history', 'auth' => false],
            ],
        ],
        'transaction' => [
            'class' => 'App\Http\Controllers\TransactionController',
            'actions' => [
                'index' => ['method' => 'GET', 'route' => 'transactions', 'auth' => true],
            ],
        ],
        'alert' => [
            'class' => 'App\Http\Controllers\AlertController',
            'actions' => [
                'index' => ['method' => 'GET', 'route' => 'alerts', 'auth' => true],
                'store' => ['method' => 'POST', 'route' => 'alerts', 'auth' => true],
                'show' => ['method' => 'GET', 'route' => 'alerts/{id}', 'auth' => true],
                'update' => ['method' => 'PUT', 'route' => 'alerts/{id}', 'auth' => true],
                'destroy' => ['method' => 'DELETE', 'route' => 'alerts/{id}', 'auth' => true],
            ],
        ],
        'notification' => [
            'class' => 'App\Http\Controllers\NotificationController',
            'actions' => [
                'index' => ['method' => 'GET', 'route' => 'notifications', 'auth' => true],
            ],
        ],
        'admin' => [
            'class' => 'App\Http\Controllers\AdminController',
            'actions' => [
                'dashboard' => ['method' => 'GET', 'route' => 'admin/dashboard', 'auth' => true, 'role' => 'admin'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mappings des Transformations
    |--------------------------------------------------------------------------
    |
    | Définit comment les données sont transformées et présentées
    |
    */

    'transformations' => [
        'user' => [
            'public' => ['id', 'name', 'email', 'role', 'avatar', 'created_at'],
            'private' => ['id', 'name', 'email', 'role', 'is_active', 'avatar', 'photo', 'created_at', 'updated_at'],
        ],
        'wallet' => [
            'default' => ['id', 'balance', 'user_id', 'public_address'],
            'detailed' => ['id', 'balance', 'user_id', 'public_address', 'walletCryptos', 'transactions'],
        ],
        'cryptocurrency' => [
            'list' => ['id', 'symbol', 'name', 'current_price'],
            'detailed' => ['id', 'symbol', 'name', 'current_price', 'market_cap', 'volume_24h', 'priceHistories'],
        ],
        'transaction' => [
            'summary' => ['id', 'type', 'quantity', 'unit_price', 'total_price', 'status', 'created_at'],
            'detailed' => ['id', 'wallet_crypto_id', 'type', 'quantity', 'unit_price', 'total_price', 'status', 'created_at'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Statuts et Énumérations
    |--------------------------------------------------------------------------
    |
    | Définit les énumérations utilisées dans l'application
    |
    */

    'enums' => [
        'user_roles' => ['user', 'admin', 'moderator'],
        'transaction_types' => ['buy', 'sell', 'transfer'],
        'alert_conditions' => ['above', 'below', 'equals'],
        'notification_types' => ['alert', 'transaction', 'system'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validations
    |--------------------------------------------------------------------------
    |
    | Règles de validation pour les données entrantes
    |
    */

    'validations' => [
        'user' => [
            'register' => [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ],
            'login' => [
                'email' => 'required|email',
                'password' => 'required|string',
            ],
            'profile' => [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email',
            ],
        ],
        'wallet' => [
            'buy' => [
                'cryptocurrency_id' => 'required|exists:cryptocurrencies,id',
                'amount' => 'required|numeric|min:0.00000001',
            ],
            'sell' => [
                'cryptocurrency_id' => 'required|exists:cryptocurrencies,id',
                'amount' => 'required|numeric|min:0.00000001',
            ],
        ],
        'alert' => [
            'store' => [
                'cryptocurrency_id' => 'required|exists:cryptocurrencies,id',
                'price_target' => 'required|numeric|min:0',
                'condition' => 'required|in:above,below,equals',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware Mapping
    |--------------------------------------------------------------------------
    |
    | Associe les middleware aux routes
    |
    */

    'middleware' => [
        'auth' => ['auth:sanctum,api', 'auth:jwt'],
        'admin' => ['auth:sanctum,api', 'admin'],
        'api' => ['api', 'throttle:api'],
    ],

];

# Vérification de la structure Laravel - Bitchest Project

## 1. Sanctum Installation & Configuration
- [x] `composer.json` contient `laravel/sanctum: ^4.0`
- [x] `.env` contient `SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1:8000,localhost:3000,127.0.0.1:5173,localhost:5173`
- [x] `app/Http/Kernel.php` contient le middleware `EnsureFrontendRequestsAreStateful` dans le groupe 'api'
- [x] `config/sanctum.php` créé avec la bonne configuration
- [x] `app/Models/User.php` utilise le trait `HasApiTokens`

## 2. Database Schema
- [x] Migration `create_users_table` mise à jour avec:
  - `temp_password` nullable
  - `balance_eur` decimal(15,2) default 0
- [x] Migration `create_cryptos_table` créée avec:
  - id, name, symbol, current_price, logo_path, timestamps
- [x] Migration `create_wallets_table` créée avec structure correcte
- [x] Migration `create_wallet_cryptos_table` créée avec:
  - id, wallet_id, cryptocurrency_id, quantity, avg_buy_price
  - unique(wallet_id, cryptocurrency_id)
- [x] Migration `create_transactions_table` créée avec:
  - id, user_id, cryptocurrency_id, type enum, quantity, price_at_transaction, eur_amount
- [x] Migration `create_price_histories_table` créée avec:
  - id, cryptocurrency_id, price, created_at
- [x] Migration `personal_access_tokens` table pour Sanctum

## 3. Eloquent Models (12 relations)
### User Model
- [x] hasOne(Wallet::class)
- [x] hasMany(Transaction::class)

### Wallet Model
- [x] belongsTo(User::class)
- [x] hasMany(WalletCrypto::class)

### WalletCrypto Model (NOUVELLEMENT CRÉÉ)
- [x] belongsTo(Wallet::class)
- [x] belongsTo(Cryptocurrency::class)

### Cryptocurrency Model (NOUVELLEMENT CRÉÉ - remplace Crypto)
- [x] hasMany(WalletCrypto::class)
- [x] hasMany(Transaction::class)
- [x] hasMany(PriceHistory::class)

### Transaction Model
- [x] belongsTo(User::class)
- [x] belongsTo(Cryptocurrency::class)

### PriceHistory Model (NOUVELLEMENT CRÉÉ)
- [x] belongsTo(Cryptocurrency::class)

## 4. User Management Features
- [x] Champ `temp_password` dans User model
- [x] Champ `balance_eur` initialisé à 500 EUR pour les clients
- [x] Observer `UserObserver` crée un wallet automatiquement à la création d'un utilisateur
- [x] Observer initialise `balance_eur` = 500 pour les clients

## 5. API Routes
- [x] POST `/api/login` (public)
- [x] GET `/api/me` (protégé)
- [x] PUT `/api/profile` (protégé)
- [x] POST `/api/logout` (protégé)
- [x] GET `/api/cryptocurrencies` (public)
- [x] GET `/api/cryptocurrencies/{id}` (public)
- [x] GET `/api/cryptocurrencies/{id}/history` (public)
- [x] GET `/api/wallet` (protégé)
- [x] POST `/api/buy` (protégé)
- [x] POST `/api/sell` (protégé)
- [x] GET `/api/transactions` (protégé)
- [x] Admin routes: `/api/admin/users`, `/api/admin/stats`

## 6. Security & Validation
- [x] CORS configuré avec `supports_credentials = true`
- [x] CORS origins: localhost:5173, 127.0.0.1:5173, localhost:3000, 127.0.0.1:3000
- [x] Password hashé avec bcrypt (Laravel par défaut)
- [x] Tokens révocables via `personal_access_tokens` table

## 7. Seeding & Test Data
- [x] `CryptoSeeder` créé avec 10 cryptos exactement
- [x] `CryptoSeeder` génère 310 prix historiques (31 jours × 10 prices/jour)
- [x] `DatabaseSeeder` appelle `CryptoSeeder`
- [x] Admin utilisateur créé: `admin@bitchest.com` / `admin123`
- [x] Client utilisateur créé: `user@bitchest.com` / `user123` (balance: 500 EUR)

## 8. Controllers à vérifier/créer
À créer/vérifier:
- [ ] `AuthController` (login, logout, profile, updateProfile)
- [ ] `CryptoController` (index, show, history)
- [ ] `WalletController` (index, buy, sell)
- [ ] `TransactionController` (index)
- [ ] `AlertController` (CRUD)
- [ ] `AdminController` (getUsers, storeUser, updateUser, deleteUser, getStats)

## 9. Fichiers à créer/vérifier
- [ ] Form Requests: LoginRequest, BuyRequest, SellRequest, UpdateProfileRequest
- [ ] Middleware: AdminMiddleware (vérifier qu'il existe)
- [ ] Services: CryptoMonitoringService, AuditService, etc.

## Next Steps (À faire après cette vérification)
1. Tester le build avec: `php artisan migrate:fresh --seed`
2. Créer les controllers manquants
3. Créer les Form Requests
4. Implémenter la logique d'achat/vente avec le calcul correct du `avg_buy_price`
5. Ajouter les validations et l'authentification

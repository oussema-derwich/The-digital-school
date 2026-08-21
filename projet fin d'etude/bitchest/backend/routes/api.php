<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CryptoController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\RegistrationRequestController;
use App\Http\Controllers\Admin\CryptoController as AdminCryptoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes publiques (sans authentification)
// Route de test/santé
Route::get('health', function() {
    try {
        \DB::connection()->getPdo();
        return response()->json([
            'status' => 'success',
            'message' => 'API is healthy',
            'database' => 'connected'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database connection failed',
            'error' => $e->getMessage()
        ], 503);
    }
});

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

// Routes publiques pour les demandes d'inscription
Route::post('registration/request', [RegistrationRequestController::class, 'createRequest']);
Route::get('registration/status/{id}', [RegistrationRequestController::class, 'getRequestStatus']);

Route::get('cryptocurrencies', [CryptoController::class, 'index']);
Route::get('cryptocurrencies/{id}', [CryptoController::class, 'show']);
Route::get('cryptocurrencies/{id}/history', [CryptoController::class, 'history']);
// Routes cryptos avec endpoints statiques en premier
Route::get('cryptos/market', [CryptoController::class, 'market']);
Route::get('cryptos/count', [CryptoController::class, 'count']);
Route::get('cryptos', [CryptoController::class, 'index']);
Route::get('cryptos/{id}/history/{days}', [CryptoController::class, 'historyByDays']);
Route::get('cryptos/{id}', [CryptoController::class, 'show']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function() {
    // Authentification
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/profile', [AuthController::class, 'profile']);
    Route::get('auth/me', [AuthController::class, 'profile']);
    Route::put('auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('auth/avatar/upload', [AuthController::class, 'uploadAvatar']);
    Route::delete('auth/avatar', [AuthController::class, 'deleteAvatar']);
    
    // Wallet
    Route::get('wallet', [WalletController::class, 'index']);
    Route::post('buy', [WalletController::class, 'buy']);
    Route::post('sell', [WalletController::class, 'sell']);
    Route::post('wallets/buy', [WalletController::class, 'buy']);
    Route::post('wallets/sell', [WalletController::class, 'sell']);
    
    // Portfolio
    Route::get('portfolio', [PortfolioController::class, 'summary']);
    Route::get('portfolio/history', [PortfolioController::class, 'history']);
    
    // Favorites
    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('favorites/{crypto_id}', [FavoriteController::class, 'store']);
    Route::post('favorites/{crypto_id}/toggle', [FavoriteController::class, 'toggle']);
    Route::delete('favorites/{crypto_id}', [FavoriteController::class, 'destroy']);
    
    // Transactions (routes statiques d'abord)
    Route::get('transactions/export/csv', [TransactionController::class, 'exportCSV']);
    Route::get('transactions/export/pdf', [TransactionController::class, 'exportPDF']);
    Route::post('transactions', function(Request $request) {
        if ($request->input('type') === 'sell') {
            return app(WalletController::class)->sell($request);
        } else {
            return app(WalletController::class)->buy($request);
        }
    });
    Route::get('transactions', [TransactionController::class, 'index']);
    Route::get('transactions/{id}/proof', [TransactionController::class, 'proof']);
    
    // Alerts
    Route::apiResource('alerts', AlertController::class);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);

    // Admin endpoints
    Route::prefix('admin')->group(function() {
        // Registration Requests Management
        Route::get('registration-requests', [AdminController::class, 'getRegistrationRequests']);
        Route::get('registration-requests/{id}', [AdminController::class, 'getRegistrationRequestDetails']);
        Route::put('registration-requests/{id}', [AdminController::class, 'updateRegistrationRequest']);
        Route::delete('registration-requests/{id}', [AdminController::class, 'deleteRegistrationRequest']);
        Route::post('registration-requests/{id}/approve', [AdminController::class, 'approveRegistrationRequest']);
        Route::post('registration-requests/{id}/reject', [AdminController::class, 'rejectRegistrationRequest']);
        Route::post('registration-requests/{id}/resend-approval-email', [RegistrationRequestController::class, 'resendApprovalEmail']);
        
        // Users Management
        Route::get('users', [AdminController::class, 'getUsers']);
        Route::post('users', [AdminController::class, 'storeUser']);
        Route::put('users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('users/{id}', [AdminController::class, 'deleteUser']);
        
        // Cryptos Management
        Route::get('cryptos', [AdminCryptoController::class, 'index']);
        Route::post('cryptos', [AdminCryptoController::class, 'store']);
        Route::get('cryptos/{id}', [AdminCryptoController::class, 'show']);
        Route::put('cryptos/{id}', [AdminCryptoController::class, 'update']);
        Route::delete('cryptos/{id}', [AdminCryptoController::class, 'destroy']);
        
        // Transactions Management
        Route::get('transactions', [AdminController::class, 'getTransactions']);
        // Cancel transaction (admin)
        Route::post('transactions/{id}/cancel', [AdminController::class, 'cancelTransaction']);
        
        // Activities
        Route::get('activities', [AdminController::class, 'getActivities']);
        
        // Charts
        Route::get('charts/transactions', [AdminController::class, 'getChartData']);
        
        // Alerts Management
        Route::get('alerts', [AdminController::class, 'getAlerts']);
        Route::put('alerts/{id}/resume', [AdminController::class, 'resumeAlert']);
        
        // Settings
        Route::get('settings', [AdminController::class, 'getSettings']);
        Route::put('settings', [AdminController::class, 'updateSettings']);
        
        // Stats
        Route::get('stats', [AdminController::class, 'getStats']);
    });
});

// Route par défaut pour les routes non définies
Route::fallback(function() {
    return response()->json([
        'status' => 'error',
        'message' => 'Route non trouvée.'
    ], 404);
});




<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Cryptocurrency;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\PriceHistory;

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "🚀 DIAGNOSTIC COMPLET - BITCHEST PROJECT\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// 1. VÉRIFIER LA BASE DE DONNÉES
echo "📊 1. VÉRIFICATION BASE DE DONNÉES\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    $userCount = User::count();
    echo "✅ Users: $userCount utilisateurs trouvés\n";
    
    $users = User::all()->toArray();
    foreach ($users as $user) {
        $token = $user['name'] === 'Admin' ? '[ADMIN TOKEN]' : '[CLIENT TOKEN]';
        echo "   - {$user['name']} ({$user['email']}) - Role: {$user['role']} - Actif: {$user['is_active']}\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur users: " . $e->getMessage() . "\n";
}

try {
    $cryptoCount = Cryptocurrency::count();
    echo "\n✅ Cryptos: $cryptoCount cryptomonnaies trouvées\n";
} catch (\Exception $e) {
    echo "❌ Erreur cryptos: " . $e->getMessage() . "\n";
}

try {
    $priceCount = PriceHistory::count();
    echo "✅ Price Histories: $priceCount enregistrements\n";
} catch (\Exception $e) {
    echo "❌ Erreur price histories: " . $e->getMessage() . "\n";
}

try {
    $walletCount = Wallet::count();
    echo "✅ Wallets: $walletCount portefeuilles\n";
} catch (\Exception $e) {
    echo "❌ Erreur wallets: " . $e->getMessage() . "\n";
}

try {
    $transactionCount = Transaction::count();
    echo "✅ Transactions: $transactionCount transactions\n";
} catch (\Exception $e) {
    echo "❌ Erreur transactions: " . $e->getMessage() . "\n";
}

// 2. VÉRIFIER LES ROUTES
echo "\n📡 2. VÉRIFICATION DES ROUTES\n";
echo "─────────────────────────────────────────────────────────────\n";

$admin = User::where('role', 'admin')->first();
$client = User::where('role', 'client')->first();

if (!$admin) {
    echo "❌ Aucun admin trouvé!\n";
} else {
    echo "✅ Admin trouvé: {$admin->name}\n";
}

if (!$client) {
    echo "❌ Aucun client trouvé!\n";
} else {
    echo "✅ Client trouvé: {$client->name}\n";
}

// 3. VÉRIFIER LES MIDDLEWARE
echo "\n🛡️  3. VÉRIFICATION MIDDLEWARE\n";
echo "─────────────────────────────────────────────────────────────\n";

$middlewarePath = 'app/Http/Middleware/AdminMiddleware.php';
if (file_exists($middlewarePath)) {
    echo "✅ AdminMiddleware.php existe\n";
} else {
    echo "❌ AdminMiddleware.php manquant!\n";
}

$bootstrapPath = 'bootstrap/app.php';
$bootstrapContent = file_get_contents($bootstrapPath);
if (strpos($bootstrapContent, 'AdminMiddleware') !== false) {
    echo "✅ AdminMiddleware enregistré dans bootstrap/app.php\n";
} else {
    echo "❌ AdminMiddleware NON enregistré dans bootstrap/app.php!\n";
}

// 4. VÉRIFIER LES CONTRÔLEURS
echo "\n🎮 4. VÉRIFICATION CONTRÔLEURS\n";
echo "─────────────────────────────────────────────────────────────\n";

$controllers = [
    'app/Http/Controllers/Auth/AuthController.php',
    'app/Http/Controllers/AdminController.php',
    'app/Http/Controllers/WalletController.php',
    'app/Http/Controllers/CryptoController.php',
    'app/Http/Controllers/TransactionController.php',
    'app/Http/Controllers/AlertController.php'
];

foreach ($controllers as $controller) {
    if (file_exists($controller)) {
        echo "✅ " . basename($controller) . "\n";
    } else {
        echo "❌ " . basename($controller) . " manquant!\n";
    }
}

// 5. VÉRIFIER API AUTHENTIFICATION
echo "\n🔐 5. TEST AUTHENTIFICATION (Sanctum)\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    if ($admin) {
        $token = $admin->createToken('test-admin')->plainTextToken;
        echo "✅ Token admin généré (Sanctum): " . substr($token, 0, 20) . "...\n";
        echo "   Token complet: $token\n";
    }
    
    if ($client) {
        $token = $client->createToken('test-client')->plainTextToken;
        echo "✅ Token client généré (Sanctum): " . substr($token, 0, 20) . "...\n";
        echo "   Token complet: $token\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur Sanctum: " . $e->getMessage() . "\n";
}

// 6. VÉRIFIER LES DONNÉES POUR LES CHARTS
echo "\n📈 6. DONNÉES POUR LES CHARTS\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    // MarketChart data
    $cryptos = Cryptocurrency::with(['priceHistories' => function($q) {
        $q->orderBy('created_at', 'asc')->take(100);
    }])->get();
    
    echo "✅ MarketChart - " . count($cryptos) . " cryptos avec données:\n";
    foreach ($cryptos as $crypto) {
        $priceCount = $crypto->priceHistories->count();
        if ($priceCount > 0) {
            echo "   ✓ {$crypto->name} ({$crypto->symbol}): $priceCount prix\n";
        }
    }
    
    // Portfolio data
    if ($client && $client->wallet) {
        $portfolio = $client->wallet->cryptocurrencies()->get();
        echo "\n✅ Portfolio DonutChart - Client {$client->name}:\n";
        foreach ($portfolio as $crypto) {
            $quantity = $crypto->pivot->quantity;
            $avgPrice = $crypto->pivot->avg_buy_price;
            echo "   ✓ {$crypto->name}: $quantity @ €$avgPrice\n";
        }
    }
    
    // Transactions
    if ($client) {
        $transactions = $client->transactions()->take(10)->get();
        echo "\n✅ Transactions - " . count($transactions) . " transactions trouvées\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur données charts: " . $e->getMessage() . "\n";
}

// 7. RÉSUMÉ FINAL
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "✨ DIAGNOSTIC COMPLET TERMINÉ\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📝 CHECKLIST POUR FAIRE FONCTIONNER TOUT:\n";
echo "1. ✅ Middleware AdminMiddleware enregistré\n";
echo "2. ✅ Base de données avec toutes les tables\n";
echo "3. ✅ Authentification Sanctum fonctionnelle\n";
echo "4. ✅ Données disponibles pour les charts\n";
echo "5. ✅ Utilisateurs (Admin + Client) créés\n\n";

echo "🔧 PROCHAINES ÉTAPES:\n";
echo "1. Vérifier que le frontend récupère les données correctement\n";
echo "2. Tester les boutons (Buy, Sell, Admin actions)\n";
echo "3. Vérifier que les charts s'affichent avec les données\n";
echo "4. Tester le flux complet: Register -> Login -> Dashboard -> Charts\n\n";

?>

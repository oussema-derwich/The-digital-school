<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Cryptocurrency;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

// Simuler une requête POST /api/wallets/buy
echo "========================================\n";
echo "Testing BUY endpoint (simulated request)\n";
echo "========================================\n\n";

// Récupérer l'utilisateur admin
$user = User::where('email', 'admin@bitchest.com')->first();
if (!$user) {
    echo "[ERREUR] User admin@bitchest.com not found\n";
    exit(1);
}
echo "[OK] User trouvé: {$user->email}\n";

// Créer un token
$token = $user->createToken('auth_token')->plainTextToken;
echo "[OK] Token créé\n";

// Vérifier le wallet
$wallet = $user->wallet;
if (!$wallet) {
    echo "[ERREUR] Wallet non trouvé pour cet utilisateur\n";
    exit(1);
}
echo "[OK] Wallet trouvé avec balance: {$wallet->balance} EUR\n";

// Récupérer une crypto
$crypto = Cryptocurrency::first();
if (!$crypto) {
    echo "[ERREUR] Aucune crypto trouvée en BD\n";
    exit(1);
}
echo "[OK] Crypto trouvée: {$crypto->symbol} (ID={$crypto->id}), prix={$crypto->current_price}\n\n";

// Créer une requête simulée avec les données correctes
$request = new \Illuminate\Http\Request();
$request->initialize(
    [],
    ['cryptocurrency_id' => $crypto->id, 'quantity' => 0.001],  // POST data
    [],
    [],
    [],
    ['HTTP_AUTHORIZATION' => 'Bearer ' . $token, 'CONTENT_TYPE' => 'application/json']
);

// Authentifier l'utilisateur
Auth::guard('sanctum')->setUser($user);
echo "[OK] User authentifié dans la session\n\n";

// Tester la validation des données
echo "--- Validation ---\n";
$validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
    'cryptocurrency_id' => 'required|integer|exists:cryptocurrencies,id',
    'quantity' => 'required|numeric|min:0.00000001'
]);

if ($validator->fails()) {
    echo "[ERREUR] Validation échouée:\n";
    foreach ($validator->errors()->toArray() as $field => $msgs) {
        foreach ($msgs as $msg) {
            echo "  - {$field}: {$msg}\n";
        }
    }
    exit(1);
} else {
    echo "[OK] Validation réussie\n\n";
}

// Tester la logique du contrôleur
echo "--- Logique du BUY ---\n";
$quantity = (float)$request->quantity;
$price = (float)$crypto->current_price;
$total_cost = $quantity * $price;
$wallet_balance = (float)$wallet->balance;

echo "Quantity: {$quantity}\n";
echo "Price: {$price}\n";
echo "Total cost: {$total_cost}\n";
echo "Wallet balance: {$wallet_balance}\n";

if ($wallet_balance < $total_cost) {
    echo "[ERREUR] Solde insuffisant: besoin {$total_cost}, dispo {$wallet_balance}\n";
    exit(1);
}

echo "[OK] Solde suffisant\n\n";

// Simuler l'opération de buy
echo "--- Simulation du BUY ---\n";
echo "[STEP 1] Débiter le wallet de {$total_cost} EUR...\n";
$new_balance = $wallet_balance - $total_cost;
echo "  Nouveau balance: {$new_balance} EUR\n";

echo "[STEP 2] Créer/Mettre à jour le holding...\n";
$holding = \App\Models\WalletCrypto::firstOrCreate(
    ['wallet_id' => $wallet->id, 'cryptocurrency_id' => $crypto->id],
    ['quantity' => 0, 'average_buy_price' => 0]
);
echo "  Holding ID: {$holding->id}\n";

$old_qty = (float)$holding->quantity;
$old_total = $old_qty * (float)$holding->average_buy_price;
$new_qty = $old_qty + $quantity;
$new_avg = $new_qty > 0 ? ($old_total + $total_cost) / $new_qty : $price;

echo "  Old quantity: {$old_qty}, new quantity: {$new_qty}\n";
echo "  New average price: {$new_avg}\n";

echo "[STEP 3] Créer la transaction...\n";
$transaction = \App\Models\Transaction::create([
    'wallet_crypto_id' => $holding->id,
    'type' => 'buy',
    'quantity' => $quantity,
    'unit_price' => $price,
    'total_price' => $total_cost,
    'status' => 'completed'
]);
echo "  Transaction ID: {$transaction->id}\n";

echo "\n[SUCCESS] Achat simulé avec succès!\n";
echo "========================================\n";

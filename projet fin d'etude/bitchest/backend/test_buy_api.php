<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Cryptocurrency;
use Illuminate\Support\Facades\DB;

// Obtenir un user admin avec token
$user = User::where('email', 'admin@bitchest.com')->first();
if (!$user) {
    echo "User admin@bitchest.com not found\n";
    exit(1);
}

// Obtenir un token
$token = $user->createToken('auth_token')->plainTextToken;
echo "Token created: " . substr($token, 0, 20) . "...\n";

// Vérifier les cryptos
$crypto = Cryptocurrency::first();
if (!$crypto) {
    echo "No cryptocurrencies found\n";
    exit(1);
}

echo "First crypto: ID=" . $crypto->id . ", symbol=" . $crypto->symbol . ", price=" . $crypto->current_price . "\n";

// Vérifier le wallet
$wallet = $user->wallet;
if (!$wallet) {
    echo "User has no wallet\n";
    exit(1);
}
echo "Wallet balance: " . $wallet->balance . "\n";

// Faire la requête simulée
echo "\nTesting BUY endpoint...\n";
echo "POST /api/wallets/buy\n";
echo "Data: cryptocurrency_id=" . $crypto->id . ", quantity=0.001\n";
echo "Auth header: Bearer " . substr($token, 0, 20) . "...\n";

// Simuler le requête via Sanctum
$request = new \Illuminate\Http\Request();
$request->setMethod('POST');
$request->initialize(
    [],
    ['cryptocurrency_id' => $crypto->id, 'quantity' => 0.001],
    [],
    [],
    [],
    ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]
);

// Authentifier manuellement
$guard = \Illuminate\Support\Facades\Auth::guard('sanctum');
$guard->user($user);

// Tester la validation
$validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
    'cryptocurrency_id' => 'required|integer|exists:cryptocurrencies,id',
    'quantity' => 'required|numeric|min:0.00000001'
]);

if ($validator->fails()) {
    echo "\nValidation FAILED:\n";
    print_r($validator->errors()->toArray());
} else {
    echo "\nValidation PASSED\n";
    echo "Cryptocurrency exists: " . ($crypto->exists ? 'YES' : 'NO') . "\n";
}

echo "\nDone.\n";

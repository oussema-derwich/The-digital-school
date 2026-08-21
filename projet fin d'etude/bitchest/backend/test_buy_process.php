<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\WalletCrypto;
use App\Models\Transaction;
use App\Models\Cryptocurrency;
use Illuminate\Support\Facades\DB;

try {
    echo "=== Test Buy Transaction ===\n\n";

    // 1. Find user
    $user = User::where('email', 'admin@bitchest.com')->first();
    if (!$user) {
        die("User not found\n");
    }
    echo "✓ Found user: {$user->email}\n";
    echo "  Balance: {$user->balance_eur} EUR\n";

    // 2. Get wallet
    $wallet = $user->wallet;
    if (!$wallet) {
        die("Wallet not found\n");
    }
    echo "✓ Found wallet: {$wallet->id}\n";

    // 3. Get crypto
    $crypto = Cryptocurrency::find(1);
    if (!$crypto) {
        die("Cryptocurrency not found\n");
    }
    echo "✓ Found crypto: {$crypto->symbol} ({$crypto->name})\n";
    echo "  Current price: {$crypto->current_price} EUR\n";

    // 4. Calculate
    $quantity = 0.1;
    $price = (float)$crypto->current_price;
    $total_cost = $quantity * $price;
    
    echo "\n=== Transaction Details ===\n";
    echo "Quantity: {$quantity}\n";
    echo "Price: {$price} EUR\n";
    echo "Total cost: {$total_cost} EUR\n";
    echo "User balance: {$user->balance_eur} EUR\n";
    echo "Can buy: " . ($user->balance_eur >= $total_cost ? "YES" : "NO") . "\n";

    if ($user->balance_eur < $total_cost) {
        die("\n✗ Insufficient balance!\n");
    }

    // 5. Debit account
    echo "\n=== Processing ===\n";
    $user->balance_eur -= $total_cost;
    $user->save();
    echo "✓ User balance debited\n";

    // 6. Find or create holding
    $holding = WalletCrypto::firstOrCreate(
        ['wallet_id' => $wallet->id, 'cryptocurrency_id' => $crypto->id],
        ['quantity' => 0, 'average_buy_price' => 0]
    );
    echo "✓ Holding found/created: ID {$holding->id}\n";

    // 7. Calculate new average
    $old_quantity = (float)$holding->quantity;
    $old_total_cost = $old_quantity * (float)$holding->average_buy_price;
    $new_quantity = $old_quantity + $quantity;
    $new_avg_price = $new_quantity > 0 ? ($old_total_cost + $total_cost) / $new_quantity : $price;

    echo "  Old quantity: {$old_quantity}\n";
    echo "  New quantity: {$new_quantity}\n";
    echo "  New avg price: {$new_avg_price}\n";

    // 8. Update holding
    $holding->update([
        'quantity' => $new_quantity,
        'average_buy_price' => $new_avg_price
    ]);
    echo "✓ Holding updated\n";

    // 9. Create transaction
    $transaction = Transaction::create([
        'user_id' => $user->id,
        'cryptocurrency_id' => $crypto->id,
        'type' => 'buy',
        'quantity' => $quantity,
        'price_at_transaction' => $price,
        'eur_amount' => $total_cost
    ]);
    echo "✓ Transaction created: ID {$transaction->id}\n";

    // 10. Create notification
    \App\Models\Notification::create([
        'user_id' => $user->id,
        'type' => 'buy',
        'title' => "Achat réussi - {$crypto->symbol}",
        'message' => "Vous avez acheté {$quantity} {$crypto->symbol} à {$price}€ chacun (Total: {$total_cost}€)"
    ]);
    echo "✓ Notification created\n";

    echo "\n=== SUCCESS ===\n";
    echo "✓ Transaction completed successfully!\n";
    
} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStacktrace:\n";
    echo $e->getTraceAsString();
}

<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

echo "========== TESTING ALERTS/NOTIFICATIONS ==========\n\n";

// 1. Get admin user
$user = User::where('email', 'admin@bitchest.com')->first();
if (!$user) {
    echo "[ERROR] User not found\n";
    exit(1);
}
echo "[OK] User: {$user->email}\n";

// 2. Create a test notification
echo "\n[TEST] Creating a test notification...\n";
$notification = Notification::create([
    'user_id' => $user->id,
    'message' => 'Test notification from system',
    'is_read' => false
]);
echo "[OK] Notification created: ID={$notification->id}\n";

// 3. Query notifications
echo "\n[TEST] Querying notifications...\n";
$notifications = Notification::where('user_id', $user->id)->get();
echo "[OK] Found " . $notifications->count() . " notifications\n";

foreach ($notifications as $notif) {
    echo "  - [{$notif->id}] {$notif->message} (read={$notif->is_read})\n";
}

// 4. Mark as read
echo "\n[TEST] Marking notification as read...\n";
$notification->markAsRead();
echo "[OK] Notification marked as read\n";

// 5. Verify via controller response
echo "\n[TEST] Simulating AlertController response...\n";
Auth::guard('sanctum')->setUser($user);

$alertController = new \App\Http\Controllers\AlertController();
$request = new \Illuminate\Http\Request();
$response = $alertController->index();
$data = json_decode($response->getContent(), true);

echo "[OK] Response status: {$data['status']}\n";
echo "[OK] Notifications returned: " . count($data['data']) . "\n";

foreach ($data['data'] as $alert) {
    echo "  - [{$alert['id']}] {$alert['message']} (read={$alert['is_read']})\n";
}

echo "\n========== TEST COMPLETE ✓ ==========\n";

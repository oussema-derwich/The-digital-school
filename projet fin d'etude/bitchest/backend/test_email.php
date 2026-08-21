<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationApprovedMail;
use App\Models\User;

// Créer un utilisateur de test
$user = new User();
$user->id = 999;
$user->name = 'Test User';
$user->email = 'test@example.com';

$tempPassword = 'TestPassword123!';

try {
    echo "📧 Envoi d'un email de test...\n";
    
    Mail::to($user->email)->send(new RegistrationApprovedMail($user, $tempPassword));
    
    echo "✅ Email envoyé avec succès!\n";
    echo "📝 Vérifiez les logs: storage/logs/laravel.log\n";
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

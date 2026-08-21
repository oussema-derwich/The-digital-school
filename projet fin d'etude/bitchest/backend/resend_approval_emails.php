<?php
/**
 * Resend approval emails to approved registration requests that failed
 * Utile pour renvoyer les emails aux utilisateurs approuvés antérieurement
 * dont l'email n'a pas pu être envoyé
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Models\RegistrationRequest;
use App\Mail\RegistrationApprovedMail;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║      RENVOI DES EMAILS D'APPROBATION NON ENVOYÉS              ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Récupérer toutes les demandes approuvées qui n'ont pas eu d'email envoyé
$failedRequests = RegistrationRequest::where('is_approved', true)
    ->where(function ($query) {
        $query->where('approval_email_sent', false)
              ->orWhereNull('approval_email_sent');
    })
    ->with('user')
    ->get();

echo "Demandes d'inscription approuvées sans email envoyé: " . $failedRequests->count() . "\n\n";

if ($failedRequests->isEmpty()) {
    echo "✅ Aucune demande d'inscription avec email non-envoyé à traiter.\n";
    exit(0);
}

$successCount = 0;
$failureCount = 0;

foreach ($failedRequests as $request) {
    $user = $request->user;
    
    if (!$user || !$request->temp_password) {
        echo "⚠️  [ID: $request->id] Données manquantes - utilisateur ou password temporaire\n";
        $failureCount++;
        continue;
    }

    try {
        // Envoyer l'email
        Mail::to($user->email)->send(new RegistrationApprovedMail($user, $request->temp_password));
        
        // Tracker le succès
        $request->approval_email_sent = true;
        $request->approval_email_sent_at = now();
        $request->approval_email_error = null;
        $request->save();
        
        echo "✅ [ID: $request->id] Email envoyé avec succès à " . $user->email . "\n";
        $successCount++;
    } catch (\Exception $e) {
        // Tracker l'erreur
        $request->approval_email_sent = false;
        $request->approval_email_error = $e->getMessage();
        $request->save();
        
        echo "❌ [ID: $request->id] Erreur lors de l'envoi à " . $user->email . "\n";
        echo "   Erreur: " . $e->getMessage() . "\n";
        $failureCount++;
    }
}

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      RÉSUMÉ DU RENVOI                         ║\n";
echo "╠════════════════════════════════════════════════════════════════╣\n";
echo "║ Emails envoyés avec succès: " . str_pad($successCount, 30) . " ║\n";
echo "║ Erreurs d'envoi: " . str_pad($failureCount, 40) . " ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";

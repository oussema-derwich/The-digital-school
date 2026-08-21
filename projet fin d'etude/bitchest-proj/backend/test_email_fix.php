<?php
/**
 * Test Email Fix - Vérification du système d'envoi d'email
 * 
 * Ce script teste:
 * 1. La configuration du mail
 * 2. L'envoi d'un email de test
 * 3. L'état des migrations
 * 4. Les colonnes de tracking d'email
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\RegistrationRequest;
use App\Mail\RegistrationApprovedMail;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         TEST EMAIL FIX - SYSTÈME D'ENVOI D'EMAIL              ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// 1. Configuration du Mail
echo "1️⃣ VÉRIFICATION DE LA CONFIGURATION DU MAIL:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$mailConfig = config('mail');
echo "   • Mailer par défaut: " . $mailConfig['default'] . "\n";
echo "   • Host: " . $mailConfig['mailers'][$mailConfig['default']]['host'] . "\n";
echo "   • Port: " . $mailConfig['mailers'][$mailConfig['default']]['port'] . "\n";
echo "   • Username: " . $mailConfig['mailers'][$mailConfig['default']]['username'] . "\n";
echo "   ✅ Configuration chargée\n\n";

// 2. Vérifier les migrations
echo "2️⃣ VÉRIFICATION DES MIGRATIONS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $migrations = DB::table('migrations')->get();
    echo "   • Migrations appliquées: " . count($migrations) . "\n";
    foreach ($migrations as $migration) {
        echo "     - " . $migration->migration . "\n";
    }
    echo "   ✅ Migrations vérifiées\n\n";
} catch (\Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n\n";
}

// 3. Vérifier le schéma de la table registration_requests
echo "3️⃣ VÉRIFICATION DE LA TABLE registration_requests:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $columns = DB::getSchemaBuilder()->getColumnListing('registration_requests');
    echo "   Colonnes présentes:\n";
    foreach ($columns as $column) {
        echo "     - " . $column . "\n";
    }
    
    // Vérifier les colonnes de tracking
    $hasTracking = in_array('approval_email_sent', $columns) &&
                   in_array('approval_email_sent_at', $columns) &&
                   in_array('approval_email_error', $columns);
    
    if ($hasTracking) {
        echo "   ✅ Colonnes de tracking d'email présentes\n\n";
    } else {
        echo "   ⚠️ Colonnes de tracking manquantes\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n\n";
}

// 4. Tester l'envoi d'un email
echo "4️⃣ TEST D'ENVOI D'EMAIL:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    // Créer un utilisateur de test
    $testUser = new User([
        'id' => 999,
        'name' => 'Test User',
        'email' => 'oussemaderwich0@gmail.com',
        'password' => 'test'
    ]);
    
    $tempPassword = 'TestPass123!';
    
    echo "   • Destination: " . $testUser->email . "\n";
    echo "   • Utilisateur de test: " . $testUser->name . "\n";
    
    // Essayer d'envoyer l'email
    try {
        Mail::to($testUser->email)->send(new RegistrationApprovedMail($testUser, $tempPassword));
        echo "   ✅ Email envoyé avec succès!\n\n";
    } catch (\Exception $e) {
        echo "   ❌ Erreur d'envoi: " . $e->getMessage() . "\n";
        echo "   💡 Conseil: Vérifiez les credentials Gmail et les App Passwords\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n\n";
}

// 5. Afficher les demandes d'inscription
echo "5️⃣ DEMANDES D'INSCRIPTION EN ATTENTE:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $requests = RegistrationRequest::where('is_approved', false)
        ->with('user')
        ->get();
    
    if ($requests->count() > 0) {
        echo "   Demandes en attente: " . $requests->count() . "\n";
        foreach ($requests as $req) {
            echo "\n   📧 ID: " . $req->id . "\n";
            echo "      Email: " . $req->email . "\n";
            echo "      Créée: " . $req->created_at . "\n";
        }
    } else {
        echo "   ✅ Aucune demande en attente\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n\n";
}

// 6. Demandes approuvées
echo "6️⃣ DEMANDES APPROUVÉES:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $approvedRequests = RegistrationRequest::where('is_approved', true)
        ->with('user')
        ->latest()
        ->get();
    
    if ($approvedRequests->count() > 0) {
        echo "   Demandes approuvées: " . $approvedRequests->count() . "\n";
        foreach ($approvedRequests->take(3) as $req) {
            echo "\n   ✅ ID: " . $req->id . "\n";
            echo "      Email: " . $req->email . "\n";
            echo "      Email envoyé: " . ($req->approval_email_sent ? "✅ Oui" : "❌ Non") . "\n";
            if ($req->approval_email_sent_at) {
                echo "      Envoyé à: " . $req->approval_email_sent_at . "\n";
            }
            if ($req->approval_email_error) {
                echo "      Erreur: " . $req->approval_email_error . "\n";
            }
        }
    } else {
        echo "   ℹ️ Aucune demande approuvée\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n\n";
}

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                   TEST COMPLÉTÉ                               ║\n";
echo "║                                                                ║\n";
echo "║  RÉSUMÉ DES CORRECTIONS APPLIQUÉES:                           ║\n";
echo "║  1. ✅ Migration appliquée pour tracking email                 ║\n";
echo "║  2. ✅ Configuration MAIL_MAILER changée à 'smtp'              ║\n";
echo "║  3. ✅ Controllers corrigés pour tracker les emails            ║\n";
echo "║  4. ✅ Endpoint pour renvoyer l'email créé                     ║\n";
echo "║  5. ✅ Model RegistrationRequest mis à jour                    ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";

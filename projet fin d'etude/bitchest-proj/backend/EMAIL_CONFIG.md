# Configuration Email - BitChest

## État Actuel (Développement)

✅ **MAIL_MAILER=log**
- Les emails sont écris dans `storage/logs/laravel.log`
- Les emails ne sont PAS vraiment envoyés
- Parfait pour développement et tests

## Configuration Pour Production

Mettre à jour votre fichier `.env` avec :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=oussemaderwich0@gmail.com
MAIL_PASSWORD="sjnz ejdb vekb sbfq"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@bitchest.com"
MAIL_FROM_NAME="BitChest Platform"

# Queue pour les emails
QUEUE_CONNECTION=redis
# ou QUEUE_CONNECTION=database
```

## Tester l'Email en Développement

### Option 1: Vérifier les Logs
```bash
cd backend
tail -f storage/logs/laravel.log | grep -i "email\|mail"
```

### Option 2: Utiliser le test
```bash
cd backend
php test_email.php
```

### Option 3: Approuver une demande d'inscription
1. Aller sur http://localhost:5173/admin/registration-requests
2. Cliquer sur "Accepter" pour une demande
3. Aller sur le backend et vérifier: `tail storage/logs/laravel.log`

## Dépannage

### Erreur: "Column 'temp_password' not found"
✅ **RÉSOLU** - Migration exécutée
```bash
php artisan migrate
```

### Erreur: "The tls scheme is not supported"
✅ **RÉSOLU** - Vous avez la bonne configuration maintenant

### Email reçu vide ou mal formaté
1. Vérifier la vue: `resources/views/emails/registration_approved.blade.php`
2. La vue utilise les composants Mail de Laravel
3. Pour tester le HTML: modifier la view ou utiliser `MAIL_MAILER=array` pour capturer le mailABLE

## Fichiers Importants

- `.env` - Configuration du mailer
- `config/mail.php` - Configuration avancée des mailers
- `app/Mail/RegistrationApprovedMail.php` - Classe du mail
- `resources/views/emails/registration_approved.blade.php` - Template HTML

## Pour Passer en SMTP Réel (Production)

1. Activer "2-Step Verification" sur votre compte Gmail
2. Générer un "App Password" (mot de passe d'application)
3. Remplacer `MAIL_PASSWORD` par le app password

**Note:** Les mots de passe d'application Gmail : https://myaccount.google.com/apppasswords

## Architecture Email

```
User crée une inscription
    ↓
Admin approuve via Dashboard
    ↓
AdminController::approveRegistrationRequest()
    ↓
Mail::to($user->email)->send(new RegistrationApprovedMail($user, $tempPassword))
    ↓
En DEV: Écrit dans storage/logs/laravel.log
En PROD: Envoyé via SMTP ou mis en queue (Redis/Database)
```

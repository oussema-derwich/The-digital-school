# Email Authentication with Admin Approval System

## Overview

Ce système d'authentification par email avec approbation d'administrateur fonctionne selon le flow suivant :

1. **Submission** : L'utilisateur soumet son email et son nom
2. **Attente** : La demande est enregistrée en attente d'approbation
3. **Approbation Admin** : L'administrateur approuve la demande
4. **Email de confirmation** : L'utilisateur reçoit un email avec un password temporaire
5. **Connexion** : L'utilisateur se connecte avec son email et le password reçu

## Architecture Backend

### Changes Made

#### 1. Migration - `registration_requests` Table
- Ajout de colonne `temp_password` pour stocker le password temporaire généré

#### 2. Mailable - `RegistrationApprovedMail`
- Located: `app/Mail/RegistrationApprovedMail.php`
- Envoie un email avec :
  - Accueil personnalisé
  - Email de l'utilisateur
  - Password temporaire généré
  - Lien de connexion

#### 3. Email Template - `emails/registration_approved`
- Located: `resources/views/emails/registration_approved.blade.php`
- Template Blade professionnelle avec lien de connexion

#### 4. Controller - `RegistrationRequestController`

**Method: `createRequest`**
- Paramètres : `name`, `email`, `role` (optionnel)
- **SANS password** - L'utilisateur ne fournit pas de password
- Crée un utilisateur avec un password placeholder
- Crée une demande d'inscription en attente

```
POST /api/registration/request
{
  "name": "John Doe",
  "email": "john@example.com",
  "role": "client"  // optional
}
```

**Method: `approveRequest`**
- Génère un password temporaire fort (12 caractères)  
- Hash et sauvegarde le password
- Valide automatiquement l'email (`email_verified_at`)
- Active le compte (`is_active = true`)
- Crée le wallet de l'utilisateur
- Envoie l'email avec le password temporaire

```
Route: POST /api/admin/registration-requests/{id}/approve
```

**Method: `rejectRequest`**
- Rejette la demande
- Raison optionnelle
- Crée une notification pour l'utilisateur

```
Route: POST /api/admin/registration-requests/{id}/reject
```

#### 5. Admin Controller - Updated
- `approveRegistrationRequest` : Updated to use new logic
  - Génère password temporaire
  - Envoie l'email
  - Valide l'email automatiquement

#### 6. User Model
- `email_verified_at` : Auto-validé lors de l'approbation
- `is_active` : Set to true lors de l'approbation
- `temp_password` : Peut être stocké ou non selon les besoins

#### 7. Configuration Updates
- `config/app.php` : Ajout de `frontend_url`
- `.env.example` : Ajout de `FRONTEND_URL`

## Architecture Frontend

### Changes Made

#### 1. registrationApi.ts
**Method: `createRegistrationRequest`**

**Before:**
```typescript
payload: {
  name: string
  email: string
  password: string
  password_confirmation: string
  role?: string
}
```

**After:**
```typescript
payload: {
  name: string
  email: string
  role?: string
}
```

#### 2. Register.vue Component
- Suppression des champs de password
- Form réduit : name + email
- Message d'information pour l'utilisateur
- Redirection vers login après soumission

**New Form Fields:**
- Name (required)
- Email (required)

**Messages:**
- Success : "Demande créée avec succès! Vous recevrez un email..."
- Error handling pour les validations

## API Endpoints

### Public Endpoints

```
POST /api/registration/request
- Créer une demande d'inscription
- Body: { name, email, role? }
- Response: { status, message, user }
```

```
GET /api/registration/status/{id}
- Vérifier le statut d'une demande
- Response: { status, data }
```

### Admin Endpoints (Authenticated)

```
GET /api/admin/registration-requests
- Lister les demandes
- Filter par status (pending/approved/rejected)
```

```
POST /api/admin/registration-requests/{id}/approve
- Approuver une demande
- Génère et envoie le password
- Valide l'email
- Crée le wallet
```

```
POST /api/admin/registration-requests/{id}/reject
- Rejeter une demande
- Body: { reason }
```

## Environment Variables

Pour que l'envoi d'emails fonctionne, configurez :

```env
# .env

# Mail Configuration
MAIL_MAILER=smtp  # ou log/sendmail/ses/etc
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="admin@bitchest.com"
MAIL_FROM_NAME="Bitchest"

# Frontend URL (pour les liens dans les emails)
FRONTEND_URL=http://localhost:5173  # dev
# ou FRONTEND_URL=https://bitchest.app  # production
```

**Development:** Utilisez `MAIL_MAILER=log` pour voir les emails dans les logs

## User Registration Flow

### Step 1: User Registration Page
```
Input: Name, Email
Button: "Soumettre la demande"
```

### Step 2: Request Created
```
Response: "Demande créée avec succès!"
Status: pending (in_approvalattente d'approbation)
```

### Step 3: Admin Approval
```
Admin Dashboard → Registration Requests
Select pending request
Click "Approve"
```

### Step 4: Email Sent
```
User receives email with:
- Subject: "Votre demande d'inscription a été approuvée - Bitchest"
- Email address
- Temporary password
- Login link
```

### Step 5: User Login
```
Email: user@example.com
Password: [temporary password from email]
System: Validates email automatically
```

### Step 6: After First Login
```
Recommended: User changes password
Email is verified ✓
Account is active ✓
User can start trading ✓
```

## Security Considerations

✅ **Strong Passwords**
- Temporary passwords: 12 random characters
- Auto-generated, never shown in database without hashing

✅ **Email Verification**
- Email verified automatically on approval
- No manual verification link needed

✅ **Admin Control**
- Only admins can approve registrations
- Prevents unauthorized account creation

✅ **Notification**
- User receives notification on approval/rejection

✅ **Wallet Protection**
- Wallet created only on approval
- Initial balance: 500 EUR

## Testing

### Via Artisan Tinker
```bash
php artisan tinker

# Test email sending
Mail::to('test@example.com')->send(
  new App\Mail\RegistrationApprovedMail(
    App\Models\User::first(),
    'TEST1234PASSWORD'
  )
);
```

### Via API Endpoints

1. **Create Registration Request**
```bash
curl -X POST http://localhost:8000/api/registration/request \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com"
  }'
```

2. **Approve Registration (as admin)**
```bash
curl -X POST http://localhost:8000/api/admin/registration-requests/1/approve \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

## Database Changes

Run migrations:
```bash
php artisan migrate
```

This will:
1. Create `registration_requests` table with `temp_password` column
2. Add necessary indices

## Email Customization

To customize the email template:

Edit: `resources/views/emails/registration_approved.blade.php`

Available variables:
- `$user` : User object (id, name, email, etc)
- `$tempPassword` : Temporary password
- `$loginUrl` : Frontend login URL

## Troubleshooting

### Emails not sending
1. Check `MAIL_MAILER` in `.env`
2. Check email credentials
3. Look at logs: `storage/logs/laravel.log`
4. Use `MAIL_MAILER=log` for development

### Admin approval fails
1. Check user exists
2. Verify admin role
3. Check email configuration
4. Look at error message in response

### User can't login with temp password
1. Verify email delivery
2. Check password hash
3. Verify user is active
4. Check email_verified_at is set

## Migration to Production

1. Update `.env` with real email configuration
2. Configure `FRONTEND_URL` to production URL
3. Test email delivery with admin approval
4. Monitor `storage/logs/laravel.log` for errors
5. Set up email service (SendGrid, Mailgun, etc)

---

**Last Updated:** 2025-04-15
**Status:** Production Ready ✅

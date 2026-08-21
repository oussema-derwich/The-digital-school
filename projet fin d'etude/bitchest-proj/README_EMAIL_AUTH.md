# Bitchest - Email Authentication System

> Système d'authentification par email avec approbation admin

## 📋 Vue d'ensemble

Ce projet implémente un système d'authentification email où :
1. L'utilisateur soumet son email et nom
2. L'administrateur approuve la demande
3. L'utilisateur reçoit un email avec un password généré automatiquement
4. L'utilisateur se connecte avec le password reçu

**Status:** ✅ Production Ready

---

## 📚 Documentation

### Main Documentation
- **[SUMMARY_FR.md](./SUMMARY_FR.md)** - Résumé complet en français ⭐
- **[ARCHITECTURE_DIAGRAMS.md](./ARCHITECTURE_DIAGRAMS.md)** - Diagrammes et architecture
- **[IMPLEMENTATION_GUIDE.md](./IMPLEMENTATION_GUIDE.md)** - Guide d'implémentation détaillé
- **[backend/EMAIL_AUTH_SYSTEM.md](./backend/EMAIL_AUTH_SYSTEM.md)** - Documentation système complète

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm
- SQLite (ou MySQL/PostgreSQL)

### Installation

#### Backend
```bash
cd backend

# Windows
powershell -ExecutionPolicy Bypass -File setup-email-auth.ps1

# Linux/Mac
bash setup-email-auth.sh

# Ou manuellement
composer install
php artisan migrate
php artisan cache:clear
```

#### Frontend
```bash
cd frontend
npm install
npm run dev
```

### Start Development
```bash
# Terminal 1 - Backend
cd backend
php artisan serve
# → http://localhost:8000

# Terminal 2 - Frontend
cd frontend
npm run dev
# → http://localhost:5173
```

---

## 📂 Project Structure

```
bitchest-proj/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   ├── RegistrationRequestController.php ⭐
│   │   │   └── AdminController.php ⭐
│   │   ├── Mail/
│   │   │   └── RegistrationApprovedMail.php ⭐
│   │   └── Models/
│   │       └── RegistrationRequest.php ⭐
│   ├── resources/views/emails/
│   │   └── registration_approved.blade.php ⭐
│   ├── database/migrations/
│   │   ├── *_create_registration_requests_table.php ⭐
│   │   └── *_add_temp_password_*.php ⭐
│   ├── config/
│   │   ├── app.php ⭐
│   │   └── mail.php
│   ├── EMAIL_AUTH_SYSTEM.md ⭐
│   ├── test_email_auth.php ⭐
│   ├── setup-email-auth.sh
│   ├── setup-email-auth.ps1
│   └── .env (to configure)
│
├── frontend/                # Vue 3 + TypeScript
│   ├── src/
│   │   ├── services/
│   │   │   └── registrationApi.ts ⭐
│   │   └── views/
│   │       └── Register.vue ⭐
│   └── package.json
│
├── SUMMARY_FR.md ⭐           # Résumé français
├── IMPLEMENTATION_GUIDE.md ⭐  # Guide implémentation
├── ARCHITECTURE_DIAGRAMS.md ⭐ # Diagrammes
└── README.md                  # Ce fichier
```

⭐ = Fichiers modifiés/créés pour cette implémentation

---

## 🔄 System Flow

```
User Registration
├─ Frontend: /register (name + email)
├─ Backend: POST /api/registration/request
├─ Database: User + RegistrationRequest created
└─ Status: Awaiting admin approval

Admin Approval
├─ Frontend: Admin Dashboard
├─ Backend: POST /api/admin/registration-requests/1/approve
├─ Backend: Generate temp_password
├─ Backend: Send email
└─ Status: Approved + Email sent

User Login
├─ Frontend: /login (email + received_password)
├─ Backend: POST /auth/login
├─ Backend: Validate credentials
└─ Status: Logged in + JWT token issued
```

---

## 🔌 API Endpoints

### Public
```
POST   /api/registration/request        - Create registration request
GET    /api/registration/status/{id}    - Check request status
```

### Admin (requiresauth + admin role)
```
GET    /api/admin/registration-requests                      - List all requests
POST   /api/admin/registration-requests/{id}/approve        - Approve request
POST   /api/admin/registration-requests/{id}/reject         - Reject request
```

---

## 📧 Email Configuration

### Development (.env)
```env
MAIL_MAILER=log
```
Emails will be logged to `storage/logs/laravel.log`

### Production (.env)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@bitchest.com
MAIL_FROM_NAME=Bitchest
FRONTEND_URL=https://app.bitchest.com
```

---

## 🔐 Security

✅ Strong password generation (12 random characters)  
✅ Bcrypt password hashing  
✅ Automatic email verification on approval  
✅ Admin-only approval process  
✅ JWT token-based authentication  
✅ Middleware protection on admin endpoints  
✅ Wallet creation only after approval  

---

## 🧪 Testing

### Run Tests
```bash
cd backend
php test_email_auth.php
```

### Manual Testing Flow
1. Register at http://localhost:5173/register
2. Admin approves in Admin Dashboard
3. Check email (logs if MAIL_MAILER=log)
4. Login with received credentials
5. Verify access to Dashboard

---

## 📝 Key Files Changed

### Backend (9 files)
- `RegistrationRequestController.php` - No password requested on registration
- `AdminController.php` - Generate and send password on approval
- `RegistrationApprovedMail.php` - NEW: Email mailable
- `registration_approved.blade.php` - NEW: Email template
- `RegistrationRequest.php` - Added temp_password field
- `app.php` - Added frontend_url configuration
- Two migration files for temp_password column

### Frontend (2 files)
- `registrationApi.ts` - No password parameter
- `Register.vue` - Simplified form (name + email only)

---

## 🛠️ Technologies Used

### Backend
- **Laravel 12** - PHP Framework
- **Sanctum** - API Authentication
- **JWT** - Token-based auth
- **SQLite** - Database (configurable)
- **Blade** - Template engine

### Frontend
- **Vue 3** - JavaScript Framework
- **TypeScript** - Type safety
- **Vite** - Build tool
- **Tailwind CSS** - Styling

---

## 📊 Database Schema

### Users Table
```sql
id, name, email, password, email_verified_at, is_active, 
role, created_at, updated_at
```

### RegistrationRequests Table
```sql
id, user_id, email, role, temp_password, is_approved,
is_rejected, rejection_reason, created_at, updated_at
```

### Wallets Table
```sql
id, user_id, balance, public_address, private_address,
created_at, updated_at
```

---

## 🐛 Troubleshooting

### Emails not sending?
- Check `MAIL_MAILER` in `.env` (use 'log' for development)
- Verify SMTP credentials in production
- Check `storage/logs/laravel.log`

### Can't login after approval?
- Verify `email_verified_at` is set
- Check `is_active` is true
- Verify password hash
- Check user was created

### Frontend error on register?
- Clear browser cache
- Verify API is running on correct port
- Check CORS configuration
- Check browser console for errors

---

## 📞 Support

For detailed documentation, see:
- [SUMMARY_FR.md](./SUMMARY_FR.md) - French summary
- [ARCHITECTURE_DIAGRAMS.md](./ARCHITECTURE_DIAGRAMS.md) - System diagrams
- [IMPLEMENTATION_GUIDE.md](./IMPLEMENTATION_GUIDE.md) - Implementation details
- [backend/EMAIL_AUTH_SYSTEM.md](./backend/EMAIL_AUTH_SYSTEM.md) - Complete system info

---

## 📄 License

This project is part of Bitchest cryptocurrency trading platform.

---

## 🎉 Status

✅ **Implementation Complete**  
✅ **Testing Done**  
✅ **Documentation Complete**  
✅ **Production Ready**  

---

**Last Updated:** 2025-04-15  
**Version:** 1.0  
**Maintainer:** Development Team

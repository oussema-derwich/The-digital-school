# Architecture et Flux du Système d'Authentification par Email

## Diagramme du Flux Complet

```mermaid
graph TD
    A["👤 Utilisateur"] 
    B["📋 Frontend Register Page"]
    C["📱 Soumet: name + email"]
    D["🔌 API POST /registration/request"]
    E["📊 Backend - Create Request"]
    F["💾 User créé (is_active=false)"]
    G["📝 RegistrationRequest créée"]
    H["⏳ En attente d'approbation"]
    
    I["👨‍💼 Admin Dashboard"]
    J["📋 Liste des demandes"]
    K["✅ Admin clique Approuver"]
    L["🔌 API POST /admin/approve"]
    M["🔐 Backend - Approval Logic"]
    N["🔑 Génère temp_password"]
    O["💾 Hash + Sauvegarde"]
    P["✓ Valide email_verified_at"]
    Q["✓ Active user is_active=true"]
    R["💰 Crée wallet"]
    S["📧 Envoie email"]
    T["🎊 Demande approuvée"]
    
    U["📩 Email Reçu"]
    V["💌 Contient: email + temp_password"]
    W["🔗 Lien de connexion"]
    
    X["👤 User vérifie email"]
    Y["🏠 Va sur Login Page"]
    Z["📧 Entre: email"]
    AA["🔑 Entre: temp_password"]
    BB["🔌 API POST /auth/login"]
    CC["✓ Verify email"]
    DD["✓ Verify password hash"]
    EE["✓ Check is_active=true"]
    FF["🔑 Génère JWT token"]
    GG["🎉 User Connecté!"]
    HH["📊 Dashboard"]
    II["✓ Email verified ✓"]
    JJ["✓ Account active ✓"]
    KK["✓ Can start trading ✓"]
    
    A --> B
    B --> C
    C --> D
    D --> E
    E --> F
    E --> G
    F --> H
    G --> H
    
    I --> J
    J --> H
    H --> K
    K --> L
    L --> M
    M --> N
    N --> O
    O --> P
    P --> Q
    Q --> R
    R --> S
    S --> T
    
    T --> U
    U --> V
    U --> W
    
    X --> Y
    Y --> Z
    Z --> AA
    AA --> BB
    BB --> CC
    CC --> DD
    DD --> EE
    EE --> FF
    FF --> GG
    GG --> HH
    HH --> II
    HH --> JJ
    HH --> KK
    
    style A fill:#e1f5ff
    style I fill:#fff3e0
    style X fill:#e1f5ff
    style GG fill:#c8e6c9
    style HH fill:#c8e6c9
```

## Diagramme de la Structure Backend

```mermaid
graph LR
    A["RegistrationRequestController<br/>- createRequest()<br/>- approveRequest()<br/>- rejectRequest()"]
    
    B["AdminController<br/>- approveRegistrationRequest()"]
    
    C["RegistrationRequest Model<br/>- user_id<br/>- email<br/>- temp_password<br/>- is_approved"]
    
    D["User Model<br/>- email_verified_at<br/>- is_active<br/>- password"]
    
    E["Wallet Model<br/>- balance<br/>- user_id"]
    
    F["RegistrationApprovedMail<br/>- Envoie email<br/>- Inclut password"]
    
    G["Email Template<br/>- registration_approved<br/>.blade.php"]
    
    A --> C
    B --> C
    C --> D
    D --> E
    F --> G
    B --> F
    
    style A fill:#bbdefb
    style B fill:#fff9c4
    style C fill:#e1bee7
    style D fill:#c8e6c9
    style E fill:#ffccbc
    style F fill:#b2dfdb
    style G fill:#f8bbd0
```

## Diagramme de la Structure Frontend

```mermaid
graph LR
    A["Register.vue<br/>- Input: name<br/>- Input: email<br/>- Button: Submit"]
    
    B["registrationApi.ts<br/>- createRegistrationRequest()<br/>- No password"]
    
    C["API Endpoint<br/>POST /api/registration/request"]
    
    D["Backend<br/>RegistrationRequestController"]
    
    E["Response<br/>- user.id<br/>- user.email"]
    
    F["UI Update<br/>- Show success message<br/>- Redirect to login"]
    
    A --> B
    B --> C
    C --> D
    D --> E
    E --> F
    
    style A fill:#bbdefb
    style B fill:#b2dfdb
    style C fill:#fff9c4
    style D fill:#ffccbc
    style E fill:#f8bbd0
    style F fill:#c8e6c9
```

## Diagramme de Sécurité

```mermaid
graph TD
    A["Point d'entrée: /register"]
    
    B["Validation Input<br/>- name: 2-100 chars<br/>- email: email format<br/>- unique check"]
    
    C["User Créé<br/>- password: aléatoire hash<br/>- is_active: false<br/>- email_verified_at: null"]
    
    D["RegistrationRequest Créée<br/>- En attente d'approbation"]
    
    E["Admin Authentifié?<br/>auth:sanctum middleware"]
    
    F["Admin Est Admin?<br/>role = 'admin' check"]
    
    G["Temp Password Généré<br/>- 12 caractères random<br/>- Bcrypt hashed<br/>- Non stocké en clair"]
    
    H["Email Validé<br/>email_verified_at = now()"]
    
    I["Account Activé<br/>is_active = true"]
    
    J["Wallet Créé<br/>- balance: 500"]
    
    K["Email Envoyé<br/>- Pas de password en clair<br/>- Lien sécurisé"]
    
    L["User Connecté<br/>- Email vérifié ✓<br/>- Compte actif ✓<br/>- Can trade ✓"]
    
    A --> B
    B --> C
    C --> D
    D --> E
    E --> F
    F --> G
    G --> H
    H --> I
    I --> J
    J --> K
    K --> L
    
    style A fill:#ffecb3
    style B fill:#fff9c4
    style E fill:#ffccbc
    style F fill:#ffccbc
    style G fill:#b2dfdb
    style H fill:#c8e6c9
    style I fill:#c8e6c9
    style K fill:#f8bbd0
    style L fill:#a5d6a7
```

## Diagramme API Endpoints

```mermaid
graph LR
    A["PUBLIC ENDPOINTS"]
    
    B["POST /api/registration/request<br/>✓ name, email, role?<br/>✗ password"]
    
    C["GET /api/registration/status/:id<br/>✓ Get request status"]
    
    D["ADMIN ENDPOINTS<br/>auth:sanctum + admin role"]
    
    E["GET /api/admin/registration-requests<br/>✓ List all requests"]
    
    F["POST /api/admin/registration-requests/:id/approve<br/>✓ Generate password<br/>✓ Send email"]
    
    G["POST /api/admin/registration-requests/:id/reject<br/>✓ Reject request"]
    
    A --> B
    A --> C
    D --> E
    D --> F
    D --> G
    
    style A fill:#bbdefb
    style B fill:#c8e6c9
    style C fill:#c8e6c9
    style D fill:#fff9c4
    style E fill:#ffccbc
    style F fill:#ffccbc
    style G fill:#ffccbc
```

## Diagramme Database Schema

```mermaid
graph LR
    A["users"]
    B["registration_requests"]
    C["wallets"]
    
    A --> |id| B
    A --> |id| C
    
    style A fill:#c8e6c9
    style B fill:#bbdefb  
    style C fill:#fff9c4
```

### users table
- id
- name
- email (unique)
- password (hashed)
- email_verified_at ← **AUTO SET AT APPROVAL**
- is_active ← **SET TO TRUE AT APPROVAL**
- role (client/admin)
- created_at, updated_at

### registration_requests table
- id
- user_id (FK)
- email
- role
- **temp_password** ← **NEW FIELD**
- is_approved
- is_rejected
- rejection_reason
- created_at, updated_at

### wallets table
- id
- user_id (FK)
- balance
- public_address
- private_address
- created_at updated_at

---

## Flux de Données - Request Complete

```mermaid
sequenceDiagram
    actor User
    participant Frontend
    participant Backend
    participant Database
    participant MailService
    actor Admin

    User->>Frontend: Va sur /register
    User->>Frontend: Entre name + email
    User->>Frontend: Clique Submit
    
    Frontend->>Backend: POST /api/registration/request
    activate Backend
    
    Backend->>Database: Create User (password=random_hash)
    Backend->>Database: Create RegistrationRequest
    
    Backend->>Frontend: Return success
    deactivate Backend
    
    Frontend->>User: Affiche: "En attente d'approbation"
    
    Admin->>Frontend: Va sur Admin Dashboard
    Admin->>Backend: GET /api/admin/registration-requests
    Frontend->>Admin: Affiche liste des demandes
    
    Admin->>Frontend: Clique Approver
    Admin->>Backend: POST /api/admin/registration-requests/1/approve
    
    activate Backend
    Backend->>Database: Generate tempPassword
    Backend->>Database: Hash password
    Backend->>Database: Set email_verified_at = now
    Backend->>Database: Set is_active = true
    Backend->>Database: Create Wallet
    Backend->>MailService: Send email with tempPassword
    Backend->>Frontend: Return success
    deactivate Backend
    
    MailService->>User: Email received!
    User->>Frontend: Reçoit email avec password
    
    User->>Frontend: Va sur /login
    User->>Frontend: Entre email + tempPassword
    User->>Backend: POST /auth/login
    
    activate Backend
    Backend->>Database: Verify email
    Backend->>Database: Verify password hash
    Backend->>Database: Check is_active = true
    Backend->>Frontend: Return JWT token
    deactivate Backend
    
    Frontend->>User: Connecté au Dashboard!
```

---

## Configuration Files Diagram

```
backend/
├── .env
│   ├── APP_URL=http://localhost:8000
│   └── FRONTEND_URL=http://localhost:5173
│
├── .env.example
│   ├── APP_URL=...
│   └── FRONTEND_URL=...
│
├── config/
│   ├── app.php
│   │   └── 'frontend_url' => env('FRONTEND_URL')
│   │
│   └── mail.php
│       └── MAIL_MAILER (log/smtp/sendmail...)
│
├── app/
│   ├── Http/Controllers/
│   │   ├── RegistrationRequestController.php
│   │   └── AdminController.php
│   │
│   ├── Mail/
│   │   └── RegistrationApprovedMail.php
│   │
│   └── Models/
│       └── RegistrationRequest.php
│
├── resources/views/
│   └── emails/
│       └── registration_approved.blade.php
│
└── database/migrations/
    ├── 2025_12_02_000001_create_registration_requests_table.php
    └── 2025_12_02_000002_add_temp_password_*.php

frontend/
├── src/
│   ├── services/
│   │   └── registrationApi.ts
│   │
│   └── views/
│       └── Register.vue
```

---

## Email Flow Diagram

```mermaid
graph TD
    A["Admin Approves<br/>/admin/registration-requests/:id/approve"]
    
    B["Backend Generates<br/>temp_password = Str::random12"]
    
    C["RegistrationApprovedMail<br/>Instantiated"]
    
    D["Mail::to email->send Mailable"]
    
    E{MAIL_MAILER?}
    
    F["log<br/>→ Logged to<br/>storage/logs/"]
    
    G["smtp<br/>→ Sent via<br/>SMTP Server"]
    
    H["sendmail<br/>→ Sent via<br/>Sendmail"]
    
    I["ses<br/>→ Sent via<br/>AWS SES"]
    
    J["User Receives<br/>Email with<br/>temp_password"]
    
    A --> B
    B --> C
    C --> D
    D --> E
    E -->|DEV| F
    E -->|PROD| G
    E -->|ALT| H
    E -->|ALT| I
    F --> J
    G --> J
    H --> J
    I --> J
    
    style A fill:#fff9c4
    style B fill:#b2dfdb
    style C fill:#f8bbd0
    style D fill:#c8e6c9
    style E fill:#ffccbc
    style J fill:#green
```

---

*Créé le: 2025-04-15*  
*Version: 1.0*  
*Status: Production Ready ✅*

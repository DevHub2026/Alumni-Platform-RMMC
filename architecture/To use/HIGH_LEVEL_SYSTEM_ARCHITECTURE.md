# High-Level System Architecture

Alumni Platform — Ramon Magsaysay Memorial College. Laravel 13 monolith with public Blade site and Filament admin.

---

## 1. Overall Ecosystem (Presentation)

```mermaid
flowchart TB
    subgraph clients [Clients]
        Browser[Web Browser]
    end

    subgraph app [Alumni Platform]
        Public[Public Alumni Site]
        Admin[Filament Admin /admin]
    end

    subgraph data [Data and External]
        DB[(Database)]
        Storage[Public File Storage]
        AI[OpenRouter API]
    end

    Browser --> Public
    Browser --> Admin
    Public --> DB
    Admin --> DB
    Public --> Storage
    Public --> AI
```

**Summary:** Single Laravel application serves alumni-facing pages and admin panel. Media on disk; chatbot calls external AI from server only.

---

## 2. Overall Ecosystem (Technical)

```mermaid
flowchart LR
    subgraph client [Client Tier]
        U1[Alumni User]
        U2[Admin User]
        G[Guest]
    end

    subgraph laravel [Laravel 13 Application]
        direction TB
        R[routes/web.php + auth.php]
        MW[Middleware: auth guest verified]
        HC[HTTP Controllers]
        FR[Filament Resources]
        M[Eloquent Models]
        N[Notifications]
        R --> MW --> HC
        R --> MW --> FR
        HC --> M
        FR --> M
        HC --> N
    end

    subgraph infra [Infrastructure]
        DB[(SQLite / MySQL)]
        FS[storage/app/public]
        OR[OpenRouter Chat API]
    end

    G --> R
    U1 --> R
    U2 --> FR
    M --> DB
    HC --> FS
    HC --> OR
```

**Summary:** No separate API app or microservices. Filament shares models and database with public controllers.

---

## 3. Frontend / Backend / Database (Presentation)

```mermaid
flowchart TB
    UI[Blade + Tailwind + Alpine.js]
    Laravel[Laravel MVC]
    DB[(Relational DB)]

    UI <-->|HTTP HTML JSON| Laravel
    Laravel <-->|Eloquent| DB
```

---

## 4. Frontend / Backend / Database (Technical)

```mermaid
flowchart TB
    subgraph presentation [Presentation Layer]
        Vite[Vite Build]
        Blade[Blade Views]
        Alpine[Alpine.js]
        Vite --> Blade
        Blade --> Alpine
    end

    subgraph application [Application Layer]
        C[Controllers]
        Req[Form Requests]
        Notif[Notification Classes]
        C --> Req
        C --> Notif
    end

    subgraph domain [Domain Layer]
        E[Eloquent Models]
    end

    subgraph persistence [Persistence]
        SQL[(users profiles posts events etc)]
        Disk[Public Disk Uploads]
    end

    Browser --> Blade
    Alpine -->|fetch JSON| C
    C --> E
    E --> SQL
    C --> Disk
```

---

## 5. Admin and Alumni Interaction (Presentation)

```mermaid
flowchart LR
    Alumni[Alumni] -->|read write own content| Site[Public Site]
    Admin[Admin] -->|manage all content| Panel[Admin Panel]
    Site --> DB[(Shared Database)]
    Panel --> DB
```

---

## 6. Admin and Alumni Interaction (Technical)

```mermaid
flowchart TB
    subgraph alumni_actions [Alumni Capabilities]
        A1[Profile Directory]
        A2[Posts Comments Reactions]
        A3[Event Register]
        A4[Gallery Upload]
        A5[Chatbot]
    end

    subgraph admin_actions [Admin Capabilities]
        B1[Users Verify Suspend]
        B2[Announcements Events]
        B3[Moderate Posts Flags]
        B4[Manage Galleries]
    end

    subgraph shared [Shared Data]
        DB[(Database)]
    end

    A1 --> DB
    A2 --> DB
    A3 --> DB
    A4 --> DB
    A5 --> DB
    B1 --> DB
    B2 --> DB
    B3 --> DB
    B4 --> DB
```

**Note:** Alumni cannot access Filament; `User.canAccessPanel` requires `role = admin`.

---

## 7. Notification and Chatbot Integration (Presentation)

```mermaid
flowchart LR
    Post[Post Comment] --> Notif[DB Notification]
    Notif --> Bell[Navbar Bell]
    User[Alumni] --> Chat[Chatbot UI]
    Chat --> API[OpenRouter]
```

---

## 8. Notification and Chatbot Integration (Technical)

```mermaid
flowchart TB
    subgraph comment_flow [Comment Notification]
        PC[PostController comment]
        PN[PostCommentNotification]
        NT[notifications table]
        NC[NotificationController JSON]
        PC --> PN --> NT
        NT --> NC
    end

    subgraph chatbot_flow [Chatbot]
        CB[chatbot.blade.php Alpine]
        CC[ChatbotController ask]
        CFG[config services gemini]
        OR[OpenRouter API]
        CB -->|POST auth| CC
        CC --> CFG
        CC --> OR
        CC -->|JSON reply| CB
    end
```

---

## 9. Deployment Overview (Presentation)

```mermaid
flowchart TB
    User[Users] --> Web[Web Server Nginx]
    Web --> PHP[PHP-FPM Laravel]
    PHP --> DB[(Database)]
    PHP --> Store[File Storage]
```

---

## 10. Deployment Overview (Technical)

```mermaid
flowchart TB
    subgraph prod [Production Environment]
        CDN[Optional CDN]
        LB[Optional Load Balancer]
        Nginx[Nginx]
        FPM[PHP 8.3 FPM]
        App[Laravel App]
        MySQL[(MySQL)]
        Redis[(Redis Sessions Cache)]
        S3[Optional S3 Media]
    end

    Internet --> CDN --> LB --> Nginx --> FPM --> App
    App --> MySQL
    App --> Redis
    App --> S3
```

**Dev default:** SQLite + local `storage/app/public` per `.env.example`.

---

## Component Inventory (Reference)

| Component | Path / Technology |
|-----------|-------------------|
| Public routes | `routes/web.php` |
| Auth routes | `routes/auth.php` |
| Admin panel | `app/Providers/Filament/AdminPanelProvider.php` |
| Models | `app/Models/*` (10 models) |
| Primary layout | `resources/views/layouts/app.blade.php` |

See [LOW_LEVEL_SYSTEM_ARCHITECTURE.md](./LOW_LEVEL_SYSTEM_ARCHITECTURE.md) for request lifecycle detail.

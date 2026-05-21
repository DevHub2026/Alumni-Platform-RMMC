# Authentication Flow

Based on Laravel Breeze (`routes/auth.php`) and custom suspension logic in `AuthenticatedSessionController`.

---

## 1. Registration (Presentation)

```mermaid
flowchart LR
    A[Visit Register] --> B[Submit Form]
    B --> C[User Created role alumni]
    C --> D[Auto Login]
    D --> E[Redirect Dashboard]
```

---

## 2. Registration (Technical)

```mermaid
sequenceDiagram
    participant U as User
    participant R as RegisteredUserController
    participant DB as users table

    U->>R: GET /register
    R-->>U: auth.register view
    U->>R: POST name email password
    R->>R: validate unique email
    R->>DB: User create password hashed
    Note over DB: role defaults alumni
    R->>R: event Registered
    R->>R: Auth login
    R-->>U: redirect dashboard
```

**No alumni_profiles row** created at registration.

---

## 3. Login (Presentation)

```mermaid
flowchart TB
    L[Login] --> OK{Valid credentials}
    OK -->|no| E[Error]
    OK -->|yes| S{Suspended}
    S -->|yes| B[Blocked with reason]
    S -->|no| H[Home session active]
```

---

## 4. Login (Technical)

```mermaid
sequenceDiagram
    participant U as User
    participant LR as LoginRequest
    participant ASC as AuthenticatedSessionController
    participant Auth as Auth Guard

    U->>ASC: POST login
    ASC->>LR: validate
    LR->>LR: rate limit check
    LR->>Auth: attempt email password
    alt failed
        Auth-->>U: validation error
    else success
        ASC->>ASC: check is_suspended
        alt suspended
            ASC->>Auth: logout
            ASC-->>U: error with suspension_reason
        else active
            ASC->>ASC: session regenerate
            ASC-->>U: redirect intended dashboard
        end
    end
```

---

## 5. Middleware Protection (Presentation)

```mermaid
flowchart TB
    Req[Request] --> G{Route group}
    G -->|public| Allow[Allow]
    G -->|auth| Check{Logged in}
    Check -->|no| Login[Redirect login]
    Check -->|yes| Allow
    G -->|verified| Email{Email verified}
    Email -->|no| Verify[Prompt verify]
```

---

## 6. Middleware Protection (Technical)

```mermaid
flowchart TB
    subgraph routes [Route Middleware Usage]
        P1[Public: home announcements events]
        P2[auth: profile posts store events register]
        P3[auth: notifications chatbot]
        P4[auth verified: dashboard only]
        P5[guest: login register]
    end

    P2 --> M1[auth middleware]
    P3 --> M1
    P4 --> M2[auth + verified]
    P5 --> M3[guest middleware]
```

**Gap documented:** `GET /posts/create` has no `auth` middleware (controller checks user).

---

## 7. Session Handling (Presentation)

```mermaid
flowchart LR
    Login[Login Success] --> Regen[Regenerate Session ID]
    Regen --> Store[(sessions table)]
    Logout[Logout] --> Flush[Invalidate Session]
```

---

## 8. Session Handling (Technical)

```mermaid
flowchart TB
    subgraph config [Session Config]
        DRV[SESSION_DRIVER database]
        TBL[sessions table]
    end

    Login[POST login success] --> RG[session regenerate]
    RG --> CK[session cookie]
    CK --> TBL

    Logout[POST logout] --> INV[session invalidate]
    INV --> TOK[regenerateToken]

    subgraph filament_sess [Filament Admin]
        FS[Same session driver]
        FA[Filament Authenticate middleware]
    end
```

---

## 9. Alumni Verification (Presentation)

```mermaid
flowchart TB
    Edit[Complete Profile] --> Check{Core fields filled}
    Check -->|yes| V[Verified Alumni]
    V --> Post[Can Create Posts]
```

---

## 10. Alumni Verification (Technical)

```mermaid
flowchart TB
    PUT[PUT /profile] --> VAL[AlumniProfileController validate]
    VAL --> UOC[AlumniProfile updateOrCreate]
    UOC --> COND{course AND graduation_year AND student_id}
    COND -->|yes| UV[users.is_verified true]
    COND -->|no| Skip[no auto verify]
    UV --> PC[PostController allows store]
    Admin[Filament UserResource] -->|manual toggle| UV
```

**Distinct from** `email_verified_at` (Breeze email verification).

---

## 11. Suspension Workflow (Presentation)

```mermaid
flowchart LR
    Admin[Admin Suspend] --> DB[(is_suspended true)]
    User[User Login] --> Block[Access Denied]
```

---

## 12. Suspension Workflow (Technical)

```mermaid
sequenceDiagram
    participant A as Admin
    participant F as Filament UserResource
    participant DB as users
    participant U as Alumni
    participant ASC as AuthenticatedSessionController

    A->>F: Suspend action + reason
    F->>DB: is_suspended true suspension_reason

    U->>ASC: POST login valid password
    ASC->>DB: load user
    ASC->>ASC: detect is_suspended
    ASC->>ASC: logout invalidate session
    ASC-->>U: error email field with reason

    A->>F: Unsuspend
    F->>DB: is_suspended false reason null
```

---

## 13. Role Authorization (Presentation)

```mermaid
flowchart TB
    User[User] --> R{role}
    R -->|alumni| Site[Public Site Features]
    R -->|admin| Site
    R -->|admin| Admin[Filament /admin]
```

---

## 14. Role Authorization (Technical)

```mermaid
flowchart TB
    subgraph public_auth [Public Authorization]
        AuthMW[auth middleware]
        Own[ownership user_id checks]
        Ver[is_verified for posts]
        Pub[is_published abort 404]
    end

    subgraph filament_auth [Filament Authorization]
        CAP[User canAccessPanel]
        CAP -->|role admin| Allow[Panel access]
        CAP -->|role alumni| Deny[403 / redirect]
    end

    AuthMW --> Own
    AuthMW --> Ver
```

**No Laravel Policies** — inline checks only.

---

## Auth File Reference

| Flow | Primary file |
|------|----------------|
| Register | `RegisteredUserController.php` |
| Login | `AuthenticatedSessionController.php`, `LoginRequest.php` |
| Panel gate | `User.php` `canAccessPanel()` |
| Profile verify | `AlumniProfileController.php` |
| Suspend | `UserResource.php` |

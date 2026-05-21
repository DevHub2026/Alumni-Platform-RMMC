# User Role and Permission Flow

Two roles (`admin`, `alumni`) plus flags (`is_verified`, `is_suspended`). No moderator role; no Policy classes.

---

## 1. Role Model (Presentation)

```mermaid
flowchart TB
    U[User Account] --> A[admin]
    U --> B[alumni default]
    B --> V[Optional is_verified]
    B --> S[Optional is_suspended]
```

---

## 2. Role Model (Technical)

```mermaid
erDiagram
    users {
        enum role "admin alumni"
        boolean is_verified
        boolean is_suspended
        text suspension_reason
        timestamp email_verified_at
    }
```

| Field | Applies to | Effect |
|-------|------------|--------|
| `role` | all | admin → Filament; alumni → default |
| `is_verified` | alumni | post create; gallery with registration |
| `is_suspended` | all | login blocked |
| `email_verified_at` | all | dashboard route only |

---

## 3. Permission Matrix (Presentation)

```mermaid
flowchart TB
    subgraph guest [Guest]
        G1[View published content]
        G2[Search directory]
    end

    subgraph alumni [Alumni Auth]
        A1[Profile Events Register]
        A2[Comment React Flag]
        A3[Post if verified]
    end

    subgraph admin [Admin]
        D1[All alumni actions]
        D2[Filament full access]
    end
```

---

## 4. Permission Matrix (Technical)

| Action | guest | alumni | verified | admin |
|--------|:-----:|:------:|:--------:|:-----:|
| View announcements/events/posts | ✓ | ✓ | ✓ | ✓ |
| Alumni directory | ✓ | ✓ | ✓ | ✓ |
| Edit own profile | — | ✓ | ✓ | ✓ |
| Create post | — | — | ✓ | ✓ |
| Comment/react/flag | — | ✓ | ✓ | ✓ |
| Event register | — | ✓ | ✓ | ✓ |
| Gallery upload | — | — | ✓* | ✓ |
| Chatbot | — | ✓ | ✓ | ✓ |
| Filament | — | — | — | ✓ |

\*Verified + confirmed `event_registrations` for that event.

---

## 5. Use Case — Alumni (Presentation)

```mermaid
flowchart LR
    Alumni((Alumni)) --> UC1[Manage Profile]
    Alumni --> UC2[Browse Directory]
    Alumni --> UC3[Engage Posts]
    Alumni --> UC4[Register Events]
    Alumni --> UC5[Upload Gallery]
```

---

## 6. Use Case — Admin (Presentation)

```mermaid
flowchart LR
    Admin((Admin)) --> UC1[Manage Users]
    Admin --> UC2[Publish Content]
    Admin --> UC3[Moderate Posts]
    Admin --> UC4[View Stats]
```

---

## 7. Verification Permission Flow (Presentation)

```mermaid
stateDiagram-v2
    [*] --> Unverified: register
    Unverified --> Verified: profile complete
    Verified --> Unverified: admin toggle off
    Verified --> Suspended: admin suspend
    Suspended --> Verified: admin unsuspend
```

---

## 8. Verification Permission Flow (Technical)

```mermaid
flowchart TB
    Start[Alumni registered] --> IV[is_verified false]

    IV --> Edit[profile update]
    Edit --> Auto{course year student_id}
    Auto -->|yes| IV2[is_verified true]
    Auto -->|no| IV

    IV2 --> Gate1[PostController store allowed]
    IV2 --> Gate2[Gallery upload if registered]

    Filament[Admin Toggle] --> IV2
    Filament --> IV

    IV --> UI[posts index shows Complete Profile CTA]
```

---

## 9. Post Ownership Flow (Technical)

```mermaid
flowchart TB
    Edit[posts.edit] --> O1{post.user_id == Auth id}
    O1 -->|no| F403[403]
    O1 -->|yes| Form[edit view]

    Flag[posts.flag] --> O2{post.user_id != Auth id}
    O2 -->|no| Err[cannot flag own]
    O2 -->|yes| Create[PostFlag create]
```

---

## 10. Gallery Permission Flow (Technical)

```mermaid
flowchart TB
    Upload[gallery.store] --> P1{event is_published}
    P1 -->|no| N404[404]
    P1 -->|yes| P2{admin OR verified+registered}
    P2 -->|no| F403[403]
    P2 -->|yes| Save[store files Gallery create]

    Destroy[gallery.destroy] --> P3{owner OR admin}
    P3 -->|no| F403
    P3 -->|yes| Delete[delete file + row]
```

---

## 11. Filament Access Flow (Presentation)

```mermaid
flowchart TB
    Login[Admin Login /admin] --> Check{role admin}
    Check -->|yes| Panel[Dashboard]
    Check -->|no| Deny[Access Denied]
```

---

## 12. Filament Access Flow (Technical)

```mermaid
sequenceDiagram
    participant U as User
    participant F as Filament Panel
    participant M as User Model

    U->>F: GET /admin
    F->>M: canAccessPanel
    alt role is admin
        M-->>F: true
        F-->>U: Dashboard
    else role is alumni
        M-->>F: false
        F-->>U: Forbidden
    end
```

Implementation: `User::canAccessPanel()` returns `$this->role === 'admin'`.

---

## Enforcement Locations

| Rule | Where enforced |
|------|----------------|
| Login required | `Route::middleware('auth')` |
| Verified posts | `PostController` |
| Published content | `abort_if` in controllers |
| Admin panel | `FilamentUser` contract |
| Suspend | `AuthenticatedSessionController` |

Future improvement: Laravel Policies per model.

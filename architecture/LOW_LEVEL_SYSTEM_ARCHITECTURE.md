# Low-Level System Architecture

Internal structure based on actual codebase. **No `app/Services` layer** — controllers and Filament resources call models directly.

---

## 1. Layer Interaction (Presentation)

```mermaid
flowchart LR
    Route[Routes] --> Controller[Controllers]
    Controller --> Model[Models]
    Model --> DB[(Database)]
    Controller --> View[Blade Views]
```

---

## 2. Layer Interaction (Technical)

```mermaid
flowchart TB
    subgraph http [HTTP Entry]
        R[routes/web.php]
        A[routes/auth.php]
    end

    subgraph middleware [Route Middleware]
        auth[auth]
        guest[guest]
        verified[verified]
    end

    subgraph controllers [HTTP Controllers]
        HC[Feature Controllers]
        AC[Auth Controllers]
    end

    subgraph filament [Filament Admin]
        AP[AdminPanelProvider]
        Res[8 Resources]
        Wgt[2 Widgets]
    end

    subgraph domain [Domain]
        EM[Eloquent Models]
    end

    subgraph infra [Infrastructure]
        DB[(Database)]
        FS[Filesystem public disk]
        HTTP[Http Client OpenRouter]
    end

    R --> auth --> HC
    A --> guest --> AC
    A --> auth --> AC
    HC --> EM
    AC --> EM
    AP --> Res --> EM
    Res --> Wgt --> EM
    EM --> DB
    HC --> FS
    HC --> HTTP
    HC --> View[Blade]
```

---

## 3. Controller–Model Interaction (Presentation)

```mermaid
flowchart TB
    C[Controller] --> V[Validate Input]
    V --> M[Model Query / Save]
    M --> R[View or JSON Response]
```

**Note:** No service classes between controller and model.

---

## 4. Controller–Model Interaction (Technical)

```mermaid
flowchart TB
    subgraph post_example [Example PostController store]
        IN[Request] --> VAL[validate rules]
        VAL --> AUTH{is_verified}
        AUTH -->|yes| CREATE[Post::create]
        AUTH -->|no| REDIR[redirect error]
        CREATE --> DISK[optional Storage store]
        DISK --> REDIR2[redirect posts.index]
    end
```

---

## 5. Middleware Flow (Presentation)

```mermaid
flowchart LR
    Req[Request] --> Web[Web Middleware Stack]
    Web --> RouteMW[Route Middleware]
    RouteMW --> Action[Controller Action]
```

---

## 6. Middleware Flow (Technical)

```mermaid
flowchart TB
    Start[HTTP Request] --> Kernel[Laravel HTTP Kernel]
    Kernel --> WebGroup[Web Middleware Group]
    WebGroup --> Session[StartSession]
    Session --> CSRF[VerifyCsrfToken]
    CSRF --> Router[Route Matching]

    Router --> G1{guest group}
    Router --> G2{auth group}
    Router --> G3{auth + verified}

    G1 --> AuthPages[Login Register Reset]
    G2 --> Protected[Profile Posts Events etc]
    G3 --> Dashboard[dashboard view]

    Protected --> Action[Controller]
    Dashboard --> Action
```

**Custom middleware:** None in `app/Http/Middleware`. Configuration in `bootstrap/app.php` is empty.

---

## 7. Route Handling (Presentation)

```mermaid
flowchart TB
    URL[URL] --> WebRoutes[web.php]
    URL --> AuthRoutes[auth.php]
    URL --> AdminRoutes[Filament /admin]
```

---

## 8. Route Handling (Technical)

```mermaid
flowchart TB
    subgraph public [Public Feature Routes]
        H[HomeController]
        AP[AlumniProfileController]
        AN[AnnouncementController]
        EV[EventController]
        GA[GalleryController]
        PO[PostController]
        SE[SearchController]
        NO[NotificationController]
        CH[ChatbotController]
    end

    subgraph auth_r [Auth Routes Breeze]
        AS[AuthenticatedSessionController]
        RU[RegisteredUserController]
        Others[Password Email Verify Controllers]
    end

    web[routes/web.php] --> public
    web --> auth_r
    require[require auth.php] --> auth_r

    filament[Filament] --> Resources[User Post Event etc]
```

---

## 9. Request Lifecycle (Presentation)

```mermaid
sequenceDiagram
    participant B as Browser
    participant L as Laravel
    participant D as Database

    B->>L: HTTP Request
    L->>L: Middleware
    L->>D: Query
    D-->>L: Data
    L-->>B: HTML or JSON
```

---

## 10. Request Lifecycle (Technical)

```mermaid
sequenceDiagram
    participant C as Client
    participant I as index.php
    participant K as Kernel
    participant R as Router
    participant M as Middleware
    participant Ctrl as Controller
    participant E as Eloquent
    participant DB as Database

    C->>I: Request
    I->>K: Handle
    K->>R: Match route
    R->>M: Pipeline
    M->>Ctrl: Dispatch
    Ctrl->>E: Query / persist
    E->>DB: SQL
    DB-->>E: Rows
    E-->>Ctrl: Models
    Ctrl-->>C: Response
```

---

## 11. Authentication Processing (Presentation)

```mermaid
flowchart TB
    Login[POST login] --> Auth{Credentials OK}
    Auth -->|no| Fail[Error]
    Auth -->|yes| Suspend{Suspended}
    Suspend -->|yes| Logout[Logout + message]
    Suspend -->|no| Session[Active Session]
```

---

## 12. Authentication Processing (Technical)

```mermaid
flowchart TB
    POST[POST login] --> LR[LoginRequest]
    LR --> Rate[RateLimiter 5 attempts]
    Rate --> Attempt[Auth attempt]
    Attempt -->|fail| Err[ValidationException]
    Attempt -->|ok| ASC[AuthenticatedSessionController]
    ASC --> Check[is_suspended]
    Check -->|true| Inv[logout invalidate session]
    Inv --> Err2[email error with reason]
    Check -->|false| Regen[session regenerate]
    Regen --> Dash[redirect dashboard]
```

---

## 13. Internal Feature Interactions (Presentation)

```mermaid
flowchart TB
    Profile[Profile Complete] --> Verify[is_verified]
    Verify --> Posts[Create Posts]
    EventReg[Event Register] --> Gallery[Gallery Upload]
    Comment[Comment] --> Notify[Notification]
```

---

## 14. Internal Feature Interactions (Technical)

```mermaid
flowchart TB
    subgraph profile [AlumniProfileController update]
        P1[updateOrCreate AlumniProfile] --> P2{course year student_id}
        P2 -->|all set| P3[User is_verified true]
    end

    subgraph posts [PostController]
        P4{is_verified} -->|yes| P5[Post create]
        P6[comment] --> P7[PostCommentNotification]
    end

    subgraph events [Event + Gallery]
        E1[EventRegistration confirmed] --> E2{is_verified alumni}
        E2 -->|yes| E3[GalleryController store allowed]
    end

    subgraph moderation [Flags]
        F1[PostFlag create] --> F2{count >= 3}
        F2 -->|yes| F3[post is_flagged true]
        F3 --> F4[Filament review]
    end
```

---

## Key Files

| Concern | Location |
|---------|----------|
| Bootstrap | `bootstrap/app.php` |
| Routes | `routes/web.php`, `routes/auth.php` |
| Auth login | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` |
| Filament | `app/Providers/Filament/AdminPanelProvider.php` |

No `app/Policies` — authorization inline in controllers.

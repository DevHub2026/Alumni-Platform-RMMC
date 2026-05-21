# Filament Admin Architecture

Panel provider: `app/Providers/Filament/AdminPanelProvider.php`. Path: `/admin`. Panel ID: `admin`.

---

## 1. Admin Structure (Presentation)

```mermaid
flowchart TB
    Login[/admin Login] --> Dash[Dashboard]
    Dash --> G1[Users and Profiles]
    Dash --> G2[Content]
    Dash --> G3[Community]
    Dash --> G4[Moderation]
```

---

## 2. Admin Structure (Technical)

```mermaid
flowchart TB
    subgraph panel [Filament Panel admin]
        AP[AdminPanelProvider]
        DW[Dashboard Page]
        W1[StatsOverview Widget]
        W2[AlumniGrowthChart Widget]
        W3[AccountWidget FilamentInfoWidget]
    end

    subgraph nav [Navigation Groups]
        UP[Users and Profiles]
        CT[Content]
        CM[Community]
        MD[Moderation]
    end

    AP --> DW
    DW --> W1
    DW --> W2
    DW --> W3

    UP --> UR[UserResource]
    UP --> AR[AlumniProfileResource]
    CT --> ANR[AnnouncementResource]
    CT --> ER[EventResource]
    CT --> ERR[EventRegistrationResource]
    CT --> GR[GalleryResource]
    CM --> PR[PostResource]
    MD --> PFR[PostFlagResource]
```

---

## 3. Component Diagram (Presentation)

```mermaid
flowchart LR
    Filament[Filament UI] --> Livewire[Livewire Pages]
    Livewire --> Eloquent[Eloquent Models]
    Eloquent --> DB[(Database)]
```

---

## 4. Component Diagram (Technical)

```mermaid
flowchart TB
    subgraph filament_stack [Filament Stack]
        Res[Resource Class]
        Pages[Page Classes List Create Edit View]
        Form[Schema form components]
        Table[Table columns filters actions]
        Res --> Pages
        Res --> Form
        Res --> Table
    end

    subgraph laravel [Laravel Core]
        Auth[Authenticate Middleware]
        User[User canAccessPanel]
        Models[Eloquent]
    end

    Browser --> Auth --> User --> Pages
    Pages --> Form --> Models
    Pages --> Table --> Models
    Models --> DB[(Database)]
```

---

## 5. Moderation Workflow (Presentation)

```mermaid
flowchart TB
    Flag[Community Flags] --> Queue[Flagged Posts List]
    Queue --> Act{Admin Action}
    Act --> Approve[Keep Visible]
    Act --> Hide[Hide]
    Act --> Remove[Remove]
```

---

## 6. Moderation Workflow (Technical)

```mermaid
flowchart TB
    U[User POST flag] --> PF[post_flags row]
    PF --> C{count >= 3}
    C -->|yes| IF[is_flagged on post]

    IF --> PFR[PostFlagResource list]
    IF --> PR[PostResource sorted by is_flagged]

    PFR --> A1[Approve Post]
    PFR --> A2[Remove Post]
    A1 --> V[status visible is_flagged false]
    A1 --> D1[delete flag]
    A2 --> R[status removed]
    A2 --> D1

    PR --> A3[approve hide remove table actions]
```

---

## 7. Content Management Flow (Presentation)

```mermaid
flowchart LR
    Admin[Admin] --> Ann[Announcements]
    Admin --> Ev[Events]
    Admin --> Gal[Galleries]
```

---

## 8. Content Management Flow (Technical)

```mermaid
flowchart TB
    subgraph publish [Publish Pattern]
        CR[Filament Create/Edit]
        TOG[is_published toggle]
        TOG -->|true| Pub[Public controllers show]
        TOG -->|false| Hid[404 on public]
    end

    AN[AnnouncementResource] --> publish
    EV[EventResource] --> publish

    subgraph registrations [Registrations]
        ERR[EventRegistrationResource]
        ERR --> ST[status enum edit]
    end

    GR[GalleryResource] --> IMG[image_path CRUD]
```

---

## 9. Verification Management (Presentation)

```mermaid
flowchart TB
    Admin[User Resource] --> V[Toggle is_verified]
    Admin --> S[Suspend / Unsuspend]
```

---

## 10. Verification Management (Technical)

```mermaid
flowchart TB
    UR[UserResource form] --> TV[Toggle is_verified]
    UR --> TS[Toggle is_suspended]
    UR --> SR[suspension_reason textarea]

    UR --> ACT1[Suspend action modal reason]
    ACT1 --> SU[is_suspended true]

    UR --> ACT2[Unsuspend action]
    ACT2 --> CL[is_suspended false reason null]

    TV --> PC[PostController create gate]
    TV --> GC[GalleryController upload gate]
```

Visible only for alumni: suspend action when `role === alumni` and not suspended.

---

## 11. Resource–Model Map (Reference)

| Resource | Model | Pages |
|----------|-------|-------|
| UserResource | User | List, Create, Edit |
| AlumniProfileResource | AlumniProfile | List, Create, Edit |
| AnnouncementResource | Announcement | List, Create, Edit |
| EventResource | Event | List, Create, Edit |
| EventRegistrationResource | EventRegistration | List, Edit |
| GalleryResource | Gallery | List, Create, Edit |
| PostResource | Post | List, View, Edit |
| PostFlagResource | PostFlag | List |

---

## 12. Dashboard Data Flow (Presentation)

```mermaid
flowchart LR
    DB[(Database)] --> Stats[Stats Widgets]
    Stats --> UI[Admin Dashboard]
```

---

## 13. Dashboard Data Flow (Technical)

```mermaid
flowchart TB
    W1[StatsOverview] --> Q1[count alumni verified posts flagged]
    W1 --> Q2[upcoming events registrations announcements]
    W2[AlumniGrowthChart] --> Q3[alumni created_at by month 6 months]
    Q1 --> DB[(Database)]
    Q2 --> DB
    Q3 --> DB
```

---

## Middleware (Filament)

EncryptCookies → StartSession → CSRF → SubstituteBindings → Filament events → **Authenticate**

Configured in `AdminPanelProvider::panel()->middleware()` and `authMiddleware()`.

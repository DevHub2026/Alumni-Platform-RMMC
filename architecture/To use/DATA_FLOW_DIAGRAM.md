# Data Flow Diagram (DFD)

Level 0 and Level 1 DFDs based on actual data stores and processes. Notation: processes (rounded), data stores (open rectangle), external entities (square).

---

## 1. Context Diagram — Level 0 (Presentation)

```mermaid
flowchart LR
    Alumni[Alumni] <-->|profiles posts events| System[Alumni Platform]
    Admin[Admin] <-->|manage moderate| System
    Guest[Guest] <-->|browse| System
    AI[OpenRouter] <-->|chat| System
    System <-->|persist| DB[(Database)]
    System <-->|files| Store[File Storage]
```

---

## 2. Context Diagram — Level 0 (Technical)

```mermaid
flowchart TB
    E1[Guest]
    E2[Alumni]
    E3[Admin]
    E4[OpenRouter API]

    P0[Alumni Platform Laravel]

    D1[(D1 Database)]
    D2[(D2 Public Files)]

    E1 -->|browse queries| P0
    E2 -->|auth CRUD engage| P0
    E3 -->|admin CRUD| P0
    P0 -->|chat completion| E4
    P0 <-->|SQL| D1
    P0 <-->|read write images| D2
    P0 -->|HTML JSON| E1
    P0 -->|HTML JSON| E2
    P0 -->|HTML| E3
```

---

## 3. Level 1 DFD — Public Site (Presentation)

```mermaid
flowchart TB
    User[User] --> P1[Browse Content]
    User --> P2[Manage Profile]
    User --> P3[Social Actions]
    P1 --> DB[(DB)]
    P2 --> DB
    P3 --> DB
```

---

## 4. Level 1 DFD — Public Site (Technical)

```mermaid
flowchart TB
    E2[Alumni]
    E1[Guest]

    subgraph processes [Processes]
        P1[1.0 Browse Published Content]
        P2[2.0 Auth Register Login]
        P3[3.0 Profile Update Verify]
        P4[4.0 Post CRUD]
        P5[5.0 Comment React Flag]
        P6[6.0 Event Register]
        P7[7.0 Gallery Upload]
        P8[8.0 Search]
        P9[9.0 Notifications Read]
        P10[10.0 Chatbot Ask]
    end

    D1[(D1 users alumni_profiles)]
    D2[(D2 posts comments reactions flags)]
    D3[(D3 events registrations)]
    D4[(D4 announcements)]
    D5[(D5 galleries)]
    D6[(D6 notifications)]
    D7[(D7 files)]

    E1 --> P1
    E2 --> P1
    P1 --> D2
    P1 --> D3
    P1 --> D4

    E2 --> P2
    P2 --> D1
    P2 --> P3
    P3 --> D1

    E2 --> P4
    P4 --> D2
    P4 --> D7

    E2 --> P5
    P5 --> D2
    P5 --> D6

    E2 --> P6
    P6 --> D3

    E2 --> P7
    P7 --> D5
    P7 --> D7

    E1 --> P8
    E2 --> P8
    P8 --> D1
    P8 --> D2
    P8 --> D3
    P8 --> D4

    E2 --> P9
    P9 --> D6

    E2 --> P10
    P10 --> E4[OpenRouter]
```

---

## 5. Level 1 DFD — Admin (Presentation)

```mermaid
flowchart TB
    Admin[Admin] --> FA[Filament Admin]
    FA --> DB[(Database)]
```

---

## 6. Level 1 DFD — Admin (Technical)

```mermaid
flowchart TB
    E3[Admin]

    subgraph admin_proc [Admin Processes]
        A1[11.0 User Management]
        A2[12.0 Content Publish]
        A3[13.0 Post Moderation]
        A4[14.0 Dashboard Stats]
    end

    D1[(D1 users profiles)]
    D2[(D2 posts flags)]
    D3[(D3 events registrations)]
    D4[(D4 announcements)]
    D5[(D5 galleries)]

    E3 --> A1
    A1 --> D1

    E3 --> A2
    A2 --> D3
    A2 --> D4
    A2 --> D5

    E3 --> A3
    A3 --> D2

    E3 --> A4
    A4 --> D1
    A4 --> D2
    A4 --> D3
    A4 --> D4
```

---

## 7. Post Data Flow (Focused — Presentation)

```mermaid
flowchart LR
    Input[Post Form] --> Proc[PostController]
    Proc --> Posts[(posts)]
    Proc --> Files[(images)]
```

---

## 8. Post Data Flow (Focused — Technical)

```mermaid
flowchart TB
    IN[Input title body category image] --> VAL[Validation]
    VAL --> AUTH{is_verified}
    AUTH -->|yes| W1[Write posts row]
    AUTH -->|no| X[Reject redirect]
    IN -->|file| W2[Write public disk]
    W2 --> W1
    W1 --> OUT[Feed visible posts]
```

---

## 9. Notification Data Flow (Focused)

```mermaid
flowchart LR
    CM[post_comments insert] --> NT[Notification class]
    NT --> NS[notifications JSON]
    NS --> RD[NotificationController read]
    RD --> UI[Navbar Bell]
```

---

## 10. Chatbot Data Flow (Focused)

```mermaid
flowchart LR
    MSG[message] --> CC[ChatbotController]
    CFG[env GEMINI key] --> CC
    CC --> API[OpenRouter]
    API --> REP[reply JSON]
    REP --> UI[chatbot UI]
```

**No chat history persisted** in database.

---

## Data Store Summary

| Store | Tables / path | Primary consumers |
|-------|---------------|-------------------|
| D1 Users | users, alumni_profiles | Auth, profile, directory |
| D2 Social | posts, comments, reactions, flags | Feed, moderation |
| D3 Events | events, event_registrations | Events, gallery gate |
| D4 News | announcements | Home, announcements |
| D5 Media | galleries + disk paths | Gallery views |
| D6 Alerts | notifications | Bell, index |
| D7 Files | storage/app/public | All uploads |

See [DATABASE_ERD.md](./DATABASE_ERD.md) for schema detail.

# Events and Registration Flow

Controllers: `EventController` (public), `EventResource` / `EventRegistrationResource` (Filament).

---

## 1. Event Lifecycle (Presentation)

```mermaid
flowchart LR
    Admin[Admin Creates Event] --> Pub[Publish]
    Pub --> List[Public Events Page]
    List --> Reg[Alumni Registers]
    Reg --> Gal[Gallery Upload Eligible]
```

---

## 2. Event Lifecycle (Technical)

```mermaid
stateDiagram-v2
    [*] --> Draft: admin create is_published false
    Draft --> Published: admin publish
    Published --> Past: event_date passed
    Published --> Full: slots exceeded
    Full --> Published: unregister Frees slot
```

---

## 3. Public Browse Flow (Presentation)

```mermaid
flowchart TB
    Visit[GET /events] --> Up[Upcoming List]
    Visit --> Pa[Past List]
    Click[Open Event] --> Show[Event Detail]
```

---

## 4. Public Browse Flow (Technical)

```mermaid
flowchart TB
    IDX[EventController index] --> Q1[is_published true AND date >= now]
    IDX --> Q2[is_published true AND date < now]
    Q1 --> V1[events.index upcoming]
    Q2 --> V2[events.index past]

    SHO[EventController show] --> A1{is_published}
    A1 -->|false| N404[404]
    A1 -->|true| Calc[isRegistered count isPast]
    Calc --> V3[events.show]
```

---

## 5. Registration Flow (Presentation)

```mermaid
flowchart TB
    R[Click Register] --> C{Checks pass}
    C -->|yes| OK[Confirmed Registration]
    C -->|no| Err[Error Message]
```

---

## 6. Registration Flow (Technical)

```mermaid
sequenceDiagram
    participant U as Alumni
    participant EC as EventController
    participant DB as event_registrations

    U->>EC: POST events/{event}/register
    EC->>EC: event_date not past
    alt past event
        EC-->>U: flash error ended
    else upcoming
        EC->>DB: count registrations
        alt slots > 0 AND count >= slots
            EC-->>U: flash error full
        else
            EC->>DB: exists for user
            alt already registered
                EC-->>U: flash error duplicate
            else
                EC->>DB: create status confirmed
                EC-->>U: flash success
            end
        end
    end
```

---

## 7. Unregister Flow (Technical)

```mermaid
sequenceDiagram
    participant U as Alumni
    participant EC as EventController
    participant DB as event_registrations

    U->>EC: DELETE events/{event}/unregister
    EC->>DB: delete where event_id and user_id
    EC-->>U: flash success
```

---

## 8. Slots Logic (Presentation)

```mermaid
flowchart TB
    S{slots value}
    S -->|0| Inf[Unlimited]
    S -->|>0| Cap[Count < slots required]
```

---

## 9. Admin Event Management (Presentation)

```mermaid
flowchart LR
    Admin[Filament Events] --> CRUD[Create Edit Publish]
    CRUD --> Live[Visible on Public Site]
```

---

## 10. Admin Event Management (Technical)

```mermaid
flowchart TB
    ER[EventResource Filament] --> Form[title description location date slots cover]
    Form --> Save[Event model save]
    Save --> Pub{is_published}
    Pub -->|true| Public[EventController shows]
    Pub -->|false| Hidden[404 on public show]

    ERR[EventRegistrationResource] --> Edit[change status pending confirmed cancelled]
```

**Public registration** always writes `confirmed` regardless of DB default `pending`.

---

## 11. Gallery Permission Coupling (Presentation)

```mermaid
flowchart TB
    Reg[Event Registration confirmed] --> Up[Can Upload Photos]
    Ver[Must be verified alumni] --> Up
```

---

## 12. Gallery Permission Coupling (Technical)

```mermaid
flowchart TB
    GC[GalleryController store] --> C1{user isAdmin}
    C1 -->|yes| OK[allow upload]
    C1 -->|no| C2{is_verified}
    C2 -->|no| Deny[403]
    C2 -->|yes| C3{EventRegistration confirmed for event}
    C3 -->|yes| OK
    C3 -->|no| Deny
```

---

## Data Entities

| Table | Role |
|-------|------|
| `events` | Event definition |
| `event_registrations` | User attendance link |

**Unique:** `(event_id, user_id)` prevents duplicate RSVPs.

See [DATABASE_ERD.md](./DATABASE_ERD.md).

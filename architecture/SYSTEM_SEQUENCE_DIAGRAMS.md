# System Sequence Diagrams

End-to-end sequences for capstone presentation. Based on actual controller and notification code paths.

---

## 1. Homepage Load (Presentation)

```mermaid
sequenceDiagram
    participant G as Guest
    participant H as HomeController
    participant DB as Database

    G->>H: GET /
    H->>DB: query announcements events alumni count
    DB-->>H: data
    H-->>G: home.blade.php HTML
```

---

## 2. Homepage Load (Technical)

```mermaid
sequenceDiagram
    participant B as Browser
    participant R as Router
    participant HC as HomeController
    participant A as Announcement
    participant E as Event
    participant U as User

    B->>R: GET /
    R->>HC: index
    HC->>A: where is_published latest take 3
    HC->>E: where is_published future take 3
    HC->>U: where role alumni count
    HC-->>B: view home compact data
```

---

## 3. Alumni Registration and First Login (Presentation)

```mermaid
sequenceDiagram
    participant U as User
    participant S as System

    U->>S: Register
    S-->>U: Logged in
    U->>S: Complete Profile
    S-->>U: Verified
```

---

## 4. Alumni Registration and First Login (Technical)

```mermaid
sequenceDiagram
    participant U as User
    participant RU as RegisteredUserController
    participant Auth as Auth
    participant APC as AlumniProfileController
    participant DB as Database

    U->>RU: POST register
    RU->>DB: insert users role alumni
    RU->>Auth: login
    RU-->>U: redirect dashboard

    U->>APC: PUT profile
    APC->>DB: updateOrCreate alumni_profiles
    APC->>DB: update is_verified if fields complete
    APC-->>U: redirect profile.show
```

---

## 5. Create Post with Image (Technical)

```mermaid
sequenceDiagram
    participant U as Alumni
    participant PC as PostController
    participant FS as Storage public
    participant DB as posts

    U->>PC: POST posts
    PC->>PC: validate is_verified
    opt image uploaded
        PC->>FS: store post-images
        FS-->>PC: path
    end
    PC->>DB: Post create status visible
    PC-->>U: redirect posts.index flash success
```

---

## 6. Comment and Notify Owner (Technical)

```mermaid
sequenceDiagram
    participant A as Commenter
    participant PC as PostController
    participant DB as DB
    participant N as PostCommentNotification
    participant O as Owner

    A->>PC: POST posts/1/comment
    PC->>DB: insert post_comments
    PC->>N: notify owner User
    N->>DB: insert notifications
    PC-->>A: redirect back

    O->>O: poll GET notifications/unread
    DB-->>O: unread count + preview
```

---

## 7. Event Registration (Presentation)

```mermaid
sequenceDiagram
    participant A as Alumni
    participant E as EventController
    participant DB as DB

    A->>E: Register
    E->>DB: Check slots
    E->>DB: Create registration
    E-->>A: Success
```

---

## 8. Event Registration (Technical)

Full sequence in [EVENTS_AND_REGISTRATION_FLOW.md](./EVENTS_AND_REGISTRATION_FLOW.md#6-registration-flow-technical).

---

## 9. Gallery Upload After Registration (Technical)

```mermaid
sequenceDiagram
    participant U as Verified Alumni
    participant GC as GalleryController
    participant ER as event_registrations
    participant FS as Storage
    participant G as galleries

    U->>GC: POST gallery/event/upload
    GC->>GC: abort if not published
    GC->>ER: exists confirmed for user event
    alt not allowed
        GC-->>U: 403
    else allowed
        loop each photo
            GC->>FS: store gallery
            GC->>G: Gallery create
        end
        GC-->>U: redirect gallery.show success
    end
```

---

## 10. Chatbot Interaction (Presentation)

```mermaid
sequenceDiagram
    participant U as Alumni
    participant S as Laravel
    participant AI as OpenRouter

    U->>S: Ask question
    S->>AI: API request
    AI-->>S: Answer
    S-->>U: Display reply
```

---

## 11. Chatbot Interaction (Technical)

```mermaid
sequenceDiagram
    participant UI as chatbot Alpine
    participant CC as ChatbotController
    participant CFG as config services
    participant API as OpenRouter

    UI->>CC: POST chatbot/ask message
    CC->>CC: validate max 500
    CC->>CFG: read gemini key model
    CC->>API: POST chat completions
    alt success
        API-->>CC: choices message content
        CC-->>UI: JSON reply
    else error
        CC-->>UI: JSON reply error text
    end
```

---

## 12. Admin Moderate Flagged Post (Technical)

```mermaid
sequenceDiagram
    participant A as Admin
    participant PR as PostResource Filament
    participant DB as posts post_flags

    A->>PR: action approve post
    PR->>DB: update status visible is_flagged false
    Note over PR,DB: Or remove / hide via other actions
    PR-->>A: success notification
```

---

## 13. Admin Suspend User (Technical)

```mermaid
sequenceDiagram
    participant A as Admin
    participant UR as UserResource
    participant DB as users
    participant U as Alumni
    participant ASC as AuthenticatedSessionController

    A->>UR: Suspend + reason
    UR->>DB: is_suspended true

    U->>ASC: POST login
    ASC->>DB: auth success
    ASC->>ASC: detect suspended logout
    ASC-->>U: error with reason
```

---

## 14. Global Search (Technical)

```mermaid
sequenceDiagram
    participant U as User
    participant SC as SearchController
    participant DB as Database

    U->>SC: GET search?q=term
    alt len q < 2
        SC-->>U: empty results view
    else
        SC->>DB: LIKE alumni posts events announcements
        DB-->>SC: limited result sets
        SC-->>U: search.index view
    end
```

---

## 15. Filament vs Public — Same Post Entity (Presentation)

```mermaid
sequenceDiagram
    participant Al as Alumni
    participant Pub as PostController
    participant DB as posts
    participant Ad as Admin Filament

    Al->>Pub: create post
    Pub->>DB: insert
    Ad->>DB: moderate status
    Pub->>DB: read visible only
```

---

## Diagram Index

| Scenario | Primary file |
|----------|----------------|
| Auth login/register | AUTHENTICATION_FLOW.md |
| Roles/permissions | USER_ROLE_AND_PERMISSION_FLOW.md |
| Events | EVENTS_AND_REGISTRATION_FLOW.md |
| Posts/social | POSTS_AND_SOCIAL_INTERACTION_FLOW.md |
| Frontend AJAX | FRONTEND_BACKEND_INTERACTION.md |
| Admin | FILAMENT_ADMIN_ARCHITECTURE.md |

All diagrams use Mermaid syntax renderable in GitHub, GitLab, VS Code, and most markdown preview tools.

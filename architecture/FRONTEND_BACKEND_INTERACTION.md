# Frontend / Backend Interaction

Public UI: Blade + Tailwind + Alpine.js + Vite. Admin UI: Filament (separate asset pipeline).

---

## 1. Overall Interaction (Presentation)

```mermaid
flowchart LR
    Browser -->|HTML forms GET| Laravel
    Browser -->|fetch JSON| Laravel
    Laravel -->|Blade HTML| Browser
    Laravel -->|JSON| Browser
```

---

## 2. Overall Interaction (Technical)

```mermaid
flowchart TB
    subgraph browser [Browser]
        Page[Rendered HTML]
        Alpine[Alpine Components]
    end

    subgraph server [Laravel Server]
        Routes[Routes]
        Ctrl[Controllers]
        Views[Blade Engine]
    end

    Page -->|form POST PUT DELETE| Routes
    Alpine -->|fetch POST GET| Routes
    Routes --> Ctrl
    Ctrl --> Views
    Views --> Page
    Ctrl -->|JSON| Alpine
```

---

## 3. Blade Rendering Flow (Presentation)

```mermaid
flowchart TB
    C[Controller] --> V[return view]
    V --> L[layouts.app]
    L --> P[Page Section]
```

---

## 4. Blade Rendering Flow (Technical)

```mermaid
sequenceDiagram
    participant Ctrl as Controller
    participant B as Blade Compiler
    participant L as layouts.app
    participant P as feature view
    participant U as Browser

    Ctrl->>B: view name + compact data
    B->>P: render section content
    P->>L: extends layout
    L->>L: navbar footer vite stack
    L-->>U: HTML response
```

**Primary layout:** `resources/views/layouts/app.blade.php`  
**Exception:** `dashboard.blade.php` uses `<x-app-layout>` (Breeze).

---

## 5. Vite Asset Flow (Presentation)

```mermaid
flowchart LR
    Dev[npm run dev] --> Vite[Vite]
    Vite --> Build[public/build]
    Build --> Blade[@vite directive]
```

---

## 6. Vite Asset Flow (Technical)

```mermaid
flowchart TB
    subgraph sources [Source Files]
        CSS[resources/css/app.css]
        JS[resources/js/app.js]
        BS[resources/js/bootstrap.js]
    end

    subgraph vite [Vite laravel-vite-plugin]
        CFG[vite.config.js]
        DEV[dev HMR]
        PROD[npm run build]
    end

    subgraph output [Output]
        MAN[public/build/manifest.json]
        ASSETS[compiled css js]
    end

    CSS --> CFG
    JS --> BS --> CFG
    CFG --> DEV
    CFG --> PROD --> MAN --> ASSETS
    Blade[@vite in layout] --> ASSETS
```

---

## 7. Alpine.js Interactions (Presentation)

```mermaid
flowchart TB
    Alpine[Alpine.js] --> Bell[Notification Bell]
    Alpine --> Chat[Chatbot]
    Alpine --> Toast[Flash Toasts]
```

---

## 8. Alpine.js Interactions (Technical)

```mermaid
flowchart TB
    appjs[app.js Alpine.start] --> DOM[Scan x-data]

    subgraph bell [notificationBell in app layout]
        Poll[fetch notifications/unread 30s]
        Mark[POST notifications/mark-read]
        Drop[Dropdown x-show]
    end

    subgraph chat [chatbot component]
        Win[x-show open]
        Send[POST chatbot/ask]
        Msg[Append messages]
    end

    DOM --> bell
    DOM --> chat
```

---

## 9. Request-Response Lifecycle (Presentation)

```mermaid
sequenceDiagram
    participant B as Browser
    participant S as Server

    B->>S: Request
    S-->>B: HTML page
    B->>S: AJAX request
    S-->>B: JSON data
```

---

## 10. Request-Response Lifecycle (Technical)

| Interaction | Method | Response | Example |
|-------------|--------|----------|---------|
| Page load | GET | HTML 200 | `/posts` |
| Form submit | POST | Redirect + flash | create post |
| Reaction | POST | JSON 200 | `/posts/{id}/react` |
| Notifications | GET | JSON 200 | `/notifications/unread` |
| Mark read | POST | JSON 200 | `/notifications/mark-read` |
| Chatbot | POST | JSON 200 | `/chatbot/ask` |

---

## 11. Notification Updates (Presentation)

```mermaid
flowchart LR
    Comment[New Comment] --> DB[(notifications)]
    DB --> Poll[Browser Poll 30s]
    Poll --> Bell[Update Badge]
```

---

## 12. Notification Updates (Technical)

```mermaid
sequenceDiagram
    participant A as Alumni A
    participant PC as PostController
    participant DB as notifications
    participant B as Alumni B Browser
    participant NC as NotificationController

    A->>PC: POST comment on B post
    PC->>DB: PostCommentNotification
    loop every 30s
        B->>NC: GET notifications/unread
        NC-->>B: JSON list + unread count
    end
    B->>NC: POST mark-read on bell click
```

---

## 13. Frontend / Backend Communication (CSRF)

```mermaid
flowchart TB
    Layout[layout meta csrf-token] --> Forms[@csrf on POST forms]
    Layout --> Fetch[Alpine fetch X-CSRF-TOKEN header]
    Fetch --> Laravel[VerifyCsrfToken middleware]
    Forms --> Laravel
```

**Axios:** `bootstrap.js` sets `X-Requested-With` and CSRF for same-origin requests.

---

## 14. Component Diagram (Public Frontend)

```mermaid
flowchart TB
    subgraph views [Blade Views]
        Home[home]
        Posts[posts/*]
        Events[events/*]
        Alumni[alumni/*]
        Gallery[gallery/*]
    end

    subgraph components [Components]
        Chatbot[chatbot.blade.php]
        Buttons[buttons inputs]
    end

    subgraph layout [Layout]
        AppLayout[layouts.app]
    end

    AppLayout --> views
    AppLayout --> Chatbot
    views --> components
```

---

## Key Non-SPA Behaviors

- Full page navigation for most actions
- Partial updates only for reactions, notifications, chatbot
- No Vue/React root application

See [SYSTEM_SEQUENCE_DIAGRAMS.md](./SYSTEM_SEQUENCE_DIAGRAMS.md) for end-to-end sequences.

# Posts and Social Interaction Flow

Controller: `PostController`. Models: `Post`, `PostComment`, `PostReaction`, `PostFlag`. Moderation: Filament `PostResource`, `PostFlagResource`.

---

## 1. Social Interaction Overview (Presentation)

```mermaid
flowchart TB
    Post[Alumni Post] --> Feed[Public Feed]
    Feed --> Comment[Comments]
    Feed --> React[Reactions]
    Feed --> Flag[Flags]
    Flag --> Mod[Admin Review]
```

---

## 2. Social Interaction Overview (Technical)

```mermaid
flowchart TB
    subgraph create [Create Path]
        V[is_verified] --> ST[PostController store]
        ST --> PT[posts table status visible]
    end

    subgraph engage [Engagement Path]
        CM[post_comments]
        RX[post_reactions]
        FL[post_flags]
    end

    subgraph notify [Notification]
        CM --> NT[PostCommentNotification]
        NT --> NB[notifications table]
    end

    subgraph mod [Moderation]
        FL --> IF{flags count >= 3}
        IF --> FG[is_flagged true]
        FG --> FR[Filament PostResource]
    end

    PT --> engage
```

---

## 3. Post Creation Flow (Presentation)

```mermaid
flowchart TB
    V[Verified Alumni] --> C[Create Post]
    C --> P[Published to Feed]
```

---

## 4. Post Creation Flow (Technical)

```mermaid
sequenceDiagram
    participant U as Alumni
    participant PC as PostController
    participant S as Storage
    participant DB as posts

    U->>PC: GET posts/create
    PC->>PC: check is_verified
    alt not verified
        PC-->>U: redirect error
    else verified
        PC-->>U: create form
        U->>PC: POST posts store
        PC->>PC: validate title body category image
        opt has image
            PC->>S: store post-images
        end
        PC->>DB: create status visible
        PC-->>U: redirect index success
    end
```

---

## 5. Comment Flow (Presentation)

```mermaid
flowchart LR
    C[Submit Comment] --> S[Saved]
    S --> N[Notify Post Owner]
```

---

## 6. Comment Flow (Technical)

```mermaid
sequenceDiagram
    participant A as Commenter
    participant PC as PostController
    participant DB as post_comments
    participant O as Post Owner
    participant N as PostCommentNotification

    A->>PC: POST posts/{post}/comment
    PC->>DB: PostComment create
    alt commenter is not owner
        PC->>O: notify PostCommentNotification
    end
    PC-->>A: redirect back success
```

---

## 7. Reaction Flow (Presentation)

```mermaid
flowchart TB
    Click[Click Reaction] --> T{Same type exists}
    T -->|yes| Off[Remove]
    T -->|no| On[Add or Change]
```

---

## 8. Reaction Flow (Technical)

```mermaid
sequenceDiagram
    participant B as Browser Alpine
    participant PC as PostController
    participant DB as post_reactions

    B->>PC: POST posts/{post}/react type
    PC->>DB: find by post_id user_id
    alt none exists
        PC->>DB: create type
        PC-->>B: JSON reacted true counts
    else exists same type
        PC->>DB: delete
        PC-->>B: JSON reacted false counts
    else exists different type
        PC->>DB: update type
        PC-->>B: JSON reacted true counts
    end
```

**Unique:** one reaction row per user per post.

---

## 9. Flagging Flow (Presentation)

```mermaid
flowchart TB
    F[User Flags Post] --> C{Count >= 3}
    C -->|yes| M[Marked for Review]
    C -->|no| W[Waiting]
```

---

## 10. Flagging Flow (Technical)

```mermaid
flowchart TB
    Start[POST posts/{post}/flag] --> O1{own post}
    O1 -->|yes| E1[error]
    O1 -->|no| O2{already flagged by user}
    O2 -->|yes| E2[error]
    O2 -->|no| CR[PostFlag create]
    CR --> CT{flags count >= 3}
    CT -->|yes| FG[post is_flagged true]
    CT -->|no| Done[done]
```

---

## 11. Moderation Flow (Presentation)

```mermaid
flowchart TB
    Admin[Admin Reviews] --> A[Approve Visible]
    Admin --> H[Hide]
    Admin --> R[Remove]
```

---

## 12. Moderation Flow (Technical)

```mermaid
flowchart TB
    subgraph filament_post [PostResource Actions]
        AP[approve] --> V[status visible is_flagged false]
        HI[hide] --> H[status hidden]
        RM[remove] --> X[status removed is_flagged false]
    end

    subgraph filament_flag [PostFlagResource Actions]
        FA[approve post] --> V
        FA --> DEL[delete flag row]
        FR[remove post] --> X
        FR --> DEL
    end
```

Public feed: only `status = visible` in `PostController@index`.

---

## 13. Post Status State (Presentation)

```mermaid
stateDiagram-v2
    [*] --> visible: alumni creates
    visible --> hidden: admin hide
    visible --> removed: admin remove
    hidden --> visible: admin approve
    visible --> flagged: 3+ flags is_flagged
    flagged --> visible: admin approve
```

**Note:** `is_flagged` is a boolean marker; visibility controlled by `status`.

---

## Category Enum (Reference)

`career_update` | `achievement` | `opportunity` | `reunion` | `general`

Defined in `Post::CATEGORIES` and migration enum.

See [DATA_FLOW_DIAGRAM.md](./DATA_FLOW_DIAGRAM.md) for DFD view.

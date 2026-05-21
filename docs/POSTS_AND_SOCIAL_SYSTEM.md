# Posts and Social System

## Overview

Community feed where **verified alumni** publish categorized updates. All users (authenticated) can engage via comments, reactions, and flags. Admins moderate via Filament.

## Model: `Post`

**File:** `app/Models/Post.php`  
**Table:** `posts`

### Categories

| Key | Label |
|-----|-------|
| `career_update` | Career Update |
| `achievement` | Achievement |
| `opportunity` | Opportunity |
| `reunion` | Reunion |
| `general` | General |

### Status values

| Status | Meaning |
|--------|---------|
| `visible` | Public feed |
| `hidden` | Removed from feed, admin reversible |
| `removed` | Moderation removal |

### Scopes

- `scopeVisible` — `status = visible`
- `scopeFlagged` — `is_flagged = true`

### Accessors

- `category_label`, `category_color` — UI badges (Tailwind classes in `CATEGORY_COLORS`)

## Controller: `PostController`

**File:** `app/Http/Controllers/PostController.php`

| Action | Route | Auth | Rules |
|--------|-------|------|-------|
| `index` | GET `/posts` | Public | Visible posts, optional `?category=` |
| `create` | GET `/posts/create` | **No middleware** | Redirects if not `is_verified` |
| `store` | POST `/posts` | Yes | Verified + validation |
| `show` | GET `/posts/{post}` | Public | 404 if not visible |
| `edit/update/destroy` | | Yes | Owner only |
| `flag` | POST | Yes | Not own post; one flag per user |
| `comment` | POST | Yes | Notifies post owner |
| `deleteComment` | DELETE | Yes | Owner only |
| `react` | POST | Yes | JSON response |

### Create validation

```
title:  required, 5-150 chars
body:   required, 10-3000 chars
category: required, in Post::CATEGORIES keys
image_path: optional image, max 4096 KB → storage/post-images/
```

New posts default `status = visible`.

## Views

| View | Purpose |
|------|---------|
| `posts/index.blade.php` | Feed, category filter, create button logic |
| `posts/show.blade.php` | Single post, comments, reactions, flag form |
| `posts/create.blade.php` | Authoring form |
| `posts/edit.blade.php` | Owner edit |

### Index UI logic

- Admin → link to `/admin` manage posts
- Verified alumni → "New Post"
- Unverified → "Complete Profile to Post" → `profile.edit`

## Flagging Threshold

When a post receives **3 or more** `post_flags` records:

```php
$post->update(['is_flagged' => true]);
```

Does not auto-hide; admin reviews in Filament.

## Image Attachments

Column `image_path` (migration `2026_05_19_141357_add_image_to_posts_table.php`).

- Stored on `public` disk
- Deleted on replace in `update()`

## Admin: Filament `PostResource`

**Navigation group:** Community

Actions:

- **Approve** — `status=visible`, `is_flagged=false`
- **Hide** — `status=hidden`
- **Remove** — `status=removed`

List sorted with flagged posts prioritized (`defaultSort('is_flagged', 'desc')`).

## Feed Query

```php
Post::where('status', 'visible')
    ->with(['user', 'user.alumniProfile'])
    ->withCount('comments')
    ->latest()
    ->paginate(10);
```

## Related Systems

- [COMMENTS_AND_REACTIONS_SYSTEM.md](./COMMENTS_AND_REACTIONS_SYSTEM.md)
- [CONTENT_MODERATION_SYSTEM.md](./CONTENT_MODERATION_SYSTEM.md)
- [NOTIFICATIONS_SYSTEM.md](./NOTIFICATIONS_SYSTEM.md)

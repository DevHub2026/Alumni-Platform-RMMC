# Backend Documentation

## Overview

The backend is standard Laravel 13 with Eloquent ORM, session authentication, and Filament for administration. Business logic is concentrated in **controllers** and **Filament resource classes**—there is no `app/Services` directory.

## HTTP Controllers

### Feature Controllers

| Controller | File | Methods | Primary views |
|------------|------|---------|---------------|
| `HomeController` | `app/Http/Controllers/HomeController.php` | `index` | `home` |
| `AlumniProfileController` | `app/Http/Controllers/AlumniProfileController.php` | `index`, `show`, `edit`, `update` | `alumni/*` |
| `AnnouncementController` | `app/Http/Controllers/AnnouncementController.php` | `index`, `show` | `announcements/*` |
| `EventController` | `app/Http/Controllers/EventController.php` | `index`, `show`, `register`, `unregister` | `events/*` |
| `GalleryController` | `app/Http/Controllers/GalleryController.php` | `index`, `show`, `store`, `destroy` | `gallery/*` |
| `PostController` | `app/Http/Controllers/PostController.php` | Full CRUD + `flag`, `comment`, `deleteComment`, `react` | `posts/*` |
| `SearchController` | `app/Http/Controllers/SearchController.php` | `index` | `search/index` |
| `NotificationController` | `app/Http/Controllers/NotificationController.php` | `index`, `unread`, `markRead` | `notifications/index` + JSON |
| `ChatbotController` | `app/Http/Controllers/ChatbotController.php` | `ask` | JSON only |
| `ProfileController` | `app/Http/Controllers/ProfileController.php` | Breeze methods | **Not routed** |

### Auth Controllers (Breeze)

Located in `app/Http/Controllers/Auth/`:

- `AuthenticatedSessionController` — login/logout; **suspension check** on login
- `RegisteredUserController` — registration (defaults `role` via DB)
- `PasswordResetLinkController`, `NewPasswordController`
- `EmailVerificationPromptController`, `VerifyEmailController`, `EmailVerificationNotificationController`
- `ConfirmablePasswordController`, `PasswordController`

## Form Requests

| Request | File | Used by |
|---------|------|---------|
| `LoginRequest` | `app/Http/Requests/Auth/LoginRequest.php` | Login — rate limiting (5 attempts), `authenticate()` |
| `ProfileUpdateRequest` | `app/Http/Requests/ProfileUpdateRequest.php` | **Unused** (Breeze profile routes not registered) |

Most validation is **inline** in controllers via `$request->validate([...])`.

## Eloquent Models

See individual system docs. Summary:

```php
// User — Filament gate
public function canAccessPanel(Panel $panel): bool
{
    return $this->role === 'admin';
}

// Post — visibility scope
public function scopeVisible($query)
{
    return $query->where('status', 'visible');
}
```

### Model Constants

| Model | Constants |
|-------|-----------|
| `Post` | `CATEGORIES`, `CATEGORY_COLORS` |
| `PostFlag` | `REASONS` |
| `PostReaction` | `TYPES` |

## Notifications

| Class | Channel | Trigger |
|-------|---------|---------|
| `PostCommentNotification` | `database` | Comment on another user's post |

Implements `via()` → `['database']` and `toArray()` with `message`, `post_id`, `post_title`, `commenter`.

## Providers

### `AppServiceProvider`

Empty `register()` and `boot()` — extension point for future bindings.

### `AdminPanelProvider`

Configures Filament panel `admin` at path `/admin`. See [FILAMENT_ADMIN_PANEL.md](./FILAMENT_ADMIN_PANEL.md).

## Validation Patterns (Controller-Level)

### Alumni profile (`AlumniProfileController@update`)

- `student_id`, `course`, `graduation_year`, contact fields, URLs, `bio`, `skills`
- `profile_photo`: image, max 2048 KB

### Posts (`PostController`)

- `title`: 5–150 chars
- `body`: 10–3000 chars
- `category`: enum keys from `Post::CATEGORIES`
- `image_path`: optional image, max 4096 KB

### Gallery (`GalleryController@store`)

- `photos`: array 1–10, each image max 4096 KB
- `captions`: optional per photo

## Authorization Approach

**No Policy classes.** Authorization uses:

| Mechanism | Example |
|-----------|---------|
| Route middleware | `auth`, `verified`, `guest` |
| `abort_if()` | Unpublished events, non-visible posts |
| Ownership checks | `$post->user_id !== Auth::id()` |
| Role checks | `$user->isAdmin()`, `is_verified` |
| Filament | `canAccessPanel()` on `User` |

## Filament Backend

Resources auto-discovered from `app/Filament/Resources`. Each resource defines:

- `form(Schema)` — create/edit fields
- `table(Table)` — list columns, filters, actions
- `getPages()` — route mapping to page classes

## External HTTP Integration

`ChatbotController` uses Laravel `Http` facade:

```php
Http::withHeaders([
    'Authorization' => 'Bearer ' . config('services.gemini.key'),
])->post('https://openrouter.ai/api/v1/chat/completions', [...]);
```

Configuration: `config/services.php` → `gemini.key`, `gemini.model`.

## Database Access Patterns

- **Pagination:** posts (10), alumni directory (12), announcements (9)
- **Eager loading:** `with(['user', 'user.alumniProfile'])`, `withCount('comments')`
- **Scopes:** `Post::visible()`, `Post::flagged()`

## Error Handling

- Standard Laravel exception rendering
- Chatbot catches exceptions and returns error string in JSON `reply`
- No custom exception handlers in `bootstrap/app.php`

## Caching & Sessions

`.env.example` defaults:

- `CACHE_STORE=database`
- `SESSION_DRIVER=database`

Uses `cache` and `sessions` tables from Laravel default migrations.

## Extension Points

Recommended locations for new backend code:

| Need | Suggested location |
|------|-------------------|
| Shared business logic | `app/Services/` (new) |
| Authorization rules | `app/Policies/` |
| Reusable validation | Form Requests in `app/Http/Requests/` |
| Async notifications | Queue `ShouldQueue` on notification class |
| Admin features | New Filament Resource under `app/Filament/Resources/` |

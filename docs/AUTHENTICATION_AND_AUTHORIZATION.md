# Authentication and Authorization

## Authentication System

**Package:** Laravel Breeze (Blade stack)  
**Guard:** `web` (session driver)  
**User model:** `App\Models\User`  
**Config:** `config/auth.php`

### Registration Flow

1. `GET /register` → `RegisteredUserController@create`
2. `POST /register` → validates name, email, password
3. `User::create([...])` — **no explicit `role`**; DB default `alumni`
4. `event(new Registered($user))`
5. `Auth::login($user)`
6. Redirect to `route('dashboard')`

**No alumni profile row** is created at registration; profile is created on first `updateOrCreate` in `AlumniProfileController@update`.

### Login Flow

1. `POST /login` → `LoginRequest` validates credentials
2. Rate limit: **5 attempts** per email+IP (`LoginRequest::ensureIsNotRateLimited`)
3. `Auth::attempt()` on success
4. **Suspension check** in `AuthenticatedSessionController@store`:

```php
if (auth()->user()->is_suspended) {
    auth()->logout();
    // session invalidated
    return back()->withErrors(['email' => 'Your account has been suspended. Reason: ...']);
}
```

5. Session regenerated → redirect intended `dashboard`

### Logout

`POST /logout` → invalidates session, regenerates token, redirects `/`.

### Password Reset

Standard Breeze flow via `password_reset_tokens` table.

### Email Verification

| Route | Middleware | Purpose |
|-------|------------|---------|
| `verification.notice` | `auth` | Prompt to verify |
| `verification.verify` | `auth`, `signed`, `throttle:6,1` | Verify link |
| `verification.send` | `auth`, `throttle:6,1` | Resend email |

**Only `/dashboard` uses `verified` middleware** in `routes/web.php`. Most alumni features require `auth` only, not verified email.

---

## Filament Admin Authentication

- Separate login at `/admin/login`
- `User implements FilamentUser`
- `canAccessPanel()` returns `$this->role === 'admin'`
- Non-admins cannot access Filament even if authenticated on public site

---

## Authorization Model

### No Laravel Policies

`app/Policies/` does not exist. Grep shows no `Gate::` or `@can` in application code.

### Authorization Mechanisms

| Layer | Implementation |
|-------|----------------|
| **Route middleware** | `auth`, `guest`, `verified` |
| **Controller guards** | `abort_if()`, `abort(403)`, `abort(404)` |
| **Business flags** | `is_verified`, `is_suspended`, `role` |
| **Ownership** | Compare `user_id` to `Auth::id()` |
| **Filament** | `canAccessPanel()` |

### Authorization Matrix (Public Site)

| Action | Guest | Alumni | Verified alumni | Admin |
|--------|-------|--------|-----------------|-------|
| View published content | ✓ | ✓ | ✓ | ✓ |
| Edit own profile | ✗ | ✓ | ✓ | ✓ |
| Create posts | ✗ | ✗ | ✓ | ✓ (also via Filament) |
| Comment / react / flag | ✗ | ✓* | ✓ | ✓ |
| Register for events | ✗ | ✓ | ✓ | ✓ |
| Upload gallery photos | ✗ | ✗ | ✓** | ✓ |
| Chatbot | ✗ | ✓ | ✓ | ✓ |
| Filament panel | ✗ | ✗ | ✗ | ✓ |

\*Requires `auth` middleware on route.  
\*\*Requires `is_verified` + confirmed `event_registrations` for that event (or admin).

### Post Ownership Rules

| Action | Rule |
|--------|------|
| Edit/delete post | `$post->user_id === Auth::id()` |
| Flag post | Cannot flag own post |
| Delete comment | `$comment->user_id === Auth::id()` |

### Content Visibility Rules

| Entity | Public visibility |
|--------|-------------------|
| Announcement | `is_published === true` |
| Event | `is_published === true` |
| Post | `status === 'visible'` (scope `visible`) |

---

## Verification vs Email Verification

Two distinct concepts:

| Concept | Field / mechanism | Effect |
|---------|-------------------|--------|
| **Email verified** | `email_verified_at` | Required for `/dashboard` only |
| **Alumni verified** | `users.is_verified` | Required to create posts; gallery upload with registration |

Auto-verification (`AlumniProfileController@update`):

```php
if ($profile->course && $profile->graduation_year && $profile->student_id) {
    $user->update(['is_verified' => true]);
}
```

Admins can toggle `is_verified` in Filament `UserResource`.

---

## Suspension System

| Field | Purpose |
|-------|---------|
| `is_suspended` | Boolean gate on login |
| `suspension_reason` | Shown in login error message |

Managed via Filament **Suspend** / **Unsuspend** actions on alumni users.

---

## CSRF Protection

- All POST/PUT/DELETE web routes require CSRF token
- Meta tag in `layouts/app.blade.php`
- JSON fetch calls must send `X-CSRF-TOKEN`

---

## Session Configuration

Default from `.env.example`:

```
SESSION_DRIVER=database
```

Sessions stored in `sessions` table — suitable for single-server deploy; use Redis for multi-server.

---

## Security Recommendations

See [SECURITY_AND_SCALABILITY_ANALYSIS.md](./SECURITY_AND_SCALABILITY_ANALYSIS.md) for:

- Adding Policies for posts/comments/gallery
- Protecting `GET /posts/create` with `auth` middleware
- Aligning email verification with posting requirements

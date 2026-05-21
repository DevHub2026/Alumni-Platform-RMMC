# Routes Reference

Routes are defined in `routes/web.php` and `routes/auth.php`. Registered via `bootstrap/app.php` → `web` channel only (no `api.php`).

## Route Summary Table

| Method | URI | Name | Middleware | Controller@method |
|--------|-----|------|------------|-------------------|
| GET | `/` | `home` | — | `HomeController@index` |
| GET | `/search` | `search.index` | — | `SearchController@index` |
| POST | `/chatbot/ask` | `chatbot.ask` | `auth` | `ChatbotController@ask` |
| GET | `/profile` | `profile.show` | `auth` | `AlumniProfileController@show` |
| GET | `/profile/edit` | `profile.edit` | `auth` | `AlumniProfileController@edit` |
| PUT | `/profile` | `profile.update` | `auth` | `AlumniProfileController@update` |
| GET | `/alumni` | `alumni.index` | — | `AlumniProfileController@index` |
| GET | `/posts` | `posts.index` | — | `PostController@index` |
| GET | `/posts/create` | `posts.create` | — | `PostController@create` |
| GET | `/posts/{post}` | `posts.show` | — | `PostController@show` |
| POST | `/posts` | `posts.store` | `auth` | `PostController@store` |
| GET | `/posts/{post}/edit` | `posts.edit` | `auth` | `PostController@edit` |
| PUT | `/posts/{post}` | `posts.update` | `auth` | `PostController@update` |
| DELETE | `/posts/{post}` | `posts.destroy` | `auth` | `PostController@destroy` |
| POST | `/posts/{post}/flag` | `posts.flag` | `auth` | `PostController@flag` |
| POST | `/posts/{post}/react` | `posts.react` | `auth` | `PostController@react` |
| POST | `/posts/{post}/comment` | `posts.comment` | `auth` | `PostController@comment` |
| DELETE | `/comments/{comment}` | `posts.comment.delete` | `auth` | `PostController@deleteComment` |
| GET | `/announcements` | `announcements.index` | — | `AnnouncementController@index` |
| GET | `/announcements/{announcement}` | `announcements.show` | — | `AnnouncementController@show` |
| GET | `/events` | `events.index` | — | `EventController@index` |
| GET | `/events/{event}` | `events.show` | — | `EventController@show` |
| POST | `/events/{event}/register` | `events.register` | `auth` | `EventController@register` |
| DELETE | `/events/{event}/unregister` | `events.unregister` | `auth` | `EventController@unregister` |
| GET | `/gallery` | `gallery.index` | — | `GalleryController@index` |
| GET | `/gallery/{event}` | `gallery.show` | — | `GalleryController@show` |
| POST | `/gallery/{event}/upload` | `gallery.store` | `auth` | `GalleryController@store` |
| DELETE | `/gallery/photo/{gallery}` | `gallery.destroy` | `auth` | `GalleryController@destroy` |
| GET | `/notifications` | `notifications.index` | `auth` | `NotificationController@index` |
| GET | `/notifications/unread` | `notifications.unread` | `auth` | `NotificationController@unread` |
| POST | `/notifications/mark-read` | `notifications.markRead` | `auth` | `NotificationController@markRead` |
| GET | `/dashboard` | `dashboard` | `auth`, `verified` | Closure → `dashboard` view |

## Authentication Routes (`routes/auth.php`)

### Guest middleware

| Method | URI | Name | Controller |
|--------|-----|------|------------|
| GET | `/register` | `register` | `RegisteredUserController@create` |
| POST | `/register` | — | `RegisteredUserController@store` |
| GET | `/login` | `login` | `AuthenticatedSessionController@create` |
| POST | `/login` | — | `AuthenticatedSessionController@store` |
| GET | `/forgot-password` | `password.request` | `PasswordResetLinkController@create` |
| POST | `/forgot-password` | `password.email` | `PasswordResetLinkController@store` |
| GET | `/reset-password/{token}` | `password.reset` | `NewPasswordController@create` |
| POST | `/reset-password` | `password.store` | `NewPasswordController@store` |

### Auth middleware

| Method | URI | Name | Controller |
|--------|-----|------|------------|
| GET | `/verify-email` | `verification.notice` | `EmailVerificationPromptController` |
| GET | `/verify-email/{id}/{hash}` | `verification.verify` | `VerifyEmailController` (+ `signed`, `throttle:6,1`) |
| POST | `/email/verification-notification` | `verification.send` | `EmailVerificationNotificationController` (+ throttle) |
| GET | `/confirm-password` | `password.confirm` | `ConfirmablePasswordController@show` |
| POST | `/confirm-password` | — | `ConfirmablePasswordController@store` |
| PUT | `/password` | `password.update` | `PasswordController@update` |
| POST | `/logout` | `logout` | `AuthenticatedSessionController@destroy` |

## Filament Admin Routes

Auto-registered by Filament under prefix **`/admin`** (panel id: `admin`).

- Login: `/admin/login`
- Resources: `/admin/{resource}/...` (e.g. `/admin/users`)

Configured in `app/Providers/Filament/AdminPanelProvider.php`.

## Health Check

`GET /up` — Laravel health route (bootstrap default).

## Implicit Route Model Binding

| Parameter | Model |
|-----------|-------|
| `{post}` | `App\Models\Post` |
| `{event}` | `App\Models\Event` |
| `{announcement}` | `App\Models\Announcement` |
| `{comment}` | `App\Models\PostComment` |
| `{gallery}` | `App\Models\Gallery` |

## Route Groups (Logical)

```
Public content
├── home, search
├── announcements.*
├── events.* (read)
├── gallery.* (read)
├── posts.* (read + create GET unprotected)
└── alumni.index

Authenticated alumni
├── profile.*
├── posts (write, flag, react, comment)
├── events.register / unregister
├── gallery.store / destroy
├── notifications.*
└── chatbot.ask

Auth (Breeze)
└── routes/auth.php

Admin
└── Filament /admin (separate stack)
```

## Routes Not Registered (Breeze leftovers)

`ProfileController` routes for account settings (`profile.edit` Breeze view at `resources/views/profile/edit.blade.php`) are **overridden** by alumni profile routes using the same name `profile.edit`.

## Security Notes

| Route | Note |
|-------|------|
| `GET /posts/create` | No `auth` middleware; controller calls `Auth::user()` |
| `GET /posts/{post}/edit` | Protected; controller checks ownership |
| Unpublished content | Controllers use `abort_if` / `abort(404)` |

See [AUTHENTICATION_AND_AUTHORIZATION.md](./AUTHENTICATION_AND_AUTHORIZATION.md).

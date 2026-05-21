# Folder Structure

Complete repository layout for the Alumni Platform (application-relevant paths).

```
alumni-platform/
├── app/
│   ├── Filament/
│   │   ├── Resources/          # Admin CRUD (Users, Posts, Events, etc.)
│   │   │   ├── AlumniProfiles/
│   │   │   ├── Announcements/
│   │   │   ├── Events/
│   │   │   ├── EventRegistrations/
│   │   │   ├── Galleries/
│   │   │   ├── PostFlags/
│   │   │   ├── Posts/
│   │   │   └── Users/
│   │   └── Widgets/            # StatsOverview, AlumniGrowthChart
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/           # Breeze auth controllers (11 files)
│   │   │   ├── AlumniProfileController.php
│   │   │   ├── AnnouncementController.php
│   │   │   ├── ChatbotController.php
│   │   │   ├── EventController.php
│   │   │   ├── GalleryController.php
│   │   │   ├── HomeController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── PostController.php
│   │   │   ├── ProfileController.php   # Breeze (unused in routes)
│   │   │   └── SearchController.php
│   │   └── Requests/
│   │       ├── Auth/LoginRequest.php
│   │       └── ProfileUpdateRequest.php  # Breeze (unused in routes)
│   ├── Models/                 # 10 Eloquent models
│   ├── Notifications/
│   │   └── PostCommentNotification.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── Filament/AdminPanelProvider.php
│   └── View/Components/
│       ├── AppLayout.php
│       └── GuestLayout.php
├── bootstrap/
│   ├── app.php                 # Application bootstrap, routing, middleware
│   ├── cache/
│   └── providers.php
├── config/                     # Laravel + services.gemini
├── database/
│   ├── factories/UserFactory.php
│   ├── migrations/             # 18 migrations
│   └── seeders/DatabaseSeeder.php
├── docs/                       # This documentation set
├── public/
│   ├── index.php
│   └── js/filament/            # Published Filament assets
├── resources/
│   ├── css/app.css
│   ├── js/
│   │   ├── app.js              # Alpine bootstrap
│   │   └── bootstrap.js        # Axios defaults
│   └── views/
│       ├── alumni/
│       ├── announcements/
│       ├── auth/
│       ├── components/         # Blade components + chatbot
│       ├── events/
│       ├── gallery/
│       ├── layouts/
│       ├── notifications/
│       ├── posts/
│       ├── profile/            # Breeze account views (orphaned routes)
│       └── search/
├── routes/
│   ├── web.php                 # Main application routes
│   ├── auth.php                # Breeze authentication routes
│   └── console.php
├── storage/
│   ├── app/public/             # Uploaded media (after storage:link)
│   ├── framework/
│   └── logs/
├── tests/
│   ├── Feature/                # Auth + profile Breeze tests
│   └── Unit/
├── composer.json
├── package.json
├── phpunit.xml
├── tailwind.config.js
├── vite.config.js
└── .env.example
```

## Directory Responsibilities

### `app/Http/Controllers/`

All public HTTP endpoints except Filament. Auth subdirectory from Laravel Breeze.

### `app/Filament/`

Admin panel only. Each resource typically contains:

- `*Resource.php` — Resource definition
- `Pages/` — List, Create, Edit, View pages
- `Schemas/` or inline `form()` — Form fields
- `Tables/` or inline `table()` — Table columns (some inlined in Resource)

### `app/Models/`

| Model | Table |
|-------|-------|
| `User` | `users` |
| `AlumniProfile` | `alumni_profiles` |
| `Announcement` | `announcements` |
| `Event` | `events` |
| `EventRegistration` | `event_registrations` |
| `Gallery` | `galleries` |
| `Post` | `posts` |
| `PostComment` | `post_comments` |
| `PostFlag` | `post_flags` |
| `PostReaction` | `post_reactions` |

### `resources/views/`

Blade templates grouped by feature. Primary public shell: `layouts/app.blade.php`.

### `database/migrations/`

Ordered schema history from April–May 2026. See [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md).

### `tests/`

PHPUnit feature tests for Breeze auth flows only—no domain feature tests.

## Not Present in Repository

| Expected path | Status |
|---------------|--------|
| `app/Services/` | Does not exist |
| `app/Policies/` | Does not exist |
| `app/Jobs/` | Does not exist |
| `app/Http/Middleware/` | Does not exist (Laravel 13 default location unused) |
| `routes/api.php` | Not registered |

## Composer Scripts (Reference)

From `composer.json`:

| Script | Purpose |
|--------|---------|
| `composer setup` | install, .env, key, migrate, npm build |
| `composer dev` | Concurrently: serve, queue, pail, vite |
| `composer test` | PHPUnit |

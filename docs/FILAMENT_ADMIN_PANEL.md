# Filament Admin Panel

## Access

| Setting | Value |
|---------|-------|
| Panel ID | `admin` |
| URL prefix | `/admin` |
| Provider | `app/Providers/Filament/AdminPanelProvider.php` |
| Primary color | Amber |
| Login | Enabled (`->login()`) |

**Authorization:** `User::canAccessPanel()` — `role === 'admin'` only.

## Panel Configuration

```php
$panel
    ->default()
    ->id('admin')
    ->path('admin')
    ->discoverResources(in: app_path('Filament/Resources'), ...)
    ->discoverWidgets(in: app_path('Filament/Widgets'), ...)
    ->navigationItems([
        NavigationItem::make('Alumni Platform')
            ->url('/')
            ->openUrlInNewTab(),
    ]);
```

## Middleware Stack

EncryptCookies, StartSession, CSRF, SubstituteBindings, Filament-specific middleware, `Authenticate`.

## Navigation Groups

| Group | Resources |
|-------|-----------|
| **Users & Profiles** | Users, Alumni Profiles |
| **Content** | Announcements, Events, Event Registrations, Galleries |
| **Community** | Posts |
| **Moderation** | Flagged Posts (PostFlags) |

## Resources Reference

| Resource | Model | Key capabilities |
|----------|-------|------------------|
| `UserResource` | `User` | CRUD, suspend/unsuspend, role, verification |
| `AlumniProfileResource` | `AlumniProfile` | CRUD all profile fields |
| `AnnouncementResource` | `Announcement` | CRUD, publish toggle, cover image |
| `EventResource` | `Event` | CRUD, slots, publish, cover image |
| `EventRegistrationResource` | `EventRegistration` | List/edit status |
| `GalleryResource` | `Gallery` | CRUD photos |
| `PostResource` | `Post` | View/edit, approve/hide/remove |
| `PostFlagResource` | `PostFlag` | Review flags, approve/remove post |

## Post Moderation Actions

From `PostResource` table actions:

- **Approve** — visible + clear flag
- **Hide** — `status=hidden`
- **Remove** — `status=removed`

From `PostFlagResource`:

- **Approve Post** — clears flag record
- **Remove Post** — removes post, deletes flag

## Dashboard Widgets

### `StatsOverview`

**File:** `app/Filament/Widgets/StatsOverview.php`

| Stat | Query |
|------|-------|
| Total Alumni | `User::where('role','alumni')->count()` |
| Verified Alumni | alumni + `is_verified` |
| Total Posts | `Post::count()` (with flagged count in description) |
| Upcoming Events | published, future dates |
| Event Registrations | `EventRegistration::count()` |
| Announcements | published count |

### `AlumniGrowthChart`

**File:** `app/Filament/Widgets/AlumniGrowthChart.php`

Line chart: new alumni registrations per month (last 6 months).

## Default Widgets

Also registered (Filament stock):

- `AccountWidget`
- `FilamentInfoWidget`

## Resource Structure Pattern

```
app/Filament/Resources/{Name}/
├── {Name}Resource.php
├── Pages/
│   ├── List*.php
│   ├── Create*.php
│   ├── Edit*.php
│   └── View*.php (where applicable)
├── Schemas/     # Form definitions (some resources)
└── Tables/      # Table definitions (some resources)
```

Some resources inline `form()` and `table()` in the Resource class (e.g. `UserResource`, `PostResource`).

## Public Site Link

Admins see **⚙️ Admin Panel** in `layouts/app.blade.php` when `Auth::user()->role === 'admin'`.

## Filament vs Public Data Entry

| Content | Created on public site | Created in Filament |
|---------|------------------------|---------------------|
| Posts | Alumni (verified) | Possible via PostResource (create page exists) |
| Announcements | Never | Yes |
| Events | Never | Yes |
| Gallery photos | Alumni/admin upload | Yes |
| Users | Registration | Yes |

## Upgrading Filament

`composer.json` post-autoload runs `php artisan filament:upgrade`.

## Related Docs

- [CONTENT_MODERATION_SYSTEM.md](./CONTENT_MODERATION_SYSTEM.md)
- [USER_AND_ROLE_SYSTEM.md](./USER_AND_ROLE_SYSTEM.md)

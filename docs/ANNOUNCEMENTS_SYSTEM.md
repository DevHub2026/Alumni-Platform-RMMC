# Announcements System

## Overview

Official **school news** published by administrators. Alumni and guests have **read-only** access on the public site; all write operations occur in Filament.

## Model

**File:** `app/Models/Announcement.php`  
**Table:** `announcements`

| Field | Purpose |
|-------|---------|
| `user_id` | Admin author |
| `title` | Headline |
| `content` | Full body (HTML/text) |
| `cover_image` | Optional thumbnail path |
| `is_published` | Visibility flag |

## Public Controller

**File:** `app/Http/Controllers/AnnouncementController.php`

| Method | Behavior |
|--------|----------|
| `index` | Published only, `latest()`, paginate 9 |
| `show` | `abort_if(!$announcement->is_published, 404)` |

## Routes

| Method | URI | Name |
|--------|-----|------|
| GET | `/announcements` | `announcements.index` |
| GET | `/announcements/{announcement}` | `announcements.show` |

## Views

- `resources/views/announcements/index.blade.php` — card grid
- `resources/views/announcements/show.blade.php` — full article

## Homepage Integration

`HomeController` loads 3 latest published announcements for preview cards on `/`.

## Search Integration

`SearchController` includes announcements where `is_published` and title/content match query (max 4 results).

## Admin (Filament)

**Resource:** `app/Filament/Resources/Announcements/AnnouncementResource.php`  
**Navigation group:** Content

Standard CRUD:

- `Pages/ListAnnouncements`, `CreateAnnouncement`, `EditAnnouncement`
- Form schema: `Schemas/AnnouncementForm.php`
- Table: `Tables/AnnouncementsTable.php`

Admins set `is_published` to make content live.

## Stats Widget

`StatsOverview` displays count of published announcements on admin dashboard.

## Permissions

| Action | Guest | Alumni | Admin |
|--------|-------|--------|-------|
| List/view published | ✓ | ✓ | ✓ |
| Create/edit | ✗ | ✗ | ✓ (Filament) |

## No Notifications

New announcements do **not** trigger alumni notifications in current implementation.

**Future enhancement:** database or email broadcast on publish.

## Related Docs

- [FILAMENT_ADMIN_PANEL.md](./FILAMENT_ADMIN_PANEL.md)
- [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)

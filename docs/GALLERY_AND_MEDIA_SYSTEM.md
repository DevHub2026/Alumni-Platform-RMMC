# Gallery and Media System

## Overview

Event-scoped photo galleries allow community members to share memories from alumni events. Photos are stored on Laravel's **`public` disk** and linked to both an **event** and **uploader**.

## Model

**File:** `app/Models/Gallery.php`  
**Table:** `galleries`

| Field | Purpose |
|-------|---------|
| `event_id` | Parent event |
| `user_id` | Uploader |
| `image_path` | Storage path |
| `caption` | Optional description |

## Controller

**File:** `app/Http/Controllers/GalleryController.php`

### `index` (public)

Lists published events that `whereHas('galleries')` with photo counts.

### `show` (public)

- All photos for event, newest first, with uploader name
- Computes `$canUpload` for authenticated users

### Upload permission logic

```php
$canUpload = $user->isAdmin() || (
    $user->is_verified &&
    EventRegistration::where('event_id', $event->id)
        ->where('user_id', $user->id)
        ->where('status', 'confirmed')
        ->exists()
);
```

### `store` (auth)

- Event must be published
- Re-validates `$canUpload` server-side (`abort_if` 403)
- Accepts 1–10 images per request (jpg/jpeg/png/webp, max 4 MB each)
- Optional parallel `captions[]` array
- Stores under `gallery/` on public disk

### `destroy` (auth)

- Owner **or** admin may delete
- Deletes physical file then DB record

## Routes

| Method | URI | Name | Middleware |
|--------|-----|------|------------|
| GET | `/gallery` | `gallery.index` | — |
| GET | `/gallery/{event}` | `gallery.show` | — |
| POST | `/gallery/{event}/upload` | `gallery.store` | auth |
| DELETE | `/gallery/photo/{gallery}` | `gallery.destroy` | auth |

## Views

- `gallery/index.blade.php` — event cards with thumbnails
- `gallery/show.blade.php` — grid/lightbox, upload form when `$canUpload`

## Storage Configuration

**Disk:** `public` (`config/filesystems.php`)

```
storage/app/public/gallery/{filename}
```

**Public URL:** `{APP_URL}/storage/gallery/...` after:

```bash
php artisan storage:link
```

## Other Media Paths

| Feature | Directory |
|---------|-----------|
| Profile photos | `profile-photos/` |
| Post images | `post-images/` |
| Event covers | Filament upload (event `cover_image`) |
| Announcement covers | Filament upload |

## Admin (Filament)

**Resource:** `GalleryResource` — group **Content**

Admins can manage gallery records directly (create/edit/delete) independent of public upload flow.

## Security Considerations

| Topic | Current state |
|-------|---------------|
| File type validation | MIME/extension via Laravel validation |
| Max size | 4096 KB per file |
| Direct URL access | Public disk — anyone with URL can view |
| Malware scanning | Not implemented |

For production, consider private disk + signed URLs or image processing pipeline.

## Related Docs

- [EVENTS_AND_REGISTRATIONS.md](./EVENTS_AND_REGISTRATIONS.md)
- [DEVELOPMENT_SETUP.md](./DEVELOPMENT_SETUP.md) — storage:link step
- [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)

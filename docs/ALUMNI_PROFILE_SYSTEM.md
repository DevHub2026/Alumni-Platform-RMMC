# Alumni Profile System

## Purpose

Stores extended graduate information beyond the core `users` account and powers the **public alumni directory** with search.

## Components

| Layer | Location |
|-------|----------|
| Model | `app/Models/AlumniProfile.php` |
| Controller | `app/Http/Controllers/AlumniProfileController.php` |
| Views | `resources/views/alumni/` |
| Admin | `app/Filament/Resources/AlumniProfiles/AlumniProfileResource.php` |

## Data Model

**Table:** `alumni_profiles`  
**Relationship:** `User` hasOne `AlumniProfile`; `AlumniProfile` belongsTo `User`

| Field | Purpose |
|-------|---------|
| `student_id` | Institutional ID |
| `course` | Program of study |
| `graduation_year` | Cohort year |
| `phone`, `address` | Contact |
| `current_job`, `company` | Employment |
| `linkedin_url`, `portfolio_url` | Professional links |
| `profile_photo` | Avatar path (public disk) |
| `bio` | Narrative (max 1000 chars) |
| `skills` | Comma or free-text skills (max 500 chars) |

## Routes

| Method | URI | Name | Auth |
|--------|-----|------|------|
| GET | `/alumni` | `alumni.index` | Public |
| GET | `/profile` | `profile.show` | Yes |
| GET | `/profile/edit` | `profile.edit` | Yes |
| PUT | `/profile` | `profile.update` | Yes |

## Directory (`index`)

- Queries `User::where('role', 'alumni')->with('alumniProfile')`
- Optional `?search=` across name, course, job, company, graduation year
- Pagination: **12 per page**
- View: `alumni/index.blade.php`

## Own Profile (`show`)

- Loads `Auth::user()` and `alumniProfile`
- View: `alumni/profile.blade.php`
- Shows verification status, profile fields, edit link

## Edit & Update

### Validation (`update`)

| Field | Rules |
|-------|-------|
| `graduation_year` | integer, 1990–current year |
| `linkedin_url`, `portfolio_url` | nullable URL |
| `profile_photo` | image jpg/jpeg/png/webp, max 2048 KB |
| `bio` | max 1000 |
| `skills` | max 500 |

### Photo handling

- Stores to `profile-photos/` on `public` disk
- Deletes previous file when replacing

### Persistence

Uses `AlumniProfile::updateOrCreate(['user_id' => $user->id], $data)`.

### Auto-verification

When all three are present after save:

- `course`
- `graduation_year`
- `student_id`

Then: `$user->update(['is_verified' => true])`.

Redirect to `profile.show` with success flash.

## Skills & Portfolio

Added in migration `2026_05_19_165022_add_skills_portfolio_to_alumni_profiles_table.php`:

- `skills` — string column
- `portfolio_url` — validated URL in controller

Displayed on profile views when populated.

## Admin Management (Filament)

**Navigation group:** Users & Profiles  
**Resource:** `AlumniProfileResource`

Admins can list, create, and edit any alumni profile independent of public self-service flow.

## Integration Points

| Feature | Dependency on profile |
|---------|----------------------|
| Posts | `is_verified` unlocked by profile completion |
| Search | Alumni section searches profile fields |
| Posts index UI | Prompts unverified users to `profile.edit` |
| Chatbot | Documented as verification path |

## Edge Cases

| Scenario | Behavior |
|----------|----------|
| New user, no profile row | `edit` uses `new AlumniProfile()` for form binding |
| User without profile in directory | Shown with null profile fields |
| Admin user in directory query | Excluded (`role = alumni` filter) |

## Related Docs

- [USER_AND_ROLE_SYSTEM.md](./USER_AND_ROLE_SYSTEM.md)
- [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)

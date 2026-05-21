# Feature Walkthrough — Presentation Format

Each section is structured for slides or oral explanation: **purpose**, **how it works**, **technical summary**, **user benefits**.

---

## 1. Authentication & Account Management

### Purpose
Securely identify users, separate alumni from administrators, and enforce account status (active vs suspended).

### How it works
- Alumni **register** with name, email, password (Breeze).
- **Login** creates a session; suspended users are logged out immediately with a reason message.
- **Password reset** and **email verification** follow Laravel Breeze flows.
- Default role on signup: **alumni**.

### Technical implementation
- `routes/auth.php`, `app/Http/Controllers/Auth/*`
- `LoginRequest` with rate limiting (5 attempts)
- `AuthenticatedSessionController` suspension check
- Session driver: database (per `.env.example`)

### User benefits
- Familiar, trustworthy login experience  
- Clear feedback when accounts are suspended  
- Password recovery without admin help  

---

## 2. Alumni Profile & Directory

### Purpose
Represent graduates beyond a login account and make them discoverable by peers and the institution.

### How it works
- Alumni edit profile at `/profile/edit`: academics, job, links, bio, skills, photo.
- **Auto-verification** when `course`, `graduation_year`, and `student_id` are saved.
- Public **directory** at `/alumni` with search and pagination.

### Technical implementation
- Models: `User`, `AlumniProfile` (hasOne)
- Controller: `AlumniProfileController`
- `updateOrCreate` on profile; photo on `public` disk → `profile-photos/`
- Filament: `AlumniProfileResource` for admin edits

### User benefits
- Professional presence for networking  
- Verification unlocks posting — incentive to complete profile  
- Search helps find batchmates and industry peers  

---

## 3. User Verification System

### Purpose
Ensure posters have identifiable academic credentials without manual approval for every field.

### How it works
- `users.is_verified` boolean.
- Set automatically on profile save when three core fields exist.
- Admins can toggle manually in Filament.
- UI shows verified state; unverified users see “Complete Profile to Post.”

### Technical implementation
- `AlumniProfileController@update` sets `is_verified`
- `PostController@create/store` checks `Auth::user()->is_verified`
- `UserResource` toggle in Filament

### User benefits
- Trust in community content  
- Low admin workload compared to manual approval per user  

---

## 4. Announcements

### Purpose
Distribute official school news and updates to all alumni.

### How it works
- Admins create announcements in Filament with publish toggle.
- Public lists and detail pages show **published only**.
- Homepage shows latest three previews.

### Technical implementation
- Model: `Announcement` (`is_published`)
- `AnnouncementController` with `abort_if` on unpublished show
- `AnnouncementResource` (Filament, Content group)

### User benefits
- Single trusted source for institutional communication  
- No confusion with unofficial posts  

---

## 5. Events & Registrations

### Purpose
Organize reunions and activities with optional capacity limits and attendance tracking.

### How it works
- Admins publish events with date, location, slots (`0` = unlimited).
- Alumni register/unregister on event detail page.
- Past events cannot accept new registrations.
- Registrations stored per user per event (unique).

### Technical implementation
- Models: `Event`, `EventRegistration`
- `EventController`: slot check, duplicate check, `status = confirmed`
- Filament: `EventResource`, `EventRegistrationResource`

### User benefits
- Easy RSVP without phone calls or spreadsheets  
- Fair capacity enforcement  
- Clear upcoming vs past archive  

---

## 6. Community Posts

### Purpose
Peer-to-peer updates: careers, achievements, opportunities, reunions.

### How it works
- Verified alumni create posts with category, text, optional image.
- Feed filters by category; only `visible` posts shown.
- Authors edit/delete own posts.

### Technical implementation
- Model: `Post` with `CATEGORIES`, `scopeVisible`
- `PostController` CRUD + validation
- Images: `post-images/` on public disk
- Filament moderation on status

### User benefits
- LinkedIn-style engagement within school community  
- Structured categories improve feed relevance  

---

## 7. Comments

### Purpose
Discussion and feedback on posts.

### How it works
- Authenticated users comment on any visible post.
- Post owner notified (except self-comments).
- Users delete only their own comments.

### Technical implementation
- Model: `PostComment`
- `PostController@comment`, `deleteComment`
- `PostCommentNotification` → `notifications` table

### User benefits
- Dialogue on achievements and opportunities  
- Awareness when others engage with your content  

---

## 8. Reactions

### Purpose
Lightweight engagement without writing a full comment.

### How it works
- Three types: like, celebrate, support.
- One reaction per user per post; click again to remove or change type.
- Counts returned as JSON.

### Technical implementation
- Model: `PostReaction` with unique `(post_id, user_id)`
- `POST /posts/{post}/react` → JSON
- Frontend fetch in `posts/show.blade.php`

### User benefits
- Quick acknowledgment  
- Smooth UX without page reload  

---

## 9. Content Flagging & Moderation

### Purpose
Community reporting plus admin oversight for inappropriate content.

### How it works
- Users flag others’ posts with reason + optional details.
- At **3 flags**, `is_flagged` set true.
- Admins approve, hide, or remove via Posts or Flagged Posts resources.

### Technical implementation
- Model: `PostFlag` with `REASONS` enum
- Unique flag per user per post
- `PostResource` / `PostFlagResource` actions

### User benefits
- Safer community  
- Scalable moderation — admins focus on reported content  

---

## 10. In-App Notifications

### Purpose
Alert post owners when someone comments.

### How it works
- Bell icon in navbar; polls unread count.
- Dropdown preview; full page at `/notifications`.
- Opening index marks all as read.

### Technical implementation
- `PostCommentNotification` (database channel)
- `NotificationController` JSON + index
- Alpine `notificationBell()` in `layouts/app.blade.php`

### User benefits
- Timely awareness of engagement  
- No email setup required for MVP  

---

## 11. Global Search

### Purpose
Find alumni, posts, events, and announcements from one place.

### How it works
- Minimum 2 characters; searches name, profile fields, titles, bodies.
- Returns up to 6 alumni, 6 posts, 4 events, 4 announcements.

### Technical implementation
- `SearchController@index`
- SQL `LIKE` queries with `whereHas` on profiles

### User benefits
- Faster discovery than browsing each section  

---

## 12. Event Gallery & Media

### Purpose
Share event photos and preserve institutional memory.

### How it works
- Galleries grouped by event.
- Upload: admin anytime; verified alumni if registered (confirmed) for that event.
- Multi-file upload with optional captions.

### Technical implementation
- Model: `Gallery`
- `GalleryController` permission logic + `store` validation
- Storage: `gallery/` on public disk

### User benefits
- Shared memories after events  
- Permission rules reduce irrelevant uploads  

---

## 13. AI Chatbot Assistant

### Purpose
Help alumni navigate features and policies without reading full documentation.

### How it works
- Floating widget for logged-in users.
- Sends message to Laravel → OpenRouter API → reply in chat UI.
- System prompt lists real routes and verification rules.

### Technical implementation
- `ChatbotController@ask`
- `config/services.php` → `GEMINI_API_KEY`, `GEMINI_MODEL`
- `components/chatbot.blade.php` (Alpine)

### User benefits
- Lower support burden on staff  
- 24/7 self-service guidance  

---

## 14. Filament Admin Panel

### Purpose
Complete operational control for school staff without custom admin code per entity.

### How it works
- Login at `/admin` (admin role only).
- Dashboard widgets + grouped resources for users, content, posts, flags.
- Actions: suspend, verify, publish, moderate.

### Technical implementation
- `AdminPanelProvider`, 8 resources, 2 custom widgets
- `User::canAccessPanel()`

### User benefits
- Efficient content and user management  
- Professional admin UX out of the box  

---

## 15. Account Suspension

### Purpose
Enforce conduct policy without deleting user records.

### How it works
- Admin suspends with required reason.
- Login fails with message showing reason.
- Unsuspend clears flag and reason.

### Technical implementation
- `users.is_suspended`, `suspension_reason`
- `UserResource` suspend/unsuspend actions
- `AuthenticatedSessionController@store`

### User benefits
- Institution protects community integrity  
- Transparent communication to affected users  

---

## Feature Matrix (Slide-Ready)

| Feature | Alumni | Guest | Admin |
|---------|--------|-------|-------|
| View announcements/events/posts | ✓ | ✓ | ✓ |
| Directory search | ✓ | ✓ | ✓ |
| Edit own profile | ✓ | — | ✓ (Filament) |
| Create posts | Verified | — | ✓ |
| Comment / react / flag | ✓* | — | ✓ |
| Event register | ✓ | — | ✓ |
| Gallery upload | Verified** | — | ✓ |
| Chatbot | ✓ | — | ✓ |
| Filament panel | — | — | ✓ |

\*Login required.  
\*\*Verified + confirmed event registration (or admin).

---

## Related Materials

- [PRESENTATION_SCRIPT.md](./PRESENTATION_SCRIPT.md)  
- [SYSTEM_DEMONSTRATION_FLOW.md](./SYSTEM_DEMONSTRATION_FLOW.md)  
- [PANEL_DEFENSE_NOTES.md](./PANEL_DEFENSE_NOTES.md)  
- `/docs` — full technical documentation

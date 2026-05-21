# Alumni Platform — Presentation Narration Script

**Estimated duration:** 18–25 minutes (adjust pacing for live demo segments)  
**Audience:** Faculty panel, stakeholders, or technical reviewers  
**Presenter tip:** Pause after each major section for questions or a short live demo.

---

## Opening (1–2 minutes)

Good morning/afternoon, everyone. Thank you for being here today.

My name is **[Your Name]**, and I am presenting the **Alumni Platform** — a web-based community management system developed for **Ramon Magsaysay Memorial College**.

This project addresses a real need: after graduation, alumni often lose touch with their school and with each other. Our platform gives the institution a central place to share official news and events, while giving graduates tools to reconnect, showcase their journeys, and participate in the alumni community online.

---

## Project Introduction (1 minute)

The Alumni Platform is a **full-stack web application** built on **Laravel 13** with **PHP 8.3**. It combines a modern public-facing website for alumni with a dedicated **administrative back office** powered by **Filament**.

On one side, alumni can register, build profiles, browse a directory, register for events, share posts, and receive notifications. On the other side, school administrators can publish announcements, manage events, moderate community content, and oversee user accounts — all without writing code.

---

## Project Purpose (1–2 minutes)

The purpose of this system is threefold.

**First**, to **strengthen institutional engagement** — announcements and events keep alumni informed about reunions, seminars, and school updates.

**Second**, to **enable peer networking** — the alumni directory, social posts, and comments help graduates find classmates, share career updates, and support one another.

**Third**, to **provide governance and trust** — verification, content flagging, and admin moderation ensure the community remains credible and appropriate for an educational institution.

This is not a generic social network. It is purpose-built for an alumni association, with clear roles, published content controls, and school-branded presentation.

---

## Target Users (1 minute)

Our platform serves two primary user groups.

**Alumni** are graduates who register on the public site. They can complete extended profiles, search the directory, register for events, upload event photos when eligible, create posts after verification, comment, react, and use an AI assistant for help navigating the system.

**Administrators** are institutional staff who manage the platform through the Filament admin panel at `/admin`. They create announcements and events, review flagged posts, verify or suspend accounts, and monitor platform statistics on the dashboard.

Guests — visitors without an account — can still browse published announcements, events, the alumni directory, gallery, and visible posts, which supports transparency and recruitment of new members.

---

## Technologies Used (2 minutes)

We chose a proven, maintainable technology stack.

**Backend:** Laravel 13 provides routing, authentication, validation, the Eloquent ORM, and a secure request lifecycle. PHP 8.3 delivers modern language features and performance.

**Admin panel:** Filament 5 gives us a professional CRUD interface with tables, forms, filters, and dashboard widgets — reducing custom admin development time.

**Frontend:** Blade templating keeps rendering server-side and SEO-friendly. **Tailwind CSS** provides consistent, responsive styling. **Alpine.js** adds lightweight interactivity for the notification bell, chatbot, and post reactions without a heavy JavaScript framework.

**Build tooling:** Vite bundles our CSS and JavaScript for fast development and optimized production builds.

**Database:** The schema is **SQLite-compatible for development** and **MySQL-ready for production**, using standard Laravel migrations.

**Testing and quality:** PHPUnit supports automated tests; Laravel Pint enforces code style.

**External integration:** The chatbot connects to **OpenRouter** using a configurable Gemini-class model, with API keys kept server-side only.

---

## System Architecture (2–3 minutes)

Architecturally, this is a **monolithic MVC application** — a single deployable unit that is appropriate for our scale and simplifies maintenance for the institution.

**Requests** enter through Laravel’s router defined in `routes/web.php` and `routes/auth.php`. Middleware handles authentication, guest protection, and email verification where applied.

**Controllers** coordinate business logic: loading data from models, validating input, enforcing permissions, and returning Blade views or JSON for AJAX features.

**Eloquent models** represent users, alumni profiles, posts, events, galleries, and related entities, with explicit relationships and database foreign keys.

**Two presentation channels** share the same database:

1. The **public alumni site** — custom layout with navigation for Home, Announcements, Events, Directory, Gallery, and Posts.
2. The **Filament admin panel** — resource-based management grouped into Users & Profiles, Content, Community, and Moderation.

There is no separate mobile API in the current version; the web interface is the primary product. Authorization is enforced in controllers and through the User model’s `canAccessPanel` method for admins — a deliberate MVP choice documented for future Policy classes.

---

## Key Features Overview (1 minute)

Before we walk through demonstrations, here is a concise map of what the system delivers:

- Alumni profiles and searchable directory  
- Official announcements  
- Events with registration and capacity limits  
- Event photo galleries with permission rules  
- Community posts with categories, images, comments, and reactions  
- User-driven flagging and admin moderation  
- Database notifications for comments  
- Global search across major content types  
- AI chatbot for authenticated alumni  
- Account verification and suspension controls  

Each feature connects to a real alumni-association workflow rather than existing in isolation.

---

## Admin Panel (2 minutes)

*[Optional: switch to `/admin` demo]*

Administrators access the platform at **`/admin`**, a Filament-powered dashboard.

The **Stats Overview widget** shows total alumni, verified alumni, post counts including flagged items, upcoming events, registrations, and published announcements — giving leadership a quick health snapshot.

The **Alumni Growth chart** displays new registrations over the last six months.

**Navigation groups** organize work efficiently:

- **Users & Profiles** — manage accounts, roles, verification, suspend or unsuspend alumni with documented reasons, and edit alumni profiles.
- **Content** — announcements, events, event registrations, and gallery records.
- **Community** — posts with approve, hide, and remove actions.
- **Moderation** — individual flag reports with approve-post or remove-post workflows.

Only users with the **admin** role can enter this panel, enforced by Laravel’s Filament integration on the User model.

This separation ensures alumni never accidentally access destructive operations, while staff have full operational control.

---

## Alumni Networking System (2 minutes)

*[Optional: demo `/alumni` and `/profile`]*

Networking begins with the **alumni profile**. Beyond basic account information, graduates add course, graduation year, student ID, employment, skills, portfolio URL, bio, and profile photo.

When a member completes key fields — course, graduation year, and student ID — the system **automatically verifies** them. Verified alumni receive posting privileges and clearer trust signals in the directory.

The **alumni directory** at `/alumni` supports search by name, course, company, job title, or graduation year, with pagination for performance.

Members manage their own profiles at `/profile` and `/profile/edit` without admin intervention for routine updates — reducing institutional workload.

From a design perspective, verification balances **open registration** with **quality control**: the school does not manually approve every field, but posting requires a completed academic identity.

---

## Posts, Comments, and Reactions (2–3 minutes)

*[Optional: demo `/posts`]*

The **community feed** is the social heart of the platform. Verified alumni create posts in categories such as Career Update, Achievement, Opportunity, Reunion, and General — optionally with images.

All authenticated alumni can **comment** on posts. When someone comments on your post, you receive an **in-app notification** — encouraging conversation without email dependency in the MVP.

**Reactions** — like, celebrate, and support — provide lightweight engagement through a JSON endpoint, so the page updates smoothly without full reloads. Each user has one reaction per post, which can be changed or removed.

**Flagging** allows community self-policing. Users report spam, inappropriate content, harassment, or misinformation. One report per user per post is enforced. When three or more flags accumulate, the post is marked for admin review.

Admins can approve, hide, or remove posts from Filament — completing the moderation loop.

---

## Events and Registrations (2 minutes)

*[Optional: demo `/events`]*

**Events** support reunions, seminars, and networking activities. Admins publish events with title, description, location, date, optional cover image, and slot limits. A slot count of zero means unlimited attendance.

Alumni view upcoming and past events separately. On the event detail page, authenticated users **register** or **unregister** with server-side checks for past dates, capacity, and duplicate registration.

Registrations are stored with a **confirmed** status in the application flow, linking members to events they attended — which also gates **gallery upload** permission for verified alumni.

This connects event management to memories: the event is not only listed — it becomes a hub for photos and follow-up engagement.

---

## Announcements and Notifications (1–2 minutes)

**Announcements** are official communications. Only administrators create them in Filament; the public site shows published items on the homepage preview and full listing pages. Unpublished drafts never leak — the controller returns 404 for unpublished content.

**Notifications** currently focus on **post comments**: when another alumni comments on your post, a database notification appears in the navbar bell, with polling every thirty seconds and a full history page.

This channel is extensible — the Laravel notification system can later add email or push channels without redesigning the core feature.

---

## Gallery and Media System (1–2 minutes)

*[Optional: demo `/gallery`]*

The **gallery** organizes photos by event. The index shows events that have at least one image. Inside an event gallery, photos display with uploader attribution and optional captions.

**Upload rules** protect quality and relevance:

- **Admins** may upload to any published event.  
- **Verified alumni** may upload only if they have a **confirmed registration** for that specific event.

Uploads accept multiple images per request, validated for type and size, stored on the public disk with Laravel’s storage system.

Deletion is restricted to the uploader or an admin — balancing community contribution with accountability.

---

## Chatbot Integration (1–2 minutes)

*[Optional: demo chatbot widget]*

Authenticated alumni see a floating **AI Assistant** powered by **OpenRouter** with a Gemini-class model. The chatbot answers questions about profiles, verification, events, posts, and navigation — grounded in a system prompt that describes actual platform features, reducing hallucinated capabilities.

The API key never reaches the browser; Laravel proxies requests server-side. This demonstrates modern **AI-assisted UX** while maintaining security best practices we document for production hardening, such as rate limiting and sanitized error messages.

---

## Security and Moderation Features (2 minutes)

Security is layered across authentication, authorization, and community governance.

**Authentication** uses Laravel Breeze with hashed passwords, CSRF protection, session regeneration, and optional email verification on the dashboard route.

**Suspension** allows admins to block accounts with a visible reason at login — important for policy enforcement.

**Authorization** combines route middleware, ownership checks on posts and comments, published-content guards, and the verified flag for posting and gallery uploads.

**Moderation** combines user flags, automatic flag markers at three reports, and admin workflows in Filament.

**File uploads** are validated by type and size. **Foreign keys** cascade appropriately to keep data consistent.

We document known improvements — Laravel Policies, rate limits on the chatbot, and protecting routes like post creation with stricter middleware — as part of honest technical maturity, not as afterthoughts.

---

## Future Improvements (1–2 minutes)

Our roadmap is organized in phases.

**Short term:** expanded automated tests, admin seed data, policy-based authorization, chatbot rate limiting, email notifications for announcements and comments, and CI with Pint and PHPUnit.

**Medium term:** queued notifications, moderation audit logs, invitation-based registration, image thumbnails, and optional auto-hide for heavily flagged posts.

**Long term:** full-text search, real-time notifications, a mobile API with Sanctum, cloud storage with CDN, analytics, and optional multi-school support.

These steps move the MVP toward production grade without abandoning the current architecture.

---

## Conclusion (1–2 minutes)

In summary, the Alumni Platform delivers a **complete alumni engagement solution** for Ramon Magsaysay Memorial College: institutional content, peer networking, event participation, moderated social interaction, and administrative control in one cohesive system.

Built on Laravel and Filament with a clear MVC structure, it is maintainable by future developers and deployable on standard PHP hosting.

We have documented the system thoroughly in the project’s `/docs` folder, and we are prepared to demonstrate live workflows for both alumni and administrators.

Thank you for your time and attention. I welcome your questions, feedback, and suggestions.

---

## Closing Line (optional)

*“Our goal is simple: help graduates stay connected to their school and to each other — with technology that is secure, manageable, and built to grow.”*

---

## Appendix: Timing Guide

| Section | Minutes |
|---------|---------|
| Opening + purpose + users | 4–5 |
| Tech + architecture | 4–5 |
| Features + admin + networking | 6–8 |
| Posts, events, gallery, chatbot | 5–7 |
| Security + future + conclusion | 4–5 |
| **Total narration** | **18–25** |
| Live demo (separate) | 10–15 |

*Combine with [SYSTEM_DEMONSTRATION_FLOW.md](./SYSTEM_DEMONSTRATION_FLOW.md) for integrated presentation.*

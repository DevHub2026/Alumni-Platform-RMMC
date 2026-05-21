# System Overview

## Purpose

The **Alumni Platform** is a Laravel 13 web application built for **Ramon Magsaysay Memorial College (RMMC)**. It connects graduates with their institution through profiles, announcements, events, photo galleries, and a community posting system—with administrative oversight via Filament.

## Technology Stack

| Layer | Technology | Version (composer/package) |
|-------|------------|----------------------------|
| Runtime | PHP | ^8.3 |
| Framework | Laravel | ^13.0 |
| Admin UI | Filament | ^5.5 |
| Auth scaffolding | Laravel Breeze | ^2.4 (dev) |
| Public UI | Blade, Tailwind CSS, Alpine.js | Tailwind ^3.1, Alpine ^3.4 |
| Asset bundler | Vite | ^8.0 |
| HTTP client (frontend) | Axios | ^1.15 |
| Testing | PHPUnit | ^12.5 |
| Code style | Laravel Pint | ^1.27 (dev) |
| Default database | SQLite (configurable MySQL) | — |

## High-Level Capabilities

1. **Authentication** — Registration, login, password reset, email verification (Breeze).
2. **Alumni profiles** — Extended profile data, directory search, auto-verification.
3. **Announcements** — Admin-published news (read-only on public site).
4. **Events** — Published events with slot-limited registration.
5. **Gallery** — Event-linked photo uploads with permission rules.
6. **Social posts** — Verified alumni create posts; comments, reactions, flagging.
7. **Moderation** — Admin review of flagged posts via Filament.
8. **Notifications** — Database notifications for post comments.
9. **Global search** — Cross-entity search (alumni, posts, events, announcements).
10. **AI chatbot** — OpenRouter-backed assistant for authenticated users.
11. **User governance** — Role-based access, verification flag, account suspension.

## Architectural Style

- **Monolithic MVC** — Controllers orchestrate Eloquent models and return Blade views or JSON.
- **Dual interface** — Public alumni site (`/`) and admin panel (`/admin`).
- **No dedicated service layer** — Business logic lives in controllers and Filament resource classes.
- **No Laravel Policies** — Authorization is enforced inline in controllers and model methods.
- **Session-based auth** — Standard Laravel `web` guard; no REST API for mobile clients.

## Primary User Types

| Role | Access |
|------|--------|
| **Guest** | Home, announcements, events, gallery, posts (read), alumni directory, search |
| **Alumni** | Above + profile, event registration, posting (if verified), comments, reactions, flags, chatbot, notifications |
| **Admin** | Filament panel + public site; can upload gallery photos broadly; manage all content |

## Key URLs

| Path | Description |
|------|-------------|
| `/` | Public homepage |
| `/login`, `/register` | Authentication |
| `/dashboard` | Breeze dashboard stub (auth + verified) |
| `/admin` | Filament admin panel (admin role only) |
| `/profile` | Alumni profile (auth) |
| `/alumni` | Public directory |
| `/posts` | Community feed |
| `/events` | Event listing |
| `/announcements` | News listing |
| `/gallery` | Event galleries |
| `/search` | Global search |

## Documentation Map

| Document | Focus |
|----------|-------|
| [PROJECT_ARCHITECTURE.md](./PROJECT_ARCHITECTURE.md) | MVC, request lifecycle, layers |
| [FOLDER_STRUCTURE.md](./FOLDER_STRUCTURE.md) | Repository layout |
| [BACKEND_DOCUMENTATION.md](./BACKEND_DOCUMENTATION.md) | Controllers, models, providers |
| [FRONTEND_DOCUMENTATION.md](./FRONTEND_DOCUMENTATION.md) | Blade, Tailwind, Alpine |
| [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md) | Tables, FKs, indexes |
| [ROUTES_REFERENCE.md](./ROUTES_REFERENCE.md) | All web routes |
| [API_REFERENCE.md](./API_REFERENCE.md) | JSON endpoints |
| [AUTHENTICATION_AND_AUTHORIZATION.md](./AUTHENTICATION_AND_AUTHORIZATION.md) | Auth flow, gates |
| [USER_AND_ROLE_SYSTEM.md](./USER_AND_ROLE_SYSTEM.md) | Roles, verification, suspension |
| [FILAMENT_ADMIN_PANEL.md](./FILAMENT_ADMIN_PANEL.md) | Admin resources |
| [DEVELOPMENT_SETUP.md](./DEVELOPMENT_SETUP.md) | Local setup |
| [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) | Production deployment |
| [SECURITY_AND_SCALABILITY_ANALYSIS.md](./SECURITY_AND_SCALABILITY_ANALYSIS.md) | Risks and recommendations |

## Institution Context

Branding and chatbot system prompts reference **Ramon Magsaysay Memorial College**. The public layout footer and hero copy reflect this affiliation.

## Repository Entry Points

- **HTTP kernel config:** `bootstrap/app.php`
- **Web routes:** `routes/web.php`, `routes/auth.php`
- **Admin panel:** `app/Providers/Filament/AdminPanelProvider.php`
- **Primary layout:** `resources/views/layouts/app.blade.php`

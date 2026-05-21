# Project Progress and Future Roadmap

## Current Development Status

**Overall:** Feature-rich **MVP** suitable for pilot deployment with institutional oversight. Core alumni engagement loops are implemented; production hardening and test coverage remain incomplete.

### Completion Assessment

| Module | Status | Notes |
|--------|--------|-------|
| Authentication (Breeze) | ✅ Complete | Suspension hook added |
| Alumni profiles & directory | ✅ Complete | Auto-verification, skills, portfolio |
| Announcements | ✅ Complete | Public read, Filament write |
| Events & registration | ✅ Complete | Slots, past/upcoming split |
| Gallery | ✅ Complete | Permission rules enforced |
| Posts & categories | ✅ Complete | Images, verification gate |
| Comments | ✅ Complete | Owner delete |
| Reactions | ✅ Complete | JSON toggle API |
| Flagging & moderation | ✅ Complete | 3-flag threshold, Filament review |
| Notifications | ⚠️ Partial | Comment-only, database channel |
| Global search | ✅ Complete | 4 entity types |
| Chatbot | ✅ Complete | OpenRouter; needs rate limit |
| Filament admin | ✅ Complete | 8 resources, 2 widgets |
| Email notifications | ❌ Not started | MAIL_MAILER=log |
| API for mobile | ❌ Not planned | — |
| Domain tests | ❌ Minimal | Breeze tests only |
| Documentation (README) | ❌ Default Laravel | This `/docs` set addresses gap |

### Migration Timeline (Feature Evolution)

| Date (filename) | Feature |
|-----------------|---------|
| 2026-04-15 | Core schema: roles, profiles, announcements, events, registrations, galleries |
| 2026-05-14 | Posts, flags, comments, `is_verified` |
| 2026-05-19 | Post images, reactions, notifications, suspension, skills/portfolio |

---

## Known Issues & Inconsistencies

| Issue | Severity | Suggested fix |
|-------|----------|---------------|
| Breeze `ProfileController` not routed | Low | Remove or wire account settings at `/account` |
| `dashboard` is placeholder | Low | Redirect to `home` or alumni profile |
| `GET /posts/create` public | Medium | Add `auth` middleware |
| `ProfileTest` may not match routes | Low | Update tests for alumni profile |
| No `GEMINI_*` in `.env.example` | Low | Document variables |
| Chatbot exposes API errors | Medium | Generic error responses |
| No admin seeder | Low | `DatabaseSeeder` admin + sample data |

---

## Future Roadmap

### Phase 1 — Production readiness (short term)

- [ ] Complete `.env.example` and project README
- [ ] Admin + sample alumni seeder
- [ ] Fix route middleware gaps (`posts.create`, auth on sensitive GETs)
- [ ] Chatbot rate limiting and safe error messages
- [ ] Laravel Policies for posts, gallery, events
- [ ] Expand PHPUnit coverage (verification, slots, flags, gallery ACL)
- [ ] CI pipeline: tests + Pint + `npm run build`
- [ ] Email verification alignment with posting policy

### Phase 2 — Engagement & operations (medium term)

- [ ] Queued notifications (`ShouldQueue`)
- [ ] Email alerts: new announcement, event reminder, comment digest
- [ ] Admin notification on post flags (threshold or any flag)
- [ ] Auto-hide post when `is_flagged` (configurable)
- [ ] Moderation audit log
- [ ] Alumni invitation / approval registration workflow
- [ ] Rich text sanitizer for posts/announcements
- [ ] Image thumbnails and optimization pipeline

### Phase 3 — Scale & product expansion (long term)

- [ ] Full-text search (Meilisearch/Algolia)
- [ ] Real-time notifications (Laravel Reverb / Pusher)
- [ ] REST or GraphQL API + mobile app auth (Sanctum)
- [ ] S3 + CDN media storage
- [ ] Multi-tenant support (multiple schools)
- [ ] Analytics dashboard (engagement metrics)
- [ ] Mentorship / job board modules
- [ ] SSO integration (Google Workspace, Microsoft)

---

## Technical Direction Recommendations

### Architecture

Introduce `app/Services/` for:

- `PostModerationService` — flag threshold, status transitions
- `VerificationService` — profile completion rules
- `GalleryPermissionService` — upload eligibility

### Roles

Consider:

- `moderator` role — Filament access limited to Community + Moderation groups
- `super_admin` — user management only

### Data

- Archive old notifications and soft-delete posts instead of status enum only
- Add `deleted_at` soft deletes on user-generated content

---

## Success Metrics (Suggested for Institution)

| Metric | Source |
|--------|--------|
| Registered alumni | `users.role=alumni` |
| Verified rate | `is_verified` ratio |
| Event registration rate | registrations / events |
| Post engagement | comments + reactions per post |
| Moderation backlog | `is_flagged` count in Filament |
| Chatbot usage | Log requests per day (not yet logged) |

---

## Documentation Maintenance

This `/docs` folder should be updated when:

- New migrations alter schema
- Routes or policies change
- Filament resources added/removed
- External integrations change (chatbot provider)

**Index:** See [SYSTEM_OVERVIEW.md](./SYSTEM_OVERVIEW.md) for full document map.

---

## Version Reference

Documentation generated from repository state:

- Laravel ^13.0
- Filament ^5.5
- PHP ^8.3
- 18 migrations, 10 models, 20 HTTP controllers, 8 Filament resources

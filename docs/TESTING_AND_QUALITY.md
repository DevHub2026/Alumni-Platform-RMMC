# Testing and Quality

## PHPUnit Configuration

**File:** `phpunit.xml`

| Setting | Value |
|---------|-------|
| Bootstrap | `vendor/autoload.php` |
| Test suites | `tests/Unit`, `tests/Feature` |
| Coverage include | `app/` directory |
| DB (testing) | SQLite `:memory:` |
| Session | `array` |
| Cache | `array` |
| Queue | `sync` |

## Running Tests

```bash
composer test
```

Equivalent to:

```bash
php artisan config:clear --ansi
php artisan test
```

Direct:

```bash
./vendor/bin/phpunit
```

## Existing Test Coverage

### Feature tests (`tests/Feature/`)

| File | Covers |
|------|--------|
| `Auth/AuthenticationTest.php` | Login/logout |
| `Auth/RegistrationTest.php` | User registration |
| `Auth/EmailVerificationTest.php` | Verification flow |
| `Auth/PasswordResetTest.php` | Reset email + reset |
| `Auth/PasswordConfirmationTest.php` | Confirm password |
| `Auth/PasswordUpdateTest.php` | Password change |
| `ProfileTest.php` | Breeze profile update (**may fail** — routes point to alumni profile) |
| `ExampleTest.php` | Scaffold |

### Unit tests (`tests/Unit/`)

| File | Covers |
|------|--------|
| `ExampleTest.php` | Scaffold only |

## Coverage Gaps

**No automated tests** for:

- Alumni profile CRUD and auto-verification
- Posts, comments, reactions, flags
- Events and registration slot logic
- Gallery upload permissions
- Notifications
- Chatbot controller
- Suspension on login
- Filament resources
- Search controller
- Published content 404 rules

## Recommended Test Additions

### Feature examples

```php
// Post creation requires verification
public function test_unverified_alumni_cannot_store_post(): void
{
    $user = User::factory()->create(['is_verified' => false]);
    $this->actingAs($user)
        ->post(route('posts.store'), [...])
        ->assertRedirect();
}

// Event registration respects slots
public function test_registration_blocked_when_event_full(): void
{
    // ...
}
```

### Filament

Use Livewire testing helpers from Filament docs or HTTP tests against admin routes with admin user.

## Laravel Pint (Code Style)

**Package:** `laravel/pint` (dev)

Run formatter:

```bash
./vendor/bin/pint
```

Check without fixing:

```bash
./vendor/bin/pint --test
```

No `pint.json` in repository — uses default Laravel preset.

### CI integration example

```yaml
- name: Run Pint
  run: vendor/bin/pint --test
```

## Static Analysis (Not Configured)

Consider adding:

- **PHPStan / Larastan** — level 5+ for model/controller types
- **PHP CS Fixer** — redundant if using Pint exclusively

## Quality Gates Checklist

| Gate | Command | Status |
|------|---------|--------|
| Tests pass | `composer test` | Breeze only |
| Code style | `pint --test` | Available |
| Security audit | `composer audit` | Manual |
| Build assets | `npm run build` | Required for deploy |

## Test Database

PHPUnit uses in-memory SQLite — migrations run per test case via `RefreshDatabase` trait in Breeze tests.

For MySQL-specific features (enum quirks), add parallel CI job with MySQL service container.

## Related Docs

- [DEVELOPMENT_SETUP.md](./DEVELOPMENT_SETUP.md)
- [PROJECT_PROGRESS_AND_FUTURE_ROADMAP.md](./PROJECT_PROGRESS_AND_FUTURE_ROADMAP.md)

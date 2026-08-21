# AGENTS.md

## Project direction

This base template (Laravel 12 + Sneat admin) is being converted into **KostKu**, a kost (boarding-room) rental platform. Standing rules for all work:

- Do **not** scaffold a new Laravel project or replace the existing admin dashboard, auth, RBAC, or UI components. Extend/refactor what exists; reuse before rewriting.
- Existing roles are `super-admin` / `admin` / `user` (+ Visitor). Map new roles (e.g., pemilik/penyewa) onto the existing `roles` table via `RoleAndMenuSeeder` — do not add a new role system.
- There is currently **no public landing route** (`/` is the auth-protected dashboard). Sneat front-page blades exist at `resources/views/pages/front-pages/` (incl. `landing-page.blade.php`) — reuse them as the base for public pages.
- Public pages must look like a realistic property-rental site: clean Bootstrap 5, one primary color, no neon/glassmorphism/excessive animation/"AI" branding.
- Booking logic: prices always computed server-side from DB (never trust request values); wrap booking creation + room-status changes in `DB::transaction`. Payments schema should stay Midtrans-ready but no gateway integration yet.

## Commands

```bash
composer dev                        # server + queue:listen + pail + vite (concurrently)
composer test                       # config:clear + artisan test (Pest)
php artisan test tests/Feature/UserPostTest.php   # single file
php artisan test --filter=name      # single test
php artisan migrate:fresh --seed    # reset DB
npm run build                       # REQUIRED before tests that render Blade pages
vendor/bin/pint --test              # code style (laravel/pint)
php artisan l5-swagger:generate     # regenerate API docs after annotation edits
```

- Tests run on SQLite `:memory:` via `phpunit.xml` — no DB setup needed.
- **Gotcha:** Feature tests rendering full pages fail with `ViteManifestNotFoundException` if `public/build` is missing. Run `npm run build` first, or use `$this->withoutVite()` in the test.
- Default seeded logins (password: `password`): `superadmin@gmail.com`, `admin@gmail.com`, `user@gmail.com`.

## Architecture (Service–Repository)

Flow: Controller → FormRequest → Service → Repository (extends `BaseRepository`/`BaseService`). Contract lives in `app/Contracts/Repositories/`, concrete in `app/Repositories/`.

- **Bindings are manual**: each contract→concrete pair must be registered in `AppServiceProvider::register()`. `make:feature` tries to auto-insert it — verify it actually landed there.
- **Scaffolding**: `php artisan make:feature Name` (supports `Sub/Name`) generates Model, migration, contract+repo, service, controller, request, and 4 Blade views under `resources/views/pages/<kebab-name>/`.
- **RBAC**: `User.role_id` → `Role.slug`; permission = role↔menu pivot with `can_{create,read,update,delete}`. Routes use `->middleware('check.permission:{menu}.index')`.
  - `CheckPermission` maps route-name suffix → action and **fails closed (403)** on unknown suffixes — custom actions (e.g., `products.export.excel`) must be added to its `$routeNameMap`.
  - `super-admin` bypasses everything (`Gate::before` + `hasPermission`). In tests, use a super-admin user to skip permission setup.
- **Menus are DB-driven**: sidebar renders from role→menu pivot. New modules appear only after adding entries in `database/seeders/RoleAndMenuSeeder.php` and re-running that seeder.
- **Audit trail**: add `App\Traits\LogsActivity` to models for automatic before/after change logging.
- **Settings/branding**: `@setting('key')` Blade directive / `get_setting()` helper; `config('variables.*')` is populated from DB settings in `AppServiceProvider::boot()`.
- **Uploads**: use `FileUploadService` (records into `Media`), not raw `$request->file()->store()`.
- **Alerts**: redirect `->with('success', '...')` triggers Toastr automatically; SweetAlert2 confirms via `.delete-record` + `window.AlertHandler` (see `ALERT_SYSTEM_GUIDE.md`).

## Frontend quirks

- Styling is **Bootstrap 5 (Sneat)** + jQuery + DataTables. Tailwind v4 appears in package.json but is **not** the styling system — don't use Tailwind classes in blades.
- Vite entry points are globbed in `vite.config.js`: page JS goes in `resources/assets/js/*.js`, vendor libs under `resources/assets/vendor/`. CRUD-table JS lives in `resources/js/laravel-user-management.js`.
- Dark mode exists in the Sneat layout — keep it working in anything you touch.

## Environment notes

- Windows dev machine; app timezone `Asia/Makassar`.
- **DB trap:** `.env` comments out `DB_CONNECTION` but sets `DB_DATABASE=harmonyhome`, so the sqlite fallback uses the root-level file `harmonyhome` as the live dev database (MySQL-style host/port lines are inert). Don't delete it; don't "fix" `.env` without re-seeding. The sibling `laravel` file is a leftover artifact.
- Tests are unaffected (SQLite `:memory:` via `phpunit.xml`).

## Repo conventions & known debt

- Update `CHANGELOG.md` (Keep a Changelog + semver): add entries under `## [Unreleased]`.
- `REFACTOR_BACKLOG.md` tracks known issues — read it before "fixing" oddities (e.g., impersonate route lacks permission middleware; some controllers validate inline instead of FormRequests).
- Deeper docs: `DEVELOPMENT_GUIDE.md` (adding modules), `FEATURES_GUIDE.md`, `ACTIVITY_LOG_GUIDE.md`, `ALERT_SYSTEM_GUIDE.md`.

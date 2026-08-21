  # AGENTS.md

  ## Project direction

  This base template (Laravel 12 + Sneat admin) is being converted into **KostKu**, a kost (boarding-room) rental platform. Standing rules for all work:

  - Do **not** scaffold a new Laravel project or replace the existing admin dashboard, auth, RBAC, or UI components. Extend/refactor what exists; reuse before rewriting.
  - Roles: `super-admin` / `admin` / `user` / `visitor` + KostKu's `pemilik` / `penyewa`, all in the existing `roles` table via `RoleAndMenuSeeder`. Do not add a new role system.
  - Public pages must look like a realistic property-rental site: clean Bootstrap 5, one primary color, no neon/glassmorphism/excessive animation/"AI" branding.
  - Booking logic: prices always computed server-side from DB (never trust request values); wrap booking creation + room-status changes in `DB::transaction` + `lockForUpdate` on the room. Payments schema is Midtrans-ready (`order_id`, `snap_token`) but no gateway integration yet.
  - **Guest vs member**: bookings carry `booking_type` (`member`/`guest`); guests have `user_id = NULL` and their data lives in `guest_*` columns on `bookings`. Never auto-create accounts for guests. Guest access to transactions is only via booking code + email/phone verification or the unguessable `access_token` (invoice/status URLs) — never expose DB IDs.

  ## Commands

  ```bash
  composer dev                        # server + queue:listen + pail + vite (concurrently)
  composer test                       # config:clear + artisan test (Pest)
  php artisan test tests/Feature/GuestBookingTest.php   # single file
  php artisan test --filter=name      # single test
  php artisan migrate:fresh --seed    # reset DB
  npm run build                       # REQUIRED before tests that render Blade pages
  vendor/bin/pint --test              # code style (laravel/pint) — run only on touched files; legacy code is not pint-clean
  php artisan l5-swagger:generate     # regenerate API docs after annotation edits
  php artisan bookings:expire         # expire stale pending bookings (scheduled hourly via routes/console.php)
  ```

  - Tests run on SQLite `:memory:` via `phpunit.xml` — no DB setup needed. Seeded logins (password `password`): `superadmin@gmail.com`, `admin@gmail.com`, `user@gmail.com`, `pemilik1@gmail.com`, `penyewa01@gmail.com`.
  - **Gotcha:** Feature tests rendering full pages fail with `ViteManifestNotFoundException` if `public/build` is missing. Run `npm run build` first, or use `$this->withoutVite()`.

  ## Architecture (Service–Repository)

  Flow: Controller → FormRequest → Service → Repository (extends `BaseRepository`/`BaseService`). Contract in `app/Contracts/Repositories/`, concrete in `app/Repositories/`.

  - **Bindings are manual**: each contract→concrete pair must be registered in `AppServiceProvider::register()`. `make:feature` tries to auto-insert it — verify it actually landed there.
  - **Scaffolding**: `php artisan make:feature Name` generates Model, migration, contract+repo, service, controller, request, and 4 Blade views under `resources/views/pages/<kebab-name>/`. It skips existing files and does NOT create models/migrations for KostKu domain entities.
  - **RBAC**: `User.role_id` → `Role.slug`; permission = role↔menu pivot with `can_{create,read,update,delete}`. Routes use `->middleware('check.permission:{menu}.index')`.
    - `CheckPermission` maps route-name suffix → action and **fails closed (403)** on unknown suffixes — custom actions MUST be added to its `$routeNameMap` (already covers `booking.confirm|reject|cancel|activate|complete` and `payment.mark-paid`).
    - `super-admin` bypasses everything. In tests, use a super-admin user to skip permission setup.
  - **Menus are DB-driven**: sidebar renders from role→menu pivot; menu rows store raw paths. New modules appear only after adding entries in `RoleAndMenuSeeder.php` and re-running it.
  - **Audit trail**: add `App\Traits\LogsActivity` to models. **Settings**: `@setting('key')` / `get_setting()`; `config('variables.*')` populated from DB in `AppServiceProvider::boot()`.
  - **Uploads**: use `FileUploadService` (records into `Media`), not raw `$request->file()->store()`.
  - **Alerts**: `->with('success', ...)` triggers Toastr; SweetAlert2 confirms via `.delete-record` + `window.AlertHandler.confirm(...)` + jQuery AJAX DELETE with `Accept: application/json` (see kost/kamar index blades for the exact pattern).

  ## Routing map (important)

  - **Public (no auth)**: `/` landing, `/kost`, `/kost/{slug}`, `/kost/{slug}/kamar/{kamar}`, `/tentang`, `/kontak`, `/cara-kerja`, `/cek-booking` (+POST), `/invoice/{token}`; guest flow under `/booking/guest/*` (`checkout/{kamar}`, `store/{kamar}`, `success/{token}`, `status/{token}`). Controllers live in `App\Http\Controllers\Front\*`, views in `resources/views/pages/front/`, layout `layouts/layoutPublic.blade.php` (own navbar/footer partials in `resources/views/front/partials/`).
  - **Member (auth)**: `/dashboard` (moved off `/`), `/profile`.
  - **Admin**: KostKu domain resources live under URL prefix `/admin/*` (`/admin/kost`, `/admin/kamar`, `/admin/booking`, ...) but keep **original route names** (`kost.index`, etc.) so RBAC middleware params and seeded menu slugs stay valid. Legacy modules (user/role/menu/products/settings...) also sit under `/admin` now except profile/dashboard.
  - Login redirects go to `/dashboard` (`bootstrap/app.php` `redirectTo(users:)`).

  ## Booking domain rules

  - Statuses: booking `pending→confirmed→active→completed` (+`cancelled`/`rejected`/`expired`); kamar `available/reserved/occupied/maintenance`. Room sync: create→reserved, activate→occupied, terminal states→available (only if still reserved). Only `available` rooms bookable; re-checked inside the transaction, not just at checkout render.
  - Codes: `BK-YYYYMMDD-XXXXX` / `INV-YYYYMM-XXXXX` with random suffix from an ambiguity-free alphabet (see `BookingService`); never expose auto-increment IDs publicly.
  - Pending bookings expire after 24h (`BookingService::PENDING_TTL_HOURS`) via `bookings:expire` (hourly scheduler); expiry releases the room and expires pending payments.
  - Emails: `BookingCreatedMail` sent on creation, wrapped in try/catch (MAIL_MAILER=log locally).

  ## Frontend quirks

  - Styling is **Bootstrap 5 (Sneat)** + jQuery + DataTables. Tailwind v4 appears in package.json but is **not** wired into Vite — don't use Tailwind classes in blades.
  - Vite entry points are globbed in `vite.config.js`: page JS in `resources/assets/js/*.js`, vendor libs under `resources/assets/vendor/`.
  - Dark mode exists in the Sneat layout — keep it working in anything you touch. Public layout uses Sneat front sections (`$isFront = true`).

  ## Environment notes

  - Windows dev machine; app timezone `Asia/Makassar`.
  - **DB trap:** `.env` comments out `DB_CONNECTION` but sets `DB_DATABASE=harmonyhome`, so the sqlite fallback uses the root-level file `harmonyhome` as the live dev database (MySQL-style host/port lines are inert). Don't delete it; don't "fix" `.env` without re-seeding. The sibling `laravel` file is a leftover artifact. A planned switch of the dev DB to MySQL is pending — audit migrations for MySQL compat before flipping `DB_CONNECTION`.
  - Tests are unaffected (SQLite `:memory:` via `phpunit.xml`). Note: SQLite ignores `lockForUpdate`, so concurrency tests there only exercise the availability re-check, not real row locks.

  ## Repo conventions & known debt

  - Update `CHANGELOG.md` (Keep a Changelog + semver): add entries under `## [Unreleased]`.
  - `REFACTOR_BACKLOG.md` tracks known issues — read it before "fixing" oddities (e.g., impersonate route lacks permission middleware; some controllers validate inline instead of FormRequests).
  - Deeper docs: `DEVELOPMENT_GUIDE.md`, `FEATURES_GUIDE.md`, `ACTIVITY_LOG_GUIDE.md`, `ALERT_SYSTEM_GUIDE.md`.
  - Known in-flight work: several `GuestBookingTest` cases were failing at last run (double-booking count assertion, member total assertion, expireStale count, one public-page 500) — investigate before trusting the suite; root causes may be test assumptions (seeded data reuse across assertions) rather than service logic.

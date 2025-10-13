## Quick context

This repository is a Laravel 12 application (PHP ^8.2) using Vite/Tailwind for frontend assets. Key entry points:

- `composer.json` — project dependencies and developer scripts (see `scripts.dev` and `scripts.test`).
- `package.json` & `vite.config.js` — frontend build with Vite/Tailwind.
- `routes/web.php` — HTTP routes; small app currently serves `resources/views/welcome.blade.php`.
- `app/Models` / `app/Http/Controllers` — main PHP app code (PSR-4 autoload `App\`).
- `database/database.sqlite` — development DB file; tests use an in-memory sqlite connection (see `phpunit.xml`).

Read these files when you need the "why" for a change (routing, DB choices, build scripts).

## Architecture & patterns (what matters)

- Framework: full-stack Laravel app. Follow Laravel conventions: controllers in `app/Http/Controllers`, Eloquent models in `app/Models`, factories in `database/factories`, seeders in `database/seeders`.
- Frontend: Vite-managed assets under `resources/js` and `resources/css`. `npm run dev` runs Vite; `vite build` produces production assets.
- Jobs/queues: project includes queue tooling in composer scripts (`php artisan queue:listen`). When adding background work, respect Laravel's queue config and the `QUEUE_CONNECTION` env var.
- Testing: `phpunit.xml` configures the test environment (notably `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:`). Unit/feature tests assume an in-memory sqlite DB by default.

## Developer workflows (concrete commands)

Use these exact commands (PowerShell examples):

1) Install and prepare environment

```powershell
composer install; npm install
copy .env.example .env; php artisan key:generate
php -r "file_exists('database/database.sqlite') || New-Item -ItemType File database/database.sqlite | Out-Null"
php artisan migrate
```

2) Local development (frontend + backend)

```powershell
# Option A: run the frontend and backend separately
npm run dev
php artisan serve --host=127.0.0.1 --port=8000

# Option B: use the composer `dev` script which runs multiple processes via concurrently
composer run dev
```

3) Tests

```powershell
composer test          # runs: php artisan config:clear && php artisan test
# or: ./vendor/bin/phpunit (Windows: vendor\phpunit\phpunit.bat)
```

4) Common inspection/debug

```powershell
php artisan route:list
php artisan tinker
php artisan env
```

## Project-specific conventions & gotchas

- Tests assume sqlite in-memory DB. If you change the DB driver for tests, update `phpunit.xml` accordingly.
- `User` model uses `protected function casts()` with `password => 'hashed'` and `HasFactory` — prefer factories for creating users in tests (`database/factories/UserFactory.php`). See `app/Models/User.php`.
- Frontend assets are wired to Vite; modify `resources/js/bootstrap.js` and `resources/css/app.css` rather than editing compiled files.
- Many Laravel convenience scripts are wired into `composer.json` (post-create hooks, `dev`, `test`). Use them to reproduce project setup reliably.

## Integration points & external deps

- PHP packages managed by Composer (see `composer.json`). Key packages: `laravel/framework` (v12), `laravel/tinker`, `phpunit/phpunit` for tests.
- Frontend via npm: `vite`, `tailwindcss`, `laravel-vite-plugin`.
- Optional local services referenced by env vars: queue, mail, telescope and other feature toggles — check `config/*.php` and `phpunit.xml` for test overrides.

## How AI agents should operate here (practical rules)

1) Always run the project's scripts and tests before proposing behavior-changing edits. Use `composer test` and `npm run dev` where applicable.
2) When changing database-related code, ensure tests still pass with sqlite in-memory. Prefer adding a test that demonstrates the intended behavior.
3) When adding routes, update `routes/web.php` and add a matching controller in `app/Http/Controllers` and a view in `resources/views` when appropriate.
4) Use existing factories/seeders for test data. Creating new factories is preferred over manual DB inserts in tests.
5) For frontend changes, edit `resources/js` and `resources/css`, then run `npm run dev` / `vite build` to validate.

## Files to inspect first for most changes

- `composer.json` — scripts and php deps
- `phpunit.xml` — test environment
- `routes/web.php` — routing for HTTP
- `app/Models/User.php` — example model patterns (casts, HasFactory)
- `resources/js/bootstrap.js` & `resources/css/app.css` — frontend entry points

If anything above is unclear or you want me to expand a section (examples for adding a route+controller+test, or a checklist for frontend PRs), tell me which part and I'll iterate.

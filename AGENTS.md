# AGENTS.md

## What this repo is

Laravel 12 monolith ("Posyandu-In") for Indonesian child-health/stunting monitoring. It has server-rendered Blade portals, a Sanctum REST API, and a standalone research ML pipeline under `ml/`. The stock `README.md` is Laravel's default and not project documentation.

## Commands

- First-time setup: `composer setup` (composer install, copy .env, key, migrate, npm install, Vite build). It does NOT seed data.
- Dev all-in-one: `composer dev` (artisan serve + queue + pail + Vite). Plain server: `php artisan serve`.
- Tests: `php artisan test` or `composer test` (clears config first). Single test: `php artisan test --filter=AnthropometricServiceTest` or `php artisan test tests/Unit/AnthropometricServiceTest.php`.
- PHP format: `vendor/bin/pint` (only formatter; no lint script/config).
- Frontend: `npm run build` / `npm run dev`.
- Local DB comes from `.env` (MySQL `sipanda`); PHPUnit forces in-memory SQLite, so tests do not touch MySQL.

## Architecture notes

- Auth model is `App\Models\Pengguna` (table `pengguna`, PK `id_user`), not `App\Models\User`. `config/auth.php` points the `web` provider at `Pengguna`.
- Roles are exact strings `SuperAdmin`, `Bidan`, `Posyandu`, `OrangTua`. A `Kader` model/legacy table exists, but the active role is `Posyandu`.
- API: `routes/api.php`, prefix `/api/v1`, Sanctum tokens, role checks via `role:*` middleware. Web: `routes/web.php`, session auth per portal (`superadmin`, `posyandu`, `bidan`, OrangTua), middleware aliases in `bootstrap/app.php`.
- Domain code is Indonesian: tables/models/fields use `nik_anak`, `pemeriksaan`, `id_periksa`, `tgl_periksa`, etc. Many models have non-standard string PKs (`Anak::nik_anak`) or non-`id` PKs. Follow existing names instead of Laravel defaults.
- Web UI is mostly Blade with Tailwind via CDN; only `layouts/auth` and `layouts/orang_tua` use `@vite`. It is not an SPA.
- `public/build` is gitignored; Docker/CI build it. Do not rely on committed assets.

## Data / seeding gotchas

- `DatabaseSeeder` (demo accounts: `superadmin`/`admin123`, `posyandu_mawar`/`kader123`, `bidan_sari`/`password123`) does NOT call other seeders. Run `php artisan db:seed`, then `php artisan db:seed --class=ReferensiAntropometriWhoSeeder` as needed.
- `AnthropometricService` requires WHO LMS rows in `referensi_antropometri_who`; without seeding it throws `RuntimeException`. Source files are in `database/data/who/`.
- `JadwalImunisasiSeeder` expects 9 vaccines (including PCV, Rotavirus, JE), but `DatabaseSeeder` inserts only 6 (BCG, Hepatitis B, DPT-HB-Hib, Polio, Campak, IPV), so it currently throws until the missing `jenis_vaksin` rows exist.
- `SuperAdminSeeder` is stale (uses `App\Models\User`/`users`); use `DatabaseSeeder`.

## ML pipeline (`ml/`)

- Python virtualenv: `ml/.venv/Scripts/python.exe` on Windows; recreate with `python -m venv ml/.venv` then `ml/.venv/Scripts/pip install -r ml/requirements.txt`.
- Real-data flow: `php artisan ml:export-stunting-dataset` (requires `APP_KEY`) -> `python ml/src/build_dataset.py` -> `python ml/src/train_compare.py`.
- Simulation flow (checked-in CSVs): `python ml/src/generate_simulation_data.py` -> `python ml/src/build_simulation_dataset.py` -> `python ml/src/train_compare_simulation.py`.
- Raw/processed data, `ml/models/*.joblib`, and `ml/reports/*.csv` are gitignored. `ml/models/` is empty and Laravel has no inference integration; the pipeline writes reports only.

## CI / deploy

- `.github/workflows/deploy.yml` runs on pushes to `main`: test -> Docker build/push to GHCR -> SSH deploy. The test job uses `continue-on-error: true`, so failing tests do not block deploy.
- Docker image runs nginx + php-fpm via supervisor on port 80 (compose maps 8082); CI uses PHP 8.3 while the Dockerfile uses PHP 8.2.
- `deploy_key` and `deploy_key.pub` are committed at repo root. Treat `deploy_key` as sensitive: do not print, copy, or add it to new files.

# POSYANDU-IN PROJECT INSTRUCTIONS

## 1. Project Overview

Posyandu-In is a web-based Posyandu information system
for managing child data, examinations, immunization,
Posyandu schedules, growth monitoring, and stunting
risk prediction.

The project was previously named SIPANDA.
Preserve existing application behavior and database structure.

Main technologies:

- Laravel 12
- PHP 8.2
- MariaDB
- Blade
- Tailwind CSS
- JavaScript
- Python
- Scikit-learn

## 2. Project Architecture

Before changing code, inspect the relevant:

- Routes
- Controllers
- Models
- Services
- Database migrations
- Blade views
- Tests

Important locations:

- app/Http/Controllers/
- app/Models/
- app/Services/
- app/Console/Commands/
- resources/views/
- routes/web.php
- routes/api.php
- database/migrations/
- tests/
- ml/

Do not assume a feature is implemented just because
it exists in a flowchart or proposal.

Distinguish:

- Implemented features
- Partially implemented features
- UI placeholders
- Planned features

## 3. User Roles

### Super Admin

- Manage Posyandu.
- Manage Bidan accounts.
- View Posyandu distribution map.
- Monitor regional data.

### Kader

- Manage child data.
- Input child examinations.
- Manage Posyandu schedules.
- View and print monthly reports.

### Bidan

- Input immunization records.
- View child data and examination history.
- Monitor child growth.
- View schedules and reports.
- Access stunting prediction when available.

### Orang Tua

- View child dashboard.
- View child profile.
- View latest examination.
- View growth monitoring.
- View immunization and examination history.
- View prediction results when available.

## 4. Important Data Rules

The child primary key is nik_anak.

The child table uses:

- nik_anak
- id_posyandu
- nama_anak
- tgl_lahir
- jenis_kelamin
- nama_ayah
- nama_ibu

Parent login uses:

- NIK anak
- tanggal lahir anak

Do not reintroduce nik_orang_tua or a separate
Orang Tua account model without explicit approval.

Do not invent database fields or API response fields.

Always inspect existing migrations and models
before changing database structure.

## 5. Anthropometric Service

Important file:

app/Services/AnthropometricService.php

The service analyzes:

- TB/U
- BB/U
- BB/PB-TB
- IMT/U
- LK/U

It uses WHO LMS reference data stored in
referensi_antropometri_who.

IMPORTANT:

- Do not rewrite the LMS calculation casually.
- Do not change WHO reference interpretation
  without checking existing tests and requirements.
- Preserve the current service interface.
- Run AnthropometricServiceTest after modifications.
- Do not confuse current anthropometric monitoring
  with future Machine Learning prediction.

## 6. Machine Learning

The current prototype uses:

- Python
- Pandas
- Scikit-learn
- Decision Tree as the temporary candidate model

The simulation dataset is located in:

ml/data/simulation/

Simulation data is for pipeline testing only.

NEVER:

- Treat simulation results as real clinical performance.
- Use simulated data as production training data.
- Display simulated predictions as actual child health results.
- Claim 94.44% accuracy on real children.

The real-data export and training pipeline must remain
separate from the simulation pipeline.

Keep target leakage out of model features.

## 7. UI Design System

The existing web interface uses:

- Primary blue: #155DFC
- White backgrounds
- Slate text colors
- Light blue active backgrounds
- Plus Jakarta Sans where already configured
- Consistent spacing and typography
- Rounded cards with restrained shadows

Before creating UI:

1. Inspect existing pages and components.
2. Reuse existing styles and patterns.
3. Maintain visual consistency across roles.
4. Keep responsive behavior.
5. Preserve existing interactions.

Do not introduce a new visual style without approval.

For UI tasks, read docs/ui-guidelines.md before editing.
Follow its design and quality requirements.

## 8. Anti-AI-Slop UI Rules

Do not produce generic AI-generated interfaces.

Avoid:

- Unnecessary gradients.
- Glassmorphism without a design reason.
- Excessive rounded cards.
- Excessive shadows.
- Random decorative icons.
- Emoji used as interface icons.
- Unnecessary floating elements.
- Oversized headings without hierarchy.
- Too many competing accent colors.
- Repeated cards with no information value.
- Generic dashboard layouts unrelated to Posyandu workflows.
- Fake statistics or fabricated health information.
- Unnecessary animations.

Prioritize:

- Clear information hierarchy.
- Consistent typography.
- Alignment and spacing.
- Practical visual grouping.
- Readable data.
- Accessible contrast.
- Responsive layouts.
- Useful empty, loading, and error states.
- Consistent buttons and form controls.

Every UI element must have a clear purpose.

## 9. Coding Rules

- Understand existing code before editing.
- Prefer small, focused changes.
- Follow existing naming conventions.
- Avoid unnecessary dependencies.
- Avoid duplicate logic.
- Do not create a new service if an existing service
  already handles the same responsibility.
- Do not remove existing features without approval.
- Do not modify API contracts casually.
- Do not change database schema without explaining
  the impact first.
- Do not overwrite unrelated user changes.

When a task is ambiguous, inspect the project
and explain the options before making large changes.

## 10. Verification

After Laravel code changes, run appropriate checks:

php artisan test
php artisan view:clear

For PHP syntax checks:

php -l path/to/file.php

For database changes:

- Explain the migration impact.
- Do not run destructive migrations automatically.

Always report:

1. Files changed.
2. What changed.
3. Tests executed.
4. Test results.
5. Remaining issues.

Never claim that a feature works unless it has
been verified.

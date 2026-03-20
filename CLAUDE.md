# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Start full dev environment (server + queue + logs + vite, concurrently)
composer dev

# First-time setup
composer setup

# Run all tests (clears config, checks lint, runs Pest)
composer test

# Run a single test
php artisan test --filter TestName
./vendor/bin/pest --filter TestName

# Lint (auto-fix with Pint)
composer lint

# Check lint without fixing
composer test:lint

# Build frontend assets
npm run build

# Run migrations + seed
php artisan migrate --seed
```

## Architecture Overview

This is a **Laravel 12 + Livewire 4** application for managing public procurement tenders (licitaciones) sourced from Spain's PLACSP platform.

### Domain Model

- **Project** — A public tender imported from PLACSP via ATOM/XML. Fields mirror the PLACSP data structure (expediente, CPV codes, organo_contratacion, presupuesto, etc.).
- **User** — Belongs to a `Company` and has a `Role`. Can be a regular user or `Super Admin` (`User::isSuperAdmin()`).
- **UserProject** — Pivot model (`Illuminate\Database\Eloquent\Relations\Pivot`) linking Users to Projects. Holds `project_status_id` and `notes`. Tasks belong to `UserProject`, not directly to `Project`.
- **Task** — Belongs to a `UserProject` (via `project_user_id`), has a `TaskState`, and is assigned to a `User`.
- **Company** / **Sector** / **CompanySector** — Company organization with sector classification.

### Route Structure

Routes are split across multiple files, all required in `web.php` or each other:
- `routes/web.php` — Main routes; redirects Super Admins to `/sadmin`
- `routes/sadmin.php` — Super admin dashboard at `/sadmin`
- `routes/task.php` — Task routes (in progress)
- `routes/userProjects.php` — User-project views
- `routes/settings.php` — User settings (Fortify-powered)

### Two Layouts

- **`layouts/app`** — Standard user layout with Flux UI sidebar
- **`layouts/sadmin`** — Super admin layout (`x-layouts::app.sidebarsadmin`)

Views live in `resources/views/superadmin/` for admin pages and `resources/views/` for general pages.

### Services Layer

- **`AtomFileUploadService`** — Validates and stores uploaded `.atom` files to the custom `files` disk (`storage/app/files/atom_file.atom`).
- **`AtomDataExtractionService`** — Parses the ATOM XML from PLACSP, filters entries by an allowed CPV codes list (IT/computing procurement codes), and upserts into the `projects` table using `expediente` as the unique key.

The `files` filesystem disk is defined in `config/filesystems.php` and points to `storage/app/files/`.

### Frontend

- Tailwind CSS 4 via `@tailwindcss/vite` plugin
- Livewire Flux components (`livewire/flux`) for UI
- Entry points: `resources/css/app.css` and `resources/js/app.js`

### Authentication

Managed by **Laravel Fortify** with optional two-factor authentication. The `TwoFactorAuthenticatable` trait is on the `User` model.

### Testing

Uses **Pest** (not PHPUnit directly). Tests are in `tests/`. The `composer test` script clears config, checks linting, then runs the full suite.

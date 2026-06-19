# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

BIU Library Management System — a Laravel 12 (PHP 8.2+) web application for managing books, copies, authors, publishers, categories, members, membership plans, and borrow/return transactions. Server-rendered Blade UI backed by yajra/laravel-datatables for all listing pages. Default DB is SQLite.

## Commands

```bash
# First-time setup (install deps, copy .env, key:generate, migrate, npm build)
composer setup

# Run the full dev stack (php serve + queue listener + pail logs + vite) concurrently
composer dev

# Frontend asset dev/build
npm run dev
npm run build

# Tests (clears config first, then runs the suite)
composer test
php artisan test                          # direct
php artisan test --filter=SomeTest        # single test class/method
php artisan test tests/Feature/ExampleTest.php

# Lint / format (Laravel Pint)
./vendor/bin/pint            # fix
./vendor/bin/pint --test     # check only

# Database
php artisan migrate
php artisan migrate:fresh --seed
```

The SQLite database file lives at `database/database.sqlite`. Docker support exists via `compose.yaml` / `docker/` and `laravel/sail`.

## Architecture

Requests flow through a strict layered pipeline. When adding a feature, mirror this stack end-to-end:

```
Route (routes/web.php)
  → Controller (App\Http\Controllers)        thin: resolves FormRequest + DataTable, delegates to Service
    → FormRequest (App\Http\Requests\<Domain>)  validation + authorization
    → Service (App\Services)                   business logic (e.g. image uploads, orchestration)
      → Repository Interface (App\Repositories\Interfaces)
        → Repository (App\Repositories)        all Eloquent/DB access lives here
          → Model (App\Models)
DataTable (App\DataTables)                     server-side table rendering for index pages
```

Key conventions:

- **Controllers are thin.** They inject a Service via constructor promotion, pass `$request->validated()` to it, and redirect with a flash message. They do not touch Eloquent directly except for simple dropdown lookups (e.g. `Category::all()` in `create`/`edit`).
- **Repositories own all data access.** Services depend on the *interface*, never the concrete repository. Every interface→implementation binding is registered in [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php) `register()` — add new bindings there.
- **Index pages use DataTables.** Each domain has an `App\DataTables\<X>DataTable` rendered by the controller's `index()` as `$dataTable->render('domain.index')`. Column action buttons and badges are built with global helper functions (see below). Use `rawColumns([...])` for any column emitting HTML.
- **Multi-step DB writes wrap in `DB::transaction`** — see [app/Repositories/BorrowRepository.php](app/Repositories/BorrowRepository.php), which creates borrow records and flips `book_copies.status` to `borrowed` atomically.

## Auth & Authorization

- Custom session auth via `App\Http\Controllers\Auth\LoginController` (no Breeze/Jetstream).
- Authorization is role-based through the `role` middleware (`RoleMiddleware`, aliased in [bootstrap/app.php](bootstrap/app.php)). Usage: `Route::middleware('role:admin,librarian')`. Roles are stored on `users.role` (string); known roles are `admin`, `librarian`, `member`.
- CRUD resources (books, members, etc.) require `role:admin,librarian`; borrow/return is open to `role:admin,librarian,member`.
- `bootstrap/app.php` also registers a custom 404 handler returning JSON or `errors.404`.

## Global Helpers

[app/Helpers/helpers.php](app/Helpers/helpers.php) is autoloaded via `composer.json` `files`. It provides view/DataTable helpers used throughout Blade and DataTable classes: `status_label()`, `status_badge()`, `format_date()`, `format_date_time()`, `format_time()`, `format_currency()`, `format_number()`, `format_decimal()`, `book_preview()`, `image_preview()`, `edit_button()`, `delete_button()`. These emit Bootstrap + FontAwesome markup — the Blade UI is a Bootstrap admin theme (assets under `public/assets/`), even though Vite is configured with Tailwind.

## Models & Domain

Core entities: `Book` (belongsTo Publisher/Category, belongsToMany Author via `book_authors`), `BookCopy` (physical copy with a `barcode` and status enum), `BorrowTransaction` (links BookCopy ↔ Member ↔ Staff with borrow/due/return dates), plus `Author`, `Publisher`, `Category`, `Member`, `MemberPlan`, `Staff`, `Reservation`, `Review`, `Notification`, `AuditLog`. `User` uses `SoftDeletes` and has the `role` column. `BookCopy` status values are defined in `App\Enums\BookCopyStatus` (`available`, `borrowed`, `damaged`, `lost`).

## Notes

- File uploads (e.g. book cover images) are stored on the `public` disk under paths like `books/covers` via Services; ensure `php artisan storage:link` has been run.
- Cover/profile image columns store the relative storage path; helpers prefix `storage/` when rendering.

# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

A newsletter management application built on **Laravel 12** (PHP 8.2+), bootstrapped from
the "Laravel Base Template". Auth scaffolding is Laravel Breeze (Blade + Alpine.js);
frontend assets are built with Vite + Tailwind CSS 4. API auth uses Laravel Sanctum.
Testing is **Pest** (not PHPUnit-style classes by default).

## Commands

```bash
# First-time setup (interactive): copies .env, generates key, installs deps,
# migrates, creates admin user, builds assets
php artisan laravel-base:install

# Manual setup
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite        # default DB is SQLite
php artisan migrate

# Dev: run these in two terminals
php artisan serve                     # backend
npm run dev                           # Vite dev server (HMR)
npm run build                         # production assets

# Tests (Pest)
php artisan test                      # full suite
php artisan test --filter=NewsletterTest        # single file/test by name
./vendor/bin/pest tests/Feature/ProfileTest.php # run one file directly

# Lint / format (Laravel Pint)
./vendor/bin/pint                     # fix
./vendor/bin/pint --test              # check only

# Seed development data (users + roles)
php artisan db:seed                   # or: php artisan migrate --seed
```

## Architecture

### Domain model (the core of this app)
The data model is a three-level hierarchy plus a role pivot:

- **Newsletter** — owned by a `User` (`owner_id`). Has many `NewsletterIssue`s.
  `settings` is a JSON column cast to array.
- **NewsletterIssue** — belongs to a Newsletter. `status` enum: `draft | scheduled | sent`.
  Has many `Article`s **ordered by the `order` column**.
- **Article** — belongs to an issue (FK is `issue_id`, not the conventional
  `newsletter_issue_id`) and to a `User` author (`author_id`).
- **NewsletterUser** — pivot joining users to newsletters with a `role` enum
  (`owner | editor | recipient`), unique per (newsletter, user).

Role helpers live on the models: `Newsletter::editors()` / `recipients()` filter the
pivot via `wherePivot('role', ...)`. When a newsletter is created, the owner is both set
as `owner_id` **and** attached to the pivot with role `owner` (see
`NewsletterController::store`). Keep both in sync when changing ownership logic.

### Routing
Routes are nested resource routes in `routes/web.php`, all behind `auth`:
- `newsletters` (full resource) + custom `newsletters.editors` / `newsletters.recipients`
- `newsletters.issues` (resource, no `index` — custom index route instead)
- `newsletters.issues.articles` (resource, no `index`/`show`)

This nesting means controllers receive parent models via route-model binding (e.g.
`ArticleController` gets `$newsletter`, `$issue`, `$article`). `routes/auth.php` holds
Breeze auth routes; `routes/api.php` exposes a Sanctum-guarded `/api/user`.

### Authorization
Access control goes through **`NewsletterPolicy`** (`app/Policies/`). Controllers call
`$this->authorize(...)` — do not scatter ad-hoc role checks in controllers; extend the
policy instead. The newsletter/issue/article ownership chain is the basis for who can
edit what.

### Template tooling (inherited from Laravel Base)
`app/Console/Commands/` contains interactive installer commands (`laravel-base:install`,
`install:spatie-packages`, `install:livewire`, `install:frontend`, plus
`CreateTestData` and a Blueprint walkthrough). These are scaffolding for the base
template, not part of the newsletter domain — most optional packages (Spatie,
Livewire/Volt, Filament, Telescope, etc.) are **not installed** until you run the
relevant installer. Check `composer.json` before assuming a package is available.

## Conventions

- Default DB is **SQLite** (`database/database.sqlite`); session, cache, and queue
  drivers all default to `database` (`.env.example`).
- Issue/article content is stored as HTML (WYSIWYG editor); `content` columns are
  `longText`/nullable.
- Foreign keys cascade on delete (deleting a newsletter removes its issues, articles via
  the chain, and pivot rows).

# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Newzly is a **multi-tenant SaaS for newsletter publications**, built on **Laravel 13**
(PHP 8.3+), bootstrapped from the "Laravel Base Template". Auth scaffolding is Laravel
Breeze (Blade + Alpine.js); frontend assets are built with Vite + Tailwind CSS 4. API
auth uses Laravel Sanctum. Testing is **Pest** (not PHPUnit-style classes by default).
The product domain term is **Publication** (a newsletter title, e.g. "Farming Monthly");
the code models a Publication → Issue → Story hierarchy. The broader product vision and
phased build roadmap live in Claude's project memory, not in the repo.

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
php artisan test --filter=PublicationTest       # single file/test by name
./vendor/bin/pest tests/Feature/ProfileTest.php # run one file directly

# Lint / format (Laravel Pint)
./vendor/bin/pint                     # fix
./vendor/bin/pint --test              # check only

# Seed development data (users + roles)
php artisan db:seed                   # or: php artisan migrate --seed
```

## Architecture

### Domain model (the core of this app)
The data model is a three-level hierarchy plus a team-role pivot. Every child table
carries `publication_id` for **tenant isolation**:

- **Publication** — owned by a `User` (`owner_id`); has a unique `slug` (auto-generated
  from the name in `Publication::booted()`). `settings` is a JSON column cast to array.
  Has many `Issue`s and `Story`s.
- **Issue** — belongs to a Publication. `status` enum: `draft | scheduled | sent`.
  Has many `Story`s **ordered by the `order` column**.
- **Story** — belongs to a Publication (`publication_id`) and an Issue (FK is `issue_id`,
  not the conventional `issue_id`-vs-`newsletter_issue_id` — it's plain `issue_id`), plus
  a nullable `User` author (`author_id`).
- **PublicationUser** — pivot joining app users to publications with a `role` enum
  (`owner | editor | contributor | fact_checker`), unique per (publication, user).

Team helpers live on the models: `Publication::members()` is the pivot relation,
`editors()` filters it via `wherePivot('role', ...)`. When a publication is created, the
owner is both set as `owner_id` **and** attached to the pivot with role `owner` (see
`PublicationController::store`). Keep both in sync when changing ownership logic.

**Subscribers are NOT app users.** The mailing list is its own model:

- **Subscriber** — belongs to a Publication; `status` enum `pending | confirmed |
  unsubscribed`; carries the GDPR consent record (`consent_at/ip/source`), a
  `confirmation_token` (double opt-in, Phase 3), and a unique `unsubscribe_token`. Tokens
  auto-generate in `booted()`. Helpers: `confirm()`, `unsubscribe()`, `recordEvent()`,
  scope `mailable()` (= confirmed). Do not reintroduce a `recipient` role on the pivot.
- **ConsentEvent** — append-only audit log (`UPDATED_AT = null`) of consent actions
  (subscribed/confirmed/unsubscribed…) with ip/user_agent/meta. Write only via
  `Subscriber::recordEvent()`.

Mailing-list management is gated by the `manageSubscribers` policy ability (owner+editor)
and lives under scoped `publications.subscribers.*` routes (`SubscriberController`).
Manual adds require an explicit consent attestation checkbox, recorded in the audit log.

### Routing
Routes are nested resource routes in `routes/web.php`, all behind `auth`:
- `publications` (full resource) + custom `publications.editors`
- `publications.issues` (`->scoped()`, no `index` — custom index route instead)
- `publications.issues.stories` (`->scoped()`, no `index`/`show`)

The `->scoped()` bindings enforce tenant isolation: a child must belong to its parent in
the URL or the binding 404s (see the test in `tests/Feature/PublicationTest.php`).
Controllers receive parent models via route-model binding (e.g. `StoryController` gets
`$publication`, `$issue`, `$story`). `routes/auth.php` holds Breeze auth routes;
`routes/api.php` exposes a Sanctum-guarded `/api/user`.

### Authorization
Access control goes through **`PublicationPolicy`** (`app/Policies/`, auto-discovered by
naming convention). Controllers call `$this->authorize(...)` — do not scatter ad-hoc role
checks in controllers; extend the policy instead. `update` allows `owner`+`editor`;
destructive abilities are owner-only.

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
- Foreign keys cascade on delete (deleting a publication removes its issues, stories via
  the chain, and pivot rows).
- **Public uploads** (publication logos; later story photos) go on the disk named by
  `config('filesystems.media_disk')` (`MEDIA_DISK` env — `public` locally, `s3` in prod).
  Use `Publication::mediaDisk()` / `Publication::logoUrl()`; local dev needs
  `php artisan storage:link`. Tests fake it with `Storage::fake(Publication::mediaDisk())`.
- A Publication carries its branding (`logo_path`, `website_url`, `social_links` JSON keyed
  by `Publication::SOCIAL_PLATFORMS`) and email identity (`from_name`, `from_email`,
  `reply_to_email`). These are edited on the publication edit form, not at creation.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- laravel/boost (BOOST) - v2
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- alpinejs (ALPINEJS) - v3
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>

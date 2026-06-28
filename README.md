# 📰 Newzly

**Newzly** is a multi-tenant SaaS for running newsletter publications — build a mailing
list with GDPR-friendly double opt-in, gather stories (from your team and the public),
lay out issues with a configurable template, and send them to confirmed subscribers.

![Laravel](https://img.shields.io/badge/Laravel-13-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-777bb4.svg)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-4-38bdf8.svg)
![Tests](https://img.shields.io/badge/tests-Pest-3bb273.svg)

---

## ✨ Features

- **Publications → Issues → Stories** with strict per-tenant isolation (every child
  carries `publication_id`, and nested routes 404 when a child doesn't belong to its parent).
- **Team & roles** — invite people by email with a role (`owner`, `editor`, `contributor`,
  `fact_checker`); access is enforced through a single `PublicationPolicy`.
- **GDPR mailing list** — subscribers are a separate model with double opt-in,
  per-event consent logging (`ConsentEvent`), and a guaranteed unsubscribe link in every email.
- **Public pages** (per publication, by slug) — branded **subscribe**, **confirm**,
  **unsubscribe**, and **story/photo submission** flows. Bot protection is honeypot +
  throttle (no third-party captcha).
- **Submission moderation** — a queue where editors approve public submissions into an issue.
- **Sending** — per-publication SMTP (encrypted creds) or the platform default; a queued
  `SendIssue` job mails confirmed subscribers, records one `IssueDelivery` per recipient
  (so re-runs never double-send), and supports scheduled issues.
- **Self-hosted rich-text editor** — the free, open-source **Tiptap** editor (no CDN / API key),
  a reusable `<x-wysiwyg-editor>` Blade component.
- **Newspaper UI theme** — a black/white/red editorial aesthetic across all public and
  admin pages, built on Tailwind 4 design tokens.

### Issue layout system

- **Configurable structure** — each publication sets the order of its `header`, `content`,
  and `footer` sections (static across all of its issues), edited via drag-and-drop with a
  live preview.
- **Configurable colour palette** — header, issue bar, accent, **event calendar**, article,
  footer, and page colours, all applied to a cross-client-safe email and a browser preview.
- **Story layouts** — `standard`, `picture`, `title_only`, plus "clear header" variants.
- **Content blocks** — reusable blocks interleave with articles in a single drag-reorderable
  stream. The first block type is the **events block** (accent / clear / no title, with an
  editable list of events: name, date, location, description), with an extensible
  "Add Block" menu for future types.

---

## 🚀 Getting Started

> Default DB is **SQLite**; session, cache, and queue drivers default to `database`.

### One-command setup (interactive)

```bash
git clone https://github.com/Kayrah87/Newzly.git
cd Newzly
php artisan laravel-base:install   # copies .env, generates key, installs deps, migrates, creates an admin, builds assets
```

### Manual setup

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite          # default DB is SQLite
php artisan migrate
php artisan db:seed                      # seeds the super admin (admin@newzly.test / password)
php artisan storage:link                 # local media (logos, photos)
```

### Run it (two terminals)

```bash
php artisan serve     # backend
npm run dev           # Vite dev server (HMR)
```

The platform super admin defaults to `admin@newzly.test` / `password` (override with
`ADMIN_EMAIL` / `ADMIN_PASSWORD`).

---

## 🧱 Domain model

A three-level hierarchy plus a team-role pivot, with subscribers as their own list:

- **Publication** — owned by a `User`; unique auto-generated `slug`; carries branding
  (`logo_path`, `website_url`, `social_links`), email identity (`from_*`, `reply_to_email`),
  optional SMTP, a `structure` (section order) and a `palette` (colours).
- **Issue** — belongs to a publication; `status` `draft | scheduled | sent`; editorial
  metadata (`issue_number`, `coverage_label`, `release_date`). Content is a drag-reorderable
  stream of stories **and** blocks.
- **Story** — belongs to an issue; has a `layout`, a `source` (`admin | public`), a `status`
  (`pending | approved | rejected` — only approved stories are emailed) and many `StoryImage`s.
- **Block / Event** — a `Block` (e.g. the events block) belongs to an issue and has many
  ordered `Event`s (name, date, location, description).
- **Subscriber / ConsentEvent** — the mailing list and its append-only consent audit log.
- **PublicationUser** — the team pivot (one role per user per publication).
- **Invitation** — tokened, 7-day email invitations to join a publication's team.

---

## 🧪 Testing & quality

```bash
php artisan test                         # full Pest suite
php artisan test --compact --filter=EventsBlockTest   # one file/test

./vendor/bin/pint                        # fix code style (Laravel Pint)
./vendor/bin/pint --test                 # check only
```

Email templates are covered by cross-client (incl. Outlook Word-engine) safety assertions,
and the layout/palette/blocks features have full feature-test coverage.

---

## 🛠 Tech stack

Laravel 13 · PHP 8.3+ · Laravel Breeze (Blade + Alpine.js) · Laravel Sanctum ·
Vite + Tailwind CSS 4 · Tiptap · Pest.

> Newzly is bootstrapped from the "Laravel Base Template"; the `app/Console/Commands`
> installers (`laravel-base:install`, `install:*`) are scaffolding from that template and
> are not part of the newsletter domain.

---

## 📜 License

Licensed under the **MIT License** — see [LICENSE](LICENSE).

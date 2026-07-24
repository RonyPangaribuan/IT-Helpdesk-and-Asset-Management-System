# deskIT

[![CI](https://github.com/RonyPangaribuan/IT-Helpdesk-and-Asset-Management-System/actions/workflows/ci.yml/badge.svg)](https://github.com/RonyPangaribuan/IT-Helpdesk-and-Asset-Management-System/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

deskIT is an IT Helpdesk & Asset Management MVP built as a Laravel monolith. It centralizes support requests, ticket assignment, technician work, resolution history, collaboration, and basic asset records.

**Project status: MVP v1.0.0 - Release Candidate**

## Problem Statement

IT support reports are often scattered across chat, verbal requests, and spreadsheets. This makes requests easy to miss, gives requesters little visibility, and leaves administrators without a consistent repair history. deskIT provides one role-aware workspace for the complete support lifecycle.

## Main Features

- Laravel Breeze authentication with public registration restricted to requester accounts.
- Administrator, technician, and requester roles with policy-based authorization.
- Ticket creation, scoped listing, search, filters, pagination, editing, and archive.
- Assignment, reassignment, controlled status transitions, and read-only history.
- Ticket comments and private attachment upload/download.
- Resolution, close, reopen, and cancellation workflows.
- Ticket category and asset category management.
- Basic asset management with optional ticket relationships.
- Role-scoped dashboards and administrator user management.
- Demo seed data, automated tests, and GitHub Actions CI.

## Screenshots

Real screenshots have not been captured yet. They will be stored in `docs/screenshots/` only after the rebranded application has been checked at the required desktop and mobile viewports.

See the [Screenshot Checklist](docs/SCREENSHOT_CHECKLIST.md).

## Role Overview

| Capability | Requester | Technician | Administrator |
| --- | --- | --- | --- |
| Register publicly | Yes | No | No |
| Create tickets | Yes | No | No |
| View tickets | Own | Assigned | All |
| Edit tickets | Own open, unassigned | No | Category, priority, asset |
| Assign or reassign | No | No | Yes |
| Start and resolve work | No | Assigned tickets | No |
| Close resolved tickets | Own | No | Yes |
| Reopen resolved tickets | Own | No | No |
| Comment | Visible active tickets | Assigned active tickets | Visible active tickets |
| Upload attachments | Own active tickets | Assigned active tickets | No |
| Manage assets and users | No | Assets read-only | Yes |

## Ticket Workflow

```text
Open -> Assigned
Open -> Cancelled
Assigned -> In Progress
Assigned -> Cancelled
In Progress -> Resolved
Resolved -> Closed
Resolved -> Reopened
Reopened -> Assigned
Reopened -> In Progress
```

Invalid transitions are rejected. Every important transition creates a read-only status-history record.

## Technology Stack

- PHP 8.2 or newer
- Laravel 12.x
- Laravel Breeze
- Blade
- Tailwind CSS 3
- Alpine.js for small interactions
- Eloquent ORM
- SQLite for local development and tests
- PostgreSQL or MySQL recommended for production
- Vite
- PHPUnit
- Laravel Pint
- GitHub Actions

## Architecture

deskIT is one deployable Laravel application:

- Routes coordinate web entry points.
- Controllers handle HTTP flow.
- Form Requests validate and authorize mutations.
- Policies and middleware enforce role and resource access.
- Services own ticket workflow, attachment storage, dashboard queries, and user-management rules.
- Eloquent models represent users, tickets, categories, comments, attachments, histories, and assets.
- Blade renders the server-side interface.

Ticket attachments use `config('deskit.attachment_disk')`, defaulting to Laravel's private local disk. Downloads are served through an authorized controller action; no public attachment URL or `storage:link` is required.

See [Architecture](docs/ARCHITECTURE.md) and [ERD](docs/ERD.md).

## Local Installation

### Windows

```bash
composer install
npm.cmd ci
copy .env.example .env
New-Item -ItemType File -Path database/database.sqlite -Force
php artisan key:generate
php artisan migrate:fresh --seed
npm.cmd run build
php artisan test
```

Use `npm.cmd` from PowerShell when the `npm.ps1` shim is blocked by the local execution policy.

### Linux And macOS

```bash
composer install
npm ci
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
npm run build
php artisan test
```

The local environment defaults to SQLite:

```text
DB_CONNECTION=sqlite
FILESYSTEM_DISK=local
TICKET_ATTACHMENT_DISK=local
```

Do not commit a real `.env`. Production configuration should start from `.env.production.example`.

## Demo Accounts

All local demo accounts use password `password`.

| Role | Email |
| --- | --- |
| Admin | `admin@deskit.test` |
| Technician | `technician1@deskit.test` |
| Technician | `technician2@deskit.test` |
| Requester | `requester1@deskit.test` |
| Requester | `requester2@deskit.test` |
| Requester | `requester3@deskit.test` |

Demo credentials and seeders are for local demonstration only. Never reuse them or run the demo seeder automatically in production.

## Testing

```bash
php artisan migrate:fresh --seed
vendor/bin/pint --test
php artisan test
npm.cmd run build
```

Additional release checks:

```bash
composer validate --strict
composer audit --locked
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize:clear
```

Manual functional, responsive, and accessibility checks are tracked separately in the [Manual QA Checklist](docs/MANUAL_QA_CHECKLIST.md).

## Continuous Integration

The `CI` workflow runs for pull requests targeting `master`, pushes to `master`, and manual dispatches. Its `laravel-quality` job checks:

- Composer validation, installation, and security audit.
- Laravel migration and demo seeding.
- Laravel Pint.
- Vite production build.
- PHPUnit tests.
- Config, route, and Blade view caches.

Workflow definition: [.github/workflows/ci.yml](.github/workflows/ci.yml).

## Documentation

- [Documentation Index](docs/README.md)
- [Product Requirements](PRD.md)
- [Implementation Plan](IMPLEMENTATION_PLAN.md)
- [Tasks](TASKS.md)
- [PRD Compliance](docs/PRD_COMPLIANCE.md)
- [Architecture](docs/ARCHITECTURE.md)
- [ERD](docs/ERD.md)
- [Manual QA Checklist](docs/MANUAL_QA_CHECKLIST.md)
- [Deployment Guide](docs/DEPLOYMENT.md)
- [Release Checklist](docs/RELEASE_CHECKLIST.md)
- [Screenshot Checklist](docs/SCREENSHOT_CHECKLIST.md)
- [Demo Script](docs/DEMO_SCRIPT.md)
- [Security Policy](SECURITY.md)
- [Changelog](CHANGELOG.md)

## Deployment Status

External deployment has not been completed or verified. No live URL is published. The platform-neutral [Deployment Guide](docs/DEPLOYMENT.md) covers production configuration, persistent private attachment storage, backups, rollback, and smoke testing.

## Known Limitations

- Manual functional QA, responsive audit, and accessibility audit are still pending.
- Real screenshots and a recorded demonstration video are not available yet.
- External deployment and production smoke testing are not complete.
- Production email delivery is not configured.
- No REST API, React, Inertia, WebSocket, real-time chat, AI, QR/barcode, advanced analytics, export, SLA automation, procurement, depreciation, or multi-tenant support is included.

## License

deskIT is open-sourced under the [MIT License](LICENSE).

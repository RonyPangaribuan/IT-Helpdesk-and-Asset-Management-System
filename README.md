# DelDesk

![CI](https://github.com/RonyPangaribuan/IT-Helpdesk-and-Asset-Management-System/actions/workflows/ci.yml/badge.svg)
![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)

DelDesk is a Laravel monolith MVP for IT helpdesk ticketing and basic asset management. It is built as a portfolio project for demonstrating Laravel fundamentals: authentication, authorization, Eloquent relationships, Form Requests, policies, middleware, services, migrations, seeders, factories, Blade, Tailwind CSS, and automated tests.

Project status: **MVP v1.0.0 - Release Candidate**

## Problem Statement

IT support reports are often scattered across chat, verbal requests, and spreadsheets. DelDesk centralizes support tickets, assignment, progress tracking, repair history, comments, attachments, and related asset records.

## Main Features

- Laravel Breeze authentication with Blade.
- Three roles: administrator, technician, requester.
- Public registration restricted to requester accounts.
- Active account enforcement.
- Admin user management.
- Ticket CRUD with search, filters, pagination, and archive.
- Ticket assignment and reassignment.
- Ticket status workflow with read-only history.
- Ticket comments.
- Private ticket attachments.
- Ticket category and asset category management.
- Basic asset management.
- Optional ticket-to-asset relationship.
- Role-based dashboard statistics.
- Demo seed data for local demonstration.
- GitHub Actions CI.

## UI Overview

DelDesk uses a clean SaaS dashboard style for the Blade MVP. The authenticated app has a role-aware sidebar, a compact sticky header, reusable page headers, neutral white content surfaces, subtle borders, and consistent status badges.

The UI direction is intentionally restrained:

- Slate/white surfaces for the main workspace.
- Indigo as the primary navigation and action color.
- Blue, amber, emerald, red, violet, and slate only for status, priority, condition, and feedback.
- Desktop tables with mobile card alternatives for ticket, asset, and user lists.
- Ticket detail pages with workflow progress, next-action context, report detail, resolution, discussion, attachments, timeline, and metadata.
- Form sections that separate identity, classification, asset context, files, access, and security.

## Role And Permission Matrix

| Capability | Requester | Technician | Administrator |
| --- | --- | --- | --- |
| Register publicly | Yes | No | No |
| Create ticket | Yes | No | No |
| View ticket list | Own tickets | Assigned tickets | All tickets |
| Edit ticket | Own open unassigned tickets | No | Category, priority, asset |
| Archive ticket | No | No | Yes |
| Assign technician | No | No | Yes |
| Start work | No | Assigned only | No |
| Resolve ticket | No | Assigned in-progress only | No |
| Close resolved ticket | Own ticket | No | Yes |
| Reopen resolved ticket | Own ticket | No | No |
| Comment | Visible active tickets | Assigned active tickets | Visible active tickets |
| Upload attachment | Own active tickets | Assigned active tickets | No |
| Download attachment | Authorized own tickets | Assigned tickets | All visible tickets |
| Manage users | No | No | Yes |
| Manage ticket categories | No | No | Yes |
| Manage assets | No | Read only | Yes |
| View dashboard | Own data | Assigned data | Operational overview |

## Ticket Workflow

Allowed transitions:

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

Every important transition creates a `ticket_status_histories` record. Invalid transitions are rejected with safe validation-style errors.

## Asset Management

Assets have code, name, category, brand, model, serial number, location, condition, description, and active status. Administrators can create, edit, view, and archive assets. Technicians can view assets but cannot modify them. Requesters cannot access inventory pages.

Asset conditions:

- Good
- Maintenance
- Damaged
- Retired

Retired assets are stored as inactive and cannot be selected for new tickets.

## Architecture Overview

DelDesk uses a single Laravel application:

- Routes in `routes/web.php`.
- Controllers for HTTP coordination.
- Form Requests for validation.
- Policies and middleware for authorization.
- Services for ticket workflow, attachments, dashboards, and user management.
- Eloquent models for domain relationships.
- Blade views and Tailwind CSS for the MVP frontend.

See [Architecture](docs/ARCHITECTURE.md) for more detail.

## Technology Stack

- PHP 8.2 or newer.
- Laravel 12.x.
- Laravel Breeze.
- Blade.
- Tailwind CSS.
- Alpine.js only for small Breeze interactions.
- Eloquent ORM.
- SQLite for local development and tests.
- PostgreSQL or MySQL recommended for production.
- Vite.
- PHPUnit.
- Laravel Pint.
- GitHub Actions.

## Database Overview

Main tables:

- `users`
- `ticket_categories`
- `asset_categories`
- `assets`
- `tickets`
- `ticket_comments`
- `ticket_attachments`
- `ticket_status_histories`
- `sessions`
- Laravel cache and job tables

See [ERD](docs/ERD.md) for the Mermaid diagram.

## Local Installation: Windows

```bash
composer install
npm.cmd install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm.cmd run build
php artisan test
```

Use `npm.cmd` on Windows PowerShell if the `npm.ps1` shim is blocked by execution policy.

## Local Installation: Linux/macOS

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan test
```

## Environment Configuration

Local `.env.example` defaults to SQLite:

```text
DB_CONNECTION=sqlite
FILESYSTEM_DISK=local
TICKET_ATTACHMENT_DISK=local
```

Create `database/database.sqlite` if it does not exist.

Production configuration should start from `.env.production.example`. Do not commit a real `.env` file.

## Migration And Seeder

```bash
php artisan migrate:fresh --seed
```

The demo seeders create:

- 1 active administrator.
- 2 active technicians.
- 3 active requesters.
- 7 ticket categories.
- 7 asset categories.
- 12 demo assets.
- 24 demo tickets across every status and priority.
- Demo comments and consistent status histories.

## Demo Accounts

All demo accounts use password `password`.

Warning:

- Demo accounts are for local/demo use only.
- Do not use demo passwords in production.
- Do not run demo seeders in production without an explicit decision.

| Role | Email |
| --- | --- |
| Admin | `admin@deldesk.test` |
| Technician | `technician1@deldesk.test` |
| Technician | `technician2@deldesk.test` |
| Requester | `requester1@deldesk.test` |
| Requester | `requester2@deldesk.test` |
| Requester | `requester3@deldesk.test` |

## Test Instructions

```bash
php artisan test
vendor/bin/pint --test
npm.cmd run build
```

Additional release checks:

```bash
composer audit --locked
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize:clear
```

## Continuous Integration

Pull requests targeting `master`, pushes to `master`, and manual workflow dispatch run the `CI` workflow. The required job name is `laravel-quality`.

CI checks:

- Composer validation.
- Composer dependency installation.
- Composer security audit.
- Laravel migration and seeder.
- Laravel Pint.
- Vite production build.
- PHPUnit feature and unit tests.
- Laravel config cache.
- Laravel route cache.
- Laravel view cache.
- Optimize clear.

## Private Attachment Behavior

Ticket attachments are stored through Laravel Storage on `config('deldesk.attachment_disk')`, defaulting to `local`. The local disk is private storage. Files are downloaded only through `TicketAttachmentController` after policy authorization.

DelDesk does not create public attachment URLs and does not require `storage:link` for ticket attachments.

## Screenshots

Screenshots: To be captured from the running redesigned UI.

Checklist: [Screenshot Checklist](docs/SCREENSHOT_CHECKLIST.md)

No screenshot image is displayed here until a real screenshot file exists.

## Demo Video

Demo video: To be recorded.

Script: [Demo Script](docs/DEMO_SCRIPT.md)

## Deployment

Deployment guide: [Deployment Guide](docs/DEPLOYMENT.md)

No live demo URL is listed because no external deployment has been completed and verified yet.

## Documentation

- [PRD Compliance](docs/PRD_COMPLIANCE.md)
- [ERD](docs/ERD.md)
- [Architecture](docs/ARCHITECTURE.md)
- [Deployment Guide](docs/DEPLOYMENT.md)
- [Release Checklist](docs/RELEASE_CHECKLIST.md)
- [Screenshot Checklist](docs/SCREENSHOT_CHECKLIST.md)
- [Demo Script](docs/DEMO_SCRIPT.md)
- [Security Policy](SECURITY.md)
- [Changelog](CHANGELOG.md)
- [License](LICENSE)

## Known Limitations

- Screenshots are not captured yet.
- Demo video is not recorded yet.
- External deployment is not completed yet.
- Email delivery is not configured for production.
- Advanced analytics charts are not included.
- No REST API, Laravel Sanctum, React, Inertia, WebSocket, real-time chat, AI, QR/barcode, export, SLA automation, procurement, depreciation, or multi-tenant support.

## License

DelDesk is open-sourced under the [MIT License](LICENSE).

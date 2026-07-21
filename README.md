# DelDesk

DelDesk is a Laravel monolith MVP for an IT helpdesk and basic asset management system. The project follows the requirements in `PRD.md` and is being implemented gradually by milestone.

Current milestone: Project Foundation.

## Stack

- Laravel 12.x
- Blade
- Tailwind CSS
- Laravel Breeze authentication
- Eloquent ORM
- SQLite for local development and tests

SQLite is used locally because this PHP environment has `pdo_sqlite` enabled, while `pdo_mysql` and `pdo_pgsql` are not enabled. The schema is intended to stay portable for MySQL or PostgreSQL later.

## Local Setup

```bash
composer install
npm.cmd install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm.cmd run build
php artisan test
```

Use `npm.cmd` on Windows PowerShell if the `npm.ps1` shim is blocked by execution policy.

## Demo Accounts

All demo accounts use the password `password`.

| Role | Email |
| --- | --- |
| Admin | `admin@deldesk.test` |
| Technician | `technician1@deldesk.test` |
| Technician | `technician2@deldesk.test` |
| Requester | `requester1@deldesk.test` |
| Requester | `requester2@deldesk.test` |
| Requester | `requester3@deldesk.test` |

## Implemented In Milestone 1

- Laravel project initialized.
- Breeze Blade authentication installed.
- User roles prepared: `admin`, `technician`, `requester`.
- Public registration creates requester accounts only.
- Demo user seeder added.
- Role middleware added.
- Role-aware dashboard placeholders added.
- Feature tests added for authentication and role restrictions.

## Not Implemented Yet

- Ticket CRUD.
- Asset CRUD.
- Ticket assignment.
- Ticket comments.
- Ticket attachments.
- Ticket status workflow/history.
- Dashboard statistics.


# DelDesk

DelDesk is a Laravel monolith MVP for an IT helpdesk and basic asset management system. The project follows the requirements in `PRD.md` and is being implemented gradually by milestone.

Current milestone: Authorization and Ticket Workflow.

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

## Implemented

### Milestone 1: Project Foundation

- Laravel project initialized.
- Breeze Blade authentication installed.
- User roles prepared: `admin`, `technician`, `requester`.
- Public registration creates requester accounts only.
- Demo user seeder added.
- Role middleware added.
- Role-aware dashboard placeholders added.
- Feature tests added for authentication and role restrictions.

### Milestone 2: Core Ticket CRUD

- Ticket categories with admin-only CRUD and soft-delete archive.
- Default ticket category seeder.
- Ticket priority and status PHP enums.
- Ticket model, migration, factory, and demo seeder.
- Requester ticket creation.
- Role-scoped ticket listing.
- Ticket detail page.
- Eligible ticket edit rules.
- Admin-only ticket archive through soft delete.
- Search by ticket code/title.
- Filters by status, priority, and category.
- Pagination with query string preservation.
- Minimal `TicketPolicy` for safe resource authorization.
- Profile deletion guard for users linked to tickets.

### Milestone 3: Authorization and Ticket Workflow

- Ticket status history with read-only audit timeline.
- Initial history is created atomically when a requester creates a ticket.
- Admin ticket assignment from Open to Assigned.
- Admin reassignment while a ticket is still Assigned.
- Assigned technician Start Work action from Assigned to In Progress.
- Requester and admin ticket cancellation rules.
- Workflow service centralizes status and technician changes.
- Expanded `TicketPolicy` abilities for workflow actions.
- Demo seeder creates Open, Assigned, In Progress, and Cancelled tickets with consistent history.

## Main Routes

| Area | Route |
| --- | --- |
| Tickets | `/tickets` |
| Create ticket | `/tickets/create` |
| Ticket detail | `/tickets/{ticket}` |
| Edit ticket | `/tickets/{ticket}/edit` |
| Assign ticket | `POST /tickets/{ticket}/assign` |
| Reassign ticket | `PATCH /tickets/{ticket}/assign` |
| Start work | `PATCH /tickets/{ticket}/start-work` |
| Cancel ticket | `PATCH /tickets/{ticket}/cancel` |
| Admin ticket categories | `/admin/ticket-categories` |

## Database And Seeders

```bash
php artisan migrate:fresh --seed
```

The seeders create demo users, seven default ticket categories, and demo tickets across Open, Assigned, In Progress, and Cancelled states. Every seeded ticket has status history; workflow transitions are created through the same service used by the web actions.

## Not Implemented Yet

- Asset CRUD.
- Ticket comments.
- Ticket attachments.
- Resolve Ticket, resolution notes, Close Ticket, and Reopen Ticket actions.
- Dashboard statistics.

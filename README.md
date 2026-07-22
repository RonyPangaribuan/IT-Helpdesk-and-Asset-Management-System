# DelDesk

DelDesk is a Laravel monolith MVP for an IT helpdesk and basic asset management system. The project follows the requirements in `PRD.md` and is being implemented gradually by milestone.

Current milestone: Asset Management and Role-Based Dashboard.

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

## Continuous Integration

Pull requests targeting `master` are checked by the `CI` GitHub Actions workflow. The workflow runs Laravel migration and seeding, Laravel Pint formatting checks, the PHPUnit test suite, and a Vite production build.

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

### Milestone 4: Collaboration and Ticket Resolution

- Ticket discussion comments for visible participants while a ticket is active.
- Comment editing by the author and deletion by admins.
- Private ticket attachments stored on Laravel's local disk.
- Attachment upload validation for JPG, JPEG, PNG, and PDF files up to 5 MB each.
- Attachment download authorization for admins, ticket requester, and assigned technician.
- Optional attachments on ticket creation.
- Technician resolution flow with required resolution note.
- Requester or admin close action after resolution.
- Requester reopen action from Resolved, preserving the assigned technician.
- Reopened tickets can be resumed by the assigned technician or reassigned by an admin.
- Closed and Cancelled tickets are read-only for collaboration and workflow actions.

### Milestone 5: Asset Management and Role-Based Dashboard

- Asset condition enum: Good, Maintenance, Damaged, and Retired.
- Admin-only asset category CRUD with archive through soft delete.
- Asset CRUD with search, filters, pagination, active status, and soft-delete archive.
- Technicians can view assets but cannot create, edit, or archive them.
- Requesters cannot access inventory pages.
- Optional related asset selection on ticket create and eligible ticket edit.
- Ticket list/detail display linked asset information, including archived asset labels.
- Asset detail shows related ticket history scoped by role.
- Requester dashboard shows own ticket totals and recent tickets.
- Technician dashboard shows assigned ticket totals and recent assigned tickets.
- Admin dashboard shows operational ticket totals, active assets, category breakdown, priority breakdown, and recent tickets.

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
| Resolve ticket | `PATCH /tickets/{ticket}/resolve` |
| Close ticket | `PATCH /tickets/{ticket}/close` |
| Reopen ticket | `PATCH /tickets/{ticket}/reopen` |
| Add ticket comment | `POST /tickets/{ticket}/comments` |
| Update ticket comment | `PATCH /tickets/{ticket}/comments/{comment}` |
| Delete ticket comment | `DELETE /tickets/{ticket}/comments/{comment}` |
| Upload ticket attachments | `POST /tickets/{ticket}/attachments` |
| Download ticket attachment | `GET /ticket-attachments/{attachment}/download` |
| Assets | `/assets` |
| Create asset | `/assets/create` |
| Asset detail | `/assets/{asset}` |
| Edit asset | `/assets/{asset}/edit` |
| Admin ticket categories | `/admin/ticket-categories` |
| Admin asset categories | `/admin/asset-categories` |

## Database And Seeders

```bash
php artisan migrate:fresh --seed
```

The seeders create demo users, seven default ticket categories, seven default asset categories, twelve demo assets, demo tickets across Open, Assigned, In Progress, Resolved, Closed, Reopened, and Cancelled states, and demo comments. Some demo tickets are linked to demo assets. Every seeded ticket has status history; workflow transitions are created through the same service used by the web actions. Attachment metadata factories exist for tests, but the demo seeder does not create fake physical attachment files.

## Not Implemented Yet

- Milestone 6 release polish.
- Screenshots, ERD image, and demo video.
- Deployment notes and production deployment.

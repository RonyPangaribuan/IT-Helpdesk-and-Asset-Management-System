# DelDesk Implementation Plan

## Source And Scope

Primary source: `PRD.md` version 1.0.

This plan starts DelDesk as a Laravel monolith using Blade and Tailwind CSS. Work is intentionally staged. The first implementation pass only covers Milestone 1: project foundation, authentication, roles, demo users, role-aware dashboard placeholders, and tests around authentication/role restrictions.

Ticket CRUD, asset CRUD, comments, attachments, status history, assignment workflow, and dashboard statistics are planned but not implemented in Milestone 1.

## Environment Findings

- Repository state: empty Laravel application; only `PRD.md` exists.
- PHP: 8.4.8 CLI.
- Composer: 2.8.9.
- Node.js: v24.14.0.
- npm: 11.9.0 via `npm.cmd`; `npm` PowerShell shim is blocked by local execution policy.
- Laravel Framework installed: 12.64.0.
- Laravel Breeze installed: 2.4.2.
- Database CLIs available: MySQL 8.0.42 and PostgreSQL 18.3.
- PHP database extensions available: `pdo_sqlite` and `sqlite3`.
- PHP database extensions not detected: `pdo_mysql` and `pdo_pgsql`.

## Technical Decisions

1. Laravel version: target Laravel 12.x.
   - Rationale: compatible with PHP 8.4 and conservative for a portfolio MVP.
   - Laravel 13 exists, but Laravel 12 keeps the foundation stable and avoids new framework areas that are unrelated to this MVP.

2. Authentication starter: Laravel Breeze with Blade stack.
   - Rationale: the PRD asks to use Breeze if compatible, and Breeze provides Blade/Tailwind authentication scaffolding without React or Inertia.

3. Local database: SQLite for development and tests.
   - Difference from PRD: the PRD recommends PostgreSQL or MySQL.
   - Reason: local PHP has SQLite PDO support, but MySQL/PostgreSQL PDO drivers are not enabled. The schema will stay portable so production can move to MySQL or PostgreSQL after enabling the proper PHP driver.

4. Role storage: `users.role` string field.
   - Rationale: matches the PRD data model directly and avoids an unnecessary roles table for the MVP.
   - Valid values: `admin`, `technician`, `requester`.

5. Public registration role: always `requester`.
   - Rationale: the PRD forbids public creation of technician/admin accounts.
   - Technician/admin users are created by seeders now and later by admin-only user management.

6. Frontend stack: Blade, Tailwind CSS, and Breeze's small Alpine usage only.
   - No React, Inertia, real-time chat, AI, microservices, or non-MVP features.

## Ambiguities And Risk Areas In The PRD

- Registration is described as optional in required pages, but account rules allow requester self-registration. Decision: enable public requester registration in Milestone 1.
- Forgot/reset password depends on starter kit support. Decision: keep Breeze-provided password reset routes/views; mail delivery configuration is not in Milestone 1.
- Asset management is "limited" in MVP but also has full admin CRUD in scope. Decision: defer all asset work until Milestone 5 as written in the milestones.
- Attachments are "Should Have" in priority but included in core acceptance criteria. Decision: defer until Milestone 4, then implement if time allows before final MVP.
- Charts are recommended but optional. Decision: simple cards/tables only for MVP; no chart library unless explicitly needed later.
- "Archive" appears for tickets/categories/assets. Decision: implement as soft delete or `is_active` depending on model semantics in later milestones.
- Ticket comments edit window is not specified. Decision deferred to Milestone 4.
- Ticket code sequence behavior under concurrency is not specified. Decision deferred to Milestone 2; likely a service with transaction-safe sequence.

## Milestone Architecture

### Milestone 1: Project Foundation

Deliverables:

- Laravel 12 project initialized.
- Breeze Blade/Tailwind authentication installed.
- Base authenticated layout adjusted for DelDesk.
- `users` table extended with `role`, `phone`, and `is_active`.
- Role constants/helpers on `User` model.
- Public registration always creates requester accounts.
- Demo user seeder for one admin, two technicians, and three requesters.
- Role middleware for `admin`, `technician`, and `requester` route protection.
- Authenticated `/dashboard` route renders a placeholder dashboard based on role.
- Feature tests for auth access and role restrictions.

### Milestone 2: Core Ticket CRUD

Deliverables:

- Ticket category migration/model/factory/seeder.
- Ticket migration/model/factory.
- Minimal TicketPolicy for safe CRUD authorization.
- Requester ticket creation.
- Role-aware ticket listing/detail/edit/archive.
- Search, filters, pagination.
- Form Requests for ticket/category validation.

Note: the original plan placed `TicketPolicy` in Milestone 3, but Milestone 2 implementation includes a minimal policy because ticket resource CRUD requires authorization before assignment/status workflow is introduced.

### Milestone 3: Authorization And Workflow

Deliverables:

- Expanded TicketPolicy abilities for `assign`, `reassign`, `startWork`, `cancel`, and `viewStatusHistory`.
- Assignment controller/request for initial assignment and limited reassignment.
- Ticket workflow controller for Start Work and Cancel actions.
- Ticket status transition service.
- Status history records with backfill for existing Milestone 2 tickets.
- Role-based ticket views and action availability.

Milestone 3 active transitions are intentionally limited to `Open -> Assigned`, `Assigned -> In Progress`, `Open -> Cancelled`, and `Assigned -> Cancelled`. The enum still represents the full PRD status lifecycle, but Resolve, Close, and Reopen endpoints remain deferred to Milestone 4.

### Milestone 4: Collaboration Features

Deliverables:

- Ticket comments.
- Ticket attachments through Laravel Storage.
- Resolution note.
- Close and reopen actions.
- Attachment authorization.

### Milestone 5: Asset Management And Dashboard

Deliverables:

- Asset category CRUD.
- Asset CRUD.
- Ticket-to-asset relationship.
- Role dashboard statistics.
- Simple dashboard tables/cards.

### Milestone 6: Quality And Release

Deliverables:

- Expanded factories/seeders.
- Feature and unit tests from PRD list.
- Responsive UI polish.
- Error handling.
- README, screenshots, deployment notes, demo data.

## Database Design

### users

- `id`
- `name`
- `email` unique
- `email_verified_at` nullable
- `password`
- `role` indexed string, default `requester`
- `phone` nullable string
- `is_active` boolean, default true
- `remember_token`
- `created_at`
- `updated_at`

Relationships:

- Has many requested tickets through `tickets.requester_id`.
- Has many assigned tickets through `tickets.technician_id`.
- Has many comments.
- Has many uploaded attachments.
- Has many status histories as changer.

### ticket_categories

- `id`
- `name` unique
- `description` nullable
- `is_active` boolean default true
- `created_at`
- `updated_at`
- `deleted_at`

Relationships:

- Has many tickets.

### asset_categories

- `id`
- `name` unique
- `description` nullable
- `is_active` boolean default true
- `created_at`
- `updated_at`
- `deleted_at`

Relationships:

- Has many assets.

### assets

- `id`
- `asset_code` unique, indexed
- `name`
- `asset_category_id` foreign key
- `brand` nullable
- `model` nullable
- `serial_number` nullable unique
- `location`
- `condition` indexed string: `good`, `maintenance`, `damaged`, `retired`
- `description` nullable
- `is_active` boolean default true
- `created_at`
- `updated_at`
- `deleted_at`

Relationships:

- Belongs to asset category.
- Has many tickets.

### tickets

- `id`
- `ticket_code` unique, indexed
- `requester_id` foreign key to users, indexed
- `technician_id` nullable foreign key to users, indexed
- `ticket_category_id` foreign key, indexed
- `asset_id` nullable foreign key, indexed
- `title`
- `description`
- `location`
- `priority` indexed string: `low`, `medium`, `high`, `critical`
- `status` indexed string: `open`, `assigned`, `in_progress`, `resolved`, `closed`, `reopened`, `cancelled`
- `resolution_note` nullable
- `resolved_at` nullable datetime
- `closed_at` nullable datetime
- `created_at`
- `updated_at`
- `deleted_at`

Relationships:

- Belongs to requester.
- Belongs to technician.
- Belongs to ticket category.
- Belongs to asset.
- Has many comments.
- Has many attachments.
- Has many status histories.

### ticket_comments

- `id`
- `ticket_id` foreign key, indexed
- `user_id` foreign key, indexed
- `body`
- `created_at`
- `updated_at`
- `deleted_at`

Relationships:

- Belongs to ticket.
- Belongs to author user.

### ticket_attachments

- `id`
- `ticket_id` foreign key, indexed
- `uploaded_by` foreign key to users, indexed
- `original_name`
- `stored_name`
- `file_path`
- `mime_type`
- `file_size`
- `created_at`
- `updated_at`

Relationships:

- Belongs to ticket.
- Belongs to uploader user.

### ticket_status_histories

- `id`
- `ticket_id` foreign key, indexed
- `changed_by` foreign key to users, indexed
- `old_status` nullable
- `new_status`
- `note` nullable
- `created_at`

Relationships:

- Belongs to ticket.
- Belongs to changer user.

## Planned Routes

Milestone 1 routes:

- `GET /` -> redirect authenticated users to dashboard or guests to login/register entry.
- Breeze authentication routes in `routes/auth.php`.
- `GET /dashboard` -> `DashboardController`, auth required.
- `GET /admin/dashboard` -> admin-only dashboard placeholder.
- `GET /technician/dashboard` -> technician-only dashboard placeholder.
- `GET /requester/dashboard` -> requester-only dashboard placeholder.
- Breeze profile routes.

Later MVP routes:

- `resource tickets` -> `TicketController`.
- `POST tickets/{ticket}/assign` -> `TicketAssignmentController@store`.
- `POST tickets/{ticket}/assign` -> `TicketAssignmentController@store`.
- `PATCH tickets/{ticket}/assign` -> `TicketAssignmentController@update`.
- `PATCH tickets/{ticket}/start-work` -> `TicketWorkflowController@startWork`.
- `PATCH tickets/{ticket}/cancel` -> `TicketWorkflowController@cancel`.
- `PATCH tickets/{ticket}/status` -> deferred; Milestone 3 uses specific workflow actions instead of a generic status endpoint.
- `resource tickets.comments` -> `TicketCommentController` limited to store/update/destroy.
- `POST tickets/{ticket}/attachments` -> `TicketAttachmentController@store`.
- `GET ticket-attachments/{attachment}` -> `TicketAttachmentController@show`.
- `resource admin/ticket-categories` -> `TicketCategoryController`.
- `resource admin/asset-categories` -> `AssetCategoryController`.
- `resource assets` -> `AssetController`.
- `resource admin/users` -> `UserController`.

## Planned Classes

### Models

- Milestone 1: `User`.
- Later: `Ticket`, `TicketCategory`, `TicketComment`, `TicketAttachment`, `TicketStatusHistory`, `Asset`, `AssetCategory`.

### Controllers

- Milestone 1: `DashboardController`, Breeze auth/profile controllers.
- Later: `TicketController`, `TicketAssignmentController`, `TicketWorkflowController`, `TicketStatusController`, `TicketCommentController`, `TicketAttachmentController`, `TicketCategoryController`, `AssetController`, `AssetCategoryController`, `UserController`.

### Middleware

- Milestone 1: `EnsureUserHasRole`.
- Later: route-specific policy checks and possibly active-user guard.

### Policies

- Later: `TicketPolicy`, `TicketCommentPolicy`, `TicketAttachmentPolicy`, `AssetPolicy`, `UserPolicy`.
- Milestone 1 uses middleware-level role checks first; policies begin when protected resources exist.

### Form Requests

- Later: `StoreTicketRequest`, `UpdateTicketRequest`, `AssignTicketRequest`, `CancelTicketRequest`, `UpdateTicketStatusRequest`, `StoreTicketCommentRequest`, `StoreTicketAttachmentRequest`, `StoreAssetRequest`, `UpdateAssetRequest`, `StoreCategoryRequest`, `UpdateCategoryRequest`, `StoreUserRequest`, `UpdateUserRequest`.
- Milestone 1 keeps Breeze's existing registration/login/profile requests and customizes requester-only registration.

### Seeders And Factories

- Milestone 1: `DemoUserSeeder`, default `DatabaseSeeder`, default `UserFactory` updated with requester role defaults.
- Later: category, asset, ticket, comment, attachment, and status history seeders/factories.

## Testing Plan

Milestone 1 tests:

- Guests are redirected from dashboard.
- Requester registration creates `requester` role only.
- Requester cannot access admin/technician dashboards.
- Technician cannot access admin/requester dashboards.
- Admin cannot access technician/requester dashboards if routes are strict by role.
- Demo users can log in.

Later MVP tests map to the PRD testing requirements for ticket creation, authorization, assignment, status workflow, comments, attachments, category CRUD, asset CRUD, uniqueness, and dashboard statistics.

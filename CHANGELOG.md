# Changelog

All notable changes to DelDesk are documented in this file.

The format is based on Keep a Changelog, and this project uses semantic versioning after the first release.

## [1.0.0] - Unreleased

### Added

- Laravel Breeze authentication with Blade and Tailwind CSS.
- Role model for administrator, technician, and requester accounts.
- Public registration restricted to requester accounts.
- Admin-only user management with active account controls.
- Ticket CRUD, role-scoped listing, search, filters, pagination, and archive.
- Ticket assignment, reassignment, status workflow, and status history.
- Ticket comments and private attachment uploads/downloads.
- Resolution, close, reopen, and cancellation flows.
- Ticket category and asset category management.
- Basic asset management with optional ticket-to-asset relationship.
- Role-based dashboard statistics.
- Demo seed data for users, categories, assets, tickets, comments, and histories.
- GitHub Actions CI for migration, seeding, Pint, PHPUnit, Vite build, Composer audit, and Laravel cache checks.
- Release documentation, ERD, architecture notes, deployment guide, security policy, and release checklist.

### Security

- Inactive accounts cannot log in and active sessions are invalidated after deactivation.
- Ticket attachments are stored on a configurable private disk and served only through authorization checks.
- Security headers are applied to web responses.

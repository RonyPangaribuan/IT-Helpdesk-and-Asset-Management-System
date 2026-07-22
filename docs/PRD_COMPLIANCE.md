# PRD Compliance

Primary source: `PRD.md` version 1.0.

| Requirement | Status | Implementation | Test | Notes |
| --- | --- | --- | --- | --- |
| Authentication: login, logout, registration, password reset/profile | Implemented | Laravel Breeze Blade auth, customized requester-only registration, profile page, password update/reset routes | `AuthenticationTest`, `RegistrationTest`, Breeze auth tests | Mail delivery remains environment-dependent. |
| Public registration cannot create admin or technician | Implemented | `RegisteredUserController` always stores `role=requester` and `is_active=true` | `RegistrationTest` | Role and active-status injection are ignored. |
| Active account enforcement | Implemented | `LoginRequest` requires active user; `EnsureUserIsActive` logs out inactive sessions | `AuthenticationTest` | Failure message remains generic. |
| Role authorization | Implemented | `EnsureUserHasRole`, policies, scoped queries | `RoleAccessTest`, ticket/asset/user tests | Admin, technician, requester scopes are enforced. |
| Ticket CRUD | Implemented | `TicketController`, `StoreTicketRequest`, `UpdateTicketRequest`, soft delete archive | `TicketCrudTest`, `TicketListingTest` | Admin edit is intentionally limited to category, priority, and asset. |
| Ticket assignment | Implemented | `TicketAssignmentController`, `AssignTicketRequest`, `TicketWorkflowService` | `TicketAssignmentWorkflowTest`, `TicketReassignmentWorkflowTest` | Only active technicians are assignable. |
| Ticket status workflow | Implemented | `TicketStatus` enum and `TicketWorkflowService` transitions | `TicketWorkflowServiceTest`, workflow feature tests | Invalid transitions return validation-style errors. |
| Status history | Implemented | `ticket_status_histories` table and workflow history recording | `TicketStatusHistoryTest`, `DemoDataSeederTest` | Histories are read-only through UI. |
| Comments | Implemented | `TicketCommentController`, policy, partial on ticket detail | `TicketCommentTest` | Comments use soft deletes. |
| Attachments | Implemented | `TicketAttachmentService`, configurable private disk, authorized downloads | `TicketAttachmentTest`, `ReleaseReadinessTest` | No public URLs or `storage:link` are required. |
| Ticket categories | Implemented | Admin CRUD with archive through soft delete | `TicketCategoryManagementTest` | Used categories are not permanently deleted. |
| Asset categories | Implemented | Admin CRUD with archive through soft delete | `AssetCategoryManagementTest` | Used categories remain displayable through `withTrashed`. |
| Asset management | Implemented | `AssetController`, `AssetPolicy`, asset form requests, enum conditions | `AssetCrudTest`, `AssetRelatedTicketAuthorizationTest` | Technicians have read-only access; requesters are denied. |
| Ticket-to-asset relationship | Implemented | Nullable `tickets.asset_id`, ticket create/edit asset selection | `TicketAssetIntegrationTest` | Archived/inactive current assets can remain on old tickets. |
| Dashboard | Implemented | `DashboardService`, role-specific cards and simple breakdowns | `DashboardStatisticsTest` | No chart library added; PRD says charts are optional. |
| Administrator user management | Implemented | `Admin\UserController`, `UserPolicy`, user requests, `UserManagementService` | `AdminUserManagementTest` | Users are deactivated, not deleted. |
| Demo seed data | Implemented | Demo users, 7 ticket categories, 7 asset categories, 12 assets, 24 tickets | `DemoDataSeederTest`, `TicketStatusHistoryTest` | Demo attachments are not faked without physical files. |
| Factory quality | Implemented | Role states, active/inactive users, consistent ticket/asset states, attachment file creation | Related feature tests | Factories support deterministic test setup. |
| Security headers | Implemented | `AddSecurityHeaders` middleware on web responses | `ReleaseReadinessTest` | CSP intentionally not added to avoid breaking Blade/Vite. |
| Custom error pages | Implemented | `resources/views/errors/403.blade.php`, `404`, `419`, `500` | `ReleaseReadinessTest` | Pages do not expose stack traces or server paths. |
| Public landing page | Implemented | Branded `welcome.blade.php` and cache-safe `Route::view` | `ReleaseReadinessTest` | No demo credentials or internal stats are shown. |
| Production cache compatibility | Implemented | Route closure removed; config/route/view cache verified locally and in CI | `ReleaseReadinessTest`, final verification | Generated cache files are not committed. |
| GitHub Actions CI | Implemented | `.github/workflows/ci.yml` job `laravel-quality` | Local YAML and command verification | Branch-level GitHub result requires push and completed Actions run. |
| README final | Implemented | Portfolio-ready README with install, demo, CI, docs, limits | Manual review | No live URL is claimed. |
| ERD documentation | Implemented | `docs/ERD.md` Mermaid ERD from actual migrations | Manual review | PNG export is not claimed. |
| Architecture documentation | Implemented | `docs/ARCHITECTURE.md` | Manual review | Monolith boundaries and request lifecycle documented. |
| Deployment guide | Implemented | `docs/DEPLOYMENT.md`, `.env.production.example` | Manual review | Actual external deployment is not performed. |
| Screenshots | Requires Manual Action | `docs/SCREENSHOT_CHECKLIST.md`, `docs/screenshots/.gitkeep` | Not automated | Real screenshots still need to be captured. |
| Demo video | Requires Manual Action | `docs/DEMO_SCRIPT.md` | Not automated | Demo video is marked "To be recorded". |
| Live deployment URL | Requires Manual Action | Deployment guide only | Not automated | No target hosting or credentials were provided. |
| Use case diagram | Not Implemented | Not part of Milestone 6 detailed deliverables | None | PRD portfolio section recommends it; ERD and architecture docs are included. |
| Notification/email notification | Out of Scope | Not implemented | N/A | Explicitly excluded from MVP Milestone 6 request. |
| SLA, QR/barcode, procurement, depreciation, REST API, Sanctum, React/Inertia, WebSocket, AI, mobile, export, multi-tenant | Out of Scope | Not implemented | N/A | Explicitly excluded by PRD and Milestone 6 request. |

# Demo Video Script

Status: To be recorded.

Target length: 3 to 5 minutes.

## Demo Accounts

All local demo accounts use password `password`.

| Role | Email |
| --- | --- |
| Admin | `admin@deldesk.test` |
| Technician | `technician1@deldesk.test` |
| Technician | `technician2@deldesk.test` |
| Requester | `requester1@deldesk.test` |
| Requester | `requester2@deldesk.test` |
| Requester | `requester3@deldesk.test` |

## Script

1. Introduce the problem: IT requests are often scattered across chat, verbal reports, and spreadsheets.
2. Introduce DelDesk as a Laravel MVP for ticket lifecycle tracking and basic asset management.
3. Open the landing page and briefly show Login/Register entry points.
4. Log in as `requester1@deldesk.test`.
5. Show the requester dashboard and recent tickets.
6. Create a new ticket with category, priority, location, related asset, and one attachment.
7. Open the ticket detail page and show initial status history, discussion, and attachment section.
8. Log out and log in as `admin@deldesk.test`.
9. Show the admin dashboard, ticket totals, category breakdown, priority breakdown, and active asset count.
10. Open the new Open ticket and assign `technician1@deldesk.test`.
11. Show status history after assignment.
12. Log out and log in as `technician1@deldesk.test`.
13. Open assigned tickets, start work, add a comment, and resolve the ticket with a resolution note.
14. Log out and log back in as the requester.
15. Open the resolved ticket and either close it or reopen it with a reason.
16. Show asset management list and an asset detail page with related ticket history.
17. Show admin user management and active/inactive account controls.
18. Show terminal output for `php artisan test` and mention GitHub Actions CI checks.
19. Close with project status: MVP v1.0.0 release candidate, pending real deployment, screenshots, and recorded demo video.

## Recording Checklist

- [ ] Use local/demo database only.
- [ ] Do not show `.env` or secrets.
- [ ] Do not claim live deployment unless a real URL has been verified.
- [ ] Show actual test output or GitHub Actions success only after it exists.

# Manual QA Checklist

Use this document for human verification of deskIT before deployment and release. Automated tests do not replace these checks.

For each test case, select exactly one status:

- **Not tested:** the case has not been executed.
- **Passed:** the observed result matches the expected result.
- **Failed:** the observed result does not match the expected result.
- **Blocked:** the case cannot be completed because of an environment or dependency issue.

All status checkboxes are intentionally left unchecked until a tester executes the case. Add a concise note, screenshot path, issue link, or other evidence for every failed or blocked case.

## Test Environment

| Field | Value |
| --- | --- |
| Commit SHA | |
| Branch | |
| PHP version | |
| Laravel version | |
| Database | |
| Browser | |
| Viewport | |
| Tester | |
| Test date | |

## Authentication

| Test case | Not tested | Passed | Failed | Blocked | Notes/evidence |
| --- | --- | --- | --- | --- | --- |
| Landing page opens. | [ ] | [ ] | [ ] | [ ] | |
| Registration creates a requester account. | [ ] | [ ] | [ ] | [ ] | |
| Requester can log in. | [ ] | [ ] | [ ] | [ ] | |
| Technician can log in. | [ ] | [ ] | [ ] | [ ] | |
| Administrator can log in. | [ ] | [ ] | [ ] | [ ] | |
| Invalid credentials are rejected with a generic message. | [ ] | [ ] | [ ] | [ ] | |
| Inactive account login is rejected. | [ ] | [ ] | [ ] | [ ] | |
| Logout ends the authenticated session. | [ ] | [ ] | [ ] | [ ] | |
| Profile name and email can be updated. | [ ] | [ ] | [ ] | [ ] | |
| Password can be updated with the current password. | [ ] | [ ] | [ ] | [ ] | |

## Requester Workflow

| Test case | Not tested | Passed | Failed | Blocked | Notes/evidence |
| --- | --- | --- | --- | --- | --- |
| Create a ticket without an asset. | [ ] | [ ] | [ ] | [ ] | |
| Create a ticket with an active asset. | [ ] | [ ] | [ ] | [ ] | |
| Upload a valid JPG, PNG, or PDF attachment. | [ ] | [ ] | [ ] | [ ] | |
| Invalid attachment type is rejected. | [ ] | [ ] | [ ] | [ ] | |
| Attachment larger than 5 MB is rejected. | [ ] | [ ] | [ ] | [ ] | |
| Edit an eligible Open and unassigned ticket. | [ ] | [ ] | [ ] | [ ] | |
| Assigned ticket cannot be edited by the requester. | [ ] | [ ] | [ ] | [ ] | |
| Add a comment to an active visible ticket. | [ ] | [ ] | [ ] | [ ] | |
| Download an authorized private attachment. | [ ] | [ ] | [ ] | [ ] | |
| Download of another requester's attachment is denied. | [ ] | [ ] | [ ] | [ ] | |
| Cancel an eligible Open and unassigned ticket. | [ ] | [ ] | [ ] | [ ] | |
| Close a Resolved ticket. | [ ] | [ ] | [ ] | [ ] | |
| Reopen a Resolved ticket with a reason. | [ ] | [ ] | [ ] | [ ] | |
| Closed ticket is read-only. | [ ] | [ ] | [ ] | [ ] | |
| Cancelled ticket is read-only. | [ ] | [ ] | [ ] | [ ] | |

## Administrator Workflow

| Test case | Not tested | Passed | Failed | Blocked | Notes/evidence |
| --- | --- | --- | --- | --- | --- |
| View all tickets. | [ ] | [ ] | [ ] | [ ] | |
| Search and filter tickets. | [ ] | [ ] | [ ] | [ ] | |
| Assign an active technician. | [ ] | [ ] | [ ] | [ ] | |
| Reassign an eligible ticket. | [ ] | [ ] | [ ] | [ ] | |
| Cancel an eligible ticket. | [ ] | [ ] | [ ] | [ ] | |
| Archive a ticket. | [ ] | [ ] | [ ] | [ ] | |
| Create a user with an allowed role. | [ ] | [ ] | [ ] | [ ] | |
| Edit a user. | [ ] | [ ] | [ ] | [ ] | |
| Reset a user's password. | [ ] | [ ] | [ ] | [ ] | |
| Deactivate an eligible user. | [ ] | [ ] | [ ] | [ ] | |
| Cannot deactivate own administrator account. | [ ] | [ ] | [ ] | [ ] | |
| Cannot remove the final active administrator. | [ ] | [ ] | [ ] | [ ] | |
| Cannot deactivate a technician with active assigned tickets. | [ ] | [ ] | [ ] | [ ] | |
| Manage ticket categories. | [ ] | [ ] | [ ] | [ ] | |
| Manage asset categories. | [ ] | [ ] | [ ] | [ ] | |
| Create an asset. | [ ] | [ ] | [ ] | [ ] | |
| Edit an asset. | [ ] | [ ] | [ ] | [ ] | |
| Archive an asset. | [ ] | [ ] | [ ] | [ ] | |

## Technician Workflow

| Test case | Not tested | Passed | Failed | Blocked | Notes/evidence |
| --- | --- | --- | --- | --- | --- |
| View only assigned tickets. | [ ] | [ ] | [ ] | [ ] | |
| Cannot view another technician's ticket. | [ ] | [ ] | [ ] | [ ] | |
| Start Work on an assigned ticket. | [ ] | [ ] | [ ] | [ ] | |
| Add a comment to an assigned active ticket. | [ ] | [ ] | [ ] | [ ] | |
| Upload an attachment to an assigned active ticket. | [ ] | [ ] | [ ] | [ ] | |
| Resolve an In Progress ticket with a resolution note. | [ ] | [ ] | [ ] | [ ] | |
| Cannot resolve without a resolution note. | [ ] | [ ] | [ ] | [ ] | |
| Resume a Reopened ticket. | [ ] | [ ] | [ ] | [ ] | |
| Asset pages are read-only. | [ ] | [ ] | [ ] | [ ] | |
| Administrator pages are denied. | [ ] | [ ] | [ ] | [ ] | |

## Ticket History

| Test case | Not tested | Passed | Failed | Blocked | Notes/evidence |
| --- | --- | --- | --- | --- | --- |
| Creation history is recorded. | [ ] | [ ] | [ ] | [ ] | |
| Assignment history is recorded. | [ ] | [ ] | [ ] | [ ] | |
| Start Work history is recorded. | [ ] | [ ] | [ ] | [ ] | |
| Resolution history is recorded. | [ ] | [ ] | [ ] | [ ] | |
| Reopen history is recorded. | [ ] | [ ] | [ ] | [ ] | |
| Close history is recorded. | [ ] | [ ] | [ ] | [ ] | |
| User and timestamp are displayed. | [ ] | [ ] | [ ] | [ ] | |
| Historical records cannot be edited. | [ ] | [ ] | [ ] | [ ] | |

## Security And Authorization

| Test case | Not tested | Passed | Failed | Blocked | Notes/evidence |
| --- | --- | --- | --- | --- | --- |
| Guest is redirected from protected routes. | [ ] | [ ] | [ ] | [ ] | |
| Requester is denied administrator routes. | [ ] | [ ] | [ ] | [ ] | |
| Technician is denied administrator routes. | [ ] | [ ] | [ ] | [ ] | |
| Requester is denied asset inventory routes. | [ ] | [ ] | [ ] | [ ] | |
| Unauthorized attachment download is denied. | [ ] | [ ] | [ ] | [ ] | |
| Direct URL manipulation returns 403 or 404. | [ ] | [ ] | [ ] | [ ] | |
| An inactive authenticated session is invalidated. | [ ] | [ ] | [ ] | [ ] | |
| Expected security headers are present. | [ ] | [ ] | [ ] | [ ] | |
| Error pages do not expose stack traces, paths, queries, or secrets. | [ ] | [ ] | [ ] | [ ] | |

## Responsive QA

At each viewport, check the landing page, login, dashboard, navigation, ticket list, ticket detail, representative form, confirmation modal, long content, and absence of horizontal page overflow.

| Viewport | Not tested | Passed | Failed | Blocked | Notes/evidence |
| --- | --- | --- | --- | --- | --- |
| 375px | [ ] | [ ] | [ ] | [ ] | |
| 430px | [ ] | [ ] | [ ] | [ ] | |
| 768px | [ ] | [ ] | [ ] | [ ] | |
| 1024px | [ ] | [ ] | [ ] | [ ] | |
| 1280px | [ ] | [ ] | [ ] | [ ] | |
| 1440px | [ ] | [ ] | [ ] | [ ] | |

## Accessibility QA

| Test case | Not tested | Passed | Failed | Blocked | Notes/evidence |
| --- | --- | --- | --- | --- | --- |
| All primary workflows can be completed by keyboard. | [ ] | [ ] | [ ] | [ ] | |
| Interactive controls have a visible focus indicator. | [ ] | [ ] | [ ] | [ ] | |
| Inputs have programmatic labels. | [ ] | [ ] | [ ] | [ ] | |
| Validation errors are associated with the relevant controls. | [ ] | [ ] | [ ] | [ ] | |
| Icon-only buttons have accessible names. | [ ] | [ ] | [ ] | [ ] | |
| Mobile menu updates `aria-expanded`. | [ ] | [ ] | [ ] | [ ] | |
| Confirmation modal exposes dialog semantics and a name. | [ ] | [ ] | [ ] | [ ] | |
| Escape closes the modal and focus behavior is usable. | [ ] | [ ] | [ ] | [ ] | |
| Heading hierarchy is logical. | [ ] | [ ] | [ ] | [ ] | |
| Text and control contrast is sufficient. | [ ] | [ ] | [ ] | [ ] | |
| Status is communicated by text as well as color. | [ ] | [ ] | [ ] | [ ] | |

## QA Summary

- Passed:
- Failed:
- Blocked:
- Bugs created:
- Release recommendation:

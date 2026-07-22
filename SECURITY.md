# Security Policy

## Supported Versions

DelDesk will support the `1.0.x` line after the first public release.

| Version | Supported |
| --- | --- |
| 1.0.x | Yes, after release |
| Earlier pre-release commits | No formal support |

## Reporting a Vulnerability

Please report security issues privately to the repository owner through an available private GitHub channel. Do not open a public issue for suspected vulnerabilities.

Include:

- A concise description of the issue.
- Steps to reproduce.
- Affected route, controller, or component if known.
- Impact and suggested severity.
- Any proof-of-concept details that do not expose real secrets or user data.

## Security Practices

- Passwords are hashed through Laravel's hashed cast.
- Login uses Laravel Breeze rate limiting.
- Public registration always creates requester accounts.
- Inactive accounts cannot authenticate.
- Protected routes use authentication, active-account checks, role middleware, and policies.
- Ticket attachments are private and downloaded only through an authorized controller.
- User-provided output is rendered through escaped Blade syntax.
- Demo credentials are for local demonstration only and must not be reused in production.

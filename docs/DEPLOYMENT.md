# Deployment Guide

This guide is platform-neutral for PHP hosting or a VPS. It does not deploy DelDesk to any external provider and does not include real credentials.

## Requirements

- PHP 8.2 or newer.
- Composer 2.
- Node.js for the build stage.
- PostgreSQL or MySQL for production.
- Web server document root pointed to `public/`.
- HTTPS enabled.
- Writable directories:
  - `storage`
  - `bootstrap/cache`
- Persistent private storage for ticket attachments when using the local disk.

## Environment

Start from `.env.production.example` and create a real `.env` on the server.

Production requirements:

- Generate `APP_KEY` during deployment.
- Set `APP_DEBUG=false`.
- Store database credentials in the hosting secret manager when available.
- Set `SESSION_SECURE_COOKIE=true` when using HTTPS.
- Use `TICKET_ATTACHMENT_DISK=local` only when private persistent storage is available.
- Configure a private S3-compatible disk if local persistent storage is not suitable.
- Never commit `.env`.

## Build And Release Commands

Run these commands during deployment:

```bash
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Important:

- Do not run `php artisan migrate:fresh` in production.
- Do not run demo seeders in production automatically.
- Use `php artisan migrate --force`.
- Back up the database before migration.
- Back up attachment storage before deployments that touch storage configuration.

## Web Server

Configure the web server to serve only the `public/` directory. Requests should be routed through `public/index.php`.

Ensure the application can write:

```text
storage/
bootstrap/cache/
```

## Health Check

Use Laravel's built-in health endpoint:

```text
/up
```

## Logging And Retention

- Set `LOG_LEVEL=warning` or stricter for production.
- Configure log rotation or platform log retention.
- Do not expose logs through the public web root.

## Backups

Back up:

- Production database.
- Private ticket attachment storage.
- Environment secret configuration through the hosting provider's backup mechanism.

Test restore procedures before relying on backups.

## Rollback Strategy

Recommended rollback flow:

1. Keep the previous release artifact available.
2. Put the app into maintenance mode if needed.
3. Restore previous code.
4. Restore database only if the migration cannot be safely rolled back.
5. Restore attachment storage if storage changes caused the incident.
6. Clear and rebuild caches.
7. Run smoke tests.
8. Disable maintenance mode.

## Deployment Checklist

- [ ] Production `.env` created outside Git.
- [ ] `APP_DEBUG=false`.
- [ ] HTTPS configured.
- [ ] Session secure cookie enabled.
- [ ] Database backup completed.
- [ ] Attachment storage backup completed.
- [ ] Persistent private attachment storage confirmed.
- [ ] Composer install completed.
- [ ] Frontend build completed.
- [ ] `php artisan migrate --force` completed.
- [ ] Config, route, and view cache completed.
- [ ] `/up` health check passed.

## Post-Deployment Smoke Test

- [ ] Open landing page.
- [ ] Log in with a non-demo production admin account.
- [ ] Confirm dashboard loads.
- [ ] Create a requester account through admin user management.
- [ ] Create a test ticket.
- [ ] Assign a technician.
- [ ] Upload and download a private attachment.
- [ ] Resolve and close the ticket.
- [ ] Confirm `APP_DEBUG=false` by checking that errors do not show stack traces.

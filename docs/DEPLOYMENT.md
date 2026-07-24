# Deployment Guide

This guide is platform-neutral for PHP hosting or a VPS. It prepares deskIT for deployment but does not claim that an external deployment has been completed.

## Runtime Requirements

- PHP 8.2 or newer.
- PHP extensions required by Laravel and the selected database, including `mbstring`, `dom`, `fileinfo`, and the appropriate PDO driver.
- Composer 2.
- PostgreSQL or MySQL for production.
- Node.js and npm during the frontend build stage.
- A web server whose document root points to `public/`.
- HTTPS.
- Writable `storage/` and `bootstrap/cache/` directories.
- Persistent private storage for ticket attachments.

## Production Environment

Create the real `.env` outside Git, starting from `.env.production.example`.

Set and verify:

```text
APP_NAME=deskIT
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
```

Generate `APP_KEY` once for a new environment:

```bash
php artisan key:generate
```

Keep that key in the platform's secret manager. Do not regenerate it during routine releases because existing encrypted data and sessions depend on it.

Configure the selected production database:

```text
DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Use `mysql` and port `3306` when MySQL is selected. Confirm the matching PHP PDO extension is enabled before deployment.

## Session, Cache, And Queue

The production example uses database-backed sessions and cache:

```text
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync
```

The existing migrations create the required session, cache, and job tables. `sync` is sufficient for the current MVP because it has no required background-job workflow. If a queue worker is introduced later, configure and supervise it explicitly before changing `QUEUE_CONNECTION`.

## Private Attachment Storage

Ticket files use:

```text
FILESYSTEM_DISK=local
TICKET_ATTACHMENT_DISK=local
```

The default local disk writes below `storage/app/private`. This directory must persist across releases and must not be served directly by the web server. Attachment downloads must continue through the authorized Laravel controller.

A private S3-compatible disk may be used only after it is configured in Laravel, tested for authorized upload/download, and included in the backup plan.

Do not run `php artisan storage:link` for private ticket attachments.

## Install And Build

Run from a release artifact or clean checkout:

```bash
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Important:

- Never run `php artisan migrate:fresh` in production.
- Never run demo seeders automatically in production.
- Back up the database before applying migrations.
- Keep private attachment storage outside disposable release directories or mount it persistently.
- Run `php artisan optimize:clear` before rebuilding caches when replacing an existing release.

## Web Server

Point the document root to:

```text
/path/to/deskit/public
```

Route requests through `public/index.php`. Do not expose the repository root, `.env`, `storage/`, vendor metadata, logs, or private attachments.

Enable HTTPS and redirect HTTP traffic to HTTPS. Confirm secure cookies are enabled after TLS is active.

## Storage Permissions

The PHP process must be able to write:

```text
storage/
bootstrap/cache/
```

Use the minimum ownership and permissions supported by the server. Do not make the entire repository world-writable.

## Production Administrator

Do not reuse `admin@deskit.test`, the demo password, or `DemoUserSeeder`.

Create the initial administrator through a controlled one-time process, such as an interactive Laravel console on the server:

```bash
php artisan tinker
```

Create a `User` with `role` set to `User::ROLE_ADMIN`, `is_active` set to `true`, a production email, and a unique password hashed through Laravel. Keep real credentials out of shell history, deployment scripts, logs, and Git. After the initial administrator exists, additional accounts can be created through administrator user management.

## Health Check

Laravel exposes:

```text
/up
```

Check it after the application, database, caches, and web server are ready. A healthy endpoint does not replace the workflow smoke test.

## Logging

- Use `LOG_LEVEL=warning` or a stricter production level.
- Configure platform log retention or log rotation.
- Keep logs outside the public web root.
- Verify error responses do not display stack traces when `APP_DEBUG=false`.

## Backups

Back up:

- The production database.
- Private ticket attachment storage.
- Environment secrets through the hosting platform's secure backup mechanism.

Define retention, encryption, access control, and restore ownership. Test restoration before relying on the backup plan.

## Rollback Plan

1. Keep the previous release artifact available.
2. Back up current database and attachment storage.
3. Put the application into maintenance mode if required.
4. Switch the web server or release symlink back to the previous code.
5. Restore data only when a reviewed migration or storage change requires it.
6. Run `php artisan optimize:clear`, then rebuild config, route, and view caches.
7. Run the health check and smoke test.
8. Disable maintenance mode.

Do not assume every migration can be safely reversed after production data has changed. Prefer a reviewed forward fix when rollback would risk data loss.

## Deployment Checklist

- [ ] Production `.env` created outside Git.
- [ ] `APP_ENV=production`.
- [ ] `APP_DEBUG=false`.
- [ ] Correct `APP_URL`.
- [ ] Stable `APP_KEY` stored securely.
- [ ] Production database and PDO driver verified.
- [ ] HTTPS configured.
- [ ] Secure session cookie enabled.
- [ ] Database backup completed.
- [ ] Private attachment backup completed.
- [ ] Persistent private attachment storage confirmed.
- [ ] Composer installation completed.
- [ ] Frontend production build completed.
- [ ] `php artisan migrate --force` completed.
- [ ] Config, route, and view caches completed.
- [ ] Web server document root points to `public/`.
- [ ] Production administrator created without demo credentials.
- [ ] `/up` health check passed.

## Post-Deployment Smoke Test

- [ ] Open the landing page over HTTPS.
- [ ] Log in with a non-demo production administrator.
- [ ] Confirm the administrator dashboard loads.
- [ ] Create a requester through administrator user management.
- [ ] Create a test ticket.
- [ ] Assign an active technician.
- [ ] Log in as that technician and start work.
- [ ] Add a comment.
- [ ] Upload and download a private attachment as an authorized participant.
- [ ] Confirm an unrelated user cannot download the attachment.
- [ ] Resolve and close the ticket.
- [ ] Confirm ticket history contains each transition.
- [ ] Confirm errors do not expose stack traces or server paths.

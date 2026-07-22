# DelDesk Tasks

## Tahap A: Analisis

- [x] Baca seluruh `PRD.md`.
- [x] Periksa isi repository.
- [x] Periksa environment PHP, Composer, Node.js, npm, dan database.
- [x] Identifikasi requirement ambigu, bertentangan, atau terlalu besar.
- [x] Tentukan stack awal yang sesuai dengan environment.
- [x] Rancang tabel dan relasi database.
- [x] Rancang daftar route, controller, model, policy, dan form request.
- [x] Buat `IMPLEMENTATION_PLAN.md`.
- [x] Buat `TASKS.md`.

## Milestone 1: Project Foundation

- [x] Inisialisasi Laravel 12 monolith.
- [x] Konfigurasi `.env.example` tanpa secret produksi.
- [x] Pastikan `.env` tetap tidak dikomit.
- [x] Pasang Laravel Breeze Blade.
- [x] Install dependensi frontend Tailwind/Vite.
- [x] Buat layout dasar DelDesk.
- [x] Tambahkan role `admin`, `technician`, dan `requester`.
- [x] Pastikan public registration selalu membuat role `requester`.
- [x] Tambahkan field awal user: `role`, `phone`, `is_active`.
- [x] Buat role middleware atau authorization dasar.
- [x] Daftarkan middleware alias role.
- [x] Buat dashboard placeholder berdasarkan role.
- [x] Buat seeder akun demo.
- [x] Update factory user dengan default requester.
- [x] Tambahkan feature test authentication dan pembatasan role.
- [x] Jalankan migration.
- [x] Jalankan formatter.
- [x] Jalankan build asset.
- [x] Jalankan test.

## Milestone 2: Core Ticket CRUD

- [x] Buat migration/model/factory/seeder `TicketCategory`.
- [x] Buat admin-only Ticket Category CRUD.
- [x] Buat archive category dengan `is_active=false` dan soft delete.
- [x] Buat tujuh default ticket categories.
- [x] Buat migration/model/factory/seeder `Ticket`.
- [x] Buat PHP backed enum `TicketPriority`.
- [x] Buat PHP backed enum `TicketStatus`.
- [x] Buat form request category.
- [x] Buat form request create/update ticket.
- [x] Buat minimal `TicketPolicy` untuk CRUD Milestone 2.
- [x] Implementasi create ticket requester.
- [x] Implementasi internal ticket code `TCK-YYYY-000001`.
- [x] Implementasi ticket list berdasarkan role.
- [x] Implementasi eager loading pada ticket list.
- [x] Implementasi ticket detail.
- [x] Implementasi edit eligible ticket.
- [x] Implementasi admin-only archive ticket.
- [x] Tambahkan search, filter, dan pagination.
- [x] Pastikan archived category tidak tersedia pada form ticket baru.
- [x] Pastikan ticket lama tetap menampilkan archived category.
- [x] Tambahkan guard penghapusan profil untuk user yang punya ticket.
- [x] Tambahkan feature test category management.
- [x] Tambahkan feature test ticket creation.
- [x] Tambahkan feature test listing, detail, search, filter, pagination.
- [x] Tambahkan feature test update dan archive.
- [x] Tambahkan feature test integration safety profile deletion.
- [x] Jalankan migration dan seeder Milestone 2.
- [x] Jalankan test Milestone 2.

## Milestone 3: Authorization And Workflow

- [x] Perluas `TicketPolicy` untuk assignment dan status workflow.
- [x] Implementasi admin assignment.
- [x] Implementasi reassignment terbatas untuk ticket `assigned`.
- [x] Implementasi status transition service.
- [x] Implementasi domain exception untuk transisi invalid.
- [x] Implementasi status history.
- [x] Tambahkan backfill initial history untuk ticket Milestone 2.
- [x] Buat initial history saat requester membuat ticket.
- [x] Implementasi Start Work oleh assigned technician.
- [x] Implementasi cancellation dengan reason wajib.
- [x] Tampilkan workflow action berdasarkan policy.
- [x] Tampilkan status timeline di detail ticket.
- [x] Tambahkan informasi technician pada ticket list.
- [x] Perbarui factory state workflow.
- [x] Perbarui seeder demo workflow.
- [x] Batasi teknisi hanya ticket assigned.
- [x] Batasi requester hanya ticket sendiri.
- [x] Tambahkan feature test assignment dan reassignment.
- [x] Tambahkan feature test Start Work.
- [x] Tambahkan feature test cancellation.
- [x] Tambahkan feature test initial history dan atomic rollback.
- [x] Tambahkan unit test transisi valid dan invalid.
- [x] Jalankan migration, formatter, build, dan test.

## Milestone 4: Collaboration And Ticket Resolution

- [x] Buat migration/model/factory `TicketComment`.
- [x] Buat migration/model/factory `TicketAttachment`.
- [x] Buat komentar ticket.
- [x] Batasi komentar berdasarkan visibilitas ticket dan status aktif.
- [x] Izinkan author mengedit komentar sendiri.
- [x] Izinkan admin menghapus komentar.
- [x] Buat attachment upload melalui Laravel Storage disk lokal.
- [x] Validasi JPG, JPEG, PNG, PDF maksimal 5 MB.
- [x] Batasi upload attachment untuk requester dan assigned technician pada ticket aktif.
- [x] Implementasi authorization download attachment.
- [x] Tambahkan attachment opsional pada create ticket.
- [x] Implementasi resolve dengan resolution note wajib.
- [x] Implementasi close oleh requester atau admin.
- [x] Implementasi reopen oleh requester dari status resolved.
- [x] Pastikan reopened ticket dapat dilanjutkan oleh assigned technician.
- [x] Pastikan reopened ticket dapat direassign oleh admin.
- [x] Pastikan closed/cancelled ticket read-only untuk kolaborasi.
- [x] Perbarui detail ticket menjadi partial Blade untuk report, actions, attachments, discussion, timeline, dan info.
- [x] Perbarui seeder demo untuk status Resolved, Closed, Reopened, dan komentar.
- [x] Tambahkan feature test komentar, attachment, resolve, close, reopen.
- [x] Jalankan migration, formatter, build, route audit, dan test.

## Milestone 5: Asset Management And Dashboard

- [x] Buat PHP backed enum `AssetCondition`.
- [x] Buat migration/model/factory/seeder `AssetCategory`.
- [x] Buat admin-only Asset Category CRUD.
- [x] Buat archive asset category dengan `is_active=false` dan soft delete.
- [x] Buat tujuh default asset categories.
- [x] Buat migration/model/factory/seeder `Asset`.
- [x] Buat `AssetPolicy` untuk admin, technician, dan requester.
- [x] Buat form request create/update asset category.
- [x] Buat form request create/update asset.
- [x] Implementasi asset CRUD admin.
- [x] Batasi technician hanya asset list dan detail.
- [x] Batasi requester dari seluruh inventory page.
- [x] Implementasi asset search, filter, pagination, dan empty state.
- [x] Tambahkan condition badge.
- [x] Tambahkan `asset_id` nullable pada ticket.
- [x] Hubungkan ticket dengan asset opsional.
- [x] Tambahkan asset selection pada create ticket.
- [x] Tambahkan asset update pada eligible ticket edit.
- [x] Pastikan current inactive/archived asset dapat dipertahankan pada edit ticket.
- [x] Tampilkan asset code pada ticket list.
- [x] Tampilkan related asset pada ticket detail.
- [x] Tampilkan related ticket history pada asset detail sesuai authorization.
- [x] Buat `DashboardService`.
- [x] Dashboard requester dengan statistik sendiri.
- [x] Dashboard technician dengan statistik assignment.
- [x] Dashboard admin dengan statistik operasional.
- [x] Perbarui navigation desktop dan mobile.
- [x] Tambahkan feature test asset category.
- [x] Tambahkan feature test asset CRUD.
- [x] Tambahkan feature test ticket-asset integration.
- [x] Tambahkan feature test related ticket authorization.
- [x] Tambahkan feature test dashboard per role.
- [x] Jalankan migration, formatter, build, route audit, dan test.

## Milestone 6: Quality And Release

- [x] Audit PRD terhadap implementasi.
- [x] Buat `docs/PRD_COMPLIANCE.md`.
- [x] Implementasi admin user management.
- [x] Tambahkan `UserPolicy`.
- [x] Tambahkan `StoreUserRequest` dan `UpdateUserRequest`.
- [x] Tambahkan `UserManagementService`.
- [x] Tambahkan user list dengan search, role filter, active filter, pagination, dan empty state.
- [x] Tambahkan create user untuk admin, technician, dan requester.
- [x] Tambahkan update user dengan optional password reset.
- [x] Cegah admin menonaktifkan atau menurunkan role dirinya sendiri.
- [x] Cegah last active admin dinonaktifkan atau diubah role.
- [x] Cegah technician dengan active assigned ticket dinonaktifkan.
- [x] Cegah role change yang merusak dependency requester/technician.
- [x] Pastikan tidak ada route delete/restore/force-delete user management.
- [x] Terapkan login hanya untuk active user.
- [x] Tambahkan middleware `EnsureUserIsActive`.
- [x] Tambahkan alias middleware `active`.
- [x] Terapkan `active` pada protected routes.
- [x] Logout dan invalidasi session user yang dinonaktifkan saat masih login.
- [x] Tambahkan `config/deldesk.php` untuk private attachment disk.
- [x] Perbarui attachment service/controller/test agar tidak hard-code disk.
- [x] Tambahkan `TICKET_ATTACHMENT_DISK=local` ke `.env.example`.
- [x] Perbarui demo seed data menjadi 24 tickets.
- [x] Pastikan demo seed mencakup semua status dan priority.
- [x] Pastikan demo seed mencakup comments dan status histories konsisten.
- [x] Audit dan lengkapi factory states.
- [x] Tambahkan factory attachment yang membuat private file fisik.
- [x] Tambahkan unit test ticket code generator.
- [x] Tambahkan `DemoDataSeederTest`.
- [x] Tambahkan test user management.
- [x] Tambahkan test inactive authentication.
- [x] Tambahkan test security headers.
- [x] Tambahkan test configurable attachment disk.
- [x] Tambahkan test custom error pages.
- [x] Tambahkan test route cache compatibility.
- [x] Tambahkan middleware `AddSecurityHeaders`.
- [x] Tambahkan custom error pages 403, 404, 419, dan 500.
- [x] Ganti root route closure menjadi cache-safe `Route::view`.
- [x] Ganti landing page default menjadi branded DelDesk landing page.
- [x] Perbarui navigation desktop dan mobile dengan menu Users untuk admin.
- [x] Tambahkan basic accessibility polish untuk flash messages.
- [x] Bersihkan separator encoding pada partial ticket.
- [x] Perbarui CI dengan `composer audit --locked`.
- [x] Perbarui CI dengan config/route/view cache checks.
- [x] Buat `.env.production.example`.
- [x] Tulis ulang README final.
- [x] Buat `docs/ERD.md`.
- [x] Buat `docs/ARCHITECTURE.md`.
- [x] Buat `docs/DEPLOYMENT.md`.
- [x] Buat `docs/SCREENSHOT_CHECKLIST.md`.
- [x] Buat folder `docs/screenshots/`.
- [x] Buat `docs/DEMO_SCRIPT.md`.
- [x] Buat `docs/RELEASE_CHECKLIST.md`.
- [x] Tambahkan `LICENSE`.
- [x] Tambahkan `CHANGELOG.md`.
- [x] Tambahkan `SECURITY.md`.
- [ ] Capture real screenshots.
- [ ] Record demo video.
- [ ] Complete external deployment.
- [ ] Run production smoke test.
- [ ] Confirm CI green after push.
- [ ] Create tag `v1.0.0`.
- [ ] Create GitHub Release.
- [ ] Jalankan final verification penuh sebelum merge.

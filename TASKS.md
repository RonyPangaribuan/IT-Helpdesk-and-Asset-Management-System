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

## Milestone 4: Collaboration Features

- [ ] Buat komentar ticket.
- [ ] Buat attachment upload melalui Laravel Storage.
- [ ] Validasi JPG, JPEG, PNG, PDF maksimal 5 MB.
- [ ] Implementasi authorization download attachment.
- [ ] Implementasi resolve dengan resolution note.
- [ ] Implementasi close dan reopen.
- [ ] Tambahkan feature test komentar, attachment, resolve, close, reopen.

## Milestone 5: Asset Management And Dashboard

- [ ] Buat asset category CRUD.
- [ ] Buat asset CRUD.
- [ ] Hubungkan ticket dengan asset opsional.
- [ ] Dashboard requester dengan statistik sendiri.
- [ ] Dashboard technician dengan statistik assignment.
- [ ] Dashboard admin dengan statistik operasional.
- [ ] Tambahkan test asset dan dashboard.

## Milestone 6: Quality And Release

- [ ] Lengkapi seed demo minimal sesuai PRD.
- [ ] Lengkapi factory untuk data realistis.
- [ ] Poles responsive UI.
- [ ] Tambahkan README instalasi dan demo account.
- [ ] Tambahkan ERD dan screenshot.
- [ ] Siapkan deployment.
- [ ] Jalankan test penuh sebelum rilis.

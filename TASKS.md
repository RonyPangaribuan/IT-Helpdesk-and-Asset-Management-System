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

- [ ] Buat migration/model/factory/seeder `TicketCategory`.
- [ ] Buat migration/model/factory `Ticket`.
- [ ] Buat enum atau konstanta priority/status ticket.
- [ ] Buat form request create/update ticket.
- [ ] Implementasi create ticket requester.
- [ ] Implementasi ticket list berdasarkan role.
- [ ] Implementasi ticket detail.
- [ ] Implementasi edit eligible ticket.
- [ ] Implementasi archive ticket.
- [ ] Tambahkan search, filter, dan pagination.
- [ ] Tambahkan feature test ticket CRUD dasar.

## Milestone 3: Authorization And Workflow

- [ ] Buat `TicketPolicy`.
- [ ] Implementasi admin assignment.
- [ ] Implementasi status transition service.
- [ ] Implementasi status history.
- [ ] Batasi teknisi hanya ticket assigned.
- [ ] Batasi requester hanya ticket sendiri.
- [ ] Tambahkan test transisi valid dan invalid.

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

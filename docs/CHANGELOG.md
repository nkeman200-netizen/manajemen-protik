## [2026-08-20]
### Added
- Dokumen Spesifikasi Teknis (PRD) awal untuk web manajemen Protik.
- Desain ERD untuk 5 tabel utama: users, finances, documents, violations, dan events.
- Penetapan standar presisi tipe data (Decimal untuk keuangan, Drive URL string untuk arsip dokumen).
- Pemilihan stack teknologi (Laravel 11, Livewire 3, Spatie Permission).

## [2026-08-20]
### Added
- Dokumen Spesifikasi Teknis (PRD) awal untuk web manajemen Protik.
- Desain ERD untuk 5 tabel utama: users, finances, documents, violations, dan events.
- Penetapan standar presisi tipe data (Decimal untuk keuangan, Drive URL string untuk arsip dokumen).
- Pemilihan stack teknologi (Laravel 11, Livewire 3, Spatie Permission).
## [2026-08-20]
### Changed
- Merevisi arsitektur ERD berdasarkan evaluasi alur kerja organisasi.
- Memisahkan entitas `events` (kegiatan besar) dan `meetings` (rapat rutin).
- Menambahkan `event_id` (Nullable) pada tabel `documents` untuk manajemen peran pembuatan surat.
- Menambahkan kolom `receipt_url` pada tabel `finances` untuk integrasi nota via Google Drive.
- Merubah tabel `violations` menjadi `warnings` untuk simplifikasi kultural.
## [2026-08-20]
### Changed
- Modifikasi tabel `events`: Menghapus `expense_total` (diganti agregasi dinamis) dan menambahkan `budget_approved` untuk limitasi anggaran.
- Modifikasi tabel `finances`: Menambahkan `event_id` (FK) untuk melacak transaksi per kegiatan.
- Menambahkan kolom `funding_source` (Enum: IOM, DIPA, KAS, SPONSOR) pada tabel `finances` untuk transparansi sumber dana kegiatan.
## [2026-08-20]
### Added
- Tabel `divisions` untuk manajemen struktur organisasi yang dinamis.
- Tabel pivot `meeting_attendances` untuk melacak status kehadiran rapat (Hadir, Izin, Sakit, Alpha) dan integrasi bukti WA.
### Changed
- Modifikasi tabel `events`: Menambahkan `start_date` dan `end_date` untuk dukungan visualisasi FullCalendar.js.
- Modifikasi tabel `users`: Menambahkan relasi `division_id`.
- Modifikasi tabel `meetings`: Mengubah format kolom tanggal menjadi `DateTime`.
## [2026-08-20]
### Added
- Implementasi Skema Migration untuk `divisions`, `events`, dan `finances` dengan tipe data presisi dan index database.
- Modifikasi tabel `users` untuk menyertakan `division_id` dan `status`.
- Implementasi Eloquent Models (`User`, `Event`, `Finance`) dengan konfigurasi `$fillable`, tipe *casting*, dan relasi ORM (One-to-Many).
## [2026-08-20]
### Added
- Menyelesaikan seluruh skema Migration database untuk entitas pendukung: `meetings`, `meeting_attendances`, `documents`, dan `warnings`.
- Mengonfigurasi relasi antar Eloquent Models dengan batasan (constraints) Strict Foreign Key untuk mencegah anomali data.
## [2026-08-20]
### Added
- Mengimplementasikan `FinanceController` dengan logika validasi ketat untuk mencegah pengeluaran melebihi `budget_approved` pada suatu *event*.
- Membuat `FinanceTest` (TDD) untuk memvalidasi *Happy Path* (Pemasukan) dan *Negative Scenario* (Penolakan limitasi anggaran, Error 422).
## [2026-08-20]
### Added
- Instalasi scaffolding rute API Laravel 11.
- `FinanceController` dengan logika validasi matematis untuk memblokir pengeluaran yang melebihi batas anggaran (Budget Cap).
- Modul TDD (Test Driven Development) `FinanceTest` untuk memvalidasi skenario pemasukan dan penolakan anggaran (Error 422).
### Fixed
- Menambahkan trait `HasFactory` pada model `User` dan mendefinisikan `EventFactory` untuk keperluan pengujian.
## [2026-08-20]
### Added
- Menyelesaikan seluruh operasi API CRUD (`index`, `store`) untuk entitas `Meeting`, `MeetingAttendance`, `Document`, dan `Warning`.
- Mendaftarkan rute API terkait ke dalam `routes/api.php`.
- Melengkapi *Test Suite* (TDD) untuk memvalidasi operasi *read* dan *create* pada seluruh entitas pendukung.
## [2026-08-20]
### Added
- Mengamankan seluruh arsitektur CRUD dengan pengujian otomatis. Menghasilkan 13 test dan 60 assertions yang tervalidasi sukses.
- Menutup Fase 2 (Core Domain) dengan integrasi penuh antara database, logika controller, dan rute API.
## [2026-08-20]
### Added
- Mendefinisikan matriks hak akses (*Access Control Matrix*) Fase 3 berdasarkan prinsip transparansi Open Government.
- Menetapkan 3 role utama: `admin` (Full CRUD), `member` (Read-Only, Isolated Warnings), dan `advisor` (Global Read-Only).
## [2026-08-20]
### Added
- Super Prompt konfigurasi Fase 3 (Security & Access Control) untuk agen eksekutor.
- Menetapkan skema perlindungan rute API (*Role Middleware*) dan isolasi data (*Data Isolation*) untuk surat peringatan.
## [2026-08-20]
### Added
- Menginstal dan mengonfigurasi package `spatie/laravel-permission`.
- Mengamankan seluruh rute API dengan middleware `auth:sanctum` dan `role:admin` (melindungi endpoint POST, PUT, DELETE).
- Mengimplementasikan isolasi data (Data Isolation) pada `WarningController` untuk melindungi privasi teguran anggota.
### Changed
- Meregistrasi middleware alias untuk Spatie pada `bootstrap/app.php` sesuai standar Laravel 11.
- Memperbarui seluruh *test suite* Fase 2 untuk menggunakan `actingAs` agar lolos tembok otorisasi Sanctum.
## [2026-08-20]
### Added
- Mendefinisikan PRD Fase 4 (Optimasi). Menetapkan standar pagination (15 data per halaman) dan matriks parameter filter/pencarian dinamis untuk seluruh entitas.
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
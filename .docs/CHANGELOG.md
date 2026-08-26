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
## [2026-08-20]
### Added
- TDD Feature Tests yang kompatibel dengan respons JSON Pagination (17 tests passed).
### Changed
- Mengimplementasikan `paginate(15)` dan Eloquent `when()` filter pada seluruh *Controller* utama.
## [2026-08-20]
### Added
- Mendefinisikan Draf PRD Fase 5 (Analytics & Reporting) untuk fitur Dashboard.
- Merancang arsitektur API `GET /api/dashboard/statistics` untuk agregasi keuangan dan operasional.
- Merancang arsitektur API `GET /api/dashboard/upcoming-agenda` untuk fitur Jadwal Agenda Terdekat.
## [2026-08-20]
### Added
- Membuat `DashboardController` untuk agregasi analitik keuangan dan kegiatan organisasi.
- Implementasi API Endpoint `GET /api/dashboard/statistics` untuk laporan metrik bulanan dan saldo total.
- Implementasi API Endpoint `GET /api/dashboard/upcoming-agenda` untuk jadwal 5 agenda/rapat terdekat.
- Menambahkan *Feature Test* (`DashboardTest`) untuk memvalidasi presisi kalkulasi matematis bulanan dan filter tanggal masa depan.
## [2026-08-20]
### Added
- Menyelesaikan seluruh siklus Fase 5 dengan 19 tes otomatis (99 assertions) lulus sempurna.
- Merancang Draf PRD Fase 6 (Gateway & Deployment) mencakup konfigurasi CORS, Rate Limiting, Environment Variables, dan skrip rilis.
### Penutupan Siklus SDLC Keseluruhan

Setelah agen mengeksekusi instruksi ini, seluruh 6 Fase *Software Development Life Cycle* (SDLC) yang kita mulai dari nol telah resmi berakhir. Organisasi Protik kini memiliki API Backend yang memiliki logika analitik mendalam, dikawal oleh TDD yang ketat, dan dilindungi dengan lapis otorisasi Spatie serta pembatasan *Gateway* jaringan.

Sebagai penutup sesi dan peresmian rilis versi 1.0.0, simpan pencapaianmu ke dalam Git dengan perintah terakhir ini:

**Draft `CHANGELOG.md`:**
```markdown
## [2026-08-20]
### Added
- Membuat file `deploy.sh` untuk otomatisasi skrip rilis produksi.
- Menambahkan dokumentasi variabel infrastruktur SPA pada `.env.example`.
### Changed
- Mengonfigurasi `config/cors.php` untuk mendukung kredensial SPA (*Single Page Application*) dan interaksi Frontend-Backend yang mulus.
- Mengimplementasikan *Rate Limiting* (60 request/menit) pada `AppServiceProvider` untuk mengamankan API dari eksploitasi dan serangan *DDoS/Spam*.
## [2026-08-20]
### Added
- Penutupan siklus pengembangan Backend API v1.0.0.
- Skrip deployment `deploy.sh` dan pembaruan environment variables.
## [2026-08-20]
### Added
- Mendefinisikan Draf PRD Refaktor Enterprise (Post-Release).
- Merancang pemisahan logika bisnis melalui *Service Pattern* dan *API Resources*.
- Menetapkan standardisasi *Centralized JSON Logging* terintegrasi.
### Deprecated
- Menolak arsitektur infrastruktur *Master-Slave* dan *Redis Caching* untuk mencegah *over-engineering* dan pemborosan utilitas VPS.
## [2026-08-20]
### Changed
- Refaktor arsitektur Backend dari MVC dasar menuju *Service Pattern*.
- Mengisolasi logika kalkulasi finansial ke dalam `FinanceService` dan agregasi data ke `DashboardService`.
- Mengimplementasikan `API Resources` untuk standarisasi transformasi respons JSON.
- Menerapkan *Centralized Exception Handling* pada `bootstrap/app.php` untuk standarisasi error balasan API dan mencegah kebocoran *stack trace*.
## [2026-08-20]
### Changed
- Menyelesaikan refaktor arsitektur Enterprise (Service Pattern, API Resources, Centralized Exception).
- Memodifikasi `bootstrap/app.php` untuk menangkap eksepsi otorisasi Spatie (`UnauthorizedException`) ke dalam format API JSON standar.
- Memperbarui 19 *Test Suite* (103 asersi) agar kompatibel dengan struktur paginasi `meta` dari Laravel API Resources.
## [2026-08-20]
### Added
- Membuat `AuthController` untuk menangani logika `login` dan `logout` SPA berbasis *Session*.
- Mendaftarkan rute otentikasi di `routes/web.php`.
### Changed
- Memperbarui `SANCTUM_STATEFUL_DOMAINS` dan `FRONTEND_URL` untuk mendukung port React Vite (5173).
## [2026-08-21]
### Added
- Membuat *Migration* dan Model `EventCommittee` untuk mengakomodasi struktur kepanitiaan kegiatan (*Contextual Authorization*).
- Menambahkan `RoleController`, `DivisionController`, dan `UserController` untuk antarmuka API *Master Data* organisasi.
- Menambahkan `EventCommitteeController` untuk manajemen penugasan panitia spesifik per *event*.
### Changed
- Memperluas `routes/api.php` dengan rute *Master Data* yang dilindungi oleh *middleware* Spatie `role:admin`.
## [2026-08-21]
### Added
- Membuat `EventController` dengan fungsionalitas operasi *Full CRUD*.
- Mengimplementasikan `authorizeEventAccess` pada *Base Controller* untuk menangani validasi otorisasi terisolasi berbasis peran kepanitiaan.
### Changed
- Mengembangkan *Full CRUD* (`update`, `destroy`) pada entitas `Finance`, `Document`, `Meeting`, dan `Warning`.
- Mengonfigurasi ulang `routes/api.php` untuk membebaskan rute mutasi Keuangan dan Dokumen dari batasan statis `role:admin` agar dapat dievaluasi secara dinamis oleh *Contextual Authorization*.
## [2026-08-21]
### Added
- Mengimplementasikan `authorizeEventAccess` pada *Base Controller* untuk mengawal *Contextual Authorization*.
- Mengimplementasikan operasi *Full CRUD* (API Resources) pada `EventController`, `FinanceController`, `DocumentController`, `MeetingController`, dan `WarningController`.
### Changed
- Merefaktor rute `api.php` untuk mendukung operasi *Full CRUD* dan memisahkan rute *Contextual Auth* dari *Middleware Role Admin*.
## [2026-08-21]
### Added
- Membuat *migration* `add_event_id_to_meetings_table` untuk mengelompokkan data notulensi rapat ke dalam ruang kerja *Event* spesifik.
### Changed
- Memperbarui `MeetingController` dan `api.php` untuk mengadopsi standar *Contextual Authorization* (Hak Akses BPH Pusat dan BPH Event) pada seluruh metode mutasi (*Full CRUD*).
## [2026-08-22]
### Changed
- Mengubah skema database `finances` menjadi standar Laporan Pertanggungjawaban (LPJ) dengan penambahan indeksasi matematis (*Quantity*, *Unit*, *Unit Price*).
- Memisahkan logika kalkulasi total agregat secara absolut ke dalam `FinanceService` guna mencegah manipulasi *payload*.
## [2026-08-22]
### Added
- Membuat *migration* `add_personal_details_to_users_table` untuk mengekspansi entitas pengguna dengan kolom PII administratif (`nim`, `phone`, `prodi`, `angkatan`, `address`).
- Mengimplementasikan `UserResource` untuk menstandarisasi *payload* data *User* yang dikirim ke *Frontend*.
- Membuat `ProfileController` untuk melayani fungsi mutasi data diri dan pergantian kata sandi yang dilengkapi dengan *Current Password Verification*.
## [2026-08-22]
### Fixed
- Menambal celah kebocoran data (*Data Bleeding*) pada seluruh Controller Direktori (`Document`, `Meeting`, `Finance`) dengan menerapkan filter `whereNull('event_id')` secara implisit untuk *workspace* BPH Pusat.
### Added
- Mengimplementasikan rute API baru `POST /meeting-attendances/bulk` yang melayani *Mass-Upsert* absensi rapat dengan proteksi `DB::transaction()` untuk menjaga integritas relasional data.
## [2026-08-22]
### Added
- Mengimplementasikan fitur *Audit Trail* (Log Aktivitas) menggunakan *Laravel Eloquent Observers* (`AuditObserver`) untuk mendeteksi *event* `created`, `updated`, dan `deleted` pada seluruh entitas utama.
- Membuat *Polymorphic Table* `audit_trails` untuk menyimpan rekam jejak aktor, aksi, dan matriks perubahan (*Old Values* vs *New Values*) dalam format JSON.
## [2026-08-22]
### Changed
- Merevisi otak *parser CSV Sync Engine* pada `DocumentController` untuk memetakan indeks kolom secara dinamis sesuai *Standard Operating Procedure* (SOP) format surat Protik terbaru.
- Mengimplementasikan *smart fallback logic* pada atribut `title` dan `drive_url` untuk mitigasi *null values* atau metadata kosong bawaan komputasi Pandas/SheetJS (`NaN`).
## [2026-08-23]
### Removed
- Melakukan *Strict Database Normalization* dengan mengeksekusi *drop column* `drive_url` pada tabel `documents` untuk mengeliminasi redundansi arsitektur.
- Membersihkan komponen `DocumentModal.jsx` dan *Kebab Menu Actions* dari *state* dan *payload* `drive_url` lama, menggantikannya secara eksklusif dengan `letter_link` dan `scan_link`.
## [2026-08-23]
### Added
- Mengekspansi tabel `finances` dengan standar atribut *Enterprise Accounting* (`category`, `qty`, `unit_price`, `pic`, `payment_method`).
- Mengimplementasikan `FinanceController@sync` dengan strategi arsitektur *Transactional Wipe & Reload* (`DB::transaction`) untuk memastikan *Single Source of Truth* (SSOT) dari *sheet* Kas Umum tanpa kompromi integritas.
### Changed
- Merombak antarmuka `Finance.jsx` dengan mengganti metode *Upload* manual menjadi tombol eksekusi *Cloud Sync*, dan memperbarui visualisasi *badge* kolom untuk mencakup *Category* dan *PIC*.
```markdown
## [2026-08-23]
### Fixed
- Menambal *bug array_search* yang gagal mengenali struktur *header* CSV akibat anomali *Hidden Whitespace* dengan menyuntikkan algoritma sanitasi `trim()` iteratif.
- Mengimplementasikan *Regex Currency Formatter* pada *parser* harga untuk memitigasi kegagalan konversi format *String* Rupiah bawaan Google Sheets (`RpX.XXX,00`) menjadi presisi *Float Desimal* yang valid.
## [2026-08-23]
### Fixed
- Menambal celah peringatan *False Positive* pada pelaporan diagnostik mesin *Cloud Sync* `MonthlyDueController` dengan mengimplementasikan filter `is_numeric`. Filter ini mencegah baris rekapitulasi data Excel (seperti "Total Keseluruhan") terproses sebagai target mutasi *database*.
## [2026-08-23]
### Added
- Melakukan mutasi arsitektural pada tabel `events` dengan menambahkan kolom *metadata* `document_sync_url` dan `finance_sync_url`. Modifikasi ini menginisiasi transisi sistem dari *Centralized BPH Sync* menjadi ekosistem *Distributed Cloud Sync* berskala *Multi-Event*.
## [2026-08-23]
### Changed
- Mengeksekusi *Refactoring* arsitektural skala masif: mengubah ekosistem `Meetings` menjadi entitas `Agendas`. Perubahan ini mencakup skema *Database* (kolom `start_date`, `end_date`, `location`, `pic`, `status`), Models ORM, dan relasi *Foreign Key* absensi.
- Memusnahkan `MeetingController` dan menggantinya dengan `AgendaController` yang ditenagai oleh mesin *Context-Aware Cloud Sync (Wipe & Reload)*, menjadikan *Spreadsheet* Daftar Agenda sebagai *Single Source of Truth* (SSOT) yang *Future-Proof*.
## [2026-08-23]
### Fixed
- Menambal kegagalan mutasi data (0 data tersinkronisasi) pada modul `AgendaController` dengan merevisi parser tanggal *Carbon*. Mengimplementasikan transformasi *string replacement* (`/` menjadi `-`) untuk meredam *Parsing Exception* akibat misinterpretasi format `d/m/Y` menjadi struktur kalender Amerika (`m/d/Y`).
## [2026-08-23]
### Added
- Mengimplementasikan entitas atribut `is_coordinator` (boolean) pada tabel `users` untuk mengakomodasi struktur hierarki makro organisasi.
- Mengimplementasikan tabel relasional `agenda_targets` yang menganut skema polimorfik semu (`target_type`, `target_value`), memungkinkan definisi otorisasi absensi multi-dimensi (Berdasarkan Divisi, Jabatan, Role, hingga Spesifik Entitas User / Target Lepas).
- Membuat API Endpoint terdedikasi `POST /api/agendas/{id}/targets` untuk proses *Bulk Upsert* matriks target absensi.
## [2026-08-24]
### Changed
- Menggugurkan strategi *Dummy Data Seeding* untuk beralih ke skema *Production-Ready Seeding*. Mengintegrasikan *Single Source of Truth* dari dokumen STO PROTIK 2026/2027 (31 Entitas Pengurus + 8 Divisi Resmi).
- Merefaktorisasi `DatabaseSeeder.php` untuk mengeksekusi inisialisasi relasional terpusat, mencakup pemetaan Spatie Roles (*Admin, Member, Advisor*), properti demografi anggota, dan injeksi *Event* statis "Makrab Protic 2026" beserta data transaksi historisnya sebagai referensi pengujian modul *Cloud Sync*.
## [2026-08-25]
### Changed
- Merefaktorisasi `DashboardService.php` secara menyeluruh untuk membangun ulang mesin agregasi Dasbor tingkat eksekutif.
- Mengimplementasikan kalkulasi dinamis untuk mendeteksi *Personal Dues Delinquency* (Tunggakan Kas Bulanan) berdasarkan *Auth Session*.
- Mengimplementasikan kalkulasi *Agenda Participation Rate* untuk mengekstrak rasio persentase tingkat kehadiran riwayat agenda terakhir yang diselesaikan.
- Mengisolasi arsitektur komputasi *Time-Series Chart* menjadi format multidimensi untuk mengak
## [2026-08-25]
### Changed
- Merevisi algoritma ekstraksi `MonthlyDueController` dan *Personal Dues Delinquency* pada `DashboardService` untuk mem- *filter* eksklusi (*Query Builder Exclusion*) entitas dengan *role* `advisor`. Hal ini mengamankan visibilitas hierarki dan mencegah penagihan iuran fiktif kepada Pembina Organisasi.
- Merombak arsitektur balasan metrik *Agenda Participation* dari *Single Point Indicator* menjadi
## [2026-08-25]
### Changed
- Merefaktorisasi seluruh operasi manipulasi dan ekstraksi CSV *Cloud Sync* (Agenda, Kas, Dokumen, Keuangan) dari *Controllers* ke dalam lapisan abstraksi `SyncService`. Implementasi ini mengeksekusi prinsip *Separation of Concerns* (SoC) dan memusatkan kapabilitas *Error Handling*.
- Mengganti arsitektur agregasi *Agenda Participation Rate* pada `DashboardService` dari *Collection iteration* (`->count()`) menuju perhitungan *Database-Level* (`withCount()`). Pendekatan ini mengeleminasi anomali *N+1 Query* dan mencegah *Out of Memory* (OOM) pada komputasi skala besar.
- Mengimplementasikan `LazyCollection` via metode `->cursor()` pada agregator ekspor `FinanceController` untuk mengoptimalkan *throughput* serialisasi JSON data masif tanpa membebani RAM server.
## [2026-08-25]
### Changed
- Merefaktorisasi algoritma *Cloud Sync Engine* pada `SyncService.php` (Modul Keuangan) dari strategi destruktif *Wipe & Reload* menjadi *Smart Composite Key Upsert* (`firstOrNew`). Evaluasi *Null Coalescing* (`??`) diterapkan untuk melindungi data parsial (PIC, Kategori, Sumber Dana) yang diinput melalui antarmuka web agar tidak tertimpa oleh sel kosong dari Spreadsheet.
- Mencabut limitasi skema Paginasi (15 baris) pada `UserController.php` (Modul Master Data). Data pengguna kini diekstraksi secara komprehensif (`get()`) dalam satu muatan JSON untuk memfasilitasi visibilitas hierarki pengurus secara utuh tanpa hambatan UX dari fitur tombol *Next/Prev*.
## [2026-08-25]
### Fixed
- Menyelesaikan anomali *Timezone Shift* (+7 Jam offset) yang mendistorsi validitas tampilan waktu kalender dan agenda di *Frontend*. Melakukan standardisasi variabel `'timezone'` pada `config/app.php` dari `UTC` menjadi `Asia/Jakarta` guna menyelaraskan komputasi waktu *Backend* (MySQL & Carbon) dengan interpretasi *Client-Side*.
## [2026-08-25]
### Changed
- Merefaktorisasi `SyncService.php` dengan merestorasi algoritma *Intelligent Column Hunter* (`findColIndex`) setelah melakukan evaluasi *Trade-off Big-O Notation* (memori vs fleksibilitas). Matriks pencarian kini menyertakan kombinasi *Primary Exact Match* (seperti `tanggal (yyyy-mm-dd)`, `pj/divisi`) dari *Spreadsheet* produksi dan *Secondary Fallback Match* (seperti `pic`, `tgl`). Arsitektur ini mengeleminasi kerapuhan sistem terhadap modifikasi *header* Excel sembari mempertahankan kapabilitas *Smart Upsert*.
## [2026-08-26]
### Added
- Menginjeksi *Migration* baru untuk menambahkan atribut `agenda_sync_url` pada entitas `events`, melengkapi trilogi integrasi *Cloud Sync* kepanitiaan (Agenda, Dokumen, Keuangan).
- Memperluas *Payload State* pada `EventModal.jsx` di *Frontend* dengan tata letak *3-Column Grid* untuk mengakomodasi konfigurasi URL Agenda.
### Fixed
- Menambal anomali *Data Bleeding* pada `AgendaController@sync` yang sebelumnya secara buta menarik *Global URL* BPH Pusat untuk setiap ruang kerja. *Endpoint* kini ditenagai oleh arsitektur *Context-Aware Routing* yang memvalidasi *Event ID* dan menyasar URL eksternal yang spesifik untuk tiap kepanitiaan.
## [2026-08-26]
### Fixed
- Menyelaraskan arsitektur *Relative Month Indexing* antara *Frontend* dan *Backend* (pada `DashboardService@calculateActiveMonthsPassed`). Memperbaiki kalkulasi distorsi peringatan tunggakan kas pada periode transisi organisasi (Juli - September) dengan menginisialisasi parameter operasional menjadi `0` tagihan berjalan. Notifikasi agregasi beban hutang (seperti `8 bulan`) pada antarmuka Dasbor Utama kini beroperasi sinkron dan akurat dengan realitas periode kepengurusan.
## [2026-08-26]
### Added
- Mengimplementasikan SDLC Fase 1 (Data Modeling) dan Fase 2 (Core Domain CRUD & TDD) untuk entitas `Setting` dan `Archive`.
- Membuat migrasi `settings` dan `archives`, Eloquent Model `Setting` & `Archive`, serta `SettingSeeder`.
- Mengimplementasikan `SettingController` (dengan `updateBatch` terproteksi `role:admin`) dan `ArchiveController` (Full CRUD API dengan *error handling* standar).
- Membuat pengujian otomatis `ArchiveFeatureTest` dengan cakupan seluruh endpoint dan lulus 100%.
## [2026-08-26]
### Changed
- Melakukan refaktorisasi arsitektur *Database Normalization* pada skema keanggotaan kepanitiaan Event untuk mengeleminasi ancaman *Full Table Scan* (FTS) yang menurunkan performa pada skala data yang besar. 
- Memisahkan data tekstual jabatan menjadi tabel master mandiri (`committee_positions`) yang dilengkapi indikator otorisasi mutlak berformat boolean (`is_bph`).
- Mengoptimasi logika filter pada middleware *Contextual Authorization* (`CheckEventBPH`) dari sebelumnya memanfaatkan fungsi `LIKE` (komputasi O(n)) menjadi evaluasi berbasis klausa *Foreign Key Relational* dan *Index Lookup* dengan performa konstan, menjamin stabilitas latensi server API secara permanen.
## [2026-08-26]
### Added
- Mengimplementasikan *RESTful API Controller* (`CommitteePositionController`) untuk mengelola master data jabatan kepanitiaan Event.
- Menerapkan arsitektur validasi *Request* yang ketat (termasuk *Unique Constraint Ignore* pada operasi Update) dan standardisasi *JSON Error Handling* (HTTP 200, 201, 404, 422, 500).
- Melindungi *Endpoint* `/api/committee-positions` dengan enkapsulasi otorisasi tingkat tinggi (middleware `role:admin`), menjamin bahwa hanya *Administrator Global* yang memiliki wewenang untuk meregistrasi jabatan baru atau mendelegasikan hak istimewa *Contextual BPH* (`is_bph`).
## [2026-08-26]
### Changed
- Mengeliminasi ketergantungan *hardcoded* pada berkas `.env` untuk manajemen *Cloud Sync Spreadsheet URLs*.
- Merefaktorisasi `AgendaController`, `DocumentController`, `FinanceController`, dan `MonthlyDueController` untuk menavigasi rute sinkronisasi secara asinkron dari *Database Configuration* (`settings` table), menjadikan aplikasi ini sepenuhnya *SaaS-Ready* lintas periode kepengurusan.
### Added
- Menginjeksi *Endpoint* `POST /api/settings/logo` di dalam `SettingController` untuk mengakomodasi fungsionalitas unggah berkas (JPG/PNG/SVG) langsung ke penyimpanan server *Backend* (`storage/app/public`), meniadakan dependensi pihak ketiga (*Google Drive / Image Hosting*) serta memecahkan tantangan restriksi CORS pada rendering aset antarmuka.
## [2026-08-27]
### Added
- Mengimplementasikan fitur *Read Receipt* pada modul Surat Peringatan (`warnings` table) dengan menambahkan atribut `read_at` (nullable timestamp).
- Menginjeksi *Endpoint* `PATCH /api/warnings/{warning}/read` di `WarningController` dengan proteksi otorisasi kepemilikan data (hanya pemilik SP yang dapat menandai telah dibaca).
- Memperluas `WarningResource` dan `Warning` Eloquent Model dengan casting `datetime` untuk serialisasi ISO8601 ke Frontend.
- Menambahkan *Feature Test Suite* pada `WarningTest` untuk memvalidasi *happy path* dan penolakan otorisasi (HTTP 403) bagi pengguna lain (100% lulus).
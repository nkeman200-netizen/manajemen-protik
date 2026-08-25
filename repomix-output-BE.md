This file is a merged representation of the entire codebase, combined into a single document by Repomix.

# File Summary

## Purpose
This file contains a packed representation of the entire repository's contents.
It is designed to be easily consumable by AI systems for analysis, code review,
or other automated processes.

## File Format
The content is organized as follows:
1. This summary section
2. Repository information
3. Directory structure
4. Repository files (if enabled)
5. Multiple file entries, each consisting of:
  a. A header with the file path (## File: path/to/file)
  b. The full contents of the file in a code block

## Usage Guidelines
- This file should be treated as read-only. Any changes should be made to the
  original repository files, not this packed version.
- When processing this file, use the file path to distinguish
  between different files in the repository.
- Be aware that this file may contain sensitive information. Handle it with
  the same level of security as you would the original repository.

## Notes
- Some files may have been excluded based on .gitignore rules and Repomix's configuration
- Binary files are not included in this packed representation. Please refer to the Repository Structure section for a complete list of file paths, including binary files
- Files matching patterns in .gitignore are excluded
- Files matching default ignore patterns are excluded
- Files are sorted by Git change count (files with more changes are at the bottom)

# Directory Structure
````
.docs/
  CHANGELOG.md
  MASTER_RULES.md
  prd.md
  siklus.md
app/
  Http/
    Controllers/
      AgendaAttendanceController.php
      AgendaController.php
      AuditTrailController.php
      AuthController.php
      Controller.php
      DashboardController.php
      DivisionController.php
      DocumentController.php
      EventCommitteeController.php
      EventController.php
      FinanceController.php
      MonthlyDueController.php
      ProfileController.php
      RoleController.php
      UserController.php
      WarningController.php
    Resources/
      AgendaResource.php
      DocumentResource.php
      EventResource.php
      FinanceResource.php
      MeetingAttendanceResource.php
      MeetingResource.php
      UserResource.php
      WarningResource.php
  Models/
    Agenda.php
    AgendaAttendance.php
    AgendaTarget.php
    AuditTrail.php
    Division.php
    Document.php
    Event.php
    EventCommittee.php
    Finance.php
    MonthlyDue.php
    User.php
    Warning.php
  Observers/
    AuditObserver.php
  Providers/
    AppServiceProvider.php
  Services/
    DashboardService.php
    FinanceService.php
bootstrap/
  cache/
    .gitignore
  app.php
  providers.php
config/
  app.php
  auth.php
  cache.php
  cors.php
  database.php
  filesystems.php
  logging.php
  mail.php
  permission.php
  queue.php
  sanctum.php
  services.php
  session.php
database/
  factories/
    DocumentFactory.php
    EventFactory.php
    FinanceFactory.php
    MeetingAttendanceFactory.php
    MeetingFactory.php
    UserFactory.php
    WarningFactory.php
  migrations/
    0001_01_01_000000_create_users_table.php
    0001_01_01_000001_create_cache_table.php
    0001_01_01_000002_create_jobs_table.php
    2026_08_19_181546_create_divisions_table.php
    2026_08_19_181547_create_events_table.php
    2026_08_19_181548_create_finances_table.php
    2026_08_19_183559_create_personal_access_tokens_table.php
    2026_08_19_185506_create_permission_tables.php
    2026_08_20_012800_create_meetings_table.php
    2026_08_20_012801_create_meeting_attendances_table.php
    2026_08_20_012802_create_documents_table.php
    2026_08_20_012803_create_warnings_table.php
    2026_08_21_143800_create_event_committees_table.php
    2026_08_21_170000_add_event_id_to_meetings_table.php
    2026_08_22_082000_add_personal_details_to_users_table.php
    2026_08_22_113300_create_audit_trails_table.php
    2026_08_23_000000_add_tracking_columns_to_documents_table.php
    2026_08_23_003200_drop_drive_url_from_documents_table.php
    2026_08_23_015100_add_enterprise_columns_to_finances_table.php
    2026_08_23_021800_create_monthly_dues_table.php
    2026_08_23_030600_add_sync_urls_to_events_table.php
    2026_08_23_183100_refactor_meetings_to_agendas_system.php
    2026_08_23_211700_add_is_coordinator_to_users_table.php
    2026_08_23_211701_create_agenda_targets_table.php
  seeders/
    DatabaseSeeder.php
    ProticUserSeeder.php
    RolePermissionSeeder.php
  .gitignore
public/
  .htaccess
  favicon.ico
  index.php
  robots.txt
resources/
  css/
    app.css
  js/
    app.js
  views/
    welcome.blade.php
routes/
  api.php
  console.php
  web.php
storage/
  app/
    private/
      .gitignore
    public/
      .gitignore
    .gitignore
  framework/
    cache/
      data/
        .gitignore
      .gitignore
    sessions/
      .gitignore
    testing/
      .gitignore
    views/
      .gitignore
    .gitignore
  logs/
    .gitignore
tests/
  Feature/
    AgendaAttendanceTest.php
    AgendaTest.php
    AuditTrailTest.php
    DashboardTest.php
    DocumentTest.php
    ExampleTest.php
    FinanceTest.php
    MonthlyDueTest.php
    ProfileTest.php
    SecurityTest.php
    WarningTest.php
  Unit/
    ExampleTest.php
  TestCase.php
.editorconfig
.env.example
.gitattributes
.gitignore
.npmrc
artisan
composer.json
deploy.sh
package.json
phpunit.xml
README.md
vite.config.js
````

# Files

## File: app/Http/Controllers/AgendaAttendanceController.php
````php
<?php

namespace App\Http\Controllers;

use App\Models\AgendaAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgendaAttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'agenda_id' => 'required|exists:agendas,id'
        ]);

        $attendances = AgendaAttendance::with('user')
            ->where('agenda_id', $request->agenda_id)
            ->get();

        return response()->json($attendances);
    }

    public function bulkSync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agenda_id'               => 'required|exists:agendas,id',
            'attendances'             => 'required|array',
            'attendances.*.user_id'   => 'required|exists:users,id',
            'attendances.*.status'    => 'required|in:present,permit,sick,absent',
            'attendances.*.proof_url' => 'nullable|url',
        ]);

        $agendaId = $validated['agenda_id'];

        DB::transaction(function () use ($agendaId, $validated) {
            // Hapus absensi lama agar bisa ditimpa (Wipe & Reload)
            AgendaAttendance::where('agenda_id', $agendaId)->delete();

            // Masukkan absensi baru
            foreach ($validated['attendances'] as $att) {
                AgendaAttendance::create([
                    'agenda_id' => $agendaId,
                    'user_id'   => $att['user_id'],
                    'status'    => $att['status'],
                    'proof_url' => $att['proof_url'] ?? null,
                ]);
            }
        });

        return response()->json(['message' => 'Data absensi berhasil disimpan.']);
    }
}
````

## File: app/Models/AgendaTarget.php
````php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaTarget extends Model {
    use HasFactory;

    protected $fillable = ['agenda_id', 'target_type', 'target_value'];

    public function agenda(): BelongsTo {
        return $this->belongsTo(Agenda::class);
    }
}
````

## File: database/migrations/2026_08_23_211700_add_is_coordinator_to_users_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_coordinator')->default(false)->after('division_id');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_coordinator');
        });
    }
};
````

## File: database/migrations/2026_08_23_211701_create_agenda_targets_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('agenda_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agendas')->cascadeOnDelete();
            
            // 'all', 'bph', 'coordinator', 'division', 'position', 'user'
            $table->string('target_type'); 
            
            // Berisi string, division_id, atau user_id (untuk target lepas)
            $table->string('target_value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('agenda_targets');
    }
};
````

## File: tests/Feature/AgendaAttendanceTest.php
````php
<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\AgendaAttendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgendaAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $this->user = User::factory()->create();
        $this->user->assignRole('member');
    }

    public function test_can_fetch_and_bulk_sync_agenda_attendances(): void
    {
        $agenda = Agenda::create([
            'title'      => 'Rapat Pleno',
            'start_date' => now(),
        ]);

        $attendee = User::factory()->create();

        $payload = [
            'agenda_id'   => $agenda->id,
            'attendances' => [
                [
                    'user_id' => $attendee->id,
                    'status'  => 'present',
                ],
            ],
        ];

        $postResponse = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/agenda-attendances/bulk', $payload);

        $postResponse->assertStatus(200);
        $this->assertDatabaseHas('agenda_attendances', [
            'agenda_id' => $agenda->id,
            'user_id'   => $attendee->id,
            'status'    => 'present',
        ]);

        $getResponse = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/agenda-attendances?agenda_id=' . $agenda->id);

        $getResponse->assertStatus(200);
        $getResponse->assertJsonCount(1);
    }
}
````

## File: .docs/MASTER_RULES.md
````markdown
# DOKUMEN MASTER & PROTOKOL PENGEMBANGAN (STATE MANAGEMENT)

**PERINGATAN UNTUK AI:** 
Dokumen ini adalah hukum tertinggi untuk sesi ini. Seluruh respons harus mematuhi standar arsitektur, SDLC, dan daftar periksa (checklist) di bawah ini tanpa terkecuali. Mode yang aktif adalah **Mode Profesional Vibe Coder** (Abaikan mode Tutor, berikan Roadmap teknis detail, Super Prompt, dan Full Code yang terstruktur).

## 1. STANDAR ALUR KERJA (SDLC 6 FASE)
Setiap fitur harus melewati fase ini secara berurutan. Jangan melompat ke fase berikutnya sebelum fase saat ini disetujui.
*   **Fase 1: System Design & Data Modeling** (Desain ERD, relasi tabel).
*   **Fase 2: Core Domain Implementation** (Logika CRUD dasar & **TDD / Unit Testing**).
*   **Fase 3: Security & Access Control** (Autentikasi, otorisasi, pembatasan akses).
*   **Fase 4: Optimasi & Enhancements** (Filtering, Searching, Pagination).
*   **Fase 5: Analytics & Reporting** (Agregasi data, pelaporan, dashboard).
*   **Fase 6: Gateway & Deployment** (CORS, Environment Variables, persiapan CI/CD Pipeline).

## 2. KONTROL MIKRO & ARSITEKTUR (PRD / SUPER PROMPT)
Sebelum menghasilkan kode, AI wajib merumuskan spesifikasi teknis (PRD) yang mencakup:
*   **Arsitektur:** Pemisahan lapisan yang tegas (Separation of Concerns: Controller, Logic/Service, Repository/Database).
*   **Tipe Data Presisi:** Penentuan spesifik (contoh: `UUID`, `Decimal` untuk uang, `BigInt`).
*   **Library/Package:** Tentukan secara eksplisit package apa yang digunakan dan alasannya.
*   **Kontrak API:** Tuliskan struktur JSON Request dan Response secara pasti.
*   **Function Signature:** Tentukan nama fungsi, tipe input, dan return type.
*   **Error Handling & Logging:** Standarisasi respons eror JSON global dan penggunaan HTTP Status Codes yang presisi. Tangkap pengecualian (exceptions) di tingkat Controller atau Middleware, bukan dibiarkan bocor.
*   **Database Management:** WAJIB menyertakan kode Migration dan Seeder untuk setiap skema tabel baru.
*   **Negative Scenarios:** Perencanaan fitur dan TDD wajib mencakup penanganan Edge Cases dan input yang tidak valid.  

## 3. SISTEM GERBANG PERSETUJUAN (GATING SYSTEM)
*   **ATURAN MUTLAK:** AI DILARANG memberikan *Full Code* atau *Super Prompt* sebelum menyajikan Roadmap/Draf PRD. 
*   AI wajib berhenti dan menunggu persetujuan (contoh: *"ayo lanjut"*) dari User sebelum mengeksekusi kode.

## 4. KEDISIPLINAN TRACEABILITY
*   Setiap akhir siklus fitur atau sesi koding, AI WAJIB menagih dan memberikan format pembaruan `CHANGELOG.md` (hanya poin terbaru dengan format tanggal tebal [YYYY-MM-DD]).
*   AI WAJIB memberikan perintah bash *Conventional Commits* (`git add .` dan `git commit -m "..."`).
*   Branching Strategy: Tentukan nama cabang Git sebelum memulai kode (contoh: git checkout -b feature/auth-login).

---

## 5. COMPLIANCE CHECKLIST (WAJIB DIJALANKAN AI)
Setiap kali AI diinstruksikan untuk menulis kode atau menyusun Super Prompt, AI WAJIB memunculkan checklist ini di awal respons dan memastikan semuanya tercentang (✔) sebelum menampilkan kode:

**[ ] Checklist Kepatuhan AI:**
- [ ] Apakah saya sudah memberikan Roadmap/Draf PRD dan mendapat persetujuan User?
- [ ] Apakah kode ini mematuhi Separation of Concerns (tidak ada spaghetti code)?
- [ ] Apakah tipe data, package, dan function signature sudah didefinisikan dengan jelas?
- [ ] Apakah fitur ini menyertakan pengujian otomatis (TDD/Unit Testing)?
- [ ] Apakah kode lolos format linting dan standar keamanan dasar?
- [ ] Apakah saya sudah menyertakan tagihan pembaruan CHANGELOG dan format git commit di akhir respons?
- [ ] Apakah penanganan eror (Error Handling), HTTP Status, dan skenario negatif sudah ditangani dengan baik?

*(Jika ada satu saja kotak yang tidak bisa dicentang, AI harus berhenti, merevisi kodenya sendiri, atau menanyakan detail yang kurang kepada User).*
````

## File: .docs/prd.md
````markdown
# PRODUCT REQUIREMENTS DOCUMENT (PRD)

Dokumen ini berisi spesifikasi teknis tingkat mikro yang WAJIB dipatuhi oleh agen AI saat menulis kode.

---

## [AKTIF] SIKLUS 2 - FASE 1: Backend Master Data & Contextual Auth

### 1. Arsitektur "Contextual Authorization" (Kepanitiaan)
**Masalah:** BPH Event butuh hak akses tinggi, tapi tidak boleh melihat/mengganggu Kas Umum atau Surat Peringatan global.
**Solusi:** DILARANG memberikan role 'admin' global kepada BPH Event. Gunakan tabel pivot untuk otorisasi spesifik.

**Entity: `event_committees` (Pivot Table)**
- Kolom: `id`, `event_id` (FK cascade), `user_id` (FK cascade), `position` (Enum/String: 'Ketua', 'Sekretaris', 'Bendahara', dll).
- Policy: Di Backend, buat `FinancePolicy` & `DocumentPolicy`. Izinkan user dengan `position` == 'Bendahara' di `event_id` terkait untuk Bypass role 'member' saat membuat input Kas event tersebut.

### 2. Modul Manajemen Role (Spatie)
- **Endpoint:** `GET /api/roles`, `POST /api/roles`.
- **Fungsi:** Mengelola daftar role global secara dinamis.

### 3. Modul Manajemen Anggota (Users)
- **Endpoint:** `GET /api/users`, `PUT /api/users/{id}`.
- **Fungsi:** Admin dapat mengupdate `division_id`, `status` (active/suspended), dan menyinkronkan role global Spatie (`syncRoles`).

### 4. Modul Manajemen Divisi
- **Endpoint:** `GET /api/divisions`, `POST /api/divisions`, `PUT`, `DELETE`.
````

## File: .docs/siklus.md
````markdown
# PETA JALAN PENGEMBANGAN (SIKLUS) - MANAJEMEN PROTIK

Dokumen ini melacak pergerakan makro proyek.

## SIKLUS 1: Minimum Viable Product (MVP) - [SELESAI]
- [x] Fase 1: System Design & Data Modeling (Tabel users, events, finances, documents, warnings, meetings).
- [x] Fase 2: Core Domain Implementation (CRUD & TDD).
- [x] Fase 3: Security & Access Control (Sanctum Auth & Spatie Global Roles).
- [x] Fase 4: Optimasi & Enhancements (SWR Pagination Frontend, UI/UX Layouting).
- [x] Fase 5: Analytics & Reporting (Dashboard Aggregation).
- [x] Fase 6: Gateway (Localhost SPA Gateway).

## SIKLUS 2: Protik v2.0 (Data Completeness & Practical Enhancements) - [SEDANG BERJALAN]
Fokus pada kelengkapan data, otorisasi kontekstual kepanitiaan, dan operasional praktis.
- [ ] Fase 1: System Design & Master Data API (Event Committees, Roles, Division, Users).
- [ ] Fase 2: UX Re-engineering (Pemisahan Kas Umum/Event, Search/Sort, Edit/Delete).
- [ ] Fase 3: Administrative Control (Halaman BPH Pusat vs BPH Event).
- [ ] Fase 4: Advanced Features (Export PDF/Excel, Audit Trail).
````

## File: app/Http/Controllers/AuditTrailController.php
````php
<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $audits = AuditTrail::with('user')->latest()->paginate(15);
        return response()->json($audits);
    }
}
````

## File: app/Http/Controllers/AuthController.php
````php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Kredensial tidak valid.',
            ]);
        }

        $request->session()->regenerate();

        return response()->json(['message' => 'Login success']);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }
}
````

## File: app/Http/Controllers/EventCommitteeController.php
````php
<?php

namespace App\Http\Controllers;

use App\Models\EventCommittee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventCommitteeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => ['required', 'exists:events,id'],
        ]);

        $committees = EventCommittee::with('user')
            ->where('event_id', $request->event_id)
            ->get();

        return response()->json([
            'message' => 'Success',
            'data' => $committees,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'user_id' => ['required', 'exists:users,id'],
            'position' => ['required', 'string'],
        ]);

        $committee = EventCommittee::create($validated);

        return response()->json([
            'message' => 'Success',
            'data' => $committee->load('user'),
        ], 201);
    }

    public function destroy(EventCommittee $eventCommittee): JsonResponse
    {
        $eventCommittee->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }
}
````

## File: app/Http/Controllers/MonthlyDueController.php
````php
<?php

namespace App\Http\Controllers;

use App\Models\MonthlyDue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyDueController extends Controller
{
    public function index(): JsonResponse
    {
        // Mengembalikan struktur untuk Heatmap Frontend
        $users = User::with('roles')->get();
        $dues = MonthlyDue::all();
        
        return response()->json([
            'users' => $users,
            'dues'  => $dues,
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $url = env('TRACKING_KAS_URL');
        if (!$url) return response()->json(['message' => 'URL Sinkronisasi belum dikonfigurasi.'], 500);

        $separator = str_contains($url, '?') ? '&' : '?';
        $freshUrl = $url . $separator . 'cb=' . time();

        try {
            $context = stream_context_create(['http' => ['header' => "Cache-Control: no-cache\r\n"]]);
            $csvData = file_get_contents($freshUrl, false, $context);
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            
            $idIdx = false;
            $dataStartIndex = 0;
            $bulanMap = [
                'JANUARI' => 1, 'FEBRUARI' => 2, 'MARET' => 3, 'APRIL' => 4,
                'MEI' => 5, 'JUNI' => 6, 'JULI' => 7, 'AGUSTUS' => 8,
                'SEPTEMBER' => 9, 'OKTOBER' => 10, 'NOVEMBER' => 11, 'DESEMBER' => 12
            ];
            $bulanIndexes = [];

            // Memindai baris per baris untuk mengakomodasi Multi-Row Headers (Merged Cells)
            foreach ($rows as $index => $row) {
                $cleanRow = array_map(fn($v) => strtoupper(trim($v)), $row);
                
                // Cari Index ID User (Biasanya ada di baris pertama Header)
                if (in_array('ID USER', $cleanRow)) {
                    $idIdx = array_search('ID USER', $cleanRow);
                }

                // Cari Index Bulan (Biasanya ada di baris kedua Header)
                if (in_array('OKTOBER', $cleanRow)) {
                    $dataStartIndex = $index + 1;
                    foreach ($bulanMap as $namaBulan => $angkaBulan) {
                        $idx = array_search($namaBulan, $cleanRow);
                        if ($idx !== false) {
                            $bulanIndexes[$namaBulan] = ['index' => $idx, 'month_num' => $angkaBulan];
                        }
                    }
                    break; // Selesai memindai header
                }
            }

            if (empty($bulanIndexes) || $idIdx === false) {
                return response()->json(['message' => 'Format Header (ID USER pada baris atas, dan OKTOBER pada baris bawah) tidak ditemukan.'], 400);
            }

            $successCount = 0;
            $unmatchedIds = [];

            DB::transaction(function () use ($rows, $dataStartIndex, $idIdx, $bulanIndexes, &$successCount, &$unmatchedIds) {
                for ($i = $dataStartIndex; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    if (empty($row) || count($row) < 3) continue;

                    $userId = trim($row[$idIdx] ?? '');
                    
                    // FIX: Abaikan jika kosong, NaN, atau BUKAN ANGKA (seperti baris "Total Keseluruhan")
                    if (empty($userId) || strtolower($userId) === 'nan' || !is_numeric($userId)) {
                        continue;
                    }

                    $user = User::find($userId);
                    if (!$user) {
                        $unmatchedIds[] = "Baris " . ($i + 1) . " (ID: $userId)";
                        continue; 
                    }

                    $successCount++;

                    foreach ($bulanIndexes as $bData) {
                        $idx = $bData['index'];
                        $monthNum = $bData['month_num'];
                        $valRaw = trim($row[$idx] ?? ''); 
                        
                        if (strtolower($valRaw) === 'nan' || $valRaw === '') {
                            $amount = 0;
                        } else {
                            // Filter format Rupiah: Pisahkan koma desimal, lalu ambil angkanya
                            $valNoDec = explode(',', $valRaw)[0];
                            $amount = (float) preg_replace('/[^0-9]/', '', $valNoDec);
                        }

                        $year = (int) date('Y');
                        if ($monthNum >= 7 && $monthNum <= 12) {
                            // Penyesuaian tahun kepengurusan
                        }

                        if ($amount > 0) {
                            MonthlyDue::updateOrCreate(
                                ['user_id' => $user->id, 'month' => $monthNum, 'year' => $year],
                                ['amount' => $amount]
                            );
                        } else {
                            MonthlyDue::where('user_id', $user->id)
                                ->where('month', $monthNum)
                                ->where('year', $year)
                                ->delete();
                        }
                    }
                }
            });

            $msg = "Berhasil: $successCount pengurus disinkronkan.";
            if (count($unmatchedIds) > 0) $msg .= " Peringatan: ID tidak valid pada " . count($unmatchedIds) . " baris (" . implode(', ', array_slice($unmatchedIds, 0, 3)) . "...).";

            return response()->json(['message' => $msg]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data kas.', 'error' => $e->getMessage()], 500);
        }
    }
}
````

## File: app/Http/Controllers/RoleController.php
````php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::all();

        return response()->json([
            'message' => 'Success',
            'data' => $roles,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'unique:roles,name'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return response()->json([
            'message' => 'Success',
            'data' => $role,
        ], 201);
    }
}
````

## File: app/Http/Resources/AgendaResource.php
````php
<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendaResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'          => $this->id,
            'event_id'    => $this->event_id,
            'title'       => $this->title,
            'start_date'  => $this->start_date?->format('Y-m-d H:i:s'),
            'end_date'    => $this->end_date?->format('Y-m-d H:i:s'),
            'location'    => $this->location,
            'pic'         => $this->pic,
            'status'      => $this->status,
            'minutes_url' => $this->minutes_url,
            'attendances' => $this->whenLoaded('attendances'),
            'targets'     => $this->whenLoaded('targets'),
        ];
    }
}
````

## File: app/Http/Resources/EventResource.php
````php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'description'       => $this->description,
            'budget_approved'   => (float) $this->budget_approved,
            'drive_folder_url'  => $this->drive_folder_url,
            'document_sync_url' => $this->document_sync_url,
            'finance_sync_url'  => $this->finance_sync_url,
            'start_date'        => $this->start_date?->format('Y-m-d'),
            'end_date'          => $this->end_date?->format('Y-m-d'),
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
            'committees'        => $this->whenLoaded('committees'),
            'finances'          => $this->whenLoaded('finances'),
        ];
    }
}
````

## File: app/Http/Resources/MeetingAttendanceResource.php
````php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'meeting_id' => $this->meeting_id,
            'user_id'    => $this->user_id,
            'status'     => $this->status,
            'proof_url'  => $this->proof_url,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'meeting'    => $this->whenLoaded('meeting'),
            'user'       => $this->whenLoaded('user'),
        ];
    }
}
````

## File: app/Http/Resources/UserResource.php
````php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'nim'            => $this->nim,
            'phone'          => $this->phone,
            'prodi'          => $this->prodi,
            'angkatan'       => $this->angkatan,
            'address'        => $this->address,
            'status'         => $this->status,
            'is_coordinator' => (bool) $this->is_coordinator,
            'division'       => $this->whenLoaded('division'),
            'roles'          => $this->whenLoaded('roles'),
        ];
    }
}
````

## File: app/Http/Resources/WarningResource.php
````php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarningResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'admin_id'   => $this->admin_id,
            'reason'     => $this->reason,
            'date'       => $this->date?->format('Y-m-d'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'user'       => $this->whenLoaded('user'),
            'admin'      => $this->whenLoaded('admin'),
        ];
    }
}
````

## File: app/Models/Agenda.php
````php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agenda extends Model {
    use HasFactory;

    protected $fillable = ['event_id', 'title', 'start_date', 'end_date', 'location', 'pic', 'status', 'minutes_url'];
    protected $casts = ['start_date' => 'datetime', 'end_date' => 'datetime'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function attendances(): HasMany { return $this->hasMany(AgendaAttendance::class); }

    public function targets(): HasMany {
        return $this->hasMany(AgendaTarget::class);
    }
}
````

## File: app/Models/AgendaAttendance.php
````php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaAttendance extends Model {
    use HasFactory;

    protected $fillable = ['agenda_id', 'user_id', 'status', 'proof_url'];
    
    public function agenda(): BelongsTo { return $this->belongsTo(Agenda::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
````

## File: app/Models/AuditTrail.php
````php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditTrail extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
````

## File: app/Models/EventCommittee.php
````php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventCommittee extends Model
{
    protected $fillable = ['event_id', 'user_id', 'position'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
````

## File: app/Models/MonthlyDue.php
````php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyDue extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'month', 'year', 'amount'];

    protected $casts = [
        'user_id' => 'integer',
        'month'   => 'integer',
        'year'    => 'integer',
        'amount'  => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
````

## File: app/Models/Warning.php
````php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warning extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'admin_id', 'reason', 'date'];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
````

## File: app/Observers/AuditObserver.php
````php
<?php

namespace App\Observers;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    protected function log($model, $action, $old = null, $new = null)
    {
        AuditTrail::create([
            'user_id'        => Auth::id(),
            'action'         => $action,
            'auditable_type' => get_class($model),
            'auditable_id'   => $model->id,
            'old_values'     => $old,
            'new_values'     => $new,
        ]);
    }

    public function created($model)
    {
        $this->log($model, 'created', null, $model->getAttributes());
    }

    public function updated($model)
    {
        $this->log($model, 'updated', array_intersect_key($model->getOriginal(), $model->getChanges()), $model->getChanges());
    }

    public function deleted($model)
    {
        $this->log($model, 'deleted', $model->getOriginal(), null);
    }
}
````

## File: bootstrap/cache/.gitignore
````
*
!.gitignore
````

## File: bootstrap/providers.php
````php
<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
];
````

## File: config/app.php
````php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache", "array"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
````

## File: config/auth.php
````php
<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
````

## File: config/cache.php
````php
<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache store that will be used by the
    | framework. This connection is utilized if another isn't explicitly
    | specified when running a cache operation inside the application.
    |
    */

    'default' => env('CACHE_STORE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "array", "database", "file", "memcached",
    |                    "redis", "dynamodb", "storage", "octane",
    |                    "session", "failover", "null"
    |
    */

    'stores' => [

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CACHE_CONNECTION'),
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'),
            'lock_table' => env('DB_CACHE_LOCK_TABLE'),
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'storage' => [
            'driver' => 'storage',
            'disk' => env('CACHE_STORAGE_DISK'),
            'path' => env('CACHE_STORAGE_PATH', 'framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

        'failover' => [
            'driver' => 'failover',
            'stores' => [
                'database',
                'array',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis, and DynamoDB cache
    | stores, there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-cache-'),

    /*
    |--------------------------------------------------------------------------
    | Serializable Classes
    |--------------------------------------------------------------------------
    |
    | This value determines the classes that can be unserialized from cache
    | storage. By default, no PHP classes will be unserialized from your
    | cache to prevent gadget chain attacks if your APP_KEY is leaked.
    |
    */

    'serializable_classes' => false,

];
````

## File: config/database.php
````php
<?php

use Illuminate\Support\Str;
use Pdo\Mysql;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'transaction_mode' => 'DEFERRED',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                Mysql::ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                Mysql::ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_SSLMODE', 'prefer'),
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

    ],

];
````

## File: config/filesystems.php
````php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
````

## File: config/logging.php
````php
<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that is utilized to write
    | messages to your logs. The value provided here should match one of
    | the channels present in the list of "channels" configured below.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Laravel
    | utilizes the Monolog PHP logging library, which includes a variety
    | of powerful log handlers and formatters that you're free to use.
    |
    | Available drivers: "single", "daily", "monthly", "slack", "syslog",
    |                    "errorlog", "monolog", "custom", "stack"
    |
    */

    'channels' => [

        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', (string) env('LOG_STACK', 'single')),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'max_files' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        'monthly' => [
            'driver' => 'monthly',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'max_files' => 3,
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', env('APP_NAME', 'Laravel')),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

    ],

];
````

## File: config/mail.php
````php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array. Examples of each type of mailer are provided.
    |
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers that can be used
    | when delivering an email. You may specify which one you're using for
    | your mailers below. You may also add additional mailers if needed.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "resend", "log", "array",
    |            "failover", "roundrobin"
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
            'retry_after' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all emails sent by your application to be sent from
    | the same address. Here you may specify a name and address that is
    | used globally for all emails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Laravel')),
    ],

];
````

## File: config/permission.php
````php
<?php

use Spatie\Permission\DefaultTeamResolver;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return [

    'models' => [

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your permissions. Of course, it
         * is often just the "Permission" model but you may use whatever you like.
         *
         * The model you want to use as a Permission model needs to implement the
         * `Spatie\Permission\Contracts\Permission` contract.
         */

        'permission' => Permission::class,

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         *
         * The model you want to use as a Role model needs to implement the
         * `Spatie\Permission\Contracts\Role` contract.
         */

        'role' => Role::class,

        /*
         * When using the "Teams" feature from this package, we need to know which
         * Eloquent model should be used to retrieve your teams. Of course, it
         * is often just the "Team" model but you may use whatever you like.
         */
        'team' => null,

        /*
         * When using the "HasModels" trait and passing raw IDs to syncModels,
         * attachModels, or detachModels, this model class will be used to
         * resolve those IDs. If null, defaults to the guard's model.
         */
        'default_model' => null,
    ],

    'table_names' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'roles' => 'roles',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your permissions. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'permissions' => 'permissions',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your models permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_permissions' => 'model_has_permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models roles. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_roles' => 'model_has_roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        /*
         * Change this if you want to name the related pivots other than defaults
         */
        'role_pivot_key' => null, // default 'role_id',
        'permission_pivot_key' => null, // default 'permission_id',

        /*
         * Change this if you want to name the related model primary key other than
         * `model_id`.
         *
         * For example, this would be nice if your primary keys are all UUIDs. In
         * that case, name this `model_uuid`.
         */

        'model_morph_key' => 'model_id',

        /*
         * Change this if you want to use the teams feature and your related model's
         * foreign key is other than `team_id`.
         */

        'team_foreign_key' => 'team_id',
    ],

    /*
     * When set to true, the method for checking permissions will be registered on the gate.
     * Set this to false if you want to implement custom logic for checking permissions.
     */

    'register_permission_check_method' => true,

    /*
     * When set to true, Laravel\Octane\Events\OperationTerminated event listener will be registered
     * this will refresh permissions on every TickTerminated, TaskTerminated and RequestTerminated
     * NOTE: This should not be needed in most cases, but an Octane/Vapor combination benefited from it.
     */
    'register_octane_reset_listener' => false,

    /*
     * Events will fire when a role or permission is assigned/unassigned:
     * \Spatie\Permission\Events\RoleAttachedEvent
     * \Spatie\Permission\Events\RoleDetachedEvent
     * \Spatie\Permission\Events\PermissionAttachedEvent
     * \Spatie\Permission\Events\PermissionDetachedEvent
     *
     * To enable, set to true, and then create listeners to watch these events.
     */
    'events_enabled' => false,

    /*
     * Teams Feature.
     * When set to true the package implements teams using the 'team_foreign_key'.
     * If you want the migrations to register the 'team_foreign_key', you must
     * set this to true before doing the migration.
     * If you already did the migration then you must make a new migration to also
     * add 'team_foreign_key' to 'roles', 'model_has_roles', and 'model_has_permissions'
     * (view the latest version of this package's migration file)
     */

    'teams' => false,

    /*
     * The class to use to resolve the permissions team id
     */
    'team_resolver' => DefaultTeamResolver::class,

    /*
     * Passport Client Credentials Grant
     * When set to true the package will use Passports Client to check permissions
     */

    'use_passport_client_credentials' => false,

    /*
     * When set to true, the required permission names are added to exception messages.
     * This could be considered an information leak in some contexts, so the default
     * setting is false here for optimum safety.
     */

    'display_permission_in_exception' => false,

    /*
     * When set to true, the required role names are added to exception messages.
     * This could be considered an information leak in some contexts, so the default
     * setting is false here for optimum safety.
     */

    'display_role_in_exception' => false,

    /*
     * By default wildcard permission lookups are disabled.
     * See documentation to understand supported syntax.
     */

    'enable_wildcard_permission' => false,

    /*
     * The class to use for interpreting wildcard permissions.
     * If you need to modify delimiters, override the class and specify its name here.
     */
    // 'wildcard_permission' => Spatie\Permission\WildcardPermission::class,

    /* Cache-specific settings */

    'cache' => [

        /*
         * By default all permissions are cached for 24 hours to speed up performance.
         * When permissions or roles are updated the cache is flushed automatically.
         */

        'expiration_time' => DateInterval::createFromDateString('24 hours'),

        /*
         * The cache key used to store all permissions.
         */

        'key' => 'spatie.permission.cache',

        /*
         * You may optionally indicate a specific cache driver to use for permission and
         * role caching using any of the `store` drivers listed in the cache.php config
         * file. Using 'default' here means to use the `default` set in cache.php.
         */

        'store' => 'default',
    ],
];
````

## File: config/queue.php
````php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue supports a variety of backends via a single, unified
    | API, giving you convenient access to each backend using identical
    | syntax for each. The default queue connection is defined below.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection options for every queue backend
    | used by your application. An example configuration is provided for
    | each backend supported by Laravel. You're also free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis",
    |          "deferred", "background", "failover", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => env('DB_QUEUE_TABLE', 'jobs'),
            'queue' => env('DB_QUEUE', 'default'),
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => env('BEANSTALKD_QUEUE_HOST', 'localhost'),
            'queue' => env('BEANSTALKD_QUEUE', 'default'),
            'retry_after' => (int) env('BEANSTALKD_QUEUE_RETRY_AFTER', 90),
            'block_for' => 0,
            'after_commit' => false,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
            'block_for' => null,
            'after_commit' => false,
        ],

        'deferred' => [
            'driver' => 'deferred',
        ],

        'background' => [
            'driver' => 'background',
        ],

        'failover' => [
            'driver' => 'failover',
            'connections' => [
                'database',
                'deferred',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Job Batching
    |--------------------------------------------------------------------------
    |
    | The following options configure the database and table that store job
    | batching information. These options can be updated to any database
    | connection and table which has been defined by your application.
    |
    */

    'batching' => [
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'job_batches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control how and where failed jobs are stored. Laravel ships with
    | support for storing failed jobs in a simple file or in a database.
    |
    | Supported drivers: "database-uuids", "dynamodb", "file", "null"
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'failed_jobs',
    ],

];
````

## File: config/sanctum.php
````php
<?php

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Laravel\Sanctum\Http\Middleware\AuthenticateSession;
use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from the following domains / hosts will receive stateful API
    | authentication cookies. Typically, these should include your local
    | and production domains which access your API via a frontend SPA.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        Sanctum::currentApplicationUrlWithPort(),
        // Sanctum::currentRequestHost(),
    ))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | This array contains the authentication guards that will be checked when
    | Sanctum is trying to authenticate a request. If none of these guards
    | are able to authenticate the request, Sanctum will use the bearer
    | token that's present on an incoming request for authentication.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until an issued token will be
    | considered expired. This will override any values set in the token's
    | "expires_at" attribute, but first-party sessions are not affected.
    |
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Sanctum can prefix new tokens in order to take advantage of numerous
    | security scanning initiatives maintained by open source platforms
    | that notify developers if they commit tokens into repositories.
    |
    | See: https://docs.github.com/en/code-security/secret-scanning/about-secret-scanning
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPA with Sanctum you may need to
    | customize some of the middleware Sanctum uses while processing the
    | request. You may change the middleware listed below as required.
    |
    */

    'middleware' => [
        'authenticate_session' => AuthenticateSession::class,
        'encrypt_cookies' => EncryptCookies::class,
        'validate_csrf_token' => ValidateCsrfToken::class,
    ],

];
````

## File: config/services.php
````php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
````

## File: config/session.php
````php
<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    |
    | This option determines the default session driver that is utilized for
    | incoming requests. Laravel supports a variety of storage options to
    | persist session data. Database storage is a great default choice.
    |
    | Supported: "file", "cookie", "database", "memcached",
    |            "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Here you may specify the number of minutes that you wish the session
    | to be allowed to remain idle before it expires. If you want them
    | to expire immediately when the browser is closed then you may
    | indicate that via the expire_on_close configuration option.
    |
    */

    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    /*
    |--------------------------------------------------------------------------
    | Session Encryption
    |--------------------------------------------------------------------------
    |
    | This option allows you to easily specify that all of your session data
    | should be encrypted before it's stored. All encryption is performed
    | automatically by Laravel and you may use the session like normal.
    |
    */

    'encrypt' => env('SESSION_ENCRYPT', false),

    /*
    |--------------------------------------------------------------------------
    | Session File Location
    |--------------------------------------------------------------------------
    |
    | When utilizing the "file" session driver, the session files are placed
    | on disk. The default storage location is defined here; however, you
    | are free to provide another location where they should be stored.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Connection
    |--------------------------------------------------------------------------
    |
    | When using the "database" or "redis" session drivers, you may specify a
    | connection that should be used to manage these sessions. This should
    | correspond to a connection in your database configuration options.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Table
    |--------------------------------------------------------------------------
    |
    | When using the "database" session driver, you may specify the table to
    | be used to store sessions. Of course, a sensible default is defined
    | for you; however, you're welcome to change this to another table.
    |
    */

    'table' => env('SESSION_TABLE', 'sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Cache Store
    |--------------------------------------------------------------------------
    |
    | When using one of the framework's cache driven session backends, you may
    | define the cache store which should be used to store the session data
    | between requests. This must match one of your defined cache stores.
    |
    | Affects: "dynamodb", "memcached", "redis"
    |
    */

    'store' => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Session Sweeping Lottery
    |--------------------------------------------------------------------------
    |
    | Some session drivers must manually sweep their storage location to get
    | rid of old sessions from storage. Here are the chances that it will
    | happen on a given request. By default, the odds are 2 out of 100.
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name
    |--------------------------------------------------------------------------
    |
    | Here you may change the name of the session cookie that is created by
    | the framework. Typically, you should not need to change this value
    | since doing so does not grant a meaningful security improvement.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')).'-session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Path
    |--------------------------------------------------------------------------
    |
    | The session cookie path determines the path for which the cookie will
    | be regarded as available. Typically, this will be the root path of
    | your application, but you're free to change this when necessary.
    |
    */

    'path' => env('SESSION_PATH', '/'),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Domain
    |--------------------------------------------------------------------------
    |
    | This value determines the domain and subdomains the session cookie is
    | available to. By default, the cookie will be available to the root
    | domain without subdomains. Typically, this shouldn't be changed.
    |
    */

    'domain' => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | HTTPS Only Cookies
    |--------------------------------------------------------------------------
    |
    | By setting this option to true, session cookies will only be sent back
    | to the server if the browser has a HTTPS connection. This will keep
    | the cookie from being sent to you when it can't be done securely.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Access Only
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will prevent JavaScript from accessing the
    | value of the cookie and the cookie will only be accessible through
    | the HTTP protocol. It's unlikely you should disable this option.
    |
    */

    'http_only' => env('SESSION_HTTP_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    |
    | This option determines how your cookies behave when cross-site requests
    | take place, and can be used to mitigate CSRF attacks. By default, we
    | will set this value to "lax" to permit secure cross-site requests.
    |
    | See: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#samesitesamesite-value
    |
    | Supported: "lax", "strict", "none", null
    |
    */

    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /*
    |--------------------------------------------------------------------------
    | Partitioned Cookies
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will tie the cookie to the top-level site for
    | a cross-site context. Partitioned cookies are accepted by the browser
    | when flagged "secure" and the Same-Site attribute is set to "none".
    |
    */

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

    /*
    |--------------------------------------------------------------------------
    | Session Serialization
    |--------------------------------------------------------------------------
    |
    | This value controls the serialization strategy for session data, which
    | is JSON by default. Setting this to "php" allows the storage of PHP
    | objects in the session but can make an application vulnerable to
    | "gadget chain" serialization attacks if the APP_KEY is leaked.
    |
    | Supported: "json", "php"
    |
    */

    'serialization' => 'json',

];
````

## File: database/factories/FinanceFactory.php
````php
<?php

namespace Database\Factories;

use App\Models\Finance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Finance>
 */
class FinanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
````

## File: database/factories/MeetingAttendanceFactory.php
````php
<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingAttendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingAttendance>
 */
class MeetingAttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['present', 'permit', 'sick', 'absent']),
            'proof_url' => fake()->optional(0.3)->url(),
        ];
    }
}
````

## File: database/factories/MeetingFactory.php
````php
<?php

namespace Database\Factories;

use App\Models\Meeting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meeting>
 */
class MeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'date' => fake()->dateTimeBetween('-3 months', '+1 month'),
            'minutes_url' => fake()->optional(0.7)->url(),
        ];
    }
}
````

## File: database/factories/UserFactory.php
````php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
````

## File: database/factories/WarningFactory.php
````php
<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Warning;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warning>
 */
class WarningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'admin_id' => User::factory(),
            'reason' => fake()->paragraph(),
            'date' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
````

## File: database/migrations/0001_01_01_000000_create_users_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
````

## File: database/migrations/0001_01_01_000001_create_cache_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->bigInteger('expiration')->index();
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->bigInteger('expiration')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
````

## File: database/migrations/0001_01_01_000002_create_jobs_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedSmallInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('connection');
            $table->string('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();

            $table->index(['connection', 'queue', 'failed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
````

## File: database/migrations/2026_08_19_181546_create_divisions_table.php
````php
<?php
// xxxx_xx_xx_create_divisions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Modifikasi tabel users bawaan Laravel
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('set null');
            $table->enum('status', ['active', 'suspended'])->default('active');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn(['division_id', 'status']);
        });
        Schema::dropIfExists('divisions');
    }
};
````

## File: database/migrations/2026_08_19_181547_create_events_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('budget_approved', 10, 2)->default(0);
            $table->string('drive_folder_url')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('events');
    }
};
````

## File: database/migrations/2026_08_19_183559_create_personal_access_tokens_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
````

## File: database/migrations/2026_08_19_185506_create_permission_tables.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        throw_if(empty($tableNames), 'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), 'Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');

        /**
         * See `docs/prerequisites.md` for suggested lengths on 'name' and 'guard_name' if "1071 Specified key was too long" errors are encountered.
         */
        Schema::create($tableNames['permissions'], static function (Blueprint $table) {
            $table->id(); // permission id
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        /**
         * See `docs/prerequisites.md` for suggested lengths on 'name' and 'guard_name' if "1071 Specified key was too long" errors are encountered.
         */
        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
            $table->id(); // role id
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->unsignedBigInteger($pivotPermission);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }
        });

        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->unsignedBigInteger($pivotRole);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->cascadeOnDelete();
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        throw_if(empty($tableNames), 'Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
````

## File: database/migrations/2026_08_20_012800_create_meetings_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('date');
            $table->string('minutes_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('meetings');
    }
};
````

## File: database/migrations/2026_08_20_012801_create_meeting_attendances_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('meeting_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->enum('status', ['present', 'permit', 'sick', 'absent']);
            $table->string('proof_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('meeting_attendances');
    }
};
````

## File: database/migrations/2026_08_20_012802_create_documents_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null');
            $table->string('letter_number')->unique();
            $table->string('title');
            $table->string('drive_url');
            $table->timestamps();

            $table->index('created_by');
            $table->index('event_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('documents');
    }
};
````

## File: database/migrations/2026_08_20_012803_create_warnings_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('admin_id')->constrained('users')->onDelete('restrict');
            $table->text('reason');
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('warnings');
    }
};
````

## File: database/migrations/2026_08_21_143800_create_event_committees_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('position');
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_committees');
    }
};
````

## File: database/migrations/2026_08_21_170000_add_event_id_to_meetings_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->after('id')->constrained('events')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });
    }
};
````

## File: database/migrations/2026_08_22_082000_add_personal_details_to_users_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('prodi')->nullable();
            $table->string('angkatan')->nullable();
            $table->text('address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nim', 'phone', 'prodi', 'angkatan', 'address']);
        });
    }
};
````

## File: database/migrations/2026_08_22_113300_create_audit_trails_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // created, updated, deleted
            $table->morphs('auditable'); // auditable_type, auditable_id
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
````

## File: database/migrations/2026_08_23_000000_add_tracking_columns_to_documents_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->date('activity_date')->nullable()->after('title');
            $table->string('letter_link')->nullable()->after('drive_url');
            $table->string('scan_link')->nullable()->after('letter_link');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['activity_date', 'letter_link', 'scan_link']);
        });
    }
};
````

## File: database/migrations/2026_08_23_003200_drop_drive_url_from_documents_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('drive_url');
        });
    }

    public function down(): void {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('drive_url')->nullable();
        });
    }
};
````

## File: database/migrations/2026_08_23_015100_add_enterprise_columns_to_finances_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('finances', function (Blueprint $table) {
            if (!Schema::hasColumn('finances', 'category')) {
                $table->string('category')->nullable()->after('type');
            }
            if (!Schema::hasColumn('finances', 'description')) {
                $table->string('description')->nullable()->after('category');
            }
            if (!Schema::hasColumn('finances', 'pic')) {
                $table->string('pic')->nullable()->after('funding_source');
            }
            if (!Schema::hasColumn('finances', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('pic');
            }
        });
    }

    public function down(): void {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropColumn(['category', 'description', 'pic', 'payment_method']);
        });
    }
};
````

## File: database/migrations/2026_08_23_021800_create_monthly_dues_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('monthly_dues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('month'); // 1-12
            $table->integer('year');
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
            
            // Mencegah duplikasi data per bulan untuk user yang sama
            $table->unique(['user_id', 'month', 'year']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('monthly_dues');
    }
};
````

## File: database/migrations/2026_08_23_030600_add_sync_urls_to_events_table.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('events', function (Blueprint $table) {
            $table->string('document_sync_url')->nullable()->after('end_date');
            $table->string('finance_sync_url')->nullable()->after('document_sync_url');
        });
    }

    public function down(): void {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['document_sync_url', 'finance_sync_url']);
        });
    }
};
````

## File: database/migrations/2026_08_23_183100_refactor_meetings_to_agendas_system.php
````php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Drop tabel lama dengan urutan yang benar (child -> parent)
        Schema::dropIfExists('meeting_attendances');
        Schema::dropIfExists('meetings');

        // 2. Buat tabel Agendas (Mencakup Rapat, Gladi, Acara, dll)
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
            $table->string('title'); // Dari "Nama Agenda"
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('location')->nullable(); // Dari "Tempat"
            $table->string('pic')->nullable(); // Dari "PJ/Divisi"
            $table->string('status')->nullable(); // Dari "Status"
            $table->string('minutes_url')->nullable(); // Dari "Link Notulensi"
            $table->timestamps();
        });

        // 3. Buat ulang tabel absensi untuk agenda yang membutuhkannya
        Schema::create('agenda_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agendas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->enum('status', ['present', 'permit', 'sick', 'absent']);
            $table->string('proof_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('agenda_attendances');
        Schema::dropIfExists('agendas');
    }
};
````

## File: database/seeders/ProticUserSeeder.php
````php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProticUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Sofyan Yunus Rohman', 'nim' => '250102125'],
            ['name' => 'Raja Ubaid Fawwaz', 'nim' => '250109084'],
            ['name' => 'Almas Salsabila Fidiarti', 'nim' => '250215005'],
            ['name' => 'Sukmaratih Nirmalasari', 'nim' => '250109059'],
            ['name' => 'Ahmad Fakhri Abdullah', 'nim' => '250215003'],
            ['name' => 'Dea Ameliana Saputri', 'nim' => '250215011'],
            ['name' => 'Afif Nur Faizin', 'nim' => '250215002'],
            ['name' => 'Bintang Fajar Jolya Anggara', 'nim' => '250315037'],
            ['name' => 'Lussy Ana Syarif', 'nim' => '250202019'],
            ['name' => 'Ari Dwi Saputra', 'nim' => '250109064'],
            ['name' => 'Igo Ilham Ramadhan', 'nim' => '250102016'],
            ['name' => 'Rahmawati', 'nim' => '250102123'],
            ['name' => 'Kayla Radifan Pramudya', 'nim' => '250209079'],
            ['name' => 'Bagus Daffa Albany', 'nim' => '250102100'],
            ['name' => 'Assyifa Saisarita', 'nim' => '250302005'],
            ['name' => 'Bhadra Nur Rouf Rudin', 'nim' => '250202038'],
            ['name' => 'Hazel Ransy Krishna', 'nim' => '250315018'],
            ['name' => 'Galuh Dwi Putra', 'nim' => '250215015'],
            ['name' => 'Wanda Tiara Levina', 'nim' => '250215063'],
            ['name' => 'Faathimah Annaafi\'ah', 'nim' => '250202011'],
            ['name' => 'Ade Ariansyah Anggoro', 'nim' => '250315034'],
            ['name' => 'Nafisa Raihana', 'nim' => '250109053'],
            ['name' => 'Hikmal', 'nim' => '240109076'],
            ['name' => 'Raihan Afdhal Athallah', 'nim' => '250202027'],
            ['name' => 'Gendhis Yuwita Sari', 'nim' => '250202014'],
            ['name' => 'Rizqi Radhityanto', 'nim' => '250215060'],
            ['name' => 'Nabila Islami Cinta Widianti', 'nim' => '250202117'],
            ['name' => 'Rindang Permatasari', 'nim' => '250110024'],
            ['name' => 'Ayla Azzura Putri Mulianingrum', 'nim' => '250215008'],
        ];

        foreach ($users as $u) {
            // Ekstrak nama depan untuk email (huruf kecil semua)
            $firstName = strtolower(explode(' ', trim($u['name']))[0]);
            // Filter karakter non-alfabet (untuk kasus nama seperti Annaafi'ah)
            $firstName = preg_replace('/[^a-z]/', '', $firstName);
            $email = $firstName . '@protik.com';

            // Jangan sentuh Admin Sofyan jika emailnya sudah ada
            $existing = User::where('email', $email)->orWhere('nim', $u['nim'])->first();
            
            if (!$existing) {
                User::create([
                    'name'     => $u['name'],
                    'email'    => $email,
                    'nim'      => $u['nim'],
                    'password' => Hash::make($u['nim']),
                    'status'   => 'active',
                ]);
            } else {
                // Hanya update NIM jika masih kosong
                if (empty($existing->nim)) {
                    $existing->update(['nim' => $u['nim']]);
                }
            }
        }
    }
}
````

## File: database/seeders/RolePermissionSeeder.php
````php
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'advisor', 'guard_name' => 'web']);
    }
}
````

## File: database/.gitignore
````
*.sqlite*
````

## File: public/.htaccess
````
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
````

## File: public/index.php
````php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
````

## File: public/robots.txt
````
User-agent: *
Disallow:
````

## File: resources/css/app.css
````css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
        'Segoe UI Symbol', 'Noto Color Emoji';
}
````

## File: resources/js/app.js
````javascript
//
````

## File: resources/views/welcome.blade.php
````php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @fonts

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */ @layer properties{@supports (((-webkit-hyphens:none)) and (not (margin-trim:inline))) or ((-moz-orient:inline) and (not (color:rgb(from red r g b)))){*,:before,:after,::backdrop{--tw-translate-x:0;--tw-translate-y:0;--tw-translate-z:0;--tw-rotate-x:initial;--tw-rotate-y:initial;--tw-rotate-z:initial;--tw-skew-x:initial;--tw-skew-y:initial;--tw-space-x-reverse:0;--tw-border-style:solid;--tw-leading:initial;--tw-font-weight:initial;--tw-tracking:initial;--tw-shadow:0 0 #0000;--tw-shadow-color:initial;--tw-shadow-alpha:100%;--tw-inset-shadow:0 0 #0000;--tw-inset-shadow-color:initial;--tw-inset-shadow-alpha:100%;--tw-ring-color:initial;--tw-ring-shadow:0 0 #0000;--tw-inset-ring-color:initial;--tw-inset-ring-shadow:0 0 #0000;--tw-ring-inset:initial;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-offset-shadow:0 0 #0000;--tw-blur:initial;--tw-brightness:initial;--tw-contrast:initial;--tw-grayscale:initial;--tw-hue-rotate:initial;--tw-invert:initial;--tw-opacity:initial;--tw-saturate:initial;--tw-sepia:initial;--tw-drop-shadow:initial;--tw-drop-shadow-color:initial;--tw-drop-shadow-alpha:100%;--tw-drop-shadow-size:initial;--tw-duration:initial;--tw-ease:initial;--tw-content:""}}}@layer theme{:root,:host{--font-sans:"Instrument Sans", ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";--font-serif:ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;--font-mono:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;--color-red-50:oklch(97.1% .013 17.38);--color-red-100:oklch(93.6% .032 17.717);--color-red-200:oklch(88.5% .062 18.334);--color-red-300:oklch(80.8% .114 19.571);--color-red-400:oklch(70.4% .191 22.216);--color-red-500:oklch(63.7% .237 25.331);--color-red-600:oklch(57.7% .245 27.325);--color-red-700:oklch(50.5% .213 27.518);--color-red-800:oklch(44.4% .177 26.899);--color-red-900:oklch(39.6% .141 25.723);--color-red-950:oklch(25.8% .092 26.042);--color-orange-50:oklch(98% .016 73.684);--color-orange-100:oklch(95.4% .038 75.164);--color-orange-200:oklch(90.1% .076 70.697);--color-orange-300:oklch(83.7% .128 66.29);--color-orange-400:oklch(75% .183 55.934);--color-orange-500:oklch(70.5% .213 47.604);--color-orange-600:oklch(64.6% .222 41.116);--color-orange-700:oklch(55.3% .195 38.402);--color-orange-800:oklch(47% .157 37.304);--color-orange-900:oklch(40.8% .123 38.172);--color-orange-950:oklch(26.6% .079 36.259);--color-amber-50:oklch(98.7% .022 95.277);--color-amber-100:oklch(96.2% .059 95.617);--color-amber-200:oklch(92.4% .12 95.746);--color-amber-300:oklch(87.9% .169 91.605);--color-amber-400:oklch(82.8% .189 84.429);--color-amber-500:oklch(76.9% .188 70.08);--color-amber-600:oklch(66.6% .179 58.318);--color-amber-700:oklch(55.5% .163 48.998);--color-amber-800:oklch(47.3% .137 46.201);--color-amber-900:oklch(41.4% .112 45.904);--color-amber-950:oklch(27.9% .077 45.635);--color-yellow-50:oklch(98.7% .026 102.212);--color-yellow-100:oklch(97.3% .071 103.193);--color-yellow-200:oklch(94.5% .129 101.54);--color-yellow-300:oklch(90.5% .182 98.111);--color-yellow-400:oklch(85.2% .199 91.936);--color-yellow-500:oklch(79.5% .184 86.047);--color-yellow-600:oklch(68.1% .162 75.834);--color-yellow-700:oklch(55.4% .135 66.442);--color-yellow-800:oklch(47.6% .114 61.907);--color-yellow-900:oklch(42.1% .095 57.708);--color-yellow-950:oklch(28.6% .066 53.813);--color-lime-50:oklch(98.6% .031 120.757);--color-lime-100:oklch(96.7% .067 122.328);--color-lime-200:oklch(93.8% .127 124.321);--color-lime-300:oklch(89.7% .196 126.665);--color-lime-400:oklch(84.1% .238 128.85);--color-lime-500:oklch(76.8% .233 130.85);--color-lime-600:oklch(64.8% .2 131.684);--color-lime-700:oklch(53.2% .157 131.589);--color-lime-800:oklch(45.3% .124 130.933);--color-lime-900:oklch(40.5% .101 131.063);--color-lime-950:oklch(27.4% .072 132.109);--color-green-50:oklch(98.2% .018 155.826);--color-green-100:oklch(96.2% .044 156.743);--color-green-200:oklch(92.5% .084 155.995);--color-green-300:oklch(87.1% .15 154.449);--color-green-400:oklch(79.2% .209 151.711);--color-green-500:oklch(72.3% .219 149.579);--color-green-600:oklch(62.7% .194 149.214);--color-green-700:oklch(52.7% .154 150.069);--color-green-800:oklch(44.8% .119 151.328);--color-green-900:oklch(39.3% .095 152.535);--color-green-950:oklch(26.6% .065 152.934);--color-emerald-50:oklch(97.9% .021 166.113);--color-emerald-100:oklch(95% .052 163.051);--color-emerald-200:oklch(90.5% .093 164.15);--color-emerald-300:oklch(84.5% .143 164.978);--color-emerald-400:oklch(76.5% .177 163.223);--color-emerald-500:oklch(69.6% .17 162.48);--color-emerald-600:oklch(59.6% .145 163.225);--color-emerald-700:oklch(50.8% .118 165.612);--color-emerald-800:oklch(43.2% .095 166.913);--color-emerald-900:oklch(37.8% .077 168.94);--color-emerald-950:oklch(26.2% .051 172.552);--color-teal-50:oklch(98.4% .014 180.72);--color-teal-100:oklch(95.3% .051 180.801);--color-teal-200:oklch(91% .096 180.426);--color-teal-300:oklch(85.5% .138 181.071);--color-teal-400:oklch(77.7% .152 181.912);--color-teal-500:oklch(70.4% .14 182.503);--color-teal-600:oklch(60% .118 184.704);--color-teal-700:oklch(51.1% .096 186.391);--color-teal-800:oklch(43.7% .078 188.216);--color-teal-900:oklch(38.6% .063 188.416);--color-teal-950:oklch(27.7% .046 192.524);--color-cyan-50:oklch(98.4% .019 200.873);--color-cyan-100:oklch(95.6% .045 203.388);--color-cyan-200:oklch(91.7% .08 205.041);--color-cyan-300:oklch(86.5% .127 207.078);--color-cyan-400:oklch(78.9% .154 211.53);--color-cyan-500:oklch(71.5% .143 215.221);--color-cyan-600:oklch(60.9% .126 221.723);--color-cyan-700:oklch(52% .105 223.128);--color-cyan-800:oklch(45% .085 224.283);--color-cyan-900:oklch(39.8% .07 227.392);--color-cyan-950:oklch(30.2% .056 229.695);--color-sky-50:oklch(97.7% .013 236.62);--color-sky-100:oklch(95.1% .026 236.824);--color-sky-200:oklch(90.1% .058 230.902);--color-sky-300:oklch(82.8% .111 230.318);--color-sky-400:oklch(74.6% .16 232.661);--color-sky-500:oklch(68.5% .169 237.323);--color-sky-600:oklch(58.8% .158 241.966);--color-sky-700:oklch(50% .134 242.749);--color-sky-800:oklch(44.3% .11 240.79);--color-sky-900:oklch(39.1% .09 240.876);--color-sky-950:oklch(29.3% .066 243.157);--color-blue-50:oklch(97% .014 254.604);--color-blue-100:oklch(93.2% .032 255.585);--color-blue-200:oklch(88.2% .059 254.128);--color-blue-300:oklch(80.9% .105 251.813);--color-blue-400:oklch(70.7% .165 254.624);--color-blue-500:oklch(62.3% .214 259.815);--color-blue-600:oklch(54.6% .245 262.881);--color-blue-700:oklch(48.8% .243 264.376);--color-blue-800:oklch(42.4% .199 265.638);--color-blue-900:oklch(37.9% .146 265.522);--color-blue-950:oklch(28.2% .091 267.935);--color-indigo-50:oklch(96.2% .018 272.314);--color-indigo-100:oklch(93% .034 272.788);--color-indigo-200:oklch(87% .065 274.039);--color-indigo-300:oklch(78.5% .115 274.713);--color-indigo-400:oklch(67.3% .182 276.935);--color-indigo-500:oklch(58.5% .233 277.117);--color-indigo-600:oklch(51.1% .262 276.966);--color-indigo-700:oklch(45.7% .24 277.023);--color-indigo-800:oklch(39.8% .195 277.366);--color-indigo-900:oklch(35.9% .144 278.697);--color-indigo-950:oklch(25.7% .09 281.288);--color-violet-50:oklch(96.9% .016 293.756);--color-violet-100:oklch(94.3% .029 294.588);--color-violet-200:oklch(89.4% .057 293.283);--color-violet-300:oklch(81.1% .111 293.571);--color-violet-400:oklch(70.2% .183 293.541);--color-violet-500:oklch(60.6% .25 292.717);--color-violet-600:oklch(54.1% .281 293.009);--color-violet-700:oklch(49.1% .27 292.581);--color-violet-800:oklch(43.2% .232 292.759);--color-violet-900:oklch(38% .189 293.745);--color-violet-950:oklch(28.3% .141 291.089);--color-purple-50:oklch(97.7% .014 308.299);--color-purple-100:oklch(94.6% .033 307.174);--color-purple-200:oklch(90.2% .063 306.703);--color-purple-300:oklch(82.7% .119 306.383);--color-purple-400:oklch(71.4% .203 305.504);--color-purple-500:oklch(62.7% .265 303.9);--color-purple-600:oklch(55.8% .288 302.321);--color-purple-700:oklch(49.6% .265 301.924);--color-purple-800:oklch(43.8% .218 303.724);--color-purple-900:oklch(38.1% .176 304.987);--color-purple-950:oklch(29.1% .149 302.717);--color-fuchsia-50:oklch(97.7% .017 320.058);--color-fuchsia-100:oklch(95.2% .037 318.852);--color-fuchsia-200:oklch(90.3% .076 319.62);--color-fuchsia-300:oklch(83.3% .145 321.434);--color-fuchsia-400:oklch(74% .238 322.16);--color-fuchsia-500:oklch(66.7% .295 322.15);--color-fuchsia-600:oklch(59.1% .293 322.896);--color-fuchsia-700:oklch(51.8% .253 323.949);--color-fuchsia-800:oklch(45.2% .211 324.591);--color-fuchsia-900:oklch(40.1% .17 325.612);--color-fuchsia-950:oklch(29.3% .136 325.661);--color-pink-50:oklch(97.1% .014 343.198);--color-pink-100:oklch(94.8% .028 342.258);--color-pink-200:oklch(89.9% .061 343.231);--color-pink-300:oklch(82.3% .12 346.018);--color-pink-400:oklch(71.8% .202 349.761);--color-pink-500:oklch(65.6% .241 354.308);--color-pink-600:oklch(59.2% .249 .584);--color-pink-700:oklch(52.5% .223 3.958);--color-pink-800:oklch(45.9% .187 3.815);--color-pink-900:oklch(40.8% .153 2.432);--color-pink-950:oklch(28.4% .109 3.907);--color-rose-50:oklch(96.9% .015 12.422);--color-rose-100:oklch(94.1% .03 12.58);--color-rose-200:oklch(89.2% .058 10.001);--color-rose-300:oklch(81% .117 11.638);--color-rose-400:oklch(71.2% .194 13.428);--color-rose-500:oklch(64.5% .246 16.439);--color-rose-600:oklch(58.6% .253 17.585);--color-rose-700:oklch(51.4% .222 16.935);--color-rose-800:oklch(45.5% .188 13.697);--color-rose-900:oklch(41% .159 10.272);--color-rose-950:oklch(27.1% .105 12.094);--color-slate-50:oklch(98.4% .003 247.858);--color-slate-100:oklch(96.8% .007 247.896);--color-slate-200:oklch(92.9% .013 255.508);--color-slate-300:oklch(86.9% .022 252.894);--color-slate-400:oklch(70.4% .04 256.788);--color-slate-500:oklch(55.4% .046 257.417);--color-slate-600:oklch(44.6% .043 257.281);--color-slate-700:oklch(37.2% .044 257.287);--color-slate-800:oklch(27.9% .041 260.031);--color-slate-900:oklch(20.8% .042 265.755);--color-slate-950:oklch(12.9% .042 264.695);--color-gray-50:oklch(98.5% .002 247.839);--color-gray-100:oklch(96.7% .003 264.542);--color-gray-200:oklch(92.8% .006 264.531);--color-gray-300:oklch(87.2% .01 258.338);--color-gray-400:oklch(70.7% .022 261.325);--color-gray-500:oklch(55.1% .027 264.364);--color-gray-600:oklch(44.6% .03 256.802);--color-gray-700:oklch(37.3% .034 259.733);--color-gray-800:oklch(27.8% .033 256.848);--color-gray-900:oklch(21% .034 264.665);--color-gray-950:oklch(13% .028 261.692);--color-zinc-50:oklch(98.5% 0 0);--color-zinc-100:oklch(96.7% .001 286.375);--color-zinc-200:oklch(92% .004 286.32);--color-zinc-300:oklch(87.1% .006 286.286);--color-zinc-400:oklch(70.5% .015 286.067);--color-zinc-500:oklch(55.2% .016 285.938);--color-zinc-600:oklch(44.2% .017 285.786);--color-zinc-700:oklch(37% .013 285.805);--color-zinc-800:oklch(27.4% .006 286.033);--color-zinc-900:oklch(21% .006 285.885);--color-zinc-950:oklch(14.1% .005 285.823);--color-neutral-50:oklch(98.5% 0 0);--color-neutral-100:oklch(97% 0 0);--color-neutral-200:oklch(92.2% 0 0);--color-neutral-300:oklch(87% 0 0);--color-neutral-400:oklch(70.8% 0 0);--color-neutral-500:oklch(55.6% 0 0);--color-neutral-600:oklch(43.9% 0 0);--color-neutral-700:oklch(37.1% 0 0);--color-neutral-800:oklch(26.9% 0 0);--color-neutral-900:oklch(20.5% 0 0);--color-neutral-950:oklch(14.5% 0 0);--color-stone-50:oklch(98.5% .001 106.423);--color-stone-100:oklch(97% .001 106.424);--color-stone-200:oklch(92.3% .003 48.717);--color-stone-300:oklch(86.9% .005 56.366);--color-stone-400:oklch(70.9% .01 56.259);--color-stone-500:oklch(55.3% .013 58.071);--color-stone-600:oklch(44.4% .011 73.639);--color-stone-700:oklch(37.4% .01 67.558);--color-stone-800:oklch(26.8% .007 34.298);--color-stone-900:oklch(21.6% .006 56.043);--color-stone-950:oklch(14.7% .004 49.25);--color-black:#000;--color-white:#fff;--spacing:.25rem;--breakpoint-sm:40rem;--breakpoint-md:48rem;--breakpoint-lg:64rem;--breakpoint-xl:80rem;--breakpoint-2xl:96rem;--container-3xs:16rem;--container-2xs:18rem;--container-xs:20rem;--container-sm:24rem;--container-md:28rem;--container-lg:32rem;--container-xl:36rem;--container-2xl:42rem;--container-3xl:48rem;--container-4xl:56rem;--container-5xl:64rem;--container-6xl:72rem;--container-7xl:80rem;--text-xs:.75rem;--text-xs--line-height:calc(1 / .75);--text-sm:.875rem;--text-sm--line-height:calc(1.25 / .875);--text-base:1rem;--text-base--line-height: 1.5 ;--text-lg:1.125rem;--text-lg--line-height:calc(1.75 / 1.125);--text-xl:1.25rem;--text-xl--line-height:calc(1.75 / 1.25);--text-2xl:1.5rem;--text-2xl--line-height:calc(2 / 1.5);--text-3xl:1.875rem;--text-3xl--line-height: 1.2 ;--text-4xl:2.25rem;--text-4xl--line-height:calc(2.5 / 2.25);--text-5xl:3rem;--text-5xl--line-height:1;--text-6xl:3.75rem;--text-6xl--line-height:1;--text-7xl:4.5rem;--text-7xl--line-height:1;--text-8xl:6rem;--text-8xl--line-height:1;--text-9xl:8rem;--text-9xl--line-height:1;--font-weight-thin:100;--font-weight-extralight:200;--font-weight-light:300;--font-weight-normal:400;--font-weight-medium:500;--font-weight-semibold:600;--font-weight-bold:700;--font-weight-extrabold:800;--font-weight-black:900;--tracking-tighter:-.05em;--tracking-tight:-.025em;--tracking-normal:0em;--tracking-wide:.025em;--tracking-wider:.05em;--tracking-widest:.1em;--leading-tight:1.25;--leading-snug:1.375;--leading-normal:1.5;--leading-relaxed:1.625;--leading-loose:2;--radius-xs:.125rem;--radius-sm:.25rem;--radius-md:.375rem;--radius-lg:.5rem;--radius-xl:.75rem;--radius-2xl:1rem;--radius-3xl:1.5rem;--radius-4xl:2rem;--shadow-2xs:0 1px #0000000d;--shadow-xs:0 1px 2px 0 #0000000d;--shadow-sm:0 1px 3px 0 #0000001a, 0 1px 2px -1px #0000001a;--shadow-md:0 4px 6px -1px #0000001a, 0 2px 4px -2px #0000001a;--shadow-lg:0 10px 15px -3px #0000001a, 0 4px 6px -4px #0000001a;--shadow-xl:0 20px 25px -5px #0000001a, 0 8px 10px -6px #0000001a;--shadow-2xl:0 25px 50px -12px #00000040;--inset-shadow-2xs:inset 0 1px #0000000d;--inset-shadow-xs:inset 0 1px 1px #0000000d;--inset-shadow-sm:inset 0 2px 4px #0000000d;--drop-shadow-xs:0 1px 1px #0000000d;--drop-shadow-sm:0 1px 2px #00000026;--drop-shadow-md:0 3px 3px #0000001f;--drop-shadow-lg:0 4px 4px #00000026;--drop-shadow-xl:0 9px 7px #0000001a;--drop-shadow-2xl:0 25px 25px #00000026;--ease-in:cubic-bezier(.4, 0, 1, 1);--ease-out:cubic-bezier(0, 0, .2, 1);--ease-in-out:cubic-bezier(.4, 0, .2, 1);--animate-spin:spin 1s linear infinite;--animate-ping:ping 1s cubic-bezier(0, 0, .2, 1) infinite;--animate-pulse:pulse 2s cubic-bezier(.4, 0, .6, 1) infinite;--animate-bounce:bounce 1s infinite;--blur-xs:4px;--blur-sm:8px;--blur-md:12px;--blur-lg:16px;--blur-xl:24px;--blur-2xl:40px;--blur-3xl:64px;--perspective-dramatic:100px;--perspective-near:300px;--perspective-normal:500px;--perspective-midrange:800px;--perspective-distant:1200px;--aspect-video:16 / 9;--default-transition-duration:.15s;--default-transition-timing-function:cubic-bezier(.4, 0, .2, 1);--default-font-family:var(--font-sans);--default-mono-font-family:var(--font-mono)}}@layer base{*,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}::file-selector-button{box-sizing:border-box;border:0 solid;margin:0;padding:0}html,:host{-webkit-text-size-adjust:100%;tab-size:4;line-height:1.5;font-family:var(--default-font-family,ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji");font-feature-settings:var(--default-font-feature-settings,normal);font-variation-settings:var(--default-font-variation-settings,normal);-webkit-tap-highlight-color:transparent}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;-webkit-text-decoration:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,samp,pre{font-family:var(--default-mono-font-family,ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace);font-feature-settings:var(--default-mono-font-feature-settings,normal);font-variation-settings:var(--default-mono-font-variation-settings,normal);font-size:1em}small{font-size:80%}sub,sup{vertical-align:baseline;font-size:75%;line-height:0;position:relative}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}:-moz-focusring{outline:auto}progress{vertical-align:baseline}summary{display:list-item}ol,ul,menu{list-style:none}img,svg,video,canvas,audio,iframe,embed,object{vertical-align:middle;display:block}img,video{max-width:100%;height:auto}button,input,select,optgroup,textarea{font:inherit;font-feature-settings:inherit;font-variation-settings:inherit;letter-spacing:inherit;color:inherit;opacity:1;background-color:#0000;border-radius:0}::file-selector-button{font:inherit;font-feature-settings:inherit;font-variation-settings:inherit;letter-spacing:inherit;color:inherit;opacity:1;background-color:#0000;border-radius:0}:where(select:is([multiple],[size])) optgroup{font-weight:bolder}:where(select:is([multiple],[size])) optgroup option{padding-inline-start:20px}::file-selector-button{margin-inline-end:4px}::placeholder{opacity:1}@supports (not ((-webkit-appearance:-apple-pay-button))) or (contain-intrinsic-size:1px){::placeholder{color:currentColor}@supports (color:color-mix(in lab,red,red)){::placeholder{color:color-mix(in oklab,currentcolor 50%,transparent)}}}textarea{resize:vertical}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-date-and-time-value{min-height:1lh;text-align:inherit}::-webkit-datetime-edit{display:inline-flex}::-webkit-datetime-edit-fields-wrapper{padding:0}::-webkit-datetime-edit{padding-block:0}::-webkit-datetime-edit-year-field{padding-block:0}::-webkit-datetime-edit-month-field{padding-block:0}::-webkit-datetime-edit-day-field{padding-block:0}::-webkit-datetime-edit-hour-field{padding-block:0}::-webkit-datetime-edit-minute-field{padding-block:0}::-webkit-datetime-edit-second-field{padding-block:0}::-webkit-datetime-edit-millisecond-field{padding-block:0}::-webkit-datetime-edit-meridiem-field{padding-block:0}::-webkit-calendar-picker-indicator{line-height:1}:-moz-ui-invalid{box-shadow:none}button,input:where([type=button],[type=reset],[type=submit]){appearance:button}::file-selector-button{appearance:button}::-webkit-inner-spin-button{height:auto}::-webkit-outer-spin-button{height:auto}[hidden]:where(:not([hidden=until-found])){display:none!important}}@layer components;@layer utilities{.absolute{position:absolute}.fixed{position:fixed}.relative{position:relative}.static{position:static}.inset-0{inset:calc(var(--spacing) * 0)}.start{inset-inline-start:var(--spacing)}.top-0{top:calc(var(--spacing) * 0)}.right-0{right:calc(var(--spacing) * 0)}.container{width:100%}@media(min-width:40rem){.container{max-width:40rem}}@media(min-width:48rem){.container{max-width:48rem}}@media(min-width:64rem){.container{max-width:64rem}}@media(min-width:80rem){.container{max-width:80rem}}@media(min-width:96rem){.container{max-width:96rem}}.mx-auto{margin-inline:auto}.-mt-\[6\.6rem\]{margin-top:-6.6rem}.-mt-px{margin-top:-1px}.mt-2{margin-top:calc(var(--spacing) * 2)}.mt-4{margin-top:calc(var(--spacing) * 4)}.mt-6{margin-top:calc(var(--spacing) * 6)}.mt-8{margin-top:calc(var(--spacing) * 8)}.mr-2{margin-right:calc(var(--spacing) * 2)}.-mb-px{margin-bottom:-1px}.mb-1{margin-bottom:calc(var(--spacing) * 1)}.mb-2{margin-bottom:calc(var(--spacing) * 2)}.mb-4{margin-bottom:calc(var(--spacing) * 4)}.mb-6{margin-bottom:calc(var(--spacing) * 6)}.-ml-8{margin-left:calc(var(--spacing) * -8)}.-ml-px{margin-left:-1px}.ml-1{margin-left:calc(var(--spacing) * 1)}.ml-2{margin-left:calc(var(--spacing) * 2)}.ml-4{margin-left:calc(var(--spacing) * 4)}.ml-12{margin-left:calc(var(--spacing) * 12)}.contents{display:contents}.flex{display:flex}.grid{display:grid}.hidden{display:none}.inline-block{display:inline-block}.inline-flex{display:inline-flex}.table{display:table}.aspect-\[335\/364\]{aspect-ratio:335/364}.h-1{height:calc(var(--spacing) * 1)}.h-1\.5{height:calc(var(--spacing) * 1.5)}.h-2{height:calc(var(--spacing) * 2)}.h-2\.5{height:calc(var(--spacing) * 2.5)}.h-3{height:calc(var(--spacing) * 3)}.h-3\.5{height:calc(var(--spacing) * 3.5)}.h-5{height:calc(var(--spacing) * 5)}.h-8{height:calc(var(--spacing) * 8)}.h-14{height:calc(var(--spacing) * 14)}.h-14\.5{height:calc(var(--spacing) * 14.5)}.h-16{height:calc(var(--spacing) * 16)}.min-h-screen{min-height:100vh}.w-1{width:calc(var(--spacing) * 1)}.w-1\.5{width:calc(var(--spacing) * 1.5)}.w-2{width:calc(var(--spacing) * 2)}.w-2\.5{width:calc(var(--spacing) * 2.5)}.w-3{width:calc(var(--spacing) * 3)}.w-3\.5{width:calc(var(--spacing) * 3.5)}.w-5{width:calc(var(--spacing) * 5)}.w-8{width:calc(var(--spacing) * 8)}.w-\[438px\]{width:438px}.w-auto{width:auto}.w-full{width:100%}.max-w-6xl{max-width:var(--container-6xl)}.max-w-\[335px\]{max-width:335px}.max-w-none{max-width:none}.max-w-xl{max-width:var(--container-xl)}.flex-1{flex:1}.shrink-0{flex-shrink:0}.translate-y-0{--tw-translate-y:calc(var(--spacing) * 0);translate:var(--tw-translate-x) var(--tw-translate-y)}.transform{transform:var(--tw-rotate-x,) var(--tw-rotate-y,) var(--tw-rotate-z,) var(--tw-skew-x,) var(--tw-skew-y,)}.cursor-default{cursor:default}.cursor-not-allowed{cursor:not-allowed}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}.flex-col{flex-direction:column}.flex-col-reverse{flex-direction:column-reverse}.items-center{align-items:center}.justify-between{justify-content:space-between}.justify-center{justify-content:center}.justify-end{justify-content:flex-end}.justify-items-center{justify-items:center}.gap-2{gap:calc(var(--spacing) * 2)}.gap-3{gap:calc(var(--spacing) * 3)}.gap-4{gap:calc(var(--spacing) * 4)}:where(.space-x-1>:not(:last-child)){--tw-space-x-reverse:0;margin-inline-start:calc(calc(var(--spacing) * 1) * var(--tw-space-x-reverse));margin-inline-end:calc(calc(var(--spacing) * 1) * calc(1 - var(--tw-space-x-reverse)))}.overflow-hidden{overflow:hidden}.rounded-full{border-radius:3.40282e38px}.rounded-md{border-radius:var(--radius-md)}.rounded-sm{border-radius:var(--radius-sm)}.rounded-t-lg{border-top-left-radius:var(--radius-lg);border-top-right-radius:var(--radius-lg)}.rounded-l-md{border-top-left-radius:var(--radius-md);border-bottom-left-radius:var(--radius-md)}.rounded-r-md{border-top-right-radius:var(--radius-md);border-bottom-right-radius:var(--radius-md)}.rounded-br-lg{border-bottom-right-radius:var(--radius-lg)}.rounded-bl-lg{border-bottom-left-radius:var(--radius-lg)}.border{border-style:var(--tw-border-style);border-width:1px}.border-t{border-top-style:var(--tw-border-style);border-top-width:1px}.border-r{border-right-style:var(--tw-border-style);border-right-width:1px}.border-\[\#19140035\]{border-color:#19140035}.border-\[\#e3e3e0\]{border-color:#e3e3e0}.border-black{border-color:var(--color-black)}.border-gray-200{border-color:var(--color-gray-200)}.border-gray-300{border-color:var(--color-gray-300)}.border-gray-400{border-color:var(--color-gray-400)}.border-transparent{border-color:#0000}.bg-\[\#1b1b18\]{background-color:#1b1b18}.bg-\[\#FDFDFC\]{background-color:#fdfdfc}.bg-\[\#dbdbd7\]{background-color:#dbdbd7}.bg-\[\#fff2f2\]{background-color:#fff2f2}.bg-gray-100{background-color:var(--color-gray-100)}.bg-gray-200{background-color:var(--color-gray-200)}.bg-white{background-color:var(--color-white)}.p-6{padding:calc(var(--spacing) * 6)}.px-2{padding-inline:calc(var(--spacing) * 2)}.px-4{padding-inline:calc(var(--spacing) * 4)}.px-5{padding-inline:calc(var(--spacing) * 5)}.px-6{padding-inline:calc(var(--spacing) * 6)}.py-1{padding-block:calc(var(--spacing) * 1)}.py-1\.5{padding-block:calc(var(--spacing) * 1.5)}.py-2{padding-block:calc(var(--spacing) * 2)}.py-4{padding-block:calc(var(--spacing) * 4)}.pt-8{padding-top:calc(var(--spacing) * 8)}.pb-6{padding-bottom:calc(var(--spacing) * 6)}.pb-12{padding-bottom:calc(var(--spacing) * 12)}.text-center{text-align:center}.text-lg{font-size:var(--text-lg);line-height:var(--tw-leading,var(--text-lg--line-height))}.text-sm{font-size:var(--text-sm);line-height:var(--tw-leading,var(--text-sm--line-height))}.text-\[13px\]{font-size:13px}.leading-5{--tw-leading:calc(var(--spacing) * 5);line-height:calc(var(--spacing) * 5)}.leading-7{--tw-leading:calc(var(--spacing) * 7);line-height:calc(var(--spacing) * 7)}.leading-\[20px\]{--tw-leading:20px;line-height:20px}.leading-normal{--tw-leading:var(--leading-normal);line-height:var(--leading-normal)}.font-medium{--tw-font-weight:var(--font-weight-medium);font-weight:var(--font-weight-medium)}.font-semibold{--tw-font-weight:var(--font-weight-semibold);font-weight:var(--font-weight-semibold)}.tracking-wider{--tw-tracking:var(--tracking-wider);letter-spacing:var(--tracking-wider)}.text-\[\#1B1B18\],.text-\[\#1b1b18\]{color:#1b1b18}.text-\[\#706f6c\]{color:#706f6c}.text-\[\#F3BEC7\]{color:#f3bec7}.text-\[\#F8B803\]{color:#f8b803}.text-\[\#F53003\],.text-\[\#f53003\]{color:#f53003}.text-gray-200{color:var(--color-gray-200)}.text-gray-300{color:var(--color-gray-300)}.text-gray-400{color:var(--color-gray-400)}.text-gray-500{color:var(--color-gray-500)}.text-gray-600{color:var(--color-gray-600)}.text-gray-700{color:var(--color-gray-700)}.text-gray-800{color:var(--color-gray-800)}.text-gray-900{color:var(--color-gray-900)}.text-white{color:var(--color-white)}.uppercase{text-transform:uppercase}.underline{text-decoration-line:underline}.underline-offset-4{text-underline-offset:4px}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.opacity-100{opacity:1}.mix-blend-color{mix-blend-mode:color}.mix-blend-darken{mix-blend-mode:darken}.mix-blend-hard-light{mix-blend-mode:hard-light}.mix-blend-multiply{mix-blend-mode:multiply}.shadow{--tw-shadow:0 1px 3px 0 var(--tw-shadow-color,#0000001a), 0 1px 2px -1px var(--tw-shadow-color,#0000001a);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.shadow-\[0px_0px_1px_0px_rgba\(0\,0\,0\,0\.03\)\,0px_1px_2px_0px_rgba\(0\,0\,0\,0\.06\)\]{--tw-shadow:0px 0px 1px 0px var(--tw-shadow-color,#00000008), 0px 1px 2px 0px var(--tw-shadow-color,#0000000f);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.shadow-\[inset_0px_0px_0px_1px_rgba\(26\,26\,0\,0\.16\)\]{--tw-shadow:inset 0px 0px 0px 1px var(--tw-shadow-color,#1a1a0029);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.shadow-sm{--tw-shadow:0 1px 3px 0 var(--tw-shadow-color,#0000001a), 0 1px 2px -1px var(--tw-shadow-color,#0000001a);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.ring-gray-300{--tw-ring-color:var(--color-gray-300)}.filter{filter:var(--tw-blur,) var(--tw-brightness,) var(--tw-contrast,) var(--tw-grayscale,) var(--tw-hue-rotate,) var(--tw-invert,) var(--tw-saturate,) var(--tw-sepia,) var(--tw-drop-shadow,)}.transition{transition-property:color,background-color,border-color,outline-color,text-decoration-color,fill,stroke,--tw-gradient-from,--tw-gradient-via,--tw-gradient-to,opacity,box-shadow,transform,translate,scale,rotate,filter,-webkit-backdrop-filter,backdrop-filter,display,content-visibility,overlay,pointer-events;transition-timing-function:var(--tw-ease,var(--default-transition-timing-function));transition-duration:var(--tw-duration,var(--default-transition-duration))}.transition-all{transition-property:all;transition-timing-function:var(--tw-ease,var(--default-transition-timing-function));transition-duration:var(--tw-duration,var(--default-transition-duration))}.transition-opacity{transition-property:opacity;transition-timing-function:var(--tw-ease,var(--default-transition-timing-function));transition-duration:var(--tw-duration,var(--default-transition-duration))}.delay-200{transition-delay:.2s}.delay-300{transition-delay:.3s}.delay-400{transition-delay:.4s}.duration-150{--tw-duration:.15s;transition-duration:.15s}.duration-750{--tw-duration:.75s;transition-duration:.75s}.ease-in-out{--tw-ease:var(--ease-in-out);transition-timing-function:var(--ease-in-out)}.\[--stroke-color\:\#1B1B18\]{--stroke-color:#1b1b18}.not-has-\[nav\]\:hidden:not(:has(:is(nav))){display:none}.before\:absolute:before{content:var(--tw-content);position:absolute}.before\:top-0:before{content:var(--tw-content);top:calc(var(--spacing) * 0)}.before\:top-1\/2:before{content:var(--tw-content);top:50%}.before\:bottom-0:before{content:var(--tw-content);bottom:calc(var(--spacing) * 0)}.before\:bottom-1\/2:before{content:var(--tw-content);bottom:50%}.before\:left-\[0\.4rem\]:before{content:var(--tw-content);left:.4rem}.before\:border-l:before{content:var(--tw-content);border-left-style:var(--tw-border-style);border-left-width:1px}.before\:border-\[\#e3e3e0\]:before{content:var(--tw-content);border-color:#e3e3e0}@media(hover:hover){.hover\:border-\[\#1915014a\]:hover{border-color:#1915014a}.hover\:border-\[\#19140035\]:hover{border-color:#19140035}.hover\:border-black:hover{border-color:var(--color-black)}.hover\:bg-black:hover{background-color:var(--color-black)}.hover\:bg-gray-100:hover{background-color:var(--color-gray-100)}.hover\:text-gray-400:hover{color:var(--color-gray-400)}.hover\:text-gray-700:hover{color:var(--color-gray-700)}}.focus\:border-blue-300:focus{border-color:var(--color-blue-300)}.focus\:ring:focus{--tw-ring-shadow:var(--tw-ring-inset,) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color,currentcolor);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.focus\:outline-none:focus{--tw-outline-style:none;outline-style:none}.active\:bg-gray-100:active{background-color:var(--color-gray-100)}.active\:text-gray-500:active{color:var(--color-gray-500)}.active\:text-gray-700:active{color:var(--color-gray-700)}.active\:text-gray-800:active{color:var(--color-gray-800)}@media(min-width:40rem){.sm\:flex{display:flex}.sm\:hidden{display:none}.sm\:flex-1{flex:1}.sm\:items-center{align-items:center}.sm\:justify-between{justify-content:space-between}.sm\:justify-start{justify-content:flex-start}.sm\:gap-2{gap:calc(var(--spacing) * 2)}.sm\:px-6{padding-inline:calc(var(--spacing) * 6)}.sm\:pt-0{padding-top:calc(var(--spacing) * 0)}}@media(min-width:64rem){.lg\:mt-10{margin-top:calc(var(--spacing) * 10)}.lg\:mb-0{margin-bottom:calc(var(--spacing) * 0)}.lg\:mb-6{margin-bottom:calc(var(--spacing) * 6)}.lg\:-ml-px{margin-left:-1px}.lg\:ml-0{margin-left:calc(var(--spacing) * 0)}.lg\:block{display:block}.lg\:aspect-auto{aspect-ratio:auto}.lg\:w-\[438px\]{width:438px}.lg\:max-w-4xl{max-width:var(--container-4xl)}.lg\:grow{flex-grow:1}.lg\:flex-row{flex-direction:row}.lg\:justify-center{justify-content:center}.lg\:rounded-t-none{border-top-left-radius:0;border-top-right-radius:0}.lg\:rounded-tl-lg{border-top-left-radius:var(--radius-lg)}.lg\:rounded-r-lg{border-top-right-radius:var(--radius-lg);border-bottom-right-radius:var(--radius-lg)}.lg\:rounded-br-none{border-bottom-right-radius:0}.lg\:p-8{padding:calc(var(--spacing) * 8)}.lg\:p-20{padding:calc(var(--spacing) * 20)}.lg\:px-8{padding-inline:calc(var(--spacing) * 8)}.lg\:pb-10{padding-bottom:calc(var(--spacing) * 10)}}.rtl\:flex-row-reverse:where(:dir(rtl),[dir=rtl],[dir=rtl] *){flex-direction:row-reverse}@media(prefers-color-scheme:dark){.dark\:border-\[\#3E3E3A\]{border-color:#3e3e3a}.dark\:border-\[\#eeeeec\]{border-color:#eeeeec}.dark\:border-gray-600{border-color:var(--color-gray-600)}.dark\:bg-\[\#0a0a0a\]{background-color:#0a0a0a}.dark\:bg-\[\#1D0002\]{background-color:#1d0002}.dark\:bg-\[\#3E3E3A\]{background-color:#3e3e3a}.dark\:bg-\[\#161615\]{background-color:#161615}.dark\:bg-\[\#eeeeec\]{background-color:#eeeeec}.dark\:bg-gray-700{background-color:var(--color-gray-700)}.dark\:bg-gray-800{background-color:var(--color-gray-800)}.dark\:bg-gray-900{background-color:var(--color-gray-900)}.dark\:text-\[\#1C1C1A\]{color:#1c1c1a}.dark\:text-\[\#4B0600\]{color:#4b0600}.dark\:text-\[\#391800\]{color:#391800}.dark\:text-\[\#733000\]{color:#733000}.dark\:text-\[\#A1A09A\]{color:#a1a09a}.dark\:text-\[\#EDEDEC\]{color:#ededec}.dark\:text-\[\#F61500\]{color:#f61500}.dark\:text-\[\#FF4433\]{color:#f43}.dark\:text-black{color:var(--color-black)}.dark\:text-gray-200{color:var(--color-gray-200)}.dark\:text-gray-300{color:var(--color-gray-300)}.dark\:text-gray-400{color:var(--color-gray-400)}.dark\:text-gray-600{color:var(--color-gray-600)}.dark\:mix-blend-hard-light{mix-blend-mode:hard-light}.dark\:mix-blend-normal{mix-blend-mode:normal}.dark\:shadow-\[inset_0px_0px_0px_1px_\#fffaed2d\]{--tw-shadow:inset 0px 0px 0px 1px var(--tw-shadow-color,#fffaed2d);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.dark\:\[--stroke-color\:\#FF750F\]{--stroke-color:#ff750f}.dark\:before\:border-\[\#3E3E3A\]:before{content:var(--tw-content);border-color:#3e3e3a}@media(hover:hover){.dark\:hover\:border-\[\#3E3E3A\]:hover{border-color:#3e3e3a}.dark\:hover\:border-\[\#62605b\]:hover{border-color:#62605b}.dark\:hover\:border-white:hover{border-color:var(--color-white)}.dark\:hover\:bg-gray-900:hover{background-color:var(--color-gray-900)}.dark\:hover\:bg-white:hover{background-color:var(--color-white)}.dark\:hover\:text-gray-200:hover{color:var(--color-gray-200)}.dark\:hover\:text-gray-300:hover{color:var(--color-gray-300)}}.dark\:focus\:border-blue-700:focus{border-color:var(--color-blue-700)}.dark\:focus\:border-blue-800:focus{border-color:var(--color-blue-800)}.dark\:active\:bg-gray-700:active{background-color:var(--color-gray-700)}.dark\:active\:text-gray-300:active{color:var(--color-gray-300)}}@starting-style{.starting\:opacity-0{opacity:0}}@media(prefers-reduced-motion:no-preference){@starting-style{.motion-safe\:starting\:-translate-x-\[26px\]{--tw-translate-x: -26px ;translate:var(--tw-translate-x) var(--tw-translate-y)}}@starting-style{.motion-safe\:starting\:-translate-x-\[51px\]{--tw-translate-x: -51px ;translate:var(--tw-translate-x) var(--tw-translate-y)}}@starting-style{.motion-safe\:starting\:-translate-x-\[78px\]{--tw-translate-x: -78px ;translate:var(--tw-translate-x) var(--tw-translate-y)}}@starting-style{.motion-safe\:starting\:-translate-x-\[102px\]{--tw-translate-x: -102px ;translate:var(--tw-translate-x) var(--tw-translate-y)}}@starting-style{.motion-safe\:starting\:translate-y-6{--tw-translate-y:calc(var(--spacing) * 6);translate:var(--tw-translate-x) var(--tw-translate-y)}}}}@property --tw-translate-x{syntax:"*";inherits:false;initial-value:0}@property --tw-translate-y{syntax:"*";inherits:false;initial-value:0}@property --tw-translate-z{syntax:"*";inherits:false;initial-value:0}@property --tw-rotate-x{syntax:"*";inherits:false}@property --tw-rotate-y{syntax:"*";inherits:false}@property --tw-rotate-z{syntax:"*";inherits:false}@property --tw-skew-x{syntax:"*";inherits:false}@property --tw-skew-y{syntax:"*";inherits:false}@property --tw-space-x-reverse{syntax:"*";inherits:false;initial-value:0}@property --tw-border-style{syntax:"*";inherits:false;initial-value:solid}@property --tw-leading{syntax:"*";inherits:false}@property --tw-font-weight{syntax:"*";inherits:false}@property --tw-tracking{syntax:"*";inherits:false}@property --tw-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-shadow-color{syntax:"*";inherits:false}@property --tw-shadow-alpha{syntax:"<percentage>";inherits:false;initial-value:100%}@property --tw-inset-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-inset-shadow-color{syntax:"*";inherits:false}@property --tw-inset-shadow-alpha{syntax:"<percentage>";inherits:false;initial-value:100%}@property --tw-ring-color{syntax:"*";inherits:false}@property --tw-ring-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-inset-ring-color{syntax:"*";inherits:false}@property --tw-inset-ring-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-ring-inset{syntax:"*";inherits:false}@property --tw-ring-offset-width{syntax:"<length>";inherits:false;initial-value:0}@property --tw-ring-offset-color{syntax:"*";inherits:false;initial-value:#fff}@property --tw-ring-offset-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-blur{syntax:"*";inherits:false}@property --tw-brightness{syntax:"*";inherits:false}@property --tw-contrast{syntax:"*";inherits:false}@property --tw-grayscale{syntax:"*";inherits:false}@property --tw-hue-rotate{syntax:"*";inherits:false}@property --tw-invert{syntax:"*";inherits:false}@property --tw-opacity{syntax:"*";inherits:false}@property --tw-saturate{syntax:"*";inherits:false}@property --tw-sepia{syntax:"*";inherits:false}@property --tw-drop-shadow{syntax:"*";inherits:false}@property --tw-drop-shadow-color{syntax:"*";inherits:false}@property --tw-drop-shadow-alpha{syntax:"<percentage>";inherits:false;initial-value:100%}@property --tw-drop-shadow-size{syntax:"*";inherits:false}@property --tw-duration{syntax:"*";inherits:false}@property --tw-ease{syntax:"*";inherits:false}@property --tw-content{syntax:"*";inherits:false;initial-value:""}@keyframes spin{to{transform:rotate(360deg)}}@keyframes ping{75%,to{opacity:0;transform:scale(2)}}@keyframes pulse{50%{opacity:.5}}@keyframes bounce{0%,to{animation-timing-function:cubic-bezier(.8,0,1,1);transform:translateY(-25%)}50%{animation-timing-function:cubic-bezier(0,0,.2,1);transform:none}}
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                <div class="text-[13px] leading-[20px] flex-1 p-6 pb-6 lg:p-20 lg:pb-10 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                    <h1 class="mb-1 font-medium">Let's get started</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">With so many options available to you,<br /> we suggest you start with the following:</p>
                    <ul class="flex flex-col mb-4 lg:mb-6">
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:top-1/2 before:bottom-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white dark:bg-[#161615]">
                                <span class="flex items-center justify-center rounded-full bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] w-3.5 h-3.5 border dark:border-[#3E3E3A] border-[#e3e3e0]">
                                    <span class="rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A] w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                Read the
                                <a href="https://laravel.com/docs" target="_blank" class="inline-flex items-center space-x-1 font-medium underline underline-offset-4 text-[#f53003] dark:text-[#FF4433] ml-1">
                                    <span>Documentation</span>
                                    <svg
                                        width="10"
                                        height="11"
                                        viewBox="0 0 10 11"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-2.5 h-2.5"
                                    >
                                        <path
                                            d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001"
                                            stroke="currentColor"
                                            stroke-linecap="square"
                                        />
                                    </svg>
                                </a>
                            </span>
                        </li>
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:bottom-1/2 before:top-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white dark:bg-[#161615]">
                                <span class="flex items-center justify-center rounded-full bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] w-3.5 h-3.5 border dark:border-[#3E3E3A] border-[#e3e3e0]">
                                    <span class="rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A] w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                Watch video tutorials at
                                <a href="https://laracasts.com" target="_blank" class="inline-flex items-center space-x-1 font-medium underline underline-offset-4 text-[#f53003] dark:text-[#FF4433] ml-1">
                                    <span>Laracasts</span>
                                    <svg
                                        width="10"
                                        height="11"
                                        viewBox="0 0 10 11"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-2.5 h-2.5"
                                    >
                                        <path
                                            d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001"
                                            stroke="currentColor"
                                            stroke-linecap="square"
                                        />
                                    </svg>
                                </a>
                            </span>
                        </li>
                    </ul>
                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            <a href="https://cloud.laravel.com" target="_blank" class="inline-block dark:bg-[#eeeeec] dark:border-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white dark:hover:border-white hover:bg-black hover:border-black px-5 py-1.5 bg-[#1b1b18] rounded-sm border border-black text-white text-sm leading-normal">
                                Deploy now
                            </a>
                        </li>
                    </ul>

                    <p class="mt-6 lg:mt-10 text-[#706f6c] dark:text-[#A1A09A]">
                        v{{ app()->version() }}
                        <a href="https://github.com/laravel/framework/blob/13.x/CHANGELOG.md" target="_blank" class="inline-flex items-center space-x-1 font-medium underline underline-offset-4 text-[#f53003] dark:text-[#FF4433] ml-1">
                            <span>View changelog</span>
                            <svg
                                width="10"
                                height="11"
                                viewBox="0 0 10 11"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-2.5 h-2.5"
                            >
                                <path
                                    d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001"
                                    stroke="currentColor"
                                    stroke-linecap="square"
                                />
                            </svg>
                        </a>
                    </p>
                </div>
                <div class="bg-[#fff2f2] dark:bg-[#1D0002] relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/364] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden">
                    {{-- Laravel Logo --}}
                    <svg class="w-full text-[#F53003] dark:text-[#F61500] transition-all translate-y-0 opacity-100 max-w-none duration-750 starting:opacity-0 motion-safe:starting:translate-y-6" viewBox="0 0 438 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z" fill="currentColor" />
                        <path d="M110.256 41.6337C108.061 38.1275 104.945 35.3731 100.905 33.3681C96.8667 31.3647 92.8016 30.3618 88.7131 30.3618C83.4247 30.3618 78.5885 31.3389 74.201 33.2923C69.8111 35.2456 66.0474 37.928 62.9059 41.3333C59.7643 44.7401 57.3198 48.6726 55.5754 53.1293C53.8287 57.589 52.9572 62.274 52.9572 67.1813C52.9572 72.1925 53.8287 76.8995 55.5754 81.3069C57.3191 85.7173 59.7636 89.6241 62.9059 93.0293C66.0474 96.4361 69.8119 99.1155 74.201 101.069C78.5885 103.022 83.4247 103.999 88.7131 103.999C92.8016 103.999 96.8667 102.997 100.905 100.994C104.945 98.9911 108.061 96.2359 110.256 92.7282V102.195H126.563V32.1642H110.256V41.6337ZM108.76 75.7472C107.762 78.4531 106.366 80.8078 104.572 82.8112C102.776 84.8161 100.606 86.4183 98.0637 87.6206C95.5202 88.823 92.7004 89.4238 89.6103 89.4238C86.5178 89.4238 83.7252 88.823 81.2324 87.6206C78.7388 86.4183 76.5949 84.8161 74.7998 82.8112C73.004 80.8078 71.6319 78.4531 70.6856 75.7472C69.7356 73.0421 69.2644 70.1868 69.2644 67.1821C69.2644 64.1758 69.7356 61.3205 70.6856 58.6154C71.6319 55.9102 73.004 53.5571 74.7998 51.5522C76.5949 49.5495 78.738 47.9451 81.2324 46.7427C83.7252 45.5404 86.5178 44.9396 89.6103 44.9396C92.7012 44.9396 95.5202 45.5404 98.0637 46.7427C100.606 47.9451 102.776 49.5487 104.572 51.5522C106.367 53.5571 107.762 55.9102 108.76 58.6154C109.756 61.3205 110.256 64.1758 110.256 67.1821C110.256 70.1868 109.756 73.0421 108.76 75.7472Z" fill="currentColor" />
                        <path d="M242.805 41.6337C240.611 38.1275 237.494 35.3731 233.455 33.3681C229.416 31.3647 225.351 30.3618 221.262 30.3618C215.974 30.3618 211.138 31.3389 206.75 33.2923C202.36 35.2456 198.597 37.928 195.455 41.3333C192.314 44.7401 189.869 48.6726 188.125 53.1293C186.378 57.589 185.507 62.274 185.507 67.1813C185.507 72.1925 186.378 76.8995 188.125 81.3069C189.868 85.7173 192.313 89.6241 195.455 93.0293C198.597 96.4361 202.361 99.1155 206.75 101.069C211.138 103.022 215.974 103.999 221.262 103.999C225.351 103.999 229.416 102.997 233.455 100.994C237.494 98.9911 240.611 96.2359 242.805 92.7282V102.195H259.112V32.1642H242.805V41.6337ZM241.31 75.7472C240.312 78.4531 238.916 80.8078 237.122 82.8112C235.326 84.8161 233.156 86.4183 230.614 87.6206C228.07 88.823 225.251 89.4238 222.16 89.4238C219.068 89.4238 216.275 88.823 213.782 87.6206C211.289 86.4183 209.145 84.8161 207.35 82.8112C205.554 80.8078 204.182 78.4531 203.236 75.7472C202.286 73.0421 201.814 70.1868 201.814 67.1821C201.814 64.1758 202.286 61.3205 203.236 58.6154C204.182 55.9102 205.554 53.5571 207.35 51.5522C209.145 49.5495 211.288 47.9451 213.782 46.7427C216.275 45.5404 219.068 44.9396 222.16 44.9396C225.251 44.9396 228.07 45.5404 230.614 46.7427C233.156 47.9451 235.326 49.5487 237.122 51.5522C238.917 53.5571 240.312 55.9102 241.31 58.6154C242.306 61.3205 242.806 64.1758 242.806 67.1821C242.805 70.1868 242.305 73.0421 241.31 75.7472Z" fill="currentColor" />
                        <path d="M438 -3H421.694V102.197H438V-3Z" fill="currentColor" />
                        <path d="M139.43 102.197H155.735V48.2834H183.712V32.1665H139.43V102.197Z" fill="currentColor" />
                        <path d="M324.49 32.1665L303.995 85.794L283.498 32.1665H266.983L293.748 102.197H314.242L341.006 32.1665H324.49Z" fill="currentColor" />
                        <path d="M376.571 30.3656C356.603 30.3656 340.797 46.8497 340.797 67.1828C340.797 89.6597 356.094 104 378.661 104C391.29 104 399.354 99.1488 409.206 88.5848L398.189 80.0226C398.183 80.031 389.874 90.9895 377.468 90.9895C363.048 90.9895 356.977 79.3111 356.977 73.269H411.075C413.917 50.1328 398.775 30.3656 376.571 30.3656ZM357.02 61.0967C357.145 59.7487 359.023 43.3761 376.442 43.3761C393.861 43.3761 395.978 59.7464 396.099 61.0967H357.02Z" fill="currentColor" />
                    </svg>

                    {{-- 13 --}}
                    <svg class="w-[438px] max-w-none relative -mt-[6.6rem] -ml-8 lg:ml-0 [--stroke-color:#1B1B18] dark:[--stroke-color:#FF750F]" viewBox="0 0 440 392" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g class="mix-blend-darken dark:mix-blend-normal transition-all delay-300 opacity-100 duration-750 starting:opacity-0 text-[#1B1B18] dark:text-black">
                            <mask id="path-1-mask" maskUnits="userSpaceOnUse" x="-0.328613" y="103" width="338" height="299" fill="black">
                                <rect fill="white" x="-0.328613" y="103" width="338" height="299"/>
                                <path d="M234.936 400.8C204.136 400.8 178.936 392.4 159.336 375.6C140.136 358.8 130.536 337 130.536 310.2H200.736C200.736 318.2 203.736 324.8 209.736 330C215.736 335.2 223.736 337.8 233.736 337.8C243.336 337.8 251.136 335 257.136 329.4C263.536 323.8 266.736 316.6 266.736 307.8C266.736 299.8 263.936 293.2 258.336 288C252.736 282.8 245.536 280.2 236.736 280.2H199.536V218.4H236.736C243.536 218.4 249.336 216 254.136 211.2C258.936 206.4 261.336 200.4 261.336 193.2C261.336 184.8 258.736 178.2 253.536 173.4C248.336 168.6 241.736 166.2 233.736 166.2C226.536 166.2 220.336 168.4 215.136 172.8C210.336 177.2 207.936 182.8 207.936 189.6H141.336C141.336 164.8 150.136 144.6 167.736 129C185.336 113 207.936 105 235.536 105C263.136 105 285.536 112.2 302.736 126.6C320.336 141 329.136 160 329.136 183.6C329.136 200.8 324.536 214.8 315.336 225.6C306.136 236 294.336 243.2 279.936 247.2C297.136 252 310.736 260.2 320.736 271.8C331.136 283.4 336.336 298 336.336 315.6C336.336 340.4 326.936 360.8 308.136 376.8C289.336 392.8 264.936 400.8 234.936 400.8Z"/>
                                <path d="M26.8714 167.6H1.67139V105.2H94.6714V400.2H26.8714V167.6Z"/>
                            </mask>
                            <path d="M234.936 400.8C204.136 400.8 178.936 392.4 159.336 375.6C140.136 358.8 130.536 337 130.536 310.2H200.736C200.736 318.2 203.736 324.8 209.736 330C215.736 335.2 223.736 337.8 233.736 337.8C243.336 337.8 251.136 335 257.136 329.4C263.536 323.8 266.736 316.6 266.736 307.8C266.736 299.8 263.936 293.2 258.336 288C252.736 282.8 245.536 280.2 236.736 280.2H199.536V218.4H236.736C243.536 218.4 249.336 216 254.136 211.2C258.936 206.4 261.336 200.4 261.336 193.2C261.336 184.8 258.736 178.2 253.536 173.4C248.336 168.6 241.736 166.2 233.736 166.2C226.536 166.2 220.336 168.4 215.136 172.8C210.336 177.2 207.936 182.8 207.936 189.6H141.336C141.336 164.8 150.136 144.6 167.736 129C185.336 113 207.936 105 235.536 105C263.136 105 285.536 112.2 302.736 126.6C320.336 141 329.136 160 329.136 183.6C329.136 200.8 324.536 214.8 315.336 225.6C306.136 236 294.336 243.2 279.936 247.2C297.136 252 310.736 260.2 320.736 271.8C331.136 283.4 336.336 298 336.336 315.6C336.336 340.4 326.936 360.8 308.136 376.8C289.336 392.8 264.936 400.8 234.936 400.8Z" fill="currentColor"/>
                            <path d="M26.8714 167.6H1.67139V105.2H94.6714V400.2H26.8714V167.6Z" fill="currentColor"/>
                            <path d="M234.936 400.8C204.136 400.8 178.936 392.4 159.336 375.6C140.136 358.8 130.536 337 130.536 310.2H200.736C200.736 318.2 203.736 324.8 209.736 330C215.736 335.2 223.736 337.8 233.736 337.8C243.336 337.8 251.136 335 257.136 329.4C263.536 323.8 266.736 316.6 266.736 307.8C266.736 299.8 263.936 293.2 258.336 288C252.736 282.8 245.536 280.2 236.736 280.2H199.536V218.4H236.736C243.536 218.4 249.336 216 254.136 211.2C258.936 206.4 261.336 200.4 261.336 193.2C261.336 184.8 258.736 178.2 253.536 173.4C248.336 168.6 241.736 166.2 233.736 166.2C226.536 166.2 220.336 168.4 215.136 172.8C210.336 177.2 207.936 182.8 207.936 189.6H141.336C141.336 164.8 150.136 144.6 167.736 129C185.336 113 207.936 105 235.536 105C263.136 105 285.536 112.2 302.736 126.6C320.336 141 329.136 160 329.136 183.6C329.136 200.8 324.536 214.8 315.336 225.6C306.136 236 294.336 243.2 279.936 247.2C297.136 252 310.736 260.2 320.736 271.8C331.136 283.4 336.336 298 336.336 315.6C336.336 340.4 326.936 360.8 308.136 376.8C289.336 392.8 264.936 400.8 234.936 400.8Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-1-mask)"/>
                            <path d="M26.8714 167.6H1.67139V105.2H94.6714V400.2H26.8714V167.6Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-1-mask)"/>
                        </g>

                        <g class="transition-all delay-400 opacity-100 duration-750 starting:opacity-0 motion-safe:starting:-translate-x-[26px] text-[#F3BEC7] dark:text-[#4B0600]">
                            <mask id="path-2-mask" maskUnits="userSpaceOnUse" x="25.3357" y="103" width="338" height="299" fill="black">
                                <rect fill="white" x="25.3357" y="103" width="338" height="299"/>
                                <path d="M260.6 400.8C229.8 400.8 204.6 392.4 185 375.6C165.8 358.8 156.2 337 156.2 310.2H226.4C226.4 318.2 229.4 324.8 235.4 330C241.4 335.2 249.4 337.8 259.4 337.8C269 337.8 276.8 335 282.8 329.4C289.2 323.8 292.4 316.6 292.4 307.8C292.4 299.8 289.6 293.2 284 288C278.4 282.8 271.2 280.2 262.4 280.2H225.2V218.4H262.4C269.2 218.4 275 216 279.8 211.2C284.6 206.4 287 200.4 287 193.2C287 184.8 284.4 178.2 279.2 173.4C274 168.6 267.4 166.2 259.4 166.2C252.2 166.2 246 168.4 240.8 172.8C236 177.2 233.6 182.8 233.6 189.6H167C167 164.8 175.8 144.6 193.4 129C211 113 233.6 105 261.2 105C288.8 105 311.2 112.2 328.4 126.6C346 141 354.8 160 354.8 183.6C354.8 200.8 350.2 214.8 341 225.6C331.8 236 320 243.2 305.6 247.2C322.8 252 336.4 260.2 346.4 271.8C356.8 283.4 362 298 362 315.6C362 340.4 352.6 360.8 333.8 376.8C315 392.8 290.6 400.8 260.6 400.8Z"/>
                                <path d="M52.5357 167.6H27.3357V105.2H120.336V400.2H52.5357V167.6Z"/>
                            </mask>
                            <path d="M260.6 400.8C229.8 400.8 204.6 392.4 185 375.6C165.8 358.8 156.2 337 156.2 310.2H226.4C226.4 318.2 229.4 324.8 235.4 330C241.4 335.2 249.4 337.8 259.4 337.8C269 337.8 276.8 335 282.8 329.4C289.2 323.8 292.4 316.6 292.4 307.8C292.4 299.8 289.6 293.2 284 288C278.4 282.8 271.2 280.2 262.4 280.2H225.2V218.4H262.4C269.2 218.4 275 216 279.8 211.2C284.6 206.4 287 200.4 287 193.2C287 184.8 284.4 178.2 279.2 173.4C274 168.6 267.4 166.2 259.4 166.2C252.2 166.2 246 168.4 240.8 172.8C236 177.2 233.6 182.8 233.6 189.6H167C167 164.8 175.8 144.6 193.4 129C211 113 233.6 105 261.2 105C288.8 105 311.2 112.2 328.4 126.6C346 141 354.8 160 354.8 183.6C354.8 200.8 350.2 214.8 341 225.6C331.8 236 320 243.2 305.6 247.2C322.8 252 336.4 260.2 346.4 271.8C356.8 283.4 362 298 362 315.6C362 340.4 352.6 360.8 333.8 376.8C315 392.8 290.6 400.8 260.6 400.8Z" fill="currentColor"/>
                            <path d="M52.5357 167.6H27.3357V105.2H120.336V400.2H52.5357V167.6Z" fill="currentColor"/>
                            <path d="M260.6 400.8C229.8 400.8 204.6 392.4 185 375.6C165.8 358.8 156.2 337 156.2 310.2H226.4C226.4 318.2 229.4 324.8 235.4 330C241.4 335.2 249.4 337.8 259.4 337.8C269 337.8 276.8 335 282.8 329.4C289.2 323.8 292.4 316.6 292.4 307.8C292.4 299.8 289.6 293.2 284 288C278.4 282.8 271.2 280.2 262.4 280.2H225.2V218.4H262.4C269.2 218.4 275 216 279.8 211.2C284.6 206.4 287 200.4 287 193.2C287 184.8 284.4 178.2 279.2 173.4C274 168.6 267.4 166.2 259.4 166.2C252.2 166.2 246 168.4 240.8 172.8C236 177.2 233.6 182.8 233.6 189.6H167C167 164.8 175.8 144.6 193.4 129C211 113 233.6 105 261.2 105C288.8 105 311.2 112.2 328.4 126.6C346 141 354.8 160 354.8 183.6C354.8 200.8 350.2 214.8 341 225.6C331.8 236 320 243.2 305.6 247.2C322.8 252 336.4 260.2 346.4 271.8C356.8 283.4 362 298 362 315.6C362 340.4 352.6 360.8 333.8 376.8C315 392.8 290.6 400.8 260.6 400.8Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-2-mask)"/>
                            <path d="M52.5357 167.6H27.3357V105.2H120.336V400.2H52.5357V167.6Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-2-mask)"/>
                        </g>
                        
                        <g class="mix-blend-color dark:mix-blend-hard-light transition-all delay-400 opacity-100 duration-750 starting:opacity-0 motion-safe:starting:-translate-x-[51px] text-[#F8B803] dark:text-[#391800]">
                            <mask id="path-3-mask" maskUnits="userSpaceOnUse" x="51" y="103" width="338" height="299" fill="black">
                                <rect fill="white" x="51" y="103" width="338" height="299"/>
                                <path d="M286.264 400.8C255.464 400.8 230.264 392.4 210.664 375.6C191.464 358.8 181.864 337 181.864 310.2H252.064C252.064 318.2 255.064 324.8 261.064 330C267.064 335.2 275.064 337.8 285.064 337.8C294.664 337.8 302.464 335 308.464 329.4C314.864 323.8 318.064 316.6 318.064 307.8C318.064 299.8 315.264 293.2 309.664 288C304.064 282.8 296.864 280.2 288.064 280.2H250.864V218.4H288.064C294.864 218.4 300.664 216 305.464 211.2C310.264 206.4 312.664 200.4 312.664 193.2C312.664 184.8 310.064 178.2 304.864 173.4C299.664 168.6 293.064 166.2 285.064 166.2C277.864 166.2 271.664 168.4 266.464 172.8C261.664 177.2 259.264 182.8 259.264 189.6H192.664C192.664 164.8 201.464 144.6 219.064 129C236.664 113 259.264 105 286.864 105C314.464 105 336.864 112.2 354.064 126.6C371.664 141 380.464 160 380.464 183.6C380.464 200.8 375.864 214.8 366.664 225.6C357.464 236 345.664 243.2 331.264 247.2C348.464 252 362.064 260.2 372.064 271.8C382.464 283.4 387.664 298 387.664 315.6C387.664 340.4 378.264 360.8 359.464 376.8C340.664 392.8 316.264 400.8 286.264 400.8Z"/>
                                <path d="M78.2 167.6H53V105.2H146V400.2H78.2V167.6Z"/>
                            </mask>
                            <path d="M286.264 400.8C255.464 400.8 230.264 392.4 210.664 375.6C191.464 358.8 181.864 337 181.864 310.2H252.064C252.064 318.2 255.064 324.8 261.064 330C267.064 335.2 275.064 337.8 285.064 337.8C294.664 337.8 302.464 335 308.464 329.4C314.864 323.8 318.064 316.6 318.064 307.8C318.064 299.8 315.264 293.2 309.664 288C304.064 282.8 296.864 280.2 288.064 280.2H250.864V218.4H288.064C294.864 218.4 300.664 216 305.464 211.2C310.264 206.4 312.664 200.4 312.664 193.2C312.664 184.8 310.064 178.2 304.864 173.4C299.664 168.6 293.064 166.2 285.064 166.2C277.864 166.2 271.664 168.4 266.464 172.8C261.664 177.2 259.264 182.8 259.264 189.6H192.664C192.664 164.8 201.464 144.6 219.064 129C236.664 113 259.264 105 286.864 105C314.464 105 336.864 112.2 354.064 126.6C371.664 141 380.464 160 380.464 183.6C380.464 200.8 375.864 214.8 366.664 225.6C357.464 236 345.664 243.2 331.264 247.2C348.464 252 362.064 260.2 372.064 271.8C382.464 283.4 387.664 298 387.664 315.6C387.664 340.4 378.264 360.8 359.464 376.8C340.664 392.8 316.264 400.8 286.264 400.8Z" fill="currentColor"/>
                            <path d="M78.2 167.6H53V105.2H146V400.2H78.2V167.6Z" fill="currentColor"/>
                            <path d="M286.264 400.8C255.464 400.8 230.264 392.4 210.664 375.6C191.464 358.8 181.864 337 181.864 310.2H252.064C252.064 318.2 255.064 324.8 261.064 330C267.064 335.2 275.064 337.8 285.064 337.8C294.664 337.8 302.464 335 308.464 329.4C314.864 323.8 318.064 316.6 318.064 307.8C318.064 299.8 315.264 293.2 309.664 288C304.064 282.8 296.864 280.2 288.064 280.2H250.864V218.4H288.064C294.864 218.4 300.664 216 305.464 211.2C310.264 206.4 312.664 200.4 312.664 193.2C312.664 184.8 310.064 178.2 304.864 173.4C299.664 168.6 293.064 166.2 285.064 166.2C277.864 166.2 271.664 168.4 266.464 172.8C261.664 177.2 259.264 182.8 259.264 189.6H192.664C192.664 164.8 201.464 144.6 219.064 129C236.664 113 259.264 105 286.864 105C314.464 105 336.864 112.2 354.064 126.6C371.664 141 380.464 160 380.464 183.6C380.464 200.8 375.864 214.8 366.664 225.6C357.464 236 345.664 243.2 331.264 247.2C348.464 252 362.064 260.2 372.064 271.8C382.464 283.4 387.664 298 387.664 315.6C387.664 340.4 378.264 360.8 359.464 376.8C340.664 392.8 316.264 400.8 286.264 400.8Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-3-mask)"/>
                            <path d="M78.2 167.6H53V105.2H146V400.2H78.2V167.6Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-3-mask)"/>
                        </g>
                        
                        <g class="mix-blend-multiply dark:mix-blend-normal transition-all delay-400 opacity-100 duration-750 starting:opacity-0 motion-safe:starting:-translate-x-[78px] text-[#F3BEC7] dark:text-[#733000]">
                            <mask id="path-4-mask" maskUnits="userSpaceOnUse" x="76.6643" y="103" width="338" height="299" fill="black">
                                <rect fill="white" x="76.6643" y="103" width="338" height="299"/>
                                <path d="M311.929 400.8C281.129 400.8 255.929 392.4 236.329 375.6C217.129 358.8 207.529 337 207.529 310.2H277.729C277.729 318.2 280.729 324.8 286.729 330C292.729 335.2 300.729 337.8 310.729 337.8C320.329 337.8 328.129 335 334.129 329.4C340.529 323.8 343.729 316.6 343.729 307.8C343.729 299.8 340.929 293.2 335.329 288C329.729 282.8 322.529 280.2 313.729 280.2H276.529V218.4H313.729C320.529 218.4 326.329 216 331.129 211.2C335.929 206.4 338.329 200.4 338.329 193.2C338.329 184.8 335.729 178.2 330.529 173.4C325.329 168.6 318.729 166.2 310.729 166.2C303.529 166.2 297.329 168.4 292.129 172.8C287.329 177.2 284.929 182.8 284.929 189.6H218.329C218.329 164.8 227.129 144.6 244.729 129C262.329 113 284.929 105 312.529 105C340.129 105 362.529 112.2 379.729 126.6C397.329 141 406.129 160 406.129 183.6C406.129 200.8 401.529 214.8 392.329 225.6C383.129 236 371.329 243.2 356.929 247.2C374.129 252 387.729 260.2 397.729 271.8C408.129 283.4 413.329 298 413.329 315.6C413.329 340.4 403.929 360.8 385.129 376.8C366.329 392.8 341.929 400.8 311.929 400.8Z"/>
                                <path d="M103.864 167.6H78.6643V105.2H171.664V400.2H103.864V167.6Z"/>
                            </mask>
                            <path d="M311.929 400.8C281.129 400.8 255.929 392.4 236.329 375.6C217.129 358.8 207.529 337 207.529 310.2H277.729C277.729 318.2 280.729 324.8 286.729 330C292.729 335.2 300.729 337.8 310.729 337.8C320.329 337.8 328.129 335 334.129 329.4C340.529 323.8 343.729 316.6 343.729 307.8C343.729 299.8 340.929 293.2 335.329 288C329.729 282.8 322.529 280.2 313.729 280.2H276.529V218.4H313.729C320.529 218.4 326.329 216 331.129 211.2C335.929 206.4 338.329 200.4 338.329 193.2C338.329 184.8 335.729 178.2 330.529 173.4C325.329 168.6 318.729 166.2 310.729 166.2C303.529 166.2 297.329 168.4 292.129 172.8C287.329 177.2 284.929 182.8 284.929 189.6H218.329C218.329 164.8 227.129 144.6 244.729 129C262.329 113 284.929 105 312.529 105C340.129 105 362.529 112.2 379.729 126.6C397.329 141 406.129 160 406.129 183.6C406.129 200.8 401.529 214.8 392.329 225.6C383.129 236 371.329 243.2 356.929 247.2C374.129 252 387.729 260.2 397.729 271.8C408.129 283.4 413.329 298 413.329 315.6C413.329 340.4 403.929 360.8 385.129 376.8C366.329 392.8 341.929 400.8 311.929 400.8Z" fill="currentColor"/>
                            <path d="M103.864 167.6H78.6643V105.2H171.664V400.2H103.864V167.6Z" fill="currentColor"/>
                            <path d="M311.929 400.8C281.129 400.8 255.929 392.4 236.329 375.6C217.129 358.8 207.529 337 207.529 310.2H277.729C277.729 318.2 280.729 324.8 286.729 330C292.729 335.2 300.729 337.8 310.729 337.8C320.329 337.8 328.129 335 334.129 329.4C340.529 323.8 343.729 316.6 343.729 307.8C343.729 299.8 340.929 293.2 335.329 288C329.729 282.8 322.529 280.2 313.729 280.2H276.529V218.4H313.729C320.529 218.4 326.329 216 331.129 211.2C335.929 206.4 338.329 200.4 338.329 193.2C338.329 184.8 335.729 178.2 330.529 173.4C325.329 168.6 318.729 166.2 310.729 166.2C303.529 166.2 297.329 168.4 292.129 172.8C287.329 177.2 284.929 182.8 284.929 189.6H218.329C218.329 164.8 227.129 144.6 244.729 129C262.329 113 284.929 105 312.529 105C340.129 105 362.529 112.2 379.729 126.6C397.329 141 406.129 160 406.129 183.6C406.129 200.8 401.529 214.8 392.329 225.6C383.129 236 371.329 243.2 356.929 247.2C374.129 252 387.729 260.2 397.729 271.8C408.129 283.4 413.329 298 413.329 315.6C413.329 340.4 403.929 360.8 385.129 376.8C366.329 392.8 341.929 400.8 311.929 400.8Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-4-mask)"/>
                            <path d="M103.864 167.6H78.6643V105.2H171.664V400.2H103.864V167.6Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-4-mask)"/>
                        </g>
                        
                        <g class="mix-blend-hard-light transition-all delay-400 opacity-100 duration-750 starting:opacity-0 motion-safe:starting:-translate-x-[102px] text-[#F3BEC7] dark:text-[#4B0600]">
                            <mask id="path-5-mask" maskUnits="userSpaceOnUse" x="102.329" y="103" width="338" height="299" fill="black">
                                <rect fill="white" x="102.329" y="103" width="338" height="299"/>
                                <path d="M337.593 400.8C306.793 400.8 281.593 392.4 261.993 375.6C242.793 358.8 233.193 337 233.193 310.2H303.393C303.393 318.2 306.393 324.8 312.393 330C318.393 335.2 326.393 337.8 336.393 337.8C345.993 337.8 353.793 335 359.793 329.4C366.193 323.8 369.393 316.6 369.393 307.8C369.393 299.8 366.593 293.2 360.993 288C355.393 282.8 348.193 280.2 339.393 280.2H302.193V218.4H339.393C346.193 218.4 351.993 216 356.793 211.2C361.593 206.4 363.993 200.4 363.993 193.2C363.993 184.8 361.393 178.2 356.193 173.4C350.993 168.6 344.393 166.2 336.393 166.2C329.193 166.2 322.993 168.4 317.793 172.8C312.993 177.2 310.593 182.8 310.593 189.6H243.993C243.993 164.8 252.793 144.6 270.393 129C287.993 113 310.593 105 338.193 105C365.793 105 388.193 112.2 405.393 126.6C422.993 141 431.793 160 431.793 183.6C431.793 200.8 427.193 214.8 417.993 225.6C408.793 236 396.993 243.2 382.593 247.2C399.793 252 413.393 260.2 423.393 271.8C433.793 283.4 438.993 298 438.993 315.6C438.993 340.4 429.593 360.8 410.793 376.8C391.993 392.8 367.593 400.8 337.593 400.8Z"/>
                                <path d="M129.529 167.6H104.329V105.2H197.329V400.2H129.529V167.6Z"/>
                            </mask>
                            <path d="M337.593 400.8C306.793 400.8 281.593 392.4 261.993 375.6C242.793 358.8 233.193 337 233.193 310.2H303.393C303.393 318.2 306.393 324.8 312.393 330C318.393 335.2 326.393 337.8 336.393 337.8C345.993 337.8 353.793 335 359.793 329.4C366.193 323.8 369.393 316.6 369.393 307.8C369.393 299.8 366.593 293.2 360.993 288C355.393 282.8 348.193 280.2 339.393 280.2H302.193V218.4H339.393C346.193 218.4 351.993 216 356.793 211.2C361.593 206.4 363.993 200.4 363.993 193.2C363.993 184.8 361.393 178.2 356.193 173.4C350.993 168.6 344.393 166.2 336.393 166.2C329.193 166.2 322.993 168.4 317.793 172.8C312.993 177.2 310.593 182.8 310.593 189.6H243.993C243.993 164.8 252.793 144.6 270.393 129C287.993 113 310.593 105 338.193 105C365.793 105 388.193 112.2 405.393 126.6C422.993 141 431.793 160 431.793 183.6C431.793 200.8 427.193 214.8 417.993 225.6C408.793 236 396.993 243.2 382.593 247.2C399.793 252 413.393 260.2 423.393 271.8C433.793 283.4 438.993 298 438.993 315.6C438.993 340.4 429.593 360.8 410.793 376.8C391.993 392.8 367.593 400.8 337.593 400.8Z" fill="currentColor"/>
                            <path d="M129.529 167.6H104.329V105.2H197.329V400.2H129.529V167.6Z" fill="currentColor"/>
                            <path d="M337.593 400.8C306.793 400.8 281.593 392.4 261.993 375.6C242.793 358.8 233.193 337 233.193 310.2H303.393C303.393 318.2 306.393 324.8 312.393 330C318.393 335.2 326.393 337.8 336.393 337.8C345.993 337.8 353.793 335 359.793 329.4C366.193 323.8 369.393 316.6 369.393 307.8C369.393 299.8 366.593 293.2 360.993 288C355.393 282.8 348.193 280.2 339.393 280.2H302.193V218.4H339.393C346.193 218.4 351.993 216 356.793 211.2C361.593 206.4 363.993 200.4 363.993 193.2C363.993 184.8 361.393 178.2 356.193 173.4C350.993 168.6 344.393 166.2 336.393 166.2C329.193 166.2 322.993 168.4 317.793 172.8C312.993 177.2 310.593 182.8 310.593 189.6H243.993C243.993 164.8 252.793 144.6 270.393 129C287.993 113 310.593 105 338.193 105C365.793 105 388.193 112.2 405.393 126.6C422.993 141 431.793 160 431.793 183.6C431.793 200.8 427.193 214.8 417.993 225.6C408.793 236 396.993 243.2 382.593 247.2C399.793 252 413.393 260.2 423.393 271.8C433.793 283.4 438.993 298 438.993 315.6C438.993 340.4 429.593 360.8 410.793 376.8C391.993 392.8 367.593 400.8 337.593 400.8Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-5-mask)"/>
                            <path d="M129.529 167.6H104.329V105.2H197.329V400.2H129.529V167.6Z" stroke="var(--stroke-color)" stroke-width="2.4" mask="url(#path-5-mask)"/>
                        </g>
                    </svg>
                    <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"></div>
                </div>
            </main>
        </div>

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
````

## File: routes/console.php
````php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
````

## File: storage/app/private/.gitignore
````
*
!.gitignore
````

## File: storage/app/public/.gitignore
````
*
!.gitignore
````

## File: storage/app/.gitignore
````
*
!private/
!public/
!.gitignore
````

## File: storage/framework/cache/data/.gitignore
````
*
!.gitignore
````

## File: storage/framework/cache/.gitignore
````
*
!data/
!.gitignore
````

## File: storage/framework/sessions/.gitignore
````
*
!.gitignore
````

## File: storage/framework/testing/.gitignore
````
*
!.gitignore
````

## File: storage/framework/views/.gitignore
````
*
!.gitignore
````

## File: storage/framework/.gitignore
````
compiled.php
config.php
down
events.scanned.php
lsp-*.php
maintenance.php
routes.php
routes.scanned.php
schedule-*
services.json
````

## File: storage/logs/.gitignore
````
*
!.gitignore
````

## File: tests/Feature/AgendaTest.php
````php
<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgendaTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $this->user = User::factory()->create();
        $this->user->assignRole('member');
    }

    public function test_can_list_agendas_with_strict_event_filtering(): void
    {
        $event = Event::factory()->create();

        Agenda::create([
            'title'      => 'BPH Pusat Agenda',
            'start_date' => now(),
            'event_id'   => null,
        ]);

        Agenda::create([
            'title'      => 'Event Agenda',
            'start_date' => now(),
            'event_id'   => $event->id,
        ]);

        // Query without event_id -> should return BPH Pusat agenda only
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/agendas');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['title' => 'BPH Pusat Agenda']);

        // Query with event_id -> should return event agenda only
        $responseEvent = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/agendas?event_id=' . $event->id);

        $responseEvent->assertStatus(200);
        $responseEvent->assertJsonCount(1, 'data');
        $responseEvent->assertJsonFragment(['title' => 'Event Agenda']);
    }

    public function test_can_set_agenda_targets(): void
    {
        $agenda = Agenda::create([
            'title'      => 'Rapat Pleno',
            'start_date' => now(),
        ]);

        $payload = [
            'targets' => [
                ['type' => 'all', 'value' => null],
                ['type' => 'division', 'value' => '1'],
                ['type' => 'coordinator', 'value' => null],
            ],
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/agendas/{$agenda->id}/targets", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseCount('agenda_targets', 3);
        $this->assertDatabaseHas('agenda_targets', [
            'agenda_id'   => $agenda->id,
            'target_type' => 'division',
            'target_value' => '1',
        ]);
    }
}
````

## File: tests/Feature/AuditTrailTest.php
````php
<?php

namespace Tests\Feature;

use App\Models\AuditTrail;
use App\Models\Division;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_model_events_are_recorded_in_audit_trails(): void
    {
        // Act as admin
        $this->actingAs($this->admin, 'sanctum');

        $event = Event::create([
            'name'            => 'Hackathon Protik',
            'budget_approved' => 5000000,
            'start_date'      => '2026-09-01',
        ]);

        $this->assertDatabaseHas('audit_trails', [
            'user_id'        => $this->admin->id,
            'action'         => 'created',
            'auditable_type' => Event::class,
            'auditable_id'   => $event->id,
        ]);

        $event->update(['name' => 'Hackathon Protik 2026']);

        $this->assertDatabaseHas('audit_trails', [
            'user_id'        => $this->admin->id,
            'action'         => 'updated',
            'auditable_type' => Event::class,
            'auditable_id'   => $event->id,
        ]);

        $event->delete();

        $this->assertDatabaseHas('audit_trails', [
            'user_id'        => $this->admin->id,
            'action'         => 'deleted',
            'auditable_type' => Event::class,
            'auditable_id'   => $event->id,
        ]);
    }

    public function test_admin_can_fetch_audit_trails(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/audit-trails');

        $response->assertStatus(200);
    }
}
````

## File: tests/Feature/ExampleTest.php
````php
<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
````

## File: tests/Feature/MonthlyDueTest.php
````php
<?php

namespace Tests\Feature;

use App\Models\MonthlyDue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MonthlyDueTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_fetch_monthly_dues(): void
    {
        $user = User::factory()->create();
        MonthlyDue::create([
            'user_id' => $user->id,
            'month'   => 10,
            'year'    => 2025,
            'amount'  => 20000,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/monthly-dues');

        $response->assertStatus(200);
        $response->assertJsonStructure(['users', 'dues']);
    }
}
````

## File: tests/Unit/ExampleTest.php
````php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
````

## File: tests/TestCase.php
````php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //
}
````

## File: .editorconfig
````
root = true

[*]
charset = utf-8
end_of_line = lf
indent_size = 4
indent_style = space
insert_final_newline = true
trim_trailing_whitespace = true

[*.md]
trim_trailing_whitespace = false

[*.{yml,yaml}]
indent_size = 2

[{compose,docker-compose}.{yml,yaml}]
indent_size = 4
````

## File: .gitattributes
````
* text=auto eol=lf

*.blade.php diff=html
*.css diff=css
*.html diff=html
*.md diff=markdown
*.php diff=php

/.github export-ignore
CHANGELOG.md export-ignore
.styleci.yml export-ignore
````

## File: .gitignore
````
*.log
.DS_Store
.env
.env.backup
.env.production
.phpactor.json
.phpunit.result.cache
/.codex
/.cursor/
/.idea
/.nova
/.phpunit.cache
/.vscode
/.zed
/auth.json
/node_modules
/public/build
/public/fonts-manifest.dev.json
/public/hot
/public/storage
/storage/*.key
/storage/pail
/vendor
_ide_helper.php
Homestead.json
Homestead.yaml
Thumbs.db
````

## File: .npmrc
````
ignore-scripts=true
audit=true
````

## File: artisan
````
#!/usr/bin/env php
<?php

use Illuminate\Foundation\Application;
use Symfony\Component\Console\Input\ArgvInput;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the command...
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$status = $app->handleCommand(new ArgvInput);

exit($status);
````

## File: deploy.sh
````bash
#!/usr/bin/env bash
set -e

composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
````

## File: package.json
````json
{
    "$schema": "https://www.schemastore.org/package.json",
    "private": true,
    "type": "module",
    "scripts": {
        "build": "vite build",
        "dev": "vite"
    },
    "devDependencies": {
        "@tailwindcss/vite": "^4.0.0",
        "concurrently": "^10.0.3",
        "laravel-vite-plugin": "^3.1",
        "tailwindcss": "^4.0.0",
        "vite": "^8.0.0"
    },
    "optionalDependencies": {
        "@laravel/multiplex": "^0.4.1"
    }
}
````

## File: phpunit.xml
````xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="APP_MAINTENANCE_DRIVER" value="file"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="BROADCAST_CONNECTION" value="null"/>
        <env name="CACHE_STORE" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="DB_URL" value=""/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="PULSE_ENABLED" value="false"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
        <env name="NIGHTWATCH_ENABLED" value="false"/>
    </php>
</phpunit>
````

## File: README.md
````markdown
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
````

## File: vite.config.js
````javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
````

## File: app/Http/Controllers/AgendaController.php
````php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\AgendaResource;
use App\Models\Agenda;
use App\Models\AgendaTarget;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $eventId = $request->input('event_id');
        $search  = $request->input('search');

        $agendas = Agenda::with(['attendances', 'targets'])
            ->when($eventId, fn($q) => $q->where('event_id', $eventId), fn($q) => $q->whereNull('event_id'))
            ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderBy('start_date', 'asc')
            ->paginate(15);

        return AgendaResource::collection($agendas);
    }

    public function sync(Request $request): JsonResponse
    {
        $eventId = $request->input('event_id');

        // Untuk Agenda, asumsikan URL berada di .env (BPH) atau event_sync_url (opsional ke depan). 
        // Sementara kita pakai TRACKING_AGENDA_URL dari env
        $url = env('TRACKING_AGENDA_URL');
        if (!$url) return response()->json(['message' => 'URL Sinkronisasi Agenda belum dikonfigurasi di .env'], 500);

        $separator = str_contains($url, '?') ? '&' : '?';
        $freshUrl = $url . $separator . 'cb=' . time();

        try {
            $context = stream_context_create(['http' => ['header' => "Cache-Control: no-cache\r\n"]]);
            $csvData = file_get_contents($freshUrl, false, $context);
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            
            $header = [];
            $dataStartIndex = 0;
            
            // Pencarian Header Agnostik
            foreach ($rows as $index => $row) {
                $cleanRow = array_map('trim', $row);
                $rowString = strtolower(implode(' | ', $cleanRow));
                
                if (str_contains($rowString, 'nama agenda') && str_contains($rowString, 'tanggal mulai')) {
                    $header = $cleanRow;
                    $dataStartIndex = $index + 1;
                    break;
                }
            }

            if (empty($header)) return response()->json(['message' => 'Format Header (Nama Agenda, Tanggal Mulai, dll) tidak ditemukan.'], 400);

            $idx = [
                'nama'      => array_search('Nama Agenda', $header),
                'start'     => array_search('Tanggal Mulai', $header),
                'end'       => array_search('Tanggal Selesai', $header),
                'tempat'    => array_search('Tempat', $header),
                'pj'        => array_search('PJ/Divisi', $header),
                'status'    => array_search('Status', $header),
                'notulensi' => array_search('Link Notulensi', $header),
            ];

            $parseDate = function ($dateStr) {
                if (empty($dateStr) || strtolower(trim($dateStr)) === 'nat' || strtolower(trim($dateStr)) === 'nan') return null;
                try { 
                    // FIX UTAMA: Ubah garis miring (/) menjadi strip (-) agar dikenali sebagai format DD-MM-YYYY oleh PHP/Carbon
                    $cleanDate = str_replace('/', '-', trim($dateStr));
                    return Carbon::parse($cleanDate)->format('Y-m-d H:i:s'); 
                } catch (\Exception $e) { 
                    return null; 
                }
            };
            
            $parseUrl = function ($urlStr) { return filter_var(trim($urlStr), FILTER_VALIDATE_URL) ? trim($urlStr) : null; };
            $val = function($row, $index) { if ($index === false || !isset($row[$index])) return null; $v = trim($row[$index]); return (strtolower($v) === 'nan' || $v === '') ? null : $v; };

            $successCount = 0;

            DB::transaction(function () use ($rows, $dataStartIndex, $idx, $parseDate, $parseUrl, $val, $eventId, &$successCount) {
                // Wipe Data (Scope Event/Global)
                if ($eventId) {
                    Agenda::where('event_id', $eventId)->delete();
                } else {
                    Agenda::whereNull('event_id')->delete();
                }

                for ($i = $dataStartIndex; $i < count($rows); $i++) {
                    $row = array_map('trim', $rows[$i]);
                    if (empty($row) || count($row) < 3) continue;

                    $nama  = $val($row, $idx['nama']);
                    $start = $parseDate($val($row, $idx['start']));
                    
                    if (empty($nama) || !$start) continue;

                    Agenda::create([
                        'event_id'    => $eventId ? (int)$eventId : null,
                        'title'       => $nama,
                        'start_date'  => $start,
                        'end_date'    => $parseDate($val($row, $idx['end'])),
                        'location'    => $val($row, $idx['tempat']),
                        'pic'         => $val($row, $idx['pj']),
                        'status'      => $val($row, $idx['status']),
                        'minutes_url' => $parseUrl($val($row, $idx['notulensi'])),
                    ]);

                    $successCount++;
                }
            });

            $target = $eventId ? "Kepanitiaan" : "BPH Pusat";
            return response()->json(['message' => "Sinkronisasi selesai. Berhasil menyinkronkan $successCount agenda $target."]);

        } catch (\Exception $e) { return response()->json(['message' => 'Gagal menyinkronisasi data agenda.', 'error' => $e->getMessage()], 500); }
    }

    public function setTargets(Request $request, $id): JsonResponse
    {
        $agenda = Agenda::findOrFail($id);
        
        $validated = $request->validate([
            'targets'          => 'required|array',
            'targets.*.type'   => 'required|in:all,bph,coordinator,division,position,user',
            'targets.*.value'  => 'nullable|string',
        ]);

        DB::transaction(function () use ($agenda, $validated) {
            // Hapus target lama
            $agenda->targets()->delete();
            
            // Masukkan target baru
            foreach ($validated['targets'] as $t) {
                AgendaTarget::create([
                    'agenda_id'    => $agenda->id,
                    'target_type'  => $t['type'],
                    'target_value' => $t['value'],
                ]);
            }
        });

        return response()->json(['message' => 'Target peserta agenda berhasil diperbarui.', 'data' => $agenda->targets]);
    }
}
````

## File: app/Http/Controllers/Controller.php
````php
<?php

namespace App\Http\Controllers;

use App\Models\EventCommittee;

abstract class Controller
{
    protected function authorizeEventAccess($user, $eventId, array $allowedPositions): void
    {
        if ($user->hasRole('admin')) return;
        if (!$eventId) abort(403, 'Anda tidak memiliki hak akses untuk Kas/Dokumen Umum.');
        
        $isCommittee = EventCommittee::where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->whereIn('position', $allowedPositions)
            ->exists();
            
        if (!$isCommittee) abort(403, 'Anda tidak memiliki hak akses untuk Event ini.');
    }
}
````

## File: app/Http/Controllers/DashboardController.php
````php
<?php
namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    public function statistics(): JsonResponse
    {
        return response()->json([
            'message' => 'Success',
            'data'    => $this->dashboardService->getStatistics(),
        ]);
    }

    public function upcomingAgenda(): JsonResponse
    {
        return response()->json([
            'message' => 'Success',
            'data'    => $this->dashboardService->getUpcomingAgenda(),
        ]);
    }
}
````

## File: app/Http/Controllers/DivisionController.php
````php
<?php
namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Division::latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:divisions,name|max:255',
        ]);
        $division = Division::create($validated);
        return response()->json(['message' => 'Divisi berhasil ditambahkan', 'data' => $division], 201);
    }

    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:divisions,name,' . $division->id . '|max:255',
        ]);
        $division->update($validated);
        return response()->json(['message' => 'Divisi berhasil diperbarui', 'data' => $division]);
    }

    public function destroy(Division $division)
    {
        $division->delete();
        return response()->json(['message' => 'Divisi berhasil dihapus']);
    }
}
````

## File: app/Http/Controllers/ProfileController.php
````php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nim'      => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'phone'    => ['nullable', 'string', 'max:50'],
            'prodi'    => ['nullable', 'string', 'max:100'],
            'angkatan' => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data'    => $user->fresh()->load(['roles', 'division']),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Kata sandi berhasil diperbarui.']);
    }
}
````

## File: app/Http/Controllers/UserController.php
````php
<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['roles', 'division'])
            ->when($request->search, fn ($q, $search) => 
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(15);
            
        return response()->json($users);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'division_id'    => 'nullable|exists:divisions,id',
            'status'         => 'required|in:active,suspended',
            'role'           => 'required|in:admin,member,advisor',
            'is_coordinator' => 'required|boolean',
        ]);

        $user->update([
            'division_id'    => $validated['division_id'],
            'status'         => $validated['status'],
            'is_coordinator' => $validated['is_coordinator'],
        ]);

        $user->syncRoles([$validated['role']]);

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui',
            'data'    => $user->load(['roles', 'division']),
        ]);
    }
}
````

## File: app/Http/Resources/DocumentResource.php
````php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'created_by'    => $this->created_by,
            'event_id'      => $this->event_id,
            'letter_number' => $this->letter_number,
            'title'         => $this->title,
            'letter_link'   => $this->letter_link,
            'scan_link'     => $this->scan_link,
            'activity_date' => $this->activity_date,
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
            'creator'       => $this->whenLoaded('creator'),
            'event'         => $this->whenLoaded('event'),
        ];
    }
}
````

## File: app/Http/Resources/MeetingResource.php
````php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'event_id'    => $this->event_id,
            'title'       => $this->title,
            'date'        => $this->date?->format('Y-m-d H:i:s'),
            'minutes_url' => $this->minutes_url,
            'created_at'  => $this->created_at?->toISOString(),
            'updated_at'  => $this->updated_at?->toISOString(),
            'attendances' => $this->whenLoaded('attendances'),
            'event'       => $this->whenLoaded('event'),
        ];
    }
}
````

## File: app/Models/Division.php
````php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['name'];
}
````

## File: app/Models/Document.php
````php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model {
    use HasFactory;

    protected $fillable = [
        'created_by', 'event_id', 'letter_number', 'title', 'letter_link', 'scan_link', 'activity_date'
    ];

    protected $casts = [
        'created_by'    => 'integer',
        'event_id'      => 'integer',
        'activity_date' => 'date',
    ];

    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function event(): BelongsTo {
        return $this->belongsTo(Event::class);
    }
}
````

## File: app/Services/DashboardService.php
````php
<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\Document;
use App\Models\Event;
use App\Models\Finance;
use Carbon\Carbon;

class DashboardService
{
    public function getStatistics(): array
    {
        $now   = Carbon::now();
        $today = $now->toDateString();

        $totalIncome  = (float) Finance::where('type', 'income')->sum('amount');
        $totalExpense = (float) Finance::where('type', 'expense')->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        $incomeThisMonth = (float) Finance::where('type', 'income')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');

        $expenseThisMonth = (float) Finance::where('type', 'expense')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');

        $activeEventsCount = Event::where('start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->where('end_date', '>=', $today)
                      ->orWhereNull('end_date');
            })
            ->count();

        $documentsIssuedThisMonth = Document::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $agendasThisMonth = Agenda::whereMonth('start_date', $now->month)
            ->whereYear('start_date', $now->year)
            ->count();

        return [
            'financial_health' => [
                'total_balance'      => $totalBalance,
                'income_this_month'  => $incomeThisMonth,
                'expense_this_month' => $expenseThisMonth,
            ],
            'event_performance' => [
                'active_events_count' => $activeEventsCount,
            ],
            'organizational_activity' => [
                'documents_issued_this_month' => $documentsIssuedThisMonth,
                'meetings_this_month'         => $agendasThisMonth,
            ],
        ];
    }

    public function getUpcomingAgenda(): array
    {
        $today = Carbon::now()->startOfDay();

        $upcomingEvents = Event::where('start_date', '>=', $today->toDateString())
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        $upcomingAgendas = Agenda::where('start_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        return [
            'upcoming_events'   => $upcomingEvents,
            'upcoming_meetings' => $upcomingAgendas,
        ];
    }
}
````

## File: app/Services/FinanceService.php
````php
<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Finance;
use Illuminate\Validation\ValidationException;

class FinanceService
{
    public function storeFinance(array $data): Finance
    {
        $data['amount'] = ($data['qty'] ?? 1) * ($data['unit_price'] ?? 0);

        if ($data['type'] === 'expense' && !empty($data['event_id'])) {
            $event = Event::findOrFail($data['event_id']);

            $totalExistingExpense = Finance::where('event_id', $event->id)
                ->where('type', 'expense')
                ->sum('amount');

            $projectedTotal = $totalExistingExpense + $data['amount'];

            if ($projectedTotal > $event->budget_approved) {
                throw ValidationException::withMessages([
                    'amount' => 'Pengeluaran melebihi anggaran yang disetujui.',
                ]);
            }
        }

        return Finance::create($data);
    }
}
````

## File: config/cors.php
````php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
````

## File: database/factories/DocumentFactory.php
````php
<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'event_id' => fake()->optional(0.5)->randomElement(
                Event::pluck('id')->toArray() ?: [null]
            ),
            'letter_number' => strtoupper(fake()->unique()->bothify('??-###/PROTIK/####')),
            'title' => fake()->sentence(5),
            'letter_link' => fake()->url(),
            'scan_link' => fake()->url(),
            'activity_date' => fake()->date(),
        ];
    }
}
````

## File: database/factories/EventFactory.php
````php
<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'budget_approved' => fake()->randomFloat(2, 100, 50000),
            'drive_folder_url' => fake()->optional()->url(),
            'start_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => fake()->optional()->dateTimeBetween('+1 month', '+3 months'),
        ];
    }
}
````

## File: database/migrations/2026_08_19_181548_create_finances_table.php
````php
<?php
// xxxx_xx_xx_create_finances_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            // Restrict delete: User tidak bisa dihapus jika punya riwayat kas
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('restrict');
            $table->enum('type', ['income', 'expense']);
            $table->string('funding_source')->nullable();
            $table->string('title');
            $table->decimal('qty', 8, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->string('receipt_url')->nullable();
            $table->date('date');
            $table->timestamps();

            // Optimasi Indexing untuk performa pencarian agregat
            $table->index('event_id');
            $table->index('user_id');
            $table->index('type');
        });
    }

    public function down(): void {
        Schema::dropIfExists('finances');
    }
};
````

## File: routes/web.php
````php
<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
````

## File: tests/Feature/ProfileTest.php
````php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $this->user = User::factory()->create([
            'name'     => 'Original Name',
            'email'    => 'user@protik.com',
            'password' => Hash::make('password123'),
        ]);
        $this->user->assignRole('member');
    }

    public function test_user_can_update_profile(): void
    {
        $payload = [
            'name'     => 'Updated Name',
            'email'    => 'updated@protik.com',
            'nim'      => '123456789',
            'phone'    => '081234567890',
            'prodi'    => 'Teknologi Informasi',
            'angkatan' => '2024',
            'address'  => 'Jl. Pendidikan No. 1',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/user/profile', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name'     => 'Updated Name',
                'email'    => 'updated@protik.com',
                'nim'      => '123456789',
                'phone'    => '081234567890',
                'prodi'    => 'Teknologi Informasi',
                'angkatan' => '2024',
                'address'  => 'Jl. Pendidikan No. 1',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'id'       => $this->user->id,
            'name'     => 'Updated Name',
            'email'    => 'updated@protik.com',
            'nim'      => '123456789',
            'phone'    => '081234567890',
            'prodi'    => 'Teknologi Informasi',
            'angkatan' => '2024',
            'address'  => 'Jl. Pendidikan No. 1',
        ]);
    }

    public function test_user_can_update_password(): void
    {
        $payload = [
            'current_password'      => 'password123',
            'password'              => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/user/password', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Kata sandi berhasil diperbarui.',
        ]);

        $this->assertTrue(Hash::check('newsecret123', $this->user->fresh()->password));
    }
}
````

## File: tests/Feature/SecurityTest.php
````php
<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Warning;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'advisor', 'guard_name' => 'web']);
    }

    public function test_member_is_forbidden_from_creating_divisions(): void
    {
        // Arrange
        $member = User::factory()->create();
        $member->assignRole('member');

        // Act
        $response = $this->actingAs($member, 'sanctum')
            ->postJson('/api/divisions', [
                'name'        => 'Divisi Ilegal',
                'description' => 'Divisi Test',
            ]);

        // Assert
        $response->assertStatus(403);
        $this->assertDatabaseMissing('divisions', ['name' => 'Divisi Ilegal']);
    }

    public function test_admin_can_create_divisions(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $payload = [
            'name'        => 'Divisi Humas',
            'description' => 'Divisi Hubungan Masyarakat',
        ];

        // Act
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/divisions', $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('divisions', ['name' => 'Divisi Humas']);
    }

    public function test_member_can_only_see_own_warnings(): void
    {
        // Arrange
        $member = User::factory()->create();
        $member->assignRole('member');
        $otherUser = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Warning milik member
        Warning::factory()->count(2)->create([
            'user_id'  => $member->id,
            'admin_id' => $admin->id,
        ]);

        // Warning milik orang lain
        Warning::factory()->count(3)->create([
            'user_id'  => $otherUser->id,
            'admin_id' => $admin->id,
        ]);

        // Act
        $response = $this->actingAs($member, 'sanctum')
            ->getJson('/api/warnings');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');

        $returnedUserIds = collect($response->json('data'))->pluck('user_id')->unique()->values();
        $this->assertEquals([$member->id], $returnedUserIds->toArray());
    }
}
````

## File: .env.example
````
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

# PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=localhost

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173,localhost:3000,127.0.0.1:3000
FRONTEND_URL=http://localhost:5173
````

## File: app/Http/Controllers/EventController.php
````php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $events = Event::with('committees.user')
            ->when($request->search, fn ($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => 'Success',
            'data'    => $events,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'budget_approved'   => ['required', 'numeric', 'min:0'],
            'drive_folder_url'  => ['nullable', 'string', 'max:255'],
            'document_sync_url' => ['nullable', 'string', 'max:255'],
            'finance_sync_url'  => ['nullable', 'string', 'max:255'],
            'start_date'        => ['required', 'date'],
            'end_date'          => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $event = Event::create($validated);

        return response()->json([
            'message' => 'Success',
            'data'    => $event->load('committees.user'),
        ], 201);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'budget_approved'   => ['required', 'numeric', 'min:0'],
            'drive_folder_url'  => ['nullable', 'string', 'max:255'],
            'document_sync_url' => ['nullable', 'string', 'max:255'],
            'finance_sync_url'  => ['nullable', 'string', 'max:255'],
            'start_date'        => ['required', 'date'],
            'end_date'          => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Success',
            'data'    => $event->load('committees.user'),
        ]);
    }

    public function destroy(Event $event): JsonResponse
    {
        $event->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }
}
````

## File: app/Http/Resources/FinanceResource.php
````php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'event_id'       => $this->event_id,
            'type'           => $this->type,
            'category'       => $this->category,
            'funding_source' => $this->funding_source,
            'title'          => $this->title ?? $this->description,
            'description'    => $this->description ?? $this->title,
            'qty'            => (float) $this->qty,
            'unit'           => $this->unit,
            'unit_price'     => (float) $this->unit_price,
            'amount'         => (float) $this->amount,
            'pic'            => $this->pic,
            'payment_method' => $this->payment_method,
            'notes'          => $this->notes,
            'receipt_url'    => $this->receipt_url,
            'date'           => $this->date?->format('Y-m-d'),
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
            'user'           => $this->whenLoaded('user'),
            'event'          => $this->whenLoaded('event'),
        ];
    }
}
````

## File: app/Models/Event.php
````php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model {
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'budget_approved', 
        'drive_folder_url', 'start_date', 'end_date',
        'document_sync_url', 'finance_sync_url',
    ];

    // Konversi presisi data otomatis
    protected $casts = [
        'budget_approved' => 'decimal:2',
        'start_date'      => 'date',
        'end_date'        => 'date',
    ];

    public function finances(): HasMany {
        return $this->hasMany(Finance::class);
    }

    public function committees(): HasMany {
        return $this->hasMany(EventCommittee::class);
    }
}
````

## File: app/Models/Finance.php
````php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finance extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 'event_id', 'type', 'category', 'funding_source', 
        'title', 'description', 'qty', 'unit', 'unit_price', 'amount', 
        'pic', 'payment_method', 'notes', 'receipt_url', 'date'
    ];

    protected $casts = [
        'qty'        => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount'     => 'decimal:2',
        'date'       => 'date',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo {
        return $this->belongsTo(Event::class);
    }
}
````

## File: app/Providers/AppServiceProvider.php
````php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Finance::observe(\App\Observers\AuditObserver::class);
        \App\Models\Document::observe(\App\Observers\AuditObserver::class);
        \App\Models\Agenda::observe(\App\Observers\AuditObserver::class);
        \App\Models\Warning::observe(\App\Observers\AuditObserver::class);
        \App\Models\Event::observe(\App\Observers\AuditObserver::class);
        \App\Models\User::observe(\App\Observers\AuditObserver::class);
    }
}
````

## File: database/seeders/DatabaseSeeder.php
````php
<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Document;
use App\Models\Event;
use App\Models\Finance;
use App\Models\Meeting;
use App\Models\MeetingAttendance;
use App\Models\User;
use App\Models\Warning;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $this->call(RolePermissionSeeder::class);

        // 2. Divisi
        $divisions = collect(['BPH', 'Pendidikan', 'Ristek', 'Humas', 'Ekraf'])
            ->map(fn (string $name) => Division::create(['name' => $name]));

        $divisionBph    = $divisions->firstWhere('name', 'BPH');
        $divisionRistek = $divisions->firstWhere('name', 'Ristek');

        // 3. User Statis
        $admin = User::factory()->create([
            'name'        => 'Admin Sofyan',
            'email'       => 'admin@protik.com',
            'division_id' => $divisionBph->id,
        ]);
        $admin->assignRole('admin');

        $member = User::factory()->create([
            'name'        => 'Member User',
            'email'       => 'member@protik.com',
            'division_id' => $divisionRistek->id,
        ]);
        $member->assignRole('member');

        $advisor = User::factory()->create([
            'name'        => 'Advisor User',
            'email'       => 'advisor@protik.com',
            'division_id' => null,
        ]);
        $advisor->assignRole('advisor');

        // 4. 20 User Acak (member)
        $randomMembers = User::factory()->count(20)->create([
            'division_id' => fn () => $divisions->random()->id,
        ]);
        $randomMembers->each(fn (User $u) => $u->assignRole('member'));

        // Kumpulkan semua member untuk attendance
        $allMembers = collect([$member])->merge($randomMembers);

        // 5. 5 Event
        $events = Event::factory()->count(5)->create();

        // 6. Finance per Event (1 income besar, 1 expense kecil)
        $fundingSources = ['IOM', 'DIPA', 'KAS', 'SPONSOR'];

        $events->each(function (Event $event) use ($admin, $fundingSources) {
            $incomeAmount = fake()->randomFloat(2, 5_000_000, 50_000_000);
            Finance::create([
                'user_id'        => $admin->id,
                'event_id'       => $event->id,
                'type'           => 'income',
                'funding_source' => fake()->randomElement($fundingSources),
                'title'          => "Dana masuk untuk {$event->name}",
                'qty'            => 1,
                'unit'           => 'Ls',
                'unit_price'     => $incomeAmount,
                'amount'         => $incomeAmount,
                'notes'          => 'Penerimaan dana kas',
                'date'           => $event->start_date?->format('Y-m-d') ?? now()->toDateString(),
            ]);

            $expenseAmount = fake()->randomFloat(2, 100_000, 2_000_000);
            Finance::create([
                'user_id'        => $admin->id,
                'event_id'       => $event->id,
                'type'           => 'expense',
                'funding_source' => null,
                'title'          => "Pengeluaran operasional {$event->name}",
                'qty'            => 1,
                'unit'           => 'Ls',
                'unit_price'     => $expenseAmount,
                'amount'         => $expenseAmount,
                'notes'          => 'Pengeluaran operasional kegiatan',
                'date'           => $event->start_date?->copy()->addDays(1)?->format('Y-m-d') ?? now()->toDateString(),
            ]);
        });

        // 7. 10 Meeting
        $meetings = Meeting::factory()->count(10)->create();

        // 8. Attendance setiap Meeting untuk semua member
        $statuses = ['present', 'permit', 'sick', 'absent'];

        $meetings->each(function (Meeting $meeting) use ($allMembers, $statuses) {
            $allMembers->each(function (User $member) use ($meeting, $statuses) {
                MeetingAttendance::create([
                    'meeting_id' => $meeting->id,
                    'user_id'    => $member->id,
                    'status'     => fake()->randomElement($statuses),
                    'proof_url'  => fake()->optional(0.3)->url(),
                ]);
            });
        });

        // 9. 15 Document
        Document::factory()->count(15)->create([
            'created_by' => $admin->id,
            'event_id'   => fn () => $events->random()->id,
        ]);

        // 10. 3 Warning
        $warnedUsers = $allMembers->random(3);
        $warnedUsers->each(function (User $user) use ($admin) {
            Warning::create([
                'user_id'  => $user->id,
                'admin_id' => $admin->id,
                'reason'   => fake()->randomElement([
                    'Tidak hadir 3 kali berturut-turut tanpa keterangan.',
                    'Melanggar tata tertib organisasi.',
                    'Tidak menyelesaikan tugas yang diberikan.',
                    'Terlambat mengumpulkan laporan kegiatan.',
                ]),
                'date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            ]);
        });
    }
}
````

## File: tests/Feature/DashboardTest.php
````php
<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Event;
use App\Models\Finance;
use App\Models\Agenda;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $this->user = User::factory()->create();
        $this->user->assignRole('member');
    }

    public function test_statistics_calculation_is_accurate(): void
    {
        // Arrange
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // 1. Finance - Bulan Ini
        Finance::create([
            'user_id'    => $this->user->id,
            'type'       => 'income',
            'title'      => 'Income Bulan Ini',
            'qty'        => 1,
            'unit_price' => 1000.00,
            'amount'     => 1000.00,
            'date'       => $now->toDateString(),
        ]);
        Finance::create([
            'user_id'    => $this->user->id,
            'type'       => 'expense',
            'title'      => 'Expense Bulan Ini',
            'qty'        => 1,
            'unit_price' => 300.00,
            'amount'     => 300.00,
            'date'       => $now->toDateString(),
        ]);

        // Finance - Bulan Lalu
        Finance::create([
            'user_id'    => $this->user->id,
            'type'       => 'income',
            'title'      => 'Income Bulan Lalu',
            'qty'        => 1,
            'unit_price' => 500.00,
            'amount'     => 500.00,
            'date'       => $lastMonth->toDateString(),
        ]);
        Finance::create([
            'user_id'    => $this->user->id,
            'type'       => 'expense',
            'title'      => 'Expense Bulan Lalu',
            'qty'        => 1,
            'unit_price' => 200.00,
            'amount'     => 200.00,
            'date'       => $lastMonth->toDateString(),
        ]);

        // 2. Events - Aktif & Tidak Aktif
        Event::factory()->create([
            'start_date' => $now->copy()->subDays(2)->toDateString(),
            'end_date'   => $now->copy()->addDays(3)->toDateString(),
        ]);
        Event::factory()->create([
            'start_date' => $now->copy()->subDays(1)->toDateString(),
            'end_date'   => null,
        ]);
        Event::factory()->create([
            'start_date' => $now->copy()->subMonth()->toDateString(),
            'end_date'   => $now->copy()->subMonth()->addDays(5)->toDateString(),
        ]);
        Event::factory()->create([
            'start_date' => $now->copy()->addMonth()->toDateString(),
            'end_date'   => $now->copy()->addMonth()->addDays(5)->toDateString(),
        ]);

        // 3. Documents - Bulan Ini vs Bulan Lalu
        Document::factory()->create([
            'created_by' => $this->user->id,
            'created_at' => $now,
        ]);
        Document::factory()->create([
            'created_by' => $this->user->id,
            'created_at' => $lastMonth,
        ]);

        // 4. Agendas - Bulan Ini vs Bulan Lalu
        Agenda::create([
            'title'      => 'Agenda Bulan Ini',
            'start_date' => $now->toDateTimeString(),
        ]);
        Agenda::create([
            'title'      => 'Agenda Bulan Lalu',
            'start_date' => $lastMonth->toDateTimeString(),
        ]);

        // Act
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/statistics');

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Success',
            'data' => [
                'financial_health' => [
                    'total_balance'      => 1000.00,
                    'income_this_month'  => 1000.00,
                    'expense_this_month' => 300.00,
                ],
                'event_performance' => [
                    'active_events_count' => 2,
                ],
                'organizational_activity' => [
                    'documents_issued_this_month' => 1,
                    'meetings_this_month'         => 1,
                ],
            ],
        ]);
    }

    public function test_upcoming_agenda_only_shows_future_dates(): void
    {
        // Arrange
        $now = Carbon::now();

        // Past Events
        Event::factory()->create([
            'name'       => 'Past Event 1',
            'start_date' => $now->copy()->subDays(10)->toDateString(),
        ]);

        // Future Events (7 events to test limit 5 and sorting)
        for ($i = 1; $i <= 7; $i++) {
            Event::factory()->create([
                'name'       => "Future Event $i",
                'start_date' => $now->copy()->addDays($i)->toDateString(),
            ]);
        }

        // Past Agendas
        Agenda::create([
            'title'      => 'Past Agenda 1',
            'start_date' => $now->copy()->subDays(5)->toDateTimeString(),
        ]);

        // Future Agendas (7 agendas to test limit 5 and sorting)
        for ($i = 1; $i <= 7; $i++) {
            Agenda::create([
                'title'      => "Future Agenda $i",
                'start_date' => $now->copy()->addDays($i)->toDateTimeString(),
            ]);
        }

        // Act
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/upcoming-agenda');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.upcoming_events');
        $response->assertJsonCount(5, 'data.upcoming_meetings');

        $events = $response->json('data.upcoming_events');
        $this->assertEquals('Future Event 1', $events[0]['name']);
        $this->assertEquals('Future Event 5', $events[4]['name']);

        $meetings = $response->json('data.upcoming_meetings');
        $this->assertEquals('Future Agenda 1', $meetings[0]['title']);
        $this->assertEquals('Future Agenda 5', $meetings[4]['title']);
    }
}
````

## File: composer.json
````json
{
    "$schema": "https://getcomposer.org/schema.json",
    "name": "laravel/laravel",
    "type": "project",
    "description": "The skeleton application for the Laravel framework.",
    "keywords": ["laravel", "framework"],
    "license": "MIT",
    "require": {
        "php": "^8.3",
        "laravel/framework": "^13.17",
        "laravel/sanctum": "^4.0",
        "laravel/tinker": "^3.0",
        "spatie/laravel-permission": "^8.3"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel/pail": "^1.2.5",
        "laravel/pao": "^1.0.6",
        "laravel/pint": "^1.27",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.6",
        "phpunit/phpunit": "^12.5.12"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "setup": [
            "composer install",
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\"",
            "@php artisan key:generate",
            "@php artisan migrate --force",
            "npm install --ignore-scripts",
            "npm run build"
        ],
        "dev": [
            "Composer\\Config::disableProcessTimeout",
            "@php artisan dev"
        ],
        "test": [
            "@php artisan config:clear --ansi @no_additional_args",
            "@php artisan test"
        ],
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi",
            "@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\"",
            "@php artisan migrate --graceful --ansi"
        ],
        "pre-package-uninstall": [
            "Illuminate\\Foundation\\ComposerScripts::prePackageUninstall"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
````

## File: tests/Feature/WarningTest.php
````php
<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Warning;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WarningTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_can_list_warnings(): void
    {
        // Arrange
        Warning::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/warnings');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [['id', 'user_id', 'admin_id', 'reason', 'date']],
            'meta' => ['current_page', 'per_page', 'total'],
        ]);
    }

    public function test_can_store_valid_warning(): void
    {
        // Arrange
        $user = User::factory()->create();

        $payload = [
            'user_id'  => $user->id,
            'admin_id' => $this->admin->id,
            'reason'   => 'Tidak hadir 3 kali berturut-turut tanpa keterangan.',
            'date'     => '2026-08-20',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/warnings', $payload);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Success');
        $this->assertDatabaseHas('warnings', [
            'user_id'  => $user->id,
            'admin_id' => $this->admin->id,
            'reason'   => 'Tidak hadir 3 kali berturut-turut tanpa keterangan.',
        ]);
    }
}
````

## File: app/Models/User.php
````php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasFactory, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'status', 'division_id',
        'is_coordinator', 'nim', 'phone', 'prodi', 'angkatan', 'address'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password'       => 'hashed',
        'is_coordinator' => 'boolean',
    ];

    public function division(): BelongsTo {
        return $this->belongsTo(Division::class);
    }

    public function finances(): HasMany {
        return $this->hasMany(Finance::class);
    }
}
````

## File: bootstrap/app.php
````php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Paksa render JSON untuk rute API, login, dan logout
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->is('login') || $request->is('logout') || $request->expectsJson()
        );

        // Tangkap CSRF Mismatch (419)
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => 'CSRF token mismatch. Sesi telah kedaluwarsa, silakan muat ulang halaman.'
            ], 419);
        });

        // Tangkap Spatie Unauthorized (403)
        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk tindakan ini.'
            ], 403);
        });
    })->create();
````

## File: tests/Feature/DocumentTest.php
````php
<?php
namespace Tests\Feature;

use App\Models\Document;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_can_list_documents(): void
    {
        // Arrange
        $user = User::factory()->create();
        Document::factory()->count(3)->create(['created_by' => $user->id]);

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/documents');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [['id', 'created_by', 'letter_number', 'title', 'letter_link', 'scan_link', 'activity_date']],
            'meta' => ['current_page', 'per_page', 'total'],
        ]);
    }

    public function test_can_store_valid_document(): void
    {
        // Arrange
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $payload = [
            'created_by'    => $user->id,
            'event_id'      => $event->id,
            'letter_number' => 'SK-001/PROTIK/2026',
            'title'         => 'Surat Keputusan Panitia',
            'letter_link'   => 'https://drive.google.com/sk-panitia',
            'scan_link'     => 'https://drive.google.com/sk-panitia-scan',
            'activity_date' => '2026-08-25',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/documents', $payload);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Success');
        $this->assertDatabaseHas('documents', [
            'letter_number' => 'SK-001/PROTIK/2026',
            'created_by'    => $user->id,
        ]);
    }
}
````

## File: app/Http/Controllers/WarningController.php
````php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\WarningResource;
use App\Models\Warning;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarningController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $warnings = Warning::with(['user', 'admin'])
            // INJEKSI LOGIKA PENCARIAN DI SINI
            ->when($request->search, fn ($q, $search) =>
                $q->where(fn ($query) =>
                    $query->where('reason', 'like', "%{$search}%")
                          ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                )
            )
            ->when($request->user()->hasRole('member'), fn ($q) =>
                $q->where('user_id', $request->user()->id)
            )
            ->latest('date')
            ->paginate(15);

        return WarningResource::collection($warnings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id'  => ['required', 'exists:users,id'],
            'admin_id' => ['required', 'exists:users,id'],
            'reason'   => ['required', 'string'],
            'date'     => ['required', 'date'],
        ]);

        $warning = Warning::create($validated);

        return response()->json(['message' => 'Success', 'data' => new WarningResource($warning)], 201);
    }

    public function update(Request $request, Warning $warning): JsonResponse
    {
        $validated = $request->validate([
            'user_id'  => ['required', 'exists:users,id'],
            'admin_id' => ['required', 'exists:users,id'],
            'reason'   => ['required', 'string'],
            'date'     => ['required', 'date'],
        ]);

        $warning->update($validated);

        return response()->json([
            'message' => 'Success',
            'data'    => new WarningResource($warning->load(['user', 'admin'])),
        ]);
    }

    public function destroy(Warning $warning): JsonResponse
    {
        $warning->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }
}
````

## File: tests/Feature/FinanceTest.php
````php
<?php
namespace Tests\Feature;

use App\Models\Event;
use App\Models\Finance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_can_list_and_filter_finances(): void
    {
        // Arrange
        Finance::create([
            'user_id'    => $this->admin->id,
            'event_id'   => null,
            'type'       => 'income',
            'title'      => 'Sponsorship Tech Conference',
            'qty'        => 1,
            'unit'       => 'Paket',
            'unit_price' => 500.00,
            'amount'     => 500.00,
            'notes'      => 'Sponsorship utama',
            'date'       => '2026-08-15',
        ]);

        Finance::create([
            'user_id'    => $this->admin->id,
            'event_id'   => null,
            'type'       => 'expense',
            'title'      => 'Beli ATK',
            'qty'        => 2,
            'unit'       => 'Rim',
            'unit_price' => 50.00,
            'amount'     => 100.00,
            'notes'      => 'Kertas HVS A4',
            'date'       => '2026-08-18',
        ]);

        // Act & Assert: List all
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/finances');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [['id', 'user_id', 'event_id', 'type', 'title', 'qty', 'unit', 'unit_price', 'amount', 'notes', 'date']],
            'meta' => ['current_page', 'per_page', 'total'],
        ]);

        // Act & Assert: Filter search
        $searchResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/finances?search=Tech');
        $searchResponse->assertStatus(200);
        $searchResponse->assertJsonCount(1, 'data');
        $searchResponse->assertJsonFragment(['title' => 'Sponsorship Tech Conference']);
    }

    public function test_admin_can_record_income_without_budget_limit(): void
    {
        // Arrange
        $event = Event::factory()->create(['budget_approved' => 1000.00]);

        $payload = [
            'user_id'     => $this->admin->id,
            'event_id'    => $event->id,
            'type'        => 'income',
            'title'       => 'Dana sponsor masuk',
            'qty'         => 1,
            'unit'        => 'Ls',
            'unit_price'  => 999999.99,
            'notes'       => 'Dana masuk sponsorship',
            'date'        => '2026-08-20',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/finances', $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('finances', [
            'user_id'  => $this->admin->id,
            'event_id' => $event->id,
            'type'     => 'income',
            'title'    => 'Dana sponsor masuk',
            'amount'   => 999999.99,
        ]);
    }

    public function test_expense_is_rejected_when_exceeding_event_budget(): void
    {
        // Arrange
        $event = Event::factory()->create(['budget_approved' => 500.00]);

        Finance::create([
            'user_id'    => $this->admin->id,
            'event_id'   => $event->id,
            'type'       => 'expense',
            'title'      => 'Sewa tempat',
            'qty'        => 1,
            'unit'       => 'Hari',
            'unit_price' => 400.00,
            'amount'     => 400.00,
            'date'       => '2026-08-19',
        ]);

        $payload = [
            'user_id'    => $this->admin->id,
            'event_id'   => $event->id,
            'type'       => 'expense',
            'title'      => 'Konsumsi rapat',
            'qty'        => 20,
            'unit'       => 'Kotak',
            'unit_price' => 10.00,
            'date'       => '2026-08-20',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/finances', $payload);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('amount');
        $this->assertDatabaseMissing('finances', [
            'title' => 'Konsumsi rapat',
        ]);
    }

    public function test_expense_within_budget_is_accepted(): void
    {
        // Arrange
        $event = Event::factory()->create(['budget_approved' => 1000.00]);

        Finance::create([
            'user_id'    => $this->admin->id,
            'event_id'   => $event->id,
            'type'       => 'expense',
            'title'      => 'Sewa tempat',
            'qty'        => 1,
            'unit'       => 'Hari',
            'unit_price' => 400.00,
            'amount'     => 400.00,
            'date'       => '2026-08-19',
        ]);

        $payload = [
            'user_id'    => $this->admin->id,
            'event_id'   => $event->id,
            'type'       => 'expense',
            'title'      => 'Dekorasi',
            'qty'        => 3,
            'unit'       => 'Paket',
            'unit_price' => 200.00,
            'date'       => '2026-08-20',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/finances', $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('finances', [
            'title'  => 'Dekorasi',
            'amount' => 600.00,
        ]);
    }
}
````

## File: app/Http/Controllers/DocumentController.php
````php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $documents = Document::with(['creator', 'event'])
            ->when($request->search, fn ($q, $search) =>
                $q->where(fn ($q) =>
                    $q->where('letter_number', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%")
                )
            )
            ->where('event_id', $request->input('event_id'))
            ->latest()
            ->paginate(15);

        return DocumentResource::collection($documents);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $request->event_id, ['Ketua', 'Sekretaris']);

        $validated = $request->validate([
            'created_by'    => ['required', 'exists:users,id'],
            'event_id'      => ['nullable', 'exists:events,id'],
            'letter_number' => ['required', 'string', 'max:255', 'unique:documents,letter_number'],
            'title'         => ['required', 'string', 'max:255'],
            'letter_link'   => ['nullable', 'string', 'max:255'],
            'scan_link'     => ['nullable', 'string', 'max:255'],
            'activity_date' => ['nullable', 'date'],
        ]);

        $document = Document::create($validated);

        return response()->json(['message' => 'Success', 'data' => new DocumentResource($document)], 201);
    }

    public function update(Request $request, Document $document): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $document->event_id, ['Ketua', 'Sekretaris']);

        $validated = $request->validate([
            'event_id'      => ['nullable', 'exists:events,id'],
            'letter_number' => ['required', 'string', 'max:255', 'unique:documents,letter_number,' . $document->id],
            'title'         => ['required', 'string', 'max:255'],
            'letter_link'   => ['nullable', 'string', 'max:255'],
            'scan_link'     => ['nullable', 'string', 'max:255'],
            'activity_date' => ['nullable', 'date'],
        ]);

        $document->update($validated);

        return response()->json([
            'message' => 'Success',
            'data'    => new DocumentResource($document->load(['creator', 'event'])),
        ]);
    }

    public function destroy(Request $request, Document $document): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $document->event_id, ['Ketua', 'Sekretaris']);

        $document->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $eventId = $request->input('event_id');

        if ($eventId) {
            $event = Event::find($eventId);
            if (!$event || empty($event->document_sync_url)) {
                return response()->json(['message' => 'URL Sinkronisasi Dokumen untuk Event ini belum diatur.'], 400);
            }
            $url = $event->document_sync_url;
        } else {
            $url = env('TRACKING_PERSURATAN_URL');
            if (!$url) return response()->json(['message' => 'URL Sinkronisasi Dokumen BPH Pusat belum dikonfigurasi.'], 500);
        }

        $separator = str_contains($url, '?') ? '&' : '?';
        $freshUrl = $url . $separator . 'cb=' . time();

        try {
            $context = stream_context_create(['http' => ['header' => "Cache-Control: no-cache\r\n"]]);
            $csvData = file_get_contents($freshUrl, false, $context);
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            
            $header = [];
            $dataStartIndex = 0;
            
            // FIX: Sanitasi Header menggunakan trim
            foreach ($rows as $index => $row) {
                $cleanRow = array_map('trim', $row);
                if (in_array('Nomor Surat', $cleanRow) && in_array('Perihal', $cleanRow)) {
                    $header = $cleanRow;
                    $dataStartIndex = $index + 1;
                    break;
                }
            }

            if (empty($header)) return response()->json(['message' => 'Format Header (Nomor Surat & Perihal) tidak ditemukan.'], 400);

            $noSuratIdx = array_search('Nomor Surat', $header);
            $perihalIdx = array_search('Perihal', $header);
            $keteranganIdx = array_search('Keterangan', $header);
            $tglBuatIdx = array_search('Tanggal Dibuat', $header);
            $tglKegiatanIdx = array_search('Tanggal Kegiatan', $header);
            $linkSuratIdx = array_search('Link Surat', $header);
            $linkScanIdx = array_search('Link Scan Surat', $header);

            $parseDate = function ($dateStr) {
                try {
                    return Carbon::parse(str_replace('/', '-', trim($dateStr)))->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };
            $parseUrl = function ($urlStr) {
                return filter_var(trim($urlStr), FILTER_VALIDATE_URL) ? trim($urlStr) : null;
            };

            $success = 0;
            $failed = 0;

            for ($i = $dataStartIndex; $i < count($rows); $i++) {
                // FIX: Sanitasi seluruh isi baris untuk membersihkan spasi tak kasat mata
                $row = array_map('trim', $rows[$i]);
                if (empty($row) || count($row) < 3) continue;

                $noSurat = $row[$noSuratIdx] ?? null;
                $perihal = $row[$perihalIdx] ?? null;
                
                // FIX: Validasi ketat terhadap spasi kosong
                if (empty($noSurat) || strtolower($noSurat) === 'nan') continue;

                $tglBuat = $parseDate(($tglBuatIdx !== false) ? ($row[$tglBuatIdx] ?? null) : null) ?? now()->toDateString();
                $title = (!empty($perihal) && strtolower($perihal) !== 'nan') ? $perihal : 'Tanpa Judul';

                try {
                    $doc = Document::updateOrCreate(
                        [
                            'letter_number' => $noSurat,
                            'event_id'      => $eventId ? (int)$eventId : null,
                        ],
                        [
                            'title'         => $title,
                            'letter_link'   => $parseUrl(($linkSuratIdx !== false) ? ($row[$linkSuratIdx] ?? null) : null),
                            'scan_link'     => $parseUrl(($linkScanIdx !== false) ? ($row[$linkScanIdx] ?? null) : null),
                            'activity_date' => $parseDate(($tglKegiatanIdx !== false) ? ($row[$tglKegiatanIdx] ?? null) : null),
                            'created_by'    => auth()->id() ?? 1,
                        ]
                    );

                    $doc->timestamps = false;
                    $doc->created_at = $tglBuat . ' 00:00:00';
                    $doc->save();
                    $doc->timestamps = true;

                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                }
            }

            return response()->json(['message' => "Sinkronisasi selesai. Berhasil: $success surat. Gagal/Dilewati: $failed surat."]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data.', 'error' => $e->getMessage()], 500);
        }
    }
}
````

## File: app/Http/Controllers/FinanceController.php
````php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\FinanceResource;
use App\Models\Event;
use App\Models\Finance;
use App\Services\FinanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __construct(
        private readonly FinanceService $financeService,
    ) {}

    public function index(Request $request)
    {
        $query = Finance::with(['user', 'event'])
            ->when($request->search, fn ($q, $search) =>
                $q->where(fn ($query) =>
                    $query->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('notes', 'like', "%{$search}%")
                )
            )
            ->when($request->type, fn ($q, $type) =>
                $q->where('type', $type)
            )
            ->where('event_id', $request->input('event_id'))
            ->when($request->start_date && $request->end_date, fn ($q) =>
                $q->whereBetween('date', [$request->start_date, $request->end_date])
            )
            ->latest('date');

        // BYPASS OPTIMASI EXPORT
        if ($request->boolean('export')) {
            $finances = $query->get();
            return response()->json([
                'message' => 'Export payload ready',
                'data' => FinanceResource::collection($finances)
            ]);
        }

        $finances = $query->paginate(15);
        return FinanceResource::collection($finances);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $request->event_id, ['Ketua', 'Bendahara']);

        $validated = $request->validate([
            'user_id'        => ['required', 'exists:users,id'],
            'event_id'       => ['nullable', 'exists:events,id'],
            'type'           => ['required', 'in:income,expense'],
            'category'       => ['nullable', 'string'],
            'funding_source' => ['nullable', 'string'],
            'title'          => ['required', 'string', 'max:255'],
            'qty'            => ['required', 'numeric', 'min:0.01'],
            'unit'           => ['nullable', 'string', 'max:50'],
            'unit_price'     => ['required', 'numeric', 'min:0'],
            'pic'            => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'notes'          => ['nullable', 'string'],
            'receipt_url'    => ['nullable', 'string'],
            'date'           => ['required', 'date'],
        ]);

        $validated['description'] = $validated['title'];

        $finance = $this->financeService->storeFinance($validated);

        return (new FinanceResource($finance))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Finance $finance): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $finance->event_id, ['Ketua', 'Bendahara']);

        $validated = $request->validate([
            'event_id'       => ['nullable', 'exists:events,id'],
            'type'           => ['required', 'in:income,expense'],
            'category'       => ['nullable', 'string'],
            'funding_source' => ['nullable', 'string'],
            'title'          => ['required', 'string', 'max:255'],
            'qty'            => ['required', 'numeric', 'min:0.01'],
            'unit'           => ['nullable', 'string', 'max:50'],
            'unit_price'     => ['required', 'numeric', 'min:0'],
            'pic'            => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'notes'          => ['nullable', 'string'],
            'receipt_url'    => ['nullable', 'string'],
            'date'           => ['required', 'date'],
        ]);

        $validated['description'] = $validated['title'];
        $validated['amount'] = ($validated['qty'] ?? 1) * ($validated['unit_price'] ?? 0);

        $finance->update($validated);

        return response()->json([
            'message' => 'Success',
            'data'    => new FinanceResource($finance->load(['user', 'event'])),
        ]);
    }

    public function destroy(Request $request, Finance $finance): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $finance->event_id, ['Ketua', 'Bendahara']);

        $finance->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $eventId = $request->input('event_id');

        if ($eventId) {
            $event = Event::find($eventId);
            if (!$event || empty($event->finance_sync_url)) {
                return response()->json(['message' => 'URL Sinkronisasi Keuangan untuk Event ini belum diatur.'], 400);
            }
            $url = $event->finance_sync_url;
        } else {
            $url = env('TRACKING_KEUANGAN_URL');
            if (!$url) return response()->json(['message' => 'URL Sinkronisasi Keuangan BPH Pusat belum dikonfigurasi.'], 500);
        }

        $separator = str_contains($url, '?') ? '&' : '?';
        $freshUrl = $url . $separator . 'cb=' . time();

        try {
            $context = stream_context_create(['http' => ['header' => "Cache-Control: no-cache\r\n"]]);
            $csvData = file_get_contents($freshUrl, false, $context);
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            
            $header = [];
            $dataStartIndex = 0;
            foreach ($rows as $index => $row) {
                $cleanRow = array_map('trim', $row);
                $rowString = strtolower(implode(' | ', $cleanRow));
                if (str_contains($rowString, 'tipe') && str_contains($rowString, 'rincian')) {
                    $header = $cleanRow;
                    $dataStartIndex = $index + 1;
                    break;
                }
            }

            if (empty($header)) return response()->json(['message' => 'Format Header tidak ditemukan.'], 400);

            $idx = [
                'tgl'      => array_search('Tanggal (YYYY-MM-DD)', $header) ?: array_search('Tanggal', $header),
                'tipe'     => array_search('Tipe (Pemasukan/Pengeluaran)', $header) ?: array_search('Tipe', $header),
                'rincian'  => array_search('Rincian', $header),
                'kategori' => array_search('Kategori', $header),
                'vol'      => array_search('Volume', $header),
                'satuan'   => array_search('Satuan', $header),
                'harga'    => array_search('Harga Satuan', $header),
                'sumber'   => array_search('Sumber Dana', $header),
                'pic'      => array_search('Penanggungjawab', $header),
                'metode'   => array_search('Metode', $header),
                'nota'     => array_search('Link Nota', $header),
                'ket'      => array_search('Keterangan', $header),
            ];

            $parseDate = function ($dateStr) {
                try {
                    return Carbon::parse(str_replace('/', '-', trim($dateStr)))->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };
            $parseUrl = function ($urlStr) {
                return filter_var(trim($urlStr), FILTER_VALIDATE_URL) ? trim($urlStr) : null;
            };
            $parsePrice = function ($priceStr) {
                return (float) preg_replace('/[^0-9]/', '', explode(',', trim($priceStr))[0] ?? '0');
            };
            $val = function($row, $index) {
                if ($index === false || !isset($row[$index])) return null;
                $v = trim($row[$index]);
                return (strtolower($v) === 'nan' || $v === '') ? null : $v;
            };

            // FIX UTAMA: Menyuntikkan $eventId ke dalam use(...)
            DB::transaction(function () use ($rows, $dataStartIndex, $idx, $parseDate, $parseUrl, $parsePrice, $val, $eventId) {
                if ($eventId) {
                    Finance::where('event_id', $eventId)->delete();
                } else {
                    Finance::whereNull('event_id')->delete();
                }

                for ($i = $dataStartIndex; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    if (empty($row) || count($row) < 3) continue;

                    $rincian = $val($row, $idx['rincian']);
                    $tipeRaw = $val($row, $idx['tipe']);
                    if (!$rincian || !$tipeRaw) continue;

                    $type = (stripos($tipeRaw, 'masuk') !== false || strtolower($tipeRaw) === 'income') ? 'income' : 'expense';
                    $qty = (float) ($val($row, $idx['vol']) ?? 1);
                    $price = $parsePrice($val($row, $idx['harga']));

                    Finance::create([
                        'user_id'        => auth()->id() ?? 1,
                        'event_id'       => $eventId ? (int)$eventId : null,
                        'type'           => $type,
                        'category'       => $val($row, $idx['kategori']),
                        'title'          => $rincian,
                        'description'    => $rincian,
                        'qty'            => $qty,
                        'unit'           => $val($row, $idx['satuan']),
                        'unit_price'     => $price,
                        'amount'         => $qty * $price,
                        'funding_source' => $val($row, $idx['sumber']),
                        'pic'            => $val($row, $idx['pic']),
                        'payment_method' => $val($row, $idx['metode']),
                        'receipt_url'    => $parseUrl($val($row, $idx['nota'])),
                        'notes'          => $val($row, $idx['ket']),
                        'date'           => $parseDate($val($row, $idx['tgl'])) ?? now()->toDateString(),
                    ]);
                }
            });

            return response()->json(['message' => "Sinkronisasi berhasil."]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data.', 'error' => $e->getMessage()], 500);
        }
    }
}
````

## File: .docs/CHANGELOG.md
````markdown
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
````

## File: routes/api.php
````php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaAttendanceController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventCommitteeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MonthlyDueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarningController;

Route::get('/user', function (Request $request) {
    return $request->user()->load(['roles', 'division']);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Profile Endpoints
    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);
    Route::put('/user/password', [ProfileController::class, 'updatePassword']);

    // Dashboard Endpoints
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
    Route::get('/dashboard/upcoming-agenda', [DashboardController::class, 'upcomingAgenda']);

    // --- READ-ONLY / GENERAL ENDPOINTS ---
    // (Aman diakses semua role yang login untuk keperluan fetch data)
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/agendas', [AgendaController::class, 'index']);
    Route::get('/agenda-attendances', [AgendaAttendanceController::class, 'index']);
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::get('/warnings', [WarningController::class, 'index']);
    Route::get('/finances', [FinanceController::class, 'index']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/divisions', [DivisionController::class, 'index']);
    Route::get('/event-committees', [EventCommitteeController::class, 'index']);

    // --- CONTEXTUAL AUTH RESOURCES ---
    // (Bisa di-POST/PUT oleh Admin DAN anggota BPH Event via Authorization Policy)
    Route::post('/agendas/sync', [AgendaController::class, 'sync']);
    Route::post('/agendas/{id}/targets', [AgendaController::class, 'setTargets']);
    Route::post('/agenda-attendances/bulk', [AgendaAttendanceController::class, 'bulkSync']);
    Route::post('/finances/sync', [FinanceController::class, 'sync']);
    Route::apiResource('finances', FinanceController::class)->except(['create', 'edit', 'index']);
    Route::post('/documents/sync', [DocumentController::class, 'sync']);
    Route::apiResource('documents', DocumentController::class)->except(['create', 'edit', 'index']);
    Route::apiResource('events', EventController::class)->except(['create', 'edit', 'index']);

    // --- STRICT ADMIN WRITE ENDPOINTS ---
    // (HANYA boleh diakses oleh Administrator BPH Pusat)
    Route::middleware('role:admin')->group(function () {
        // Audit Trails
        Route::get('/audit-trails', [AuditTrailController::class, 'index']);

        // Kas Pengurus (Monthly Dues)
        Route::get('/monthly-dues', [MonthlyDueController::class, 'index']);
        Route::post('/monthly-dues/sync', [MonthlyDueController::class, 'sync']);

        // Master Data
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::post('/divisions', [DivisionController::class, 'store']);
        Route::put('/divisions/{division}', [DivisionController::class, 'update']);
        Route::delete('/divisions/{division}', [DivisionController::class, 'destroy']);
        
        // Panitia & Kehadiran
        Route::post('/event-committees', [EventCommitteeController::class, 'store']);
        Route::delete('/event-committees/{eventCommittee}', [EventCommitteeController::class, 'destroy']);

        // Peringatan Organisasi
        Route::post('/warnings', [WarningController::class, 'store']);
        Route::put('/warnings/{warning}', [WarningController::class, 'update']);
        Route::delete('/warnings/{warning}', [WarningController::class, 'destroy']);
    });
});
````

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
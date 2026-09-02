# Manajemen Protik - API Backend 🚀

Sistem informasi terintegrasi untuk manajemen sumber daya, anggota, dan operasional keuangan UKM PROTIC Politeknik Negeri Cilacap. Repositori ini berisi layanan *Backend* berbasis REST API yang melayani aplikasi *Frontend* React.

## 🛠️ Tech Stack
- **Framework:** Laravel (PHP 8 OOP)
- **Database:** MySQL / MariaDB
- **Autentikasi:** Laravel Sanctum (Stateful Cookie)
- **Dokumentasi API:** Scramble (OpenAPI / Swagger)

## 📋 Prasyarat Sistem Lokal
Sebelum mulai melakukan *coding*, pastikan komputermu sudah terinstal:
- PHP >= 8.2
- Composer
- Database MySQL/MariaDB (Melalui Laravel Herd, Laragon, XAMPP, atau Docker)
- Git

## 🚀 Panduan Instalasi Lokal

1. **Kloning Repositori**
   ```bash
   git clone https://github.com/nkeman200-netizen/manajemen-protik.git
   cd manajemen-protik
   ```

2. **Instalasi Dependensi**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (.env)**
   Duplikat konfigurasi bawaan dan buat kunci aplikasi:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Penting: Buka file `.env` dan pastikan konfigurasi koneksi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sudah sesuai dengan database lokal Anda.*

4. **Migrasi & Seeding Database**
   Buat tabel dan isi dengan data *dummy* standar:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Server Lokal**
   Gunakan Laravel Herd atau jalankan perintah bawaan:
   ```bash
   php artisan serve
   ```
   Dokumentasi *endpoint* API (Swagger) kini dapat diakses secara interaktif di: `http://localhost:8000/docs/api`.

## 📜 Standar Kontribusi (Git Workflow)
Proyek ini mewajibkan penggunaan standar **Conventional Commits** untuk memudahkan pelacakan versi (*Changelog*). Format wajib pesan komit:
`<tipe>(<opsional ruang-lingkup>): <pesan singkat>`

**Tipe yang wajib digunakan:**
- `feat:` Menambah fitur baru (misal: `feat(auth): add login endpoint`)
- `fix:` Memperbaiki *bug* (misal: `fix(event): fix calculation error`)
- `docs:` Memperbarui dokumentasi (misal: `docs: update README`)
- `ci:` Memperbarui *pipeline server* (misal: `ci: add github actions`)
- `refactor:` Merombak struktur kode tanpa mengubah fungsi utama.
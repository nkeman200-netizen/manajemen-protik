# Arsitektur Sistem Manajemen PROTIC

Sistem Manajemen Protik dikembangkan untuk memfasilitasi kebutuhan operasional UKM PROTIC Politeknik Negeri Cilacap. Dokumen ini memetakan topologi infrastruktur jaringan, server produksi, dan alur CI/CD (*Continuous Integration & Deployment*) sebagai referensi mutlak bagi tim pengembang dan administrator DevOps di masa mendatang.

## 1. Topologi Jaringan Utama (Gateway & Security)
Aplikasi ini menggunakan pendekatan **Zero-Trust Network** melalui **Cloudflare Tunnel**.
- **Blokade NAT/Firewall:** VPS Proxmox tidak membuka *port* publik apa pun (termasuk *port* 80/443).
- **Inbound Traffic:** Semua lalu lintas masuk dari luar ditangkap oleh jaringan CDN Cloudflare, dienkripsi SSL otomatis, dan disalurkan secara aman ke dalam *server* lokal melalui terowongan (*tunnel*) terenkripsi.
- **Keuntungan:** Melindungi server dari serangan DDoS langsung, *port scanning*, dan intrusi SSH pihak ketiga.

## 2. Arsitektur Frontend (Client-Side)
- **Framework:** React.js (Vite) / Tailwind CSS.
- **Hosting:** Cloudflare Pages (Serverless CDN Edge).
- **Domain:** `protic.sofyan.app`
- **Routing:** SPA (*Single Page Application*) dengan penanganan rute sisi klien. Permintaan data (*fetching*) diarahkan langsung ke *endpoint* API Backend.

## 3. Arsitektur Backend (Server-Side)
- **Framework:** Laravel (PHP 8 OOP).
- **Infrastruktur:** Proxmox VPS (Ubuntu/Debian).
- **Domain API:** `api-protic.sofyan.app`
- **Containerization:** Berjalan di dalam ekosistem **Docker** (`docker-compose`) yang mengisolasi *service* Nginx, PHP-FPM, dan Database.
- **Autentikasi:** Laravel Sanctum (Stateful Cookie / Bearer Token) dengan validasi *CORS* ketat yang mengunci akses hanya dari domain `.sofyan.app`.
- **Dokumentasi API:** Tergenerasi secara otomatis menggunakan *Scramble* (OpenAPI/Swagger) dan dapat diakses pada `/docs/api` (Dibatasi oleh *Gate Authorization* khusus admin).

## 4. Pipeline CI/CD (Otomatisasi Deployment)
Sistem ini menganut prinsip pengiriman kode instan (*Continuous Deployment*).

### A. CI/CD Frontend (Cloudflare Webhook)
- Terintegrasi langsung dengan repositori GitHub (`manajemen-protik-ui`).
- Setiap *push* ke *branch* `main` akan memicu *build* otomatis (`npm run build`) oleh mesin Cloudflare dan mendistribusikan aset statis ke CDN global tanpa intervensi manual.

### B. CI/CD Backend (GitHub Self-Hosted Runner)
- Menggunakan arsitektur *Pull-Based Agent*.
- Sebuah agen GitHub berjalan sebagai *background service/daemon* (`svc.sh`) di dalam VPS Proxmox.
- Karena Proxmox tersembunyi di balik Cloudflare Tunnel, agen ini melakukan *outbound long-polling* ke GitHub secara aman.
- Saat terjadi *push* ke *branch* `main`, agen menarik kode terbaru secara lokal dan mengeksekusi automasi Docker (`composer install`, `php artisan migrate`, `optimize`).

---
*Dokumen ini wajib diperbarui setiap kali ada perubahan mayor pada infrastruktur server atau perpindahan penyedia layanan cloud.*
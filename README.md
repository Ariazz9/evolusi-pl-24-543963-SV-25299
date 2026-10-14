# Tugas Konstruksi & Evolusi Perangkat Lunak (UTS)

Repositori ini digunakan untuk memenuhi tugas Ujian Tengah Semester (UTS) mata kuliah **Konstruksi & Evolusi Perangkat Lunak**.

## Identitas

- **Nama:** Aurell Achmad Madina Hartama
- **NIM:** 543963
- **Kelas:** AA

---

## 🏗️ Arsitektur & Fitur Aplikasi

Aplikasi ini dibangun menggunakan arsitektur modern *decoupled full-stack*:

1. **Backend RESTful API (Laravel 12):**
   - Autentikasi berbasis token menggunakan **Laravel Sanctum** (`POST /api/login`, `POST /api/logout`, `GET /api/me`).
   - Endpoint CRUD Inventaris lengkap dilindungi middleware `auth:sanctum`.
   - Standar respons JSON dengan kode status HTTP eksplisit (**200, 201, 401, 404, 422**).
   - Pengujian otomatis (*automated feature tests*) menggunakan **Pest PHP** (`php artisan test`).
   - **Nilai Tambahan:** Dijalankan dengan application server **FrankenPHP** berbasis Caddy untuk performa tinggi.

2. **Frontend SPA (Vue.js 3 + Vite):**
   - Single Page Application berbasis **Vue 3** (Composition API) dan **Vue Router**.
   - Integrasi API menggunakan **Axios** dengan interceptor otomatis untuk menyematkan `Authorization: Bearer <token>`.
   - Proteksi navigasi (*route guard*) yang memastikan rute inventaris hanya bisa diakses setelah login.
   - Antarmuka CRUD interaktif: tambah barang baru, edit stok/harga, dan hapus barang.
   - Pengujian unit utilitas dengan **Vitest** dan pengecekan style dengan **ESLint**.

3. **Containerization & Orkestrasi (Docker & Docker Compose):**
   - **Multi-stage Build Backend:** Builder (Composer) $\to$ Runtime (**FrankenPHP Alpine**).
   - **Multi-stage Build Frontend:** Builder (Node 22 Alpine) $\to$ Production Server (**Nginx 1.27 Alpine** dengan konfigurasi fallback SPA).
   - **Docker Compose:** Mengorkestrasi tiga layanan terisolasi (`db`, `backend`, `frontend`) dengan *isolated bridge network* dan *named volume* untuk persistensi database.

4. **CI/CD Pipeline (GitHub Actions):**
   - Workflow pengujian dan build otomatis untuk backend dan frontend.
   - Pembangunan dan publikasi image Docker ke **GitHub Container Registry (GHCR)**.
   - Job deployment produksi yang memanfaatkan **GitHub Secrets**.

---

## 🔄 Alur Aliran Data (Data Flow)

Alur transmisi data dari antarmuka pengguna hingga ke basis data:

```
[ User Interaction ]
         │
         ▼
[ Vue.js Component (InventoryView.vue / LoginView.vue) ]
         │
         ▼
[ Axios Client (src/utils/api.js) ]
  • Menyisipkan header 'Authorization: Bearer <token>' dari localStorage
  • Mengirimkan payload JSON via HTTP Request
         │
         ▼
[ Web Server (FrankenPHP / Nginx) ]
         │
         ▼
[ Laravel Routing (routes/api.php) ]
         │
         ▼
[ Middleware Pipeline (HandleCors, auth:sanctum) ]
  • Memvalidasi origin CORS
  • Memverifikasi validitas token Sanctum pada header Authorization
         │
         ▼
[ Controller (Api\InventoryItemController / Api\AuthController) ]
  • Validasi request data (mengembalikan HTTP 422 jika format salah)
         │
         ▼
[ Eloquent ORM (App\Models\InventoryItem) ]
         │
         ▼
[ Database MySQL (Service 'db' via port 3306) ]
```

---

## 🛠️ Kendala yang Ditemui dan Solusinya

1. **Kendala CORS saat Frontend Memanggil API Backend:**
   - *Penyebab:* Browser memblokir request Axios dari `localhost:3000` ke `localhost:8000` karena perbedaan *origin*.
   - *Solusi:* Menerbitkan konfigurasi CORS di Laravel (`config/cors.php`) dan mengizinkan rute `api/*` dengan header `Authorization`.

2. **Hilangnya Routing SPA saat Page Refresh di Nginx (HTTP 404):**
   - *Penyebab:* Nginx di container frontend mencari berkas statis fisik sesuai URI rute Vue Router (misal `/inventory`), yang sebenarnya tidak ada di disk.
   - *Solusi:* Menambahkan directive `try_files $uri $uri/ /index.html;` pada konfigurasi `frontend/nginx.conf`.

3. **Hardcoded Credentials di Dockerfile (Anti-Pattern):**
   - *Penyebab:* Dockerfile sebelumnya memodifikasi berkas `.env` menggunakan `sed` saat startup container.
   - *Solusi:* Menggunakan berkas entrypoint (`docker/entrypoint.sh`) dan menginjeksi kredensial dinamis melalui environment variable Docker Compose (`DB_HOST=db`, `DB_PORT=3306`), sesuai prinsip *12-Factor App*.

---

## 🚀 Panduan Menjalankan Aplikasi

### Opsi A: Menggunakan Docker Compose (Direkomendasikan)

1. Salin berkas environment:
   ```bash
   cp .env.example .env
   cp frontend/.env.example frontend/.env
   ```
2. Jalankan seluruh container (Database, Backend FrankenPHP, Frontend Nginx):
   ```bash
   docker compose up -d --build
   ```
3. Periksa status container yang berjalan:
   ```bash
   docker compose ps
   ```
4. Akses antarmuka:
   - **Frontend Web:** [http://localhost:3000](http://localhost:3000)
   - **Backend API:** [http://localhost:8000/api](http://localhost:8000/api)
   - **Kredensial Default:**
     - Email: `admin@example.com`
     - Password: `password`

### Opsi B: Menjalankan Secara Lokal (Manual)

1. **Backend:**
   ```bash
   composer install
   php artisan migrate --seed
   php artisan serve --port=8000
   ```
2. **Frontend:**
   ```bash
   cd frontend
   npm install
   npm run dev
   ```

---

## 🧪 Pengujian (Testing)

### 1. Backend Automated Tests (Pest PHP)
```bash
php artisan test
```
*Menguji skenario autentikasi Sanctum, kode status HTTP (200, 201, 401, 404, 422), dan operasi CRUD API.*

### 2. Frontend Tests (Vitest & ESLint)
```bash
cd frontend
npm test
npm run lint
```

### 3. Postman Collection
Koleksi pengujian Postman telah disertakan pada berkas:
[`postman/Inventori_API.postman_collection.json`](postman/Inventori_API.postman_collection.json)
- Import file tersebut ke Postman.
- Jalankan *Request* `Login (200 OK)` terlebih dahulu untuk menyimpan token Sanctum secara otomatis ke variabel koleksi.
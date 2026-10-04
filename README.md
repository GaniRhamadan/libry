# RentalHub Starter Kit

**RentalHub Starter Kit** (`rental-hub/starter-kit`) adalah Laravel Composer package production-grade untuk sistem manajemen rental armada kendaraan (mobil/motor). Paket ini dirancang plug-and-play: setelah menjalankan `composer require` dan `php artisan rental:install`, seluruh sistem rental langsung dapat digunakan tanpa konfigurasi tambahan atau proses kompilasi aset (Node.js/npm).

---

## Fitur Utama

1. **Plug and Play**: Siap pakai langsung tanpa setup rumit.
2. **Tanpa Wajib Compile Aset**: Frontend menggunakan Tailwind CSS dan Alpine.js via CDN secara default, dengan opsi beralih ke aset lokal via file konfigurasi.
3. **100% Responsif (Mobile-First)**: Sidebar otomatis menjadi drawer dengan toggle hamburger di layar ponsel. Tabel dilengkapi scroll horizontal dan layout adaptif hingga resolusi 360px.
4. **Isolasi Namespace & Prefix**: Semua tabel database menggunakan prefix `rental_` (configurable) dan seluruh route menggunakan URL `/rental` dan nama `rental.*`.
5. **Autentikasi Mandiri & Aman**:
   - Memakai guard `web` bawaan Laravel dengan session regeneration dan logout invalidation.
   - Dilengkapi proteksi RateLimiter (maksimal 5 percobaan login per menit).
   - Pengaturan profil pengguna dan penggantian kata sandi dengan verifikasi kata sandi lama.
6. **Peran Akun Terintegrasi (Admin & Pelanggan)**:
   - Middleware `rental.role:admin` dan `rental.role:customer`.
   - Dashboard dinamis pada satu endpoint `/rental/dashboard` yang disesuaikan secara otomatis berdasarkan peran akun.
7. **Katalog Armada & Kategori Lengkap**:
   - CRUD unit armada dengan kategori, nama, kode plat unik, tarif sewa per jam dan per hari, spesifikasi teknis dinamis (JSON), serta upload foto kendaraan.
   - Soft deletes pada armada.
   - Filter pencarian, kategori, status ketersediaan, dan paginasi.
8. **Mesin Reservasi & Anti-Tabrakan Jadwal (Overlap Validation)**:
   - Kalkulasi tarif otomatis: durasi >= 24 jam dihitung berbasis tarif harian ditambah sisa jam.
   - Deteksi bentrok jadwal di dalam database transaction dengan `lockForUpdate` untuk mencegah race condition.
   - Proteksi pembatalan oleh pelanggan (hanya saat status masih pending).
   - Alur persetujuan admin: setujui, tolak, tandai armada diambil, dan konfirmasi pengembalian.
   - Kalkulasi denda keterlambatan otomatis berbasis jam keterlambatan dengan opsi penyesuaian manual oleh admin.
9. **Perintah Instalasi Otomatis & Idempotent**:
   - `php artisan rental:install` dapat dijalankan berulang kali tanpa risiko duplikasi data.

---

## Persyaratan Sistem

- PHP: `^8.2`
- Laravel Framework: `^10.0`, `^11.0`, atau `^12.0`
- Driver Database: MySQL, PostgreSQL, SQLite, atau SQL Server

---

## Panduan Instalasi di Komputer Lain (Plug & Play)

Paket ini **100% Plug-and-Play**: tidak memerlukan instalasi Node.js/NPM, tidak perlu build aset frontend, dan langsung menyediakan antarmuka modern responsif berbasis Tailwind CSS & Alpine.js via CDN.

Pilih salah satu metode instalasi di bawah ini sesuai kebutuhan Anda:

### Metode 1: Dipasang ke Proyek Laravel Baru / Proyek yang Sudah Ada (Standar)

Gunakan cara ini jika Anda ingin mengintegrasikan sistem rental ke aplikasi Laravel Anda:

1. **Buka terminal proyek Laravel Anda** (atau buat proyek baru jika belum ada):
   ```bash
   composer create-project laravel/laravel rental-app
   cd rental-app
   ```

2. **Daftarkan repository GitHub library ini ke Composer**:
   ```bash
   composer config repositories.rental-hub vcs https://github.com/GaniRhamadan/libry.git
   ```

3. **Unduh package**:
   ```bash
   composer require rental-hub/starter-kit:dev-main
   ```

4. **Jalankan installer otomatis**:
   ```bash
   php artisan rental:install
   ```
   *Installer otomatis mempublikasikan konfigurasi, menjalankan migrasi database dengan isolasi prefix `rental_`, membuat symlink storage, membuat akun admin, dan menyiapkan 3 unit kendaraan demo.*

5. **Jalankan server aplikasi**:
   ```bash
   php artisan serve
   ```
   Buka browser di: **`http://127.0.0.1:8000/rental/login`**

---

### Metode 2: Clone Langsung Repositori Ini (Mode Mandiri / Standalone Demo)

Gunakan cara ini jika rekan atau pengguna lain hanya ingin langsung mencoba/mendemokan library ini secara mandiri tanpa membuat proyek Laravel terpisah:

1. **Clone repositori dari GitHub**:
   ```bash
   git clone https://github.com/GaniRhamadan/libry.git
   cd libry
   ```

2. **Pasang dependensi PHP**:
   ```bash
   composer install
   ```

3. **Jalankan installer rental**:
   ```bash
   php artisan rental:install
   ```

4. **Nyalakan server**:
   ```bash
   php artisan serve
   ```
   Buka browser di: **`http://127.0.0.1:8000/rental/login`**

---

## Akun Default & Kredensial

Jika Anda menjalankan instalasi dengan opsi default (tanpa mengisi password khusus), kredensial yang dibuat adalah:

- **URL Masuk**: `http://localhost:8000/rental/login`
- **Email Administrator**: `admin@rental.test`
- **Password**: Password acak sepanjang 12 karakter yang ditampilkan **sekali** di konsol terminal Anda saat instalasi selesai, atau password yang Anda tentukan sendiri melalui opsi `--admin-password`.

Contoh instalasi non-interaktif dengan kredensial yang ditentukan langsung:

```bash
php artisan rental:install --admin-email=admin@rental.test --admin-password=rahasia123456
```

---

## Opsi Perintah `php artisan rental:install`

| Opsi | Penjelasan |
| --- | --- |
| `--force` | Menimpa file konfigurasi yang sudah ada sebelumnya. |
| `--no-seed` | Melewati pembuatan data demo kategori dan unit armada. |
| `--admin-email=` | Menentukan alamat email untuk akun administrator. |
| `--admin-password=` | Menentukan kata sandi untuk akun administrator. |

---

## Konfigurasi (`config/rental-hub.php`)

File konfigurasi paket berada di `config/rental-hub.php`. Parameter yang dapat diatur meliputi:

```php
return [
    // Prefix URL dan nama route
    'route_prefix' => env('RENTAL_ROUTE_PREFIX', 'rental'),
    'route_name_prefix' => env('RENTAL_ROUTE_NAME_PREFIX', 'rental.'),
    'middleware' => ['web'],

    // Prefix tabel database paket
    'table_prefix' => env('RENTAL_TABLE_PREFIX', 'rental_'),

    // Model User aplikasi host
    'user_model' => env('RENTAL_USER_MODEL', 'App\\Models\\User'),

    // Penggunaan CDN Tailwind CSS & Alpine.js
    'use_cdn' => (bool) env('RENTAL_USE_CDN', true),

    // Batas maksimal durasi sewa (dalam hari)
    'max_booking_days' => (int) env('RENTAL_MAX_BOOKING_DAYS', 30),

    // Tarif denda keterlambatan per jam (Rupiah)
    'late_fee_per_hour' => (int) env('RENTAL_LATE_FEE_PER_HOUR', 50000),

    // Mata uang dan format tanggal
    'currency' => 'IDR',
    'currency_symbol' => 'Rp ',
    'date_format' => 'd M Y H:i',

    // Disk penyimpanan foto kendaraan
    'disk' => env('RENTAL_DISK', 'public'),
];
```

---

## Kustomisasi Tampilan (Views)

Jika Anda ingin mengubah tampilan antarmuka (UI) agar menyatu dengan identitas visual merek Anda, publikasikan file Blade view ke aplikasi utama dengan perintah:

```bash
php artisan vendor:publish --tag=rental-views
```

Seluruh view Blade akan dipublikasikan ke direktori:
`resources/views/vendor/rental-hub/`

### Publikasi Tag Lainnya

- **Konfigurasi saja**:
  ```bash
  php artisan vendor:publish --tag=rental-config
  ```
- **File Migrasi Database**:
  ```bash
  php artisan vendor:publish --tag=rental-migrations
  ```
- **File Terjemahan Bahasa**:
  ```bash
  php artisan vendor:publish --tag=rental-lang
  ```

---

## Daftar Lengkap Route

| Method | URL | Route Name | Hak Akses | Deskripsi |
| --- | --- | --- | --- | --- |
| `GET` | `/rental/login` | `rental.login` | Tamu | Formulir masuk |
| `POST` | `/rental/login` | `rental.login.submit` | Tamu | Proses autentikasi masuk |
| `GET` | `/rental/register` | `rental.register` | Tamu | Formulir registrasi pelanggan |
| `POST` | `/rental/register` | `rental.register.submit` | Tamu | Proses registrasi pelanggan |
| `POST` | `/rental/logout` | `rental.logout` | Auth | Keluar dari sesi rental |
| `GET` | `/rental/profile` | `rental.profile.edit` | Auth | Formulir edit profil & sandi |
| `PUT` | `/rental/profile` | `rental.profile.update` | Auth | Perbarui profil |
| `GET` | `/rental/dashboard` | `rental.dashboard` | Auth | Dashboard dinamis peran akun (Admin / Penyewa) |
| `GET` | `/rental/catalog` | `rental.catalog` | Auth | Katalog armada siap sewa (eksplorasi mobil & motor) |
| `GET` | `/rental/bookings` | `rental.bookings.index` | Auth | Daftar reservasi (Admin: Kelola Pelanggan / User: Sewa Saya) |
| `GET` | `/rental/bookings/create` | `rental.bookings.create` | Penyewa | Formulir pemesanan sewa armada |
| `POST` | `/rental/bookings` | `rental.bookings.store` | Penyewa | Simpan reservasi sewa baru |
| `GET` | `/rental/bookings/{booking}` | `rental.bookings.show` | Auth | Detail tagihan & status booking |
| `POST` | `/rental/bookings/{booking}/cancel` | `rental.bookings.cancel` | Auth (Policy) | Batalkan booking (status pending) |
| `GET` | `/rental/units` | `rental.units.index` | Admin | Daftar dan filter inventaris armada |
| `GET` | `/rental/units/create` | `rental.units.create` | Admin | Formulir tambah armada baru |
| `POST` | `/rental/units` | `rental.units.store` | Admin | Simpan armada baru |
| `GET` | `/rental/units/{unit}/edit` | `rental.units.edit` | Admin | Formulir ubah data armada |
| `PUT` | `/rental/units/{unit}` | `rental.units.update` | Admin | Perbarui data armada |
| `DELETE` | `/rental/units/{unit}` | `rental.units.destroy` | Admin | Hapus armada (soft delete) |
| `GET` | `/rental/categories` | `rental.categories.index` | Admin | Daftar kategori armada |
| `POST` | `/rental/categories` | `rental.categories.store` | Admin | Simpan kategori baru |
| `PUT` | `/rental/categories/{category}` | `rental.categories.update` | Admin | Perbarui kategori |
| `DELETE` | `/rental/categories/{category}` | `rental.categories.destroy` | Admin | Hapus kategori |
| `POST` | `/rental/bookings/{booking}/approve` | `rental.bookings.approve` | Admin | Setujui reservasi |
| `POST` | `/rental/bookings/{booking}/reject` | `rental.bookings.reject` | Admin | Tolak reservasi |
| `POST` | `/rental/bookings/{booking}/activate` | `rental.bookings.activate` | Admin | Tandai armada diambil (aktif) |
| `POST` | `/rental/bookings/{booking}/return` | `rental.bookings.return` | Admin | Konfirmasi pengembalian armada |

---

## Panduan Pemecahan Masalah (Troubleshooting)

### 1. Foto Armada Tidak Muncul
Pastikan symlink direktori publik ke storage telah dibuat dengan benar:
```bash
php artisan storage:link
```
Jika aplikasi berjalan pada shared hosting tanpa akses symlink shell, periksa apakah file `public/storage` mengarah ke `storage/app/public`.

### 2. Kolom `rental_role` atau `phone` Tidak Muncul pada Tabel User
Migrasi `2026_01_01_000004_add_rental_fields_to_users_table.php` secara otomatis mendeteksi nama tabel model User dari konfigurasi `rental-hub.user_model`. Jika model User aplikasi host menggunakan nama tabel kustom, jalankan:
```bash
php artisan migrate
```

### 3. Bentrok Jadwal Sewa (Overlap)
Sistem menolak reservasi baru apabila waktu mulai dan selesai bertabrakan dengan booking lain yang berstatus `pending`, `approved`, atau `active` pada unit yang sama. Pastikan booking sebelumnya telah diselesaikan (`returned`), dibatalkan (`cancelled`), atau ditolak (`rejected`).

---

## Lisensi

Paket ini dirilis di bawah lisensi terbuka [MIT](LICENSE).

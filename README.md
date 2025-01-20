
# Sikat
![Contoh Gambar](public/img/logo2.jpg)
Aplikasi ini adalah sistem dashboard penjualan yang dibangun menggunakan Laravel. Aplikasi ini memungkinkan pengguna untuk mencari produk, mengurutkan berdasarkan popularitas atau harga, dan menambahkan produk ke keranjang belanja.

## Fitur
- Pencarian produk
- Filter berdasarkan kategori
- Pengurutan produk berdasarkan popularitas dan harga
- Menambahkan produk ke keranjang belanja
- Halaman dashboard yang responsif

## Prerequisites
Sebelum memulai, pastikan Anda memiliki hal-hal berikut:
- **PHP** ≥ 8.2
- **Composer**
- **Laravel** ≥ 11.x
- **Node.js** dan **npm** (untuk mengelola dependensi frontend)
- **Database** (MySQL, SQLite, dll.)

## Instalasi
Ikuti langkah-langkah berikut untuk menginstal aplikasi ini di lingkungan lokal Anda:

### 1. Clone Repository
Clone repositori ini ke mesin lokal Anda menggunakan Git:
```bash
git clone https://github.com/username/repo-name.git
```
Gantilah `username` dan `repo-name` dengan nama pengguna dan nama repositori Anda.

### 2. Masuk ke Direktori Proyek
Pindah ke direktori proyek:
```bash
cd repo-name
```

### 3. Instal Dependensi Backend
Instal dependensi menggunakan Composer:
```bash
composer install
```

### 4. Konfigurasi Environment
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Kemudian, buka file `.env` dan sesuaikan pengaturan database dan konfigurasi lainnya sesuai kebutuhan Anda.

### 5. Instal Dependensi Frontend
Pindah ke direktori proyek dan instal dependensi frontend menggunakan npm:
```bash
npm install
```

### 6. Compile Assets Frontend
Setelah instalasi, kompilasi aset frontend menggunakan salah satu perintah berikut:
- Untuk mode pengembangan:
  ```bash
  npm run dev
  ```
- Untuk mode produksi:
  ```bash
  npm run build
  ```

### 7. Generate Kunci Aplikasi
Jalankan perintah berikut untuk menghasilkan kunci aplikasi:
```bash
php artisan key:generate
```

### 8. Migrasi Database
Jalankan migrasi untuk membuat tabel yang diperlukan di database:
```bash
php artisan migrate
```
Jika Anda memiliki seeder, Anda dapat menjalankannya dengan:
```bash
php artisan db:seed
```

### 9. Buat Symlink untuk Storage
Jalankan perintah berikut untuk membuat symlink dari direktori `storage` ke `public`:
```bash
php artisan storage:link
```
Perintah ini akan membuat tautan simbolis yang memungkinkan Anda mengakses file yang disimpan di direktori `storage/app/public` melalui URL publik.

### 10. Jalankan Server
Jalankan server pengembangan Laravel:
```bash
php artisan serve
```
Aplikasi Anda sekarang dapat diakses di [http://localhost:8000](http://localhost:8000).

## Dependensi
Aplikasi ini menggunakan beberapa paket dan dependensi penting, termasuk:

### Dependensi Utama
- **PHP**: ^8.2
- **Laravel Framework**: ^11.31
- **Laravel Sanctum**: ^4.0 (untuk autentikasi API)
- **Laravel Tinker**: ^2.9 (untuk interaksi dengan aplikasi melalui command line)
- **Maatwebsite Excel**: ^3.1 (untuk ekspor dan impor file Excel)
- **Barryvdh Laravel DomPDF**: ^3.0 (untuk menghasilkan PDF)
- **Spatie Laravel Permission**: ^6.10 (untuk manajemen izin dan peran)

### Dependensi Pengembangan
- **FakerPHP**: ^1.23 (untuk menghasilkan data palsu)
- **Laravel Breeze**: ^2.3 (untuk autentikasi sederhana)
- **Laravel Sail**: ^1.26 (untuk pengembangan menggunakan Docker)
- **PHPUnit**: ^11.0.1 (untuk pengujian)
- **Mockery**: ^1.6 (untuk pengujian)
- **Nunomaduro Collision**: ^8.1 (untuk penanganan kesalahan di command line)

## Penggunaan
Setelah aplikasi berjalan, Anda dapat mengakses dashboard penjualan. Anda dapat:
- Mencari produk
- Mengurutkan produk berdasarkan popularitas atau harga
- Menambahkan produk ke keranjang belanja

## Kontribusi
Jika Anda ingin berkontribusi pada proyek ini, silakan buat fork repositori ini dan kirim pull request.

## Lisensi
Proyek ini dilisensikan di bawah [MIT License](LICENSE).
```

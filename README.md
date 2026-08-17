# Stok Dasen - Aplikasi Stok Barang Portable

Aplikasi manajemen stok barang internal berbasis Laravel + Vue.js yang bisa dijalankan **tanpa Laragon dan tanpa MySQL** — cukup double-click `start.bat`.

## Fitur
- 📦 CRUD Master Data (Barang, Kategori, Lokasi, Satuan)
- 📥 Transaksi Barang Masuk
- 📤 Transaksi Barang Keluar
- 📊 Dashboard & Laporan Stok
- 🔐 Login & Role-based Access (Admin/User)
- 🌐 Akses dari PC lain via jaringan lokal (intranet)

## Persyaratan
- Windows 10/11 (64-bit)
- PHP 8.3+ (portable, disertakan dalam folder `php/`)

## Setup Pertama Kali

### 1. Download PHP Portable
1. Buka https://windows.php.net/download
2. Cari bagian **PHP 8.3** → Download **VS16 x64 Thread Safe** (ZIP)
3. Ekstrak isi ZIP ke folder `php/` di root project ini

Struktur folder seharusnya:
```
Stok-dasen/
├── php/
│   ├── php.exe
│   ├── ext/
│   └── ...
├── start.bat
├── install.bat
└── ...
```

### 2. Copy php.ini
```
copy php.ini.portable php\php.ini
```
Atau salin manual file `php.ini.portable` ke `php/php.ini`.

### 3. Jalankan Installer
Double-click **`install.bat`** — ini akan:
- Generate application key
- Membuat tabel di database SQLite
- Mengisi data awal (seeder)

### 4. Jalankan Aplikasi
Double-click **`start.bat`** — browser akan terbuka otomatis di `http://localhost:8000`

## Akses dari PC Lain (Intranet)
Karena server berjalan dengan `--host=0.0.0.0`, PC lain di jaringan yang sama bisa mengakses via:
```
http://[IP-PC-SERVER]:8000
```
Contoh: `http://192.168.100.44:8000`

## Menghentikan Server
- Tekan `Ctrl+C` di jendela command prompt, atau
- Double-click **`stop.bat`**

## Akun Default
| Role | Email | Password |
|------|-------|----------|
| Admin | *(lihat UserSeeder)* | *(lihat UserSeeder)* |

## Teknologi
- **Backend**: Laravel 13 + SQLite
- **Frontend**: Vue 3 + Vite
- **Auth**: Laravel Sanctum (Token-based)

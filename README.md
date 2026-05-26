# SIPOLAI — Sistema Manajementu Dadus Populasaun (CI4)

Sistem manajemen data populasi dibuat dengan CodeIgniter 4, dikembangkan untuk mengelola data keluarga, penduduk, kartu kependudukan, inventaris, dan laporan terkait.

## Demo (online)

https://sipolai-sistema-manajementu-dadus.onrender.com/

Gunakan kredensial demo untuk login pada aplikasi:

- Username: `admin`
- Password: `sipolai2026admin`

> Catatan: Akun demo diberikan hanya untuk pemeriksaan fitur. Jangan gunakan di lingkungan produksi tanpa mengubah kata sandi.

## Fitur Utama

- Autentikasi pengguna (login/admin)
- Manajemen `Aldeia` (desa) dan struktur suku/komunitas
- Manajemen keluarga (`Familia`) dan anggota keluarga
- Pencatatan kartu (kartu sai / karta tama)
- Inventarisasi dan pelacakan barang (`Inventoriu` / `Kargu`)
- Formulir permintaan dan jenis permintaan (`Pedidu`, `TipuPedidu`)
- Data demografis: populasi, profesi, agama, literatur
- Seeders dan migrasi database untuk setup awal
- Upload dan penyimpanan dokumen/foto keluarga pada `public/uploads/`
- Sistem modul berbasis CodeIgniter 4, mudah dikembangkan dan dimodifikasi

## Struktur Proyek (singkat)

- `app/` — kode aplikasi (Controllers, Models, Config)
- `public/` — entrypoint `index.php` dan assets
- `database/` — migrations dan seeds (setup DB)
- `writable/` — cache, logs, uploads
- `tests/` — pengujian unit dan contoh

Untuk detail struktur, lihat folder `app/Models` dan `app/Controllers`.

## Database

Proyek ini dapat dijalankan menggunakan MySQL/MariaDB dan telah dihubungkan oleh pengguna dengan TiDB (compatible dengan MySQL). Pastikan konfigurasi koneksi database di `app/Config/Database.php` disesuaikan dengan instance TiDB Anda.

Contoh pengaturan environment (.env atau config):

DB.default.hostname = your-tidb-host
DB.default.database = your_database
DB.default.username = your_user
DB.default.password = your_password
DB.default.DBDriver = MySQLi

Jalankan migrasi dan seed (CodeIgniter spark commands):

```
php spark migrate
php spark db:seed YourSeederName
```

## Deployment

Proyek ini telah dideploy ke Render.com pada URL demo di atas. Untuk deployment manual:

1. Pastikan environment variable untuk database dan baseURL sudah diatur.
2. Jalankan `composer install` untuk mengunduh dependency.
3. Jalankan migrasi dan seed jika perlu.
4. Pastikan `writable/` directory dapat ditulis.

## Cara Menjalankan Secara Lokal

1. Salin file `.env.example` ke `.env` dan sesuaikan pengaturan.
2. Jalankan `composer install`.
3. Jalankan server development:

```
php spark serve
```

Lalu buka `http://localhost:8080`.

## Kontribusi

Silakan buka issue atau kirim pull request. Ikuti standar coding dan jangan lupa menambahkan test ketika menambah fitur.

## Kontak

Untuk pertanyaan lebih lanjut, hubungi maintainer repo.
# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

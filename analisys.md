# Analisis Sistem SIPOLAI

Tanggal analisis: 2026-05-31

## Ringkasan Eksekutif

SIPOLAI sudah memiliki fondasi fitur yang cukup sesuai untuk sistem manajemen populasi tingkat suku/aldeia: ada modul penduduk, keluarga/fixa familia, master data, struktur suku, pedidu/deklarasaun, inventoriu, eleitores, kbiit laek, dan relatoriu. Dari sisi cakupan domain, sistem ini sudah mengarah ke kebutuhan nyata manajemen populasi.

Namun dari sisi logic, keamanan, dan integritas data, sistem belum siap dianggap aman untuk produksi. Ada beberapa alur yang bisa berjalan untuk demo, tetapi masih rapuh ketika data mulai besar, ada nama penduduk ganda, user mencoba akses langsung ke URL, atau database memakai mode SQL yang ketat. Temuan paling kritis adalah route debug reset admin yang terbuka, CSRF global belum aktif, beberapa endpoint tulis hanya dibatasi login tanpa otorisasi role yang kuat, dan relasi antar data penting masih banyak memakai nama lengkap, bukan ID penduduk.

Kesimpulan: fitur inti sudah cocok sebagai prototype/versi awal sistem manajemen populasi, tetapi workflow dan logic perlu diperkuat sebelum dipakai sebagai sistem operasional resmi.

## Scope Analisis

Yang diperiksa:

- Struktur proyek CodeIgniter 4.
- Route utama di `app/Config/Routes.php` dan route boilerplate.
- Controller admin: populasaun, familia, pedidu, relatoriu, dashboard, eleitor, kbiit laek, inventoriu, estrutura suku, master data, karta.
- Model dan migration utama.
- Seeder dan log runtime di `writable/logs`.
- Konfigurasi auth, filter, database, dan migration.

Yang tidak bisa diverifikasi secara eksekusi:

- `php`, `spark`, dan `vendor/bin/phpunit` tidak tersedia di PATH workspace ini, sehingga test otomatis dan route list tidak bisa dijalankan langsung.
- `writable/database.sqlite` ada, tetapi ukuran file 0 byte, jadi tidak ada data SQLite lokal yang bisa dipakai untuk validasi isi database.

## Peta Workflow Saat Ini

Workflow yang tersedia sudah cukup lengkap:

1. User login melalui Myth/Auth dan Boilerplate.
2. Admin mengelola master data: aldeia, profisaun, relijiaun, literatura, kargu.
3. Admin atau role terkait mengelola data populasaun dengan status `Moris`, `Mate`, atau `Muda`.
4. Familia dibuat sebagai fixa familia/KK, lalu penduduk yang belum punya keluarga bisa dimasukkan sebagai anggota.
5. Pedidu/deklarasaun dibuat dari daftar penduduk hidup atau daftar keluarga untuk deklarasaun nascimentu.
6. Admin atau xefe-suku menyetujui/menolak pedidu.
7. Jika pedidu disetujui, sebagian jenis pedidu memicu efek otomatis:
   - Mortalidade mengubah penduduk menjadi `Mate`.
   - Muda domisiliu mengubah penduduk menjadi `Muda`.
   - Nascimentu membuat data penduduk baru.
   - Eleitoral lakon menghapus nomor eleitoral dari penduduk.
   - Semua pedidu yang disetujui dibuatkan snapshot di inventoriu.
8. Eleitores dan Kbiit Laek memiliki halaman tersendiri setelah deklarasaun disetujui.
9. Relatoriu menampilkan laporan populasaun, familia, mortalidade, nascimentu, muda, eleitores, kbiit laek, dan pedidu.
10. Estrutura suku menghubungkan penduduk dengan jabatan struktur dan bisa membuat user untuk beberapa jabatan.

Secara bentuk besar, workflow ini masuk akal untuk sistem manajemen populasi lokal.

## Fitur Yang Sudah Tepat

1. Modul domain sudah cukup lengkap.
   Data penduduk, keluarga, status hidup/pindah/meninggal, surat permohonan, laporan, dan struktur pemerintahan lokal sudah tersedia.

2. Pemisahan data master sudah baik.
   Aldeia, profesi, agama, pendidikan, dan kargu dipisahkan ke tabel/model sendiri. Ini memudahkan standardisasi data.

3. Ada workflow persetujuan dokumen.
   `PediduController::updateStatus()` sudah memodelkan status `Pendiente`, `Aprovadu`, dan `Rezeitadu`. Ini cocok untuk alur sekretaria/xefe-suku.

4. Ada snapshot inventoriu saat pedidu disetujui.
   Ide menyimpan snapshot dokumen di `tabela_inventoriu` bagus karena dokumen yang sudah dicetak tidak berubah jika data penduduk berubah di masa depan.

5. Role xefe-aldeia mulai diperhatikan.
   Banyak list dan laporan sudah membatasi data berdasarkan `user()->id_aldeia`.

6. Laporan cukup kaya.
   Dashboard dan relatoriu mencakup total populasi, gender, usia, aldeia, pendidikan, profesi, agama, status pernikahan, pedidu, mortalidade, nascimentu, muda, eleitores, dan kbiit laek.

7. UI memakai DataTables server-side.
   Untuk data populasi yang besar, ini arah yang tepat dibanding memuat semua data sekaligus.

## Temuan Kritis

### 1. Route debug reset admin terbuka

File: `app/Config/Routes.php`

Ada route `/force-reset-admin` yang:

- Menghapus user tertentu.
- Menghapus menu dan group menu.
- Menjalankan seeder.
- Mengubah password admin.
- Mencoba login admin.

Route ini berada di luar group `admin` dan tidak memakai filter login/role. Ini sangat berbahaya jika aplikasi dapat diakses publik.

Rekomendasi:

- Hapus route ini dari production.
- Jika masih diperlukan untuk lokal, pindahkan ke command `spark` khusus development.
- Rotasi semua kredensial admin yang pernah tertulis di kode/README/migration.

### 2. Password admin hardcoded di migration 

File terkait:

- `app/Database/Migrations/2026-05-26-014400_ResetAdminCredentials.php`
- `app/Database/Migrations/2026-05-26-020500_ResetAdminCredentialsFix.php`

Credential admin demo tertulis langsung di repo dan migration. Untuk demo ini bisa dimengerti, tetapi untuk production ini risiko tinggi.

Rekomendasi:

- Jangan simpan password tetap di migration.
- Gunakan environment variable untuk seeder lokal.
- Setelah deploy, rotasi password.
- Pisahkan README demo dan README deployment production.

### 3. CSRF global belum aktif

File: `app/Config/Filters.php`

Filter `csrf` ada sebagai alias, tetapi di `$globals['before']` masih dikomentari. Banyak form memang menyertakan `csrf_field()` atau token AJAX, tetapi tanpa filter global token itu tidak benar-benar melindungi request.

Rekomendasi:

- Aktifkan CSRF untuk semua request state-changing.
- Pastikan endpoint AJAX `POST`, `PUT`, `DELETE` mengirim header/token yang valid.
- Hindari operasi ubah data via `GET`.

### 4. Banyak endpoint tulis hanya berada di group login, bukan role/policy kuat

File: `app/Config/Routes.php`

Route berikut berada di dalam group `admin` dengan filter `login`, tetapi tidak semuanya dibungkus role yang ketat:

- `resource('populasaun')`
- `resource('pedidu')`
- `resource('familia')`
- `familia/(:num)/add`
- `familia/(:num)/remove/(:num)`
- `familia/(:num)/upload-foto`
- `eleitores/(:num)/update`
- `kbiit-laek/(:num)/update`

Sebagian tombol memang disembunyikan di view, tetapi security tidak boleh bergantung pada UI. User yang sudah login bisa mencoba hit URL langsung.

Rekomendasi:

- Tambahkan policy server-side untuk setiap aksi tulis.
- Bedakan hak baca, tambah, edit, hapus, approve, print, dan update nomor kartu.
- Untuk xefe-aldeia, validasi target record harus selalu berada di aldeia user.

### 5. Relasi pedidu ke penduduk memakai nama lengkap

File utama: `app/Controllers/Admin/PediduController.php`, `EleitorController.php`, `KbiitLaekController.php`, `DashboardController.php`, `RelatoriuController.php`, `PopulasaunController.php`

Banyak query memakai `pemohon` atau `naran_kompletu` sebagai penghubung:

- Mortalidade mencari penduduk dari nama pemohon.
- Muda domisiliu mencari penduduk dari nama pemohon.
- Eleitores join `tabela_pedidu.pemohon = tabela_populasaun.naran_kompletu`.
- Kbiit Laek memakai pola yang sama.
- Snapshot inventoriu juga fallback ke nama.

Ini rawan salah orang jika ada dua penduduk bernama sama, nama berubah, atau ada perbedaan ejaan.

Rekomendasi:

- Tambahkan `id_populasaun` di `tabela_pedidu` untuk pedidu yang terkait penduduk.
- Untuk nascimentu, tambahkan field eksplisit seperti `id_familia`, `naran_labarik`, dan bila sudah dibuat, `id_populasaun_baru`.
- Tetap simpan nama sebagai snapshot display, tetapi logic harus berbasis ID.

## Temuan Logic Workflow

### 1. Approval pedidu tidak memakai transaksi

`PediduController::updateStatus()` mengubah status pedidu lebih dulu, lalu menjalankan efek samping seperti update status penduduk, membuat penduduk baru, menghapus nomor eleitoral, dan membuat inventoriu.

Jika salah satu langkah gagal, data bisa setengah jadi. Contoh: status pedidu sudah `Aprovadu`, tetapi snapshot inventoriu gagal dibuat.

Rekomendasi:

- Bungkus approval dalam database transaction.
- Update status dan efek samping harus commit bersama.
- Jika gagal, rollback dan tampilkan error.

### 2. Status pedidu bisa diubah ulang tanpa undo efek samping

`updateStatus()` menerima `Aprovadu`, `Rezeitadu`, dan `Pendiente`. Tetapi jika pedidu yang sudah disetujui diubah lagi ke `Rezeitadu` atau `Pendiente`, efek sebelumnya tidak dibatalkan.

Contoh:

- Mortalidade yang sudah mengubah penduduk ke `Mate` tidak otomatis kembali.
- Nascimentu yang sudah membuat penduduk baru tidak dihapus/ditandai batal.
- Inventoriu snapshot tetap ada.

Rekomendasi:

- Terapkan state machine: `Pendiente -> Aprovadu/Rezeitadu` saja.
- Jika perlu koreksi, buat aksi khusus `void/cancel` dengan audit trail.
- Jangan izinkan revert status tanpa prosedur pembatalan yang jelas.

### 3. Workflow nascimentu belum konsisten

Di UI `pedidu/create.php`, `pemohon` untuk `Deklarasaun Nascimentu` adalah nama anak. Tetapi di `RealDataSeeder`, data birth history pernah memakai kepala keluarga sebagai `pemohon` dan nama anak di `meta_data['naran_labarik']`.

Akibatnya laporan nascimentu yang membaca `pemohon` sebagai nama anak bisa salah untuk data seed atau data historis.

Rekomendasi:

- Standarkan schema birth request.
- Simpan `naran_labarik`, `id_familia`, `id_aldeia`, `data_moris`, `fatin_moris`, `jeneru` sebagai kolom atau JSON yang konsisten.
- Jangan gunakan `pemohon` untuk dua makna berbeda.

### 4. Deklarasaun eleitoral dan kbiit laek belum otomatis menetapkan nomor kartu

Approval `Deklarasaun Eleitoral` membuat seseorang muncul di halaman eleitores karena join ke pedidu approved, tetapi nomor eleitoral baru diisi lewat endpoint terpisah. Alur ini bisa diterima, tetapi perlu aturan:

- Apakah orang dianggap eleitor setelah deklarasaun disetujui, atau setelah nomor kartu diisi?
- Apakah nomor kartu wajib sebelum print?
- Apakah nomor kartu harus unik?

Saat ini `no_eleitoral` dan `no_kbiit_laek` tidak terlihat punya unique constraint.

Rekomendasi:

- Definisikan status proses: deklarasaun approved, kartu diterbitkan, nomor kartu aktif.
- Tambahkan unique index untuk nomor kartu yang tidak null.
- Validasi nomor sebelum print/aktif.

### 5. Deklarasaun Eleitoral Lakon menghapus nomor sebelum snapshot

Dalam approval `Deklarasaun Eleitoral Lakon`, sistem menghapus `no_eleitoral` dari penduduk, lalu setelah itu membuat snapshot inventoriu. Ini berarti snapshot bisa kehilangan nomor kartu lama yang justru penting untuk kasus kartu hilang.

Rekomendasi:

- Ambil snapshot sebelum menghapus nomor.
- Simpan nomor lama di `meta_data`, misalnya `no_eleitoral_lama`.
- Baru setelah snapshot aman, kosongkan nomor aktif.

### 6. Status populasi bisa diubah langsung tanpa dokumen

Ada endpoint `populasaun/(:num)/status` yang dapat mengubah `Moris`, `Mate`, `Muda`. Ini berguna untuk koreksi admin, tetapi jika dipakai tanpa audit bisa memotong workflow pedidu.

Rekomendasi:

- Batasi hanya admin super.
- Wajib alasan perubahan.
- Buat tabel riwayat status populasi.
- Hubungkan perubahan status ke id pedidu jika berasal dari dokumen.

### 7. Pengelolaan keluarga belum menjaga invariant kuat

Sistem ingin satu `Xefe Familia` per keluarga dan harus `Mane`. Sebagian logic sudah memeriksa gender, tetapi:

- `PopulasaunController::create/update` bisa memasukkan penduduk sebagai `Xefe Familia` ke keluarga yang mungkin sudah punya xefe.
- `FamiliaController::addMembro` tidak memeriksa apakah keluarga target memang masih belum punya xefe selain berdasarkan UI show.
- Tidak ada constraint database untuk mencegah lebih dari satu xefe dalam satu keluarga.

Rekomendasi:

- Validasi server-side sebelum insert/update semua jalur.
- Tambahkan aturan unik berbasis aplikasi: satu `relasaun_familia = Xefe Familia` per `id_familia`.
- Pertimbangkan tabel relasi keluarga khusus jika logic keluarga makin kompleks.

### 8. Delete data masih hard delete

Beberapa delete langsung menghapus data:

- Populasaun delete.
- Pedidu delete, sekaligus inventoriu terkait.
- Familia delete mengosongkan relasi anggota lalu menghapus keluarga.
- Master data delete.

Untuk sistem administrasi penduduk, hard delete berisiko karena menghilangkan jejak dokumen dan laporan.

Rekomendasi:

- Gunakan soft delete untuk data inti.
- Untuk pedidu/inventoriu, hindari menghapus dokumen yang sudah approved.
- Tambahkan audit log untuk siapa menghapus/mengubah apa.

## Temuan Database dan Integritas Data

### 1. Foreign key utama belum ada

Migration utama membuat `id_aldeia`, `id_profisaun`, `id_relijiaun`, `id_literatura`, `id_familia`, `id_pedidu`, tetapi tidak menambahkan foreign key untuk tabel domain utama.

Dampak:

- Penduduk bisa menunjuk aldeia/profesi/agama/pendidikan yang tidak ada.
- Pedidu bisa menunjuk aldeia yang sudah dihapus.
- Inventoriu bisa menunjuk pedidu yang sudah hilang.

Rekomendasi:

- Tambahkan foreign key untuk relasi inti.
- Jika tidak ingin hard FK karena deployment, minimal tambahkan validation dan index.

### 2. Index belum cukup untuk query besar

DataTables dan laporan banyak filter berdasarkan:

- `id_aldeia`
- `istadu`
- `naran_kompletu`
- `nik`
- `naran_pedidu`
- `status`
- `data_pedidu`
- `id_familia`

Belum terlihat index komposit untuk pola query tersebut.

Rekomendasi:

- Tambahkan index `tabela_populasaun(id_aldeia, istadu)`.
- Tambahkan index `tabela_populasaun(id_familia, relasaun_familia)`.
- Tambahkan index `tabela_pedidu(naran_pedidu, status, id_aldeia)`.
- Tambahkan index `tabela_pedidu(id_populasaun, naran_pedidu, status)` setelah field ID ditambahkan.

### 3. NIK generated random dan belum transaction-safe

`generateUniqueNip()` membuat angka acak lalu mengecek database. Dengan traffic bersamaan, dua request bisa lolos pengecekan sebelum insert. Unique constraint akan menolak salah satu, tetapi controller belum terlihat menangani exception secara rapi.

Rekomendasi:

- Gunakan format NIK/NIP yang deterministic jika ada aturan resmi.
- Jika tetap random, bungkus insert dalam retry saat duplicate key.
- Jangan tampilkan sukses jika save gagal karena unique violation.

### 4. Migration timestamp tidak rapi

Ada migration dengan timestamp seperti `2026-05-19-240000` dan `2026-05-19-250000`. Format konfigurasi migration adalah `Y-m-d-His_`, sehingga jam 24/25 bukan jam valid secara kalender. Ini bisa membuat migration runner bermasalah atau minimal membingungkan urutan riwayat.

Rekomendasi:

- Rename migration baru ke timestamp valid.
- Jangan rename migration yang sudah pernah jalan di production tanpa strategi, karena tabel `migrations` menyimpan versi.

## Temuan Query dan Runtime

### 1. Error `ONLY_FULL_GROUP_BY` sudah muncul di log

Log terbaru menunjukkan error berulang:

`Expression #20 of SELECT list is not in GROUP BY clause ... incompatible with sql_mode=only_full_group_by`

Kemungkinan besar berasal dari query eleitores/kbiit yang select `tabela_populasaun.*` plus kolom pedidu, lalu `groupBy` hanya sebagian kolom.

File terkait:

- `app/Controllers/Admin/EleitorController.php`
- `app/Controllers/Admin/KbiitLaekController.php`
- `app/Controllers/Admin/DashboardController.php`

Rekomendasi:

- Jangan join by name dan group by manual.
- Ambil latest approved pedidu lewat subquery yang memilih `MAX(id_pedidu)` per `id_populasaun`.
- Atau gunakan `DISTINCT` dengan select kolom yang eksplisit, bukan `tabela_populasaun.*`.

### 2. Ada error koneksi database di log

Log `2026-05-25` menunjukkan `Access denied for user ''@'localhost'`. Ini menandakan konfigurasi environment pernah kosong/tidak terbaca.

Rekomendasi:

- Pastikan `.env` production memakai variabel DB lengkap.
- Jangan bergantung pada default kosong di `app/Config/Database.php`.
- Tambahkan health check DB.

### 3. Warning route lowercase dari Boilerplate

Log berulang memperlihatkan warning deprecation karena route memakai method lowercase `get`/`post` di package boilerplate. Di CodeIgniter 4.7, ini sudah deprecated.

Rekomendasi:

- Ubah route method array menjadi uppercase `GET`, `POST`.
- Atau update package jika upstream sudah memperbaiki.

### 4. Side effect terjadi di constructor controller

Contoh:

- `PopulasaunController::__construct()` menjalankan update data `Mene -> Mane`.
- `RelatoriuController::__construct()` menghapus template `Relatoriu Maternidade`.

Constructor sebaiknya tidak mengubah database setiap request. Ini membuat GET biasa punya efek samping.

Rekomendasi:

- Pindahkan cleanup ke migration/seeder/command maintenance.
- Controller constructor hanya inisialisasi dependency.

## Temuan Security Tambahan

### 1. Public registration masih aktif

File: `app/Config/Auth.php`

`allowRegistration = true`. Jika sistem ini untuk internal administrasi suku, registrasi publik sebaiknya dimatikan dan user dibuat oleh admin/struktur.

Rekomendasi:

- Set `allowRegistration = false` untuk production.
- Gunakan modul `EstruturaSukuController::createUser()` atau user management admin untuk membuat akun.

### 2. Environment masih development

File: `.env`

`CI_ENVIRONMENT = development`. Untuk production, ini harus `production` agar error detail tidak bocor dan security behavior lebih ketat.

Rekomendasi:

- Production harus memakai `CI_ENVIRONMENT = production`.
- Matikan toolbar/debugbar di production.

### 3. Upload foto belum tervalidasi ketat

`FamiliaController::uploadFoto()` menerima file image dan memindahkan ke public uploads. View memakai `accept="image/*"`, tetapi server-side belum terlihat membatasi mime, ukuran, dan dimensi.

Rekomendasi:

- Tambahkan validasi server-side `uploaded`, `is_image`, `mime_in`, `max_size`, dan mungkin `max_dims`.
- Simpan file di lokasi aman dan hanya expose path yang diperlukan.

### 4. Operasi hapus anggota keluarga memakai GET

Route `familia/(:num)/remove/(:num)` memakai GET. Ini operasi ubah data dan seharusnya tidak GET.

Rekomendasi:

- Ubah menjadi POST/DELETE.
- Wajib CSRF.
- Validasi bahwa anggota benar-benar milik keluarga target.

## Kesesuaian Untuk Sistem Manajemen Populasi

Secara fitur, SIPOLAI sudah sesuai untuk kebutuhan dasar:

- Registrasi penduduk.
- Pengelompokan penduduk ke keluarga.
- Pembagian berdasarkan aldeia.
- Status hidup/pindah/meninggal.
- Surat/deklarasi administratif.
- Laporan demografi.
- Struktur pemerintahan lokal.
- Role admin, xefe-suku, xefe-aldeia, sekretaria.

Namun untuk sistem manajemen populasi yang benar-benar dipakai, ada kebutuhan yang belum kuat:

- Audit trail perubahan data.
- Identitas berbasis ID, bukan nama.
- Riwayat status penduduk yang lengkap.
- Integritas database dengan foreign key/index.
- Otorisasi server-side per role dan per aldeia.
- Workflow pembatalan/koreksi dokumen.
- Validasi dokumen resmi dan nomor kartu.
- Test otomatis untuk alur penting.

Jadi jawaban singkatnya: sistem sudah cocok sebagai fondasi, tetapi logic belum sepenuhnya berjalan "semestinya" untuk skenario nyata yang kompleks.

## Prioritas Perbaikan

### P0 - Harus diperbaiki sebelum production

1. Hapus `/force-reset-admin`.
2. Rotasi kredensial admin dan hapus hardcoded password dari migration/README production.
3. Aktifkan CSRF global dan ubah operasi GET yang mengubah data menjadi POST/DELETE.
4. Tambahkan server-side authorization untuk semua endpoint tulis.
5. Pastikan xefe-aldeia tidak bisa membaca/mengubah record di luar aldeia sendiri lewat request manual.
6. Perbaiki query eleitores/kbiit yang error di `ONLY_FULL_GROUP_BY`.
7. Nonaktifkan public registration untuk production.
8. Set environment production dengan konfigurasi DB valid.

### P1 - Penting untuk data benar

1. Tambahkan `id_populasaun` ke `tabela_pedidu`.
2. Refactor logic pedidu agar tidak lagi join/mencari berdasarkan nama lengkap.
3. Bungkus approval pedidu dalam transaction.
4. Terapkan state machine pedidu agar approved tidak bisa diubah sembarangan.
5. Tambahkan audit log untuk status penduduk, pedidu, keluarga, dan nomor kartu.
6. Tambahkan foreign key dan index utama.
7. Tambahkan unique constraint untuk `no_eleitoral` dan `no_kbiit_laek`.
8. Perkuat validasi satu xefe familia per keluarga.

### P2 - Peningkatan kualitas dan maintainability

1. Pindahkan cleanup data dari constructor ke migration/command.
2. Kurangi query N+1 di dashboard dan laporan dengan aggregate query.
3. Rapikan encoding UTF-8 di README/view/template yang masih mojibake.
4. Tambahkan test otomatis untuk workflow utama.
5. Buat dokumentasi role-permission yang eksplisit.
6. Rapikan migration timestamp yang tidak valid untuk file baru ke depan.

## Test Yang Perlu Dibuat

Minimal test yang disarankan:

1. Pedidu mortalidade approved mengubah penduduk target menjadi `Mate`.
2. Pedidu muda domisiliu approved mengubah penduduk target menjadi `Muda`.
3. Pedidu nascimentu approved membuat penduduk baru tepat di keluarga target.
4. Duplicate nama penduduk tidak menyebabkan pedidu mengubah orang yang salah.
5. Xefe-aldeia tidak bisa membuat/mengubah/menghapus data di luar aldeia.
6. Pedidu approved tidak bisa diubah kembali tanpa workflow cancel.
7. Inventoriu snapshot tetap menyimpan data lama walaupun penduduk berubah.
8. Satu familia tidak bisa punya dua `Xefe Familia`.
9. Nomor eleitoral/kbiit laek tidak bisa duplicate.
10. Endpoint delete/update gagal jika CSRF/token/role tidak valid.

## Rekomendasi Desain Data Baru Untuk Pedidu

Struktur minimal yang lebih aman:

```text
tabela_pedidu
- id_pedidu
- id_populasaun nullable
- id_familia nullable
- naran_pedidu
- pemohon_snapshot
- data_pedidu
- status
- id_aldeia
- meta_data
- approved_by
- approved_at
- rejected_by
- rejected_at
- voided_by
- voided_at
- void_reason
```

Untuk nascimentu:

```text
meta_data
- naran_labarik
- jeneru
- fatin_moris
- data_moris
- id_relijiaun
- id_profisaun
- id_literatura
- nik
- id_populasaun_created
```

Dengan pola ini, logic tetap bisa mencetak snapshot nama, tetapi operasi database memakai ID.

## Rekomendasi State Machine Pedidu

Status yang lebih aman:

```text
Pendiente -> Aprovadu
Pendiente -> Rezeitadu
Aprovadu -> Voided/Kanseladu melalui aksi koreksi khusus
Rezeitadu -> tidak bisa approve ulang kecuali dibuat pedidu baru
```

Efek samping hanya boleh terjadi saat transisi pertama ke `Aprovadu`. Jika ada pembatalan, sistem harus membuat event koreksi, bukan sekadar mengganti status.

## Catatan Akhir

SIPOLAI punya arah produk yang bagus dan domain fit-nya kuat. Bagian yang perlu dibenahi bukan terutama "fitur kurang", melainkan aturan inti agar data resmi tidak mudah rusak:

- Identitas harus berbasis ID.
- Semua perubahan penting harus punya otorisasi dan audit.
- Approval dokumen harus atomic.
- Laporan harus membaca sumber data yang konsisten.
- Endpoint server harus aman meskipun UI disembunyikan.

Jika prioritas P0 dan P1 selesai, sistem ini akan jauh lebih layak sebagai sistem manajemen populasi yang stabil.

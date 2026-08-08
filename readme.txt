====================================================================
PANDUAN DEPLOYMENT & SETTING DOCKER DI VPS - MONITORING HEALTH KID
====================================================================

Dokumen ini berisi langkah-langkah untuk melakukan instalasi dan setup
aplikasi Monitoring Health Kid (Laravel) menggunakan Docker di dalam 
lingkungan produksi (VPS).

--------------------------------------------------------------------
1. PERSIAPAN DI VPS
--------------------------------------------------------------------
Pastikan VPS Anda sudah terpasang Docker dan Docker Compose.
Jika belum, install dengan perintah (Ubuntu/Debian):
$ sudo apt update
$ sudo apt install docker.io docker-compose -y
$ sudo systemctl enable docker
$ sudo systemctl start docker

--------------------------------------------------------------------
2. CLONE & KONFIGURASI ENV
--------------------------------------------------------------------
- Clone repository ke VPS Anda:
  $ git clone https://github.com/arkawidyasoftwaretechnologies-hash/monitoring_health_kid.git
  $ cd monitoring_health_kid

- Salin file environment:
  $ cp .env.example .env

- Sesuaikan file .env Anda (khususnya untuk Docker):
  DB_CONNECTION=mysql
  DB_HOST=db       <-- (Harus 'db' menyesuaikan nama service di docker-compose.yml)
  DB_PORT=3306
  DB_DATABASE=monitoring_stunting
  DB_USERNAME=root
  DB_PASSWORD=secret

--------------------------------------------------------------------
3. BUILD & JALANKAN CONTAINER
--------------------------------------------------------------------
- Build dan jalankan container di background (detached mode):
  $ docker-compose up -d --build

- Verifikasi container yang berjalan (pastikan app dan db berstatus Up):
  $ docker-compose ps

--------------------------------------------------------------------
4. INSTALASI & MIGRASI DATABASE (INSIDE DOCKER)
--------------------------------------------------------------------
Masuk ke dalam container aplikasi Laravel Anda untuk menjalankan
perintah artisan:
$ docker-compose exec app bash

*(Atau jalankan perintah langsung tanpa masuk ke shell container)*

Langkah A: Install Dependensi & Generate Key
$ docker-compose exec app composer install --optimize-autoloader --no-dev
$ docker-compose exec app php artisan key:generate

Langkah B: Migrasi Skema Tabel (Otomatis membuat seluruh tabel)
$ docker-compose exec app php artisan migrate:fresh

Langkah C: Import Data Referensi Medis WHO & WHZ (WAJIB)
Jalankan seeder berikut agar kalkulator Z-Score berfungsi dengan akurat:
$ docker-compose exec app php artisan db:seed --class=FullWhoReferenceSeeder
$ docker-compose exec app php artisan db:seed --class=WhoWhzReferenceSeeder

Langkah D: Import Engine Rekomendasi Klinis (WAJIB)
Jalankan seeder ini untuk mengaktifkan draft otomatis assessment dokter:
$ docker-compose exec app php artisan db:seed --class=TemplateRekomendasiSeeder

Langkah E: Import Data Dummy & User (Untuk Testing/Demo)
Jalankan seeder ini untuk memunculkan riwayat grafik dan 10 anak sample:
$ docker-compose exec app php artisan db:seed --class=DummyDataSeeder

--------------------------------------------------------------------
5. UPDATE DATA / KALIBRASI (JIKA ADA PERUBAHAN TABEL WHO)
--------------------------------------------------------------------
Jika di masa depan Anda melakukan pembaruan pada tabel referensi WHO
atau mengubah aturan peringatan (Red Flag), Anda WAJIB menjalankan
skrip kalibrasi agar seluruh riwayat histori pasien lama disesuaikan:
$ docker-compose exec app php artisan app:recalculate-zscore

--------------------------------------------------------------------
6. OPTIMASI & PERMISSION (PRODUCTION)
--------------------------------------------------------------------
Jalankan optimasi cache agar performa aplikasi maksimal di VPS:
$ docker-compose exec app php artisan config:cache
$ docker-compose exec app php artisan route:cache
$ docker-compose exec app php artisan view:cache

Berikan hak akses pada folder storage agar aplikasi bisa menyimpan file:
$ docker-compose exec app chmod -R 777 storage bootstrap/cache

--------------------------------------------------------------------
SELESAI!
--------------------------------------------------------------------
Aplikasi Monitoring Health Kid Anda sekarang sudah berjalan di VPS.
Silakan akses IP Publik VPS Anda melalui browser:
http://<IP_VPS_ANDA>:8000 (Atau sesuaikan dengan port di docker-compose)

Gunakan akun login yang di-generate dari DummyDataSeeder untuk masuk.

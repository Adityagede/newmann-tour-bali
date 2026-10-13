# Perlindungan alur booking

Perubahan ini hanya mencakup Custom Trip, Tour Booking Request, availability, dan pengiriman notifikasi admin. Susunan UI tetap memakai komponen yang ada. Tombol submit dinonaktifkan selama pengiriman; pesan pembatasan muncul di area error yang sudah ada.

## Perilaku

- Setiap formulir baru mendapat token tersembunyi. Token yang sama dalam sesi yang sama menghasilkan satu order dan satu antrean notifikasi, termasuk saat dua submit berlomba. Token baru tetap bisa membuat order baru dengan detail yang sama.
- Token tidak menggantikan CSRF. Pembatasan berjalan per sesi dengan batas IP yang lebih luas untuk tamu yang berbagi jaringan. Bahasa Indonesia dan Inggris memakai kuota yang sama.
- Custom Trip success hanya tersedia bagi sesi pembuat selama 120 menit secara default. Mengetahui kode booking saja tidak memberikan akses. Link dari sesi lain, sesi kedaluwarsa, dan link lama tanpa izin sesi menghasilkan 404. Data order tetap ada di admin.
- Order tersimpan sebelum proses email. Email masuk antrean database khusus `booking-notifications`; batas email hanya menunda pengiriman, tanpa membatalkan order yang sudah diterima.
- Enqueue yang gagal dapat dipulihkan. Kegagalan SMTP dicoba ulang dengan jeda, maksimal lima exception sebelum masuk `failed_jobs`. Pelepasan job karena batas frekuensi tidak menghabiskan jatah exception. Job yang sudah tercatat terkirim dilewati saat diproses ulang.
- Seperti pengiriman SMTP pada umumnya, pengiriman tepat sekali tidak dapat dijamin jika server email sudah menerima pesan lalu proses berhenti sebelum penanda terkirim disimpan. Perlindungan ini mencegah duplikasi akibat submit ulang, enqueue ulang normal, dan pemrosesan ulang job yang telah selesai.
- Order lama tidak otomatis dikirim ulang. Kolom baru bersifat nullable atau memiliki nilai default `false`.

## Nilai awal

| Pengaturan `.env` | Default | Efek |
| --- | ---: | --- |
| `BOOKING_SESSION_PER_MINUTE` | 10 | Custom Trip dan Tour Booking berbagi batas per sesi |
| `BOOKING_IP_PER_MINUTE` | 300 | Batas gabungan booking per IP |
| `AVAILABILITY_SESSION_PER_MINUTE` | 60 | Availability dan review berbagi batas per sesi |
| `AVAILABILITY_IP_PER_MINUTE` | 1200 | Batas availability/review per IP |
| `BOOKING_NOTIFICATION_EMAILS_PER_MINUTE` | 20 | Kecepatan pengiriman email admin |
| `CUSTOM_TRIP_SUCCESS_ACCESS_MINUTES` | 120 | Lama akses halaman sukses Custom Trip |
| `MAIL_TIMEOUT` | 20 | Timeout koneksi SMTP dalam detik |

Tidak ada batas total order untuk seluruh pelanggan. Batas IP tetap dapat tercapai pada lonjakan besar dari jaringan yang sama; sesuaikan berdasarkan trafik nyata. Nilai email harus mengikuti kuota SMTP hosting. Antrean dapat menumpuk apabila jumlah email masuk melebihi kapasitas worker/cron.

## Aktivasi di hosting

### GitHub Actions untuk produksi Newman

Workflow **Test cPanel SSH Connection** membaca versi PHP, cache, status migration, tabel antrean, dan keberadaan cron tanpa mengubah data pelanggan atau menampilkan kredensial. Jalankan pemeriksaan ini sebelum deploy.

Workflow **Deploy Newman Production** hanya menerima branch `main`. Workflow membuat backup kode, `.env`, crontab, serta dump database terkompresi di direktori privat `$HOME/backups/newman-production-TIMESTAMP` dengan permission direktori `700`. Website masuk maintenance selama backup database, penggantian kode, migration, dan validasi. Credential MySQL sementara memakai file privat, tidak dikirim melalui argumen password, dan dibersihkan setelah backup atau saat kegagalan.

Hanya migration `2026_10_13_100000_add_booking_submission_protection.php` yang dijalankan. Tabel pendukung harus sudah tersedia, konfigurasi cache harus persisten dan mendukung lock, serta recipient/SMTP harus terpasang. Ketidaksesuaian antara kolom booking dan catatan migration menghentikan deploy untuk pemeriksaan manual. Workflow memeriksa jumlah order lama sebelum/sesudah migration dan memperbaiki nilai `APP_URL` produksi yang sebelumnya salah (`http:https://newmantourbali.com`) menjadi `https://newmantourbali.com`.

Input `booking_cron_minutes` memilih interval `5` (default) atau `1`. Cron memakai binary PHP aktif yang sudah diperiksa dan command `booking-notifications:work --max-time=50`; cron yang sudah ada dipertahankan. Input ini menentukan interval saat cron pertama kali dibuat. Mengubah interval cron yang sudah terpasang dilakukan melalui cPanel. Log berada di `$HOME/booking-notifications-cron.log`, di luar folder publik dengan permission `600`.

Jika deploy gagal, workflow memulihkan kode, `.env`, dan crontab yang diubah serta mencoba membuka kembali website setelah pemulihan berhasil. Kegagalan pemulihan memerlukan pemeriksaan manual. Perubahan kolom tambahan tidak dibatalkan otomatis, dan dump database tidak di-restore otomatis agar tidak menimpa order. Backup privat disimpan untuk pemulihan manual. Workflow membangun ulang cache konfigurasi/Blade dan me-restart worker sebelum membuka website.

Workflow SSH setelah deploy juga melaporkan jumlah job booking yang antre/gagal dan jumlah eksekusi pemulihan dari log cron. Status cron terpasang belum membuktikan email terkirim; verifikasi eksekusi cron dan lakukan booking uji yang disepakati untuk memastikan penerimaan email.

### Aktivasi manual jika menggunakan mekanisme deploy lain

1. Buat backup database melalui fasilitas hosting sebelum deploy. Pasang perubahan dalam maintenance window agar kode baru tidak mengakses kolom yang belum tersedia.
2. Jalankan migration tambahan ini setelah tabel aplikasi, `jobs`, `failed_jobs`, `cache`, dan `cache_locks` tersedia:

   ```sh
   php artisan migrate --path=database/migrations/2026_10_13_100000_add_booking_submission_protection.php --force
   ```

3. Isi `BOOKING_NOTIFICATION_EMAIL` dengan alamat admin dan pastikan SMTP berfungsi. Nama lama `NEWMAN_BOOKING_NOTIFICATION_EMAIL` tetap diterima jika pengaturan baru kosong. Gunakan `CACHE_STORE=database` atau penyimpanan persisten lain yang mendukung atomic lock. Hindari `array` di hosting: kuota dan lock harus bertahan antar-request/proses. Koneksi antrean khusus memakai database utama dan tabel `jobs`, sehingga insert job dan penanda antrean berada dalam transaksi yang sama.
4. Bangun ulang konfigurasi dan Blade setelah migration:

   ```sh
   php artisan config:cache
   php artisan view:cache
   ```

5. Aktifkan salah satu cara pemrosesan email berikut sebelum website dibuka kembali.

### Rumahweb shared hosting

Rumahweb mendokumentasikan [Cron Jobs di cPanel](https://www.rumahweb.com/journal/panduan-setting-cron-jobs-di-cpanel/). Ketersediaan daemon queue worker dan interval minimum paket **Unlimited M** belum dikonfirmasi. Periksa opsi interval pada cPanel atau tanyakan dukungan Rumahweb; jangan mengasumsikan setiap satu menit diperbolehkan.

Isi perintah Cron Jobs dengan PHP CLI dan path proyek yang benar dari hosting:

```sh
/PATH/KE/PHP-CLI /home/USERNAME/PATH-PROYEK/artisan booking-notifications:work --max-time=50 >> /home/USERNAME/booking-notifications-cron.log 2>&1
```

Command memulihkan kegagalan enqueue lalu mengerjakan antrean notifikasi saja. Lock mencegah dua pemanggilan cron berjalan bersamaan. Worker berhenti saat antrean kosong atau setelah batas waktu di antara job; satu job yang sedang berjalan dapat membuat durasi total melebihi 50 detik. PHP CLI harus memenuhi versi proyek (PHP 8.3+) dan batas proses hosting harus cukup untuk satu job. Jangan arahkan log ke folder publik.

Email mulai dikirim ketika cron berjalan, sehingga interval cron menentukan penundaan minimum. Jika interval yang diizinkan terlalu jarang atau volume order tinggi, diperlukan dukungan worker yang berjalan terus atau penyesuaian kapasitas pengiriman. Order tetap tersimpan di admin selama email menunggu.

### Hosting dengan worker terus berjalan / pengembangan lokal

Gunakan process manager hosting atau terminal lokal:

```sh
php artisan queue:work booking-notifications --queue=booking-notifications --tries=0 --timeout=45
```

`composer dev` yang memakai antrean default tidak mengerjakan antrean notifikasi khusus ini. Jalankan command di atas pada terminal lain atau gunakan command cron yang terbatas. Restart worker saat deploy kode baru:

```sh
php artisan queue:restart
```

## Pemeriksaan dan pemulihan

Lakukan satu booking uji dan periksa order di admin, lalu jalankan worker/cron dan pastikan email diterima. Periksa juga submit ulang: referensinya tetap sama dan tidak menghasilkan order tambahan.

```sh
php artisan booking-notifications:dispatch
php artisan queue:failed
php artisan queue:retry ID-JOB-YANG-GAGAL
```

Command `dispatch` hanya menangani order dengan `admin_notification_pending = true` dan `admin_notification_queued_at = null`. Ia tidak menggandakan job yang telah masuk antrean. Setelah memperbaiki SMTP, gunakan `queue:retry` untuk job yang tercatat gagal. Jangan menghapus tabel/isi antrean atau semua failed jobs sebagai cara memperbaiki email.

Penanda `admin_notification_pending`, `admin_notification_queued_at`, dan `admin_notified_at` membantu membedakan menunggu enqueue, menunggu pengiriman, serta sudah terkirim. Jika recipient kosong saat order dibuat, order tetap tersimpan dan notifikasi tidak diaktifkan untuk order tersebut, sama seperti perilaku konfigurasi sebelumnya.

Jika website berada di belakang CDN/proxy, pastikan konfigurasi trusted proxy membuat IP pengunjung terbaca benar sebelum menilai batas IP. Jangan mempercayai header IP dari semua sumber secara bebas.

## Verifikasi lokal

Tes memakai SQLite terisolasi dan notifikasi palsu. Cakupan meliputi kepemilikan halaman sukses, token replay/detail berubah, pesanan baru yang disengaja, kuota sesi/IP/bahasa, availability/review, rollback enqueue, kegagalan SMTP, pemulihan, penundaan email, cron overlap, serta dua proses PHP yang melakukan submit bersamaan. Pengujian SQLite tidak menggantikan smoke test pada MySQL dan konfigurasi hosting sebenarnya.

Hasil pemeriksaan perubahan ini: 78 tes lulus dengan 442 assertion, build Vite berhasil, Blade berhasil dikompilasi, dan pemeriksaan sintaks PHP serta `git diff --check` berhasil. Tiga tes lama gagal pada source HEAD asli juga: `Feature/ExampleTest` tidak menyiapkan tabel database, dan dua tes `TourVerifiedStatisticsTest` memakai fixture active tour yang tidak memenuhi aturan publishing. File tes lama tersebut tidak diubah.

Migration tambahan sudah diterapkan pada MySQL **lokal** setelah backup terenkripsi dua tabel dibuat di `storage/app/private/booking-protection-backups/`. Jumlah order lama terverifikasi tetap sama. Smoke test MySQL lokal untuk submit ulang kedua flow, form review, kepemilikan halaman sukses, dan insert antrean juga berhasil; seluruh baris sintetis di-rollback dan tidak ada email nyata dikirim. Aktivasi cron/worker, migration, dan smoke test pada hosting Rumahweb masih perlu dilakukan saat deploy.

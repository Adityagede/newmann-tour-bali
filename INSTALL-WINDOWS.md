# Instalasi Windows / Laragon

1. Salin `.env` milik project utama ke root folder ini.
2. Buka PowerShell di root project.
3. Jalankan:

```powershell
composer install
npm install
php artisan optimize:clear
php artisan storage:link
php artisan serve
```

Tidak perlu menjalankan migration untuk update Step 17g-c karena struktur database tidak diubah.

Cek file inti sebelum menjalankan Artisan:

```powershell
Test-Path .\artisan
Test-Path .\bootstrap\app.php
Test-Path .\app
Test-Path .\resources
```

Semua hasil harus `True`.

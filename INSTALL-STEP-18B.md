# Step 18B — Custom Trip V2 Patch

This patch upgrades only the Custom Trip experience. It does not change routes, migrations, database tables, Tour Package V2, or `.env`.

## 1. Backup the current files

From the project root in PowerShell:

```powershell
$backup = ".\backups\step-18b-before-custom-trip-v2"
New-Item -ItemType Directory -Force $backup | Out-Null

Copy-Item .\app\Http\Controllers\CustomTripRequestController.php $backup
Copy-Item .\app\Models\CustomTripRequest.php $backup
Copy-Item .\app\Notifications\NewCustomTripRequestNotification.php $backup
Copy-Item .\resources\views\pages\custom-trip.blade.php $backup
Copy-Item .\resources\views\pages\custom-trip-success.blade.php $backup
Copy-Item .\resources\views\admin\custom-trip-requests\index.blade.php $backup
Copy-Item .\resources\views\admin\custom-trip-requests\show.blade.php $backup
Copy-Item .\resources\views\pages\home.blade.php $backup
```

## 2. Copy the patch files

Copy the `app`, `resources`, and `tests` folders from this patch into the root of the Newman project. Allow Windows to replace files with matching names.

Do not copy the `patches` folder into application source folders. It only contains the optional Home diff.

## 3. Update the Home vehicle section

The full `home.blade.php` is deliberately not included because the main project may already contain Step 18A edits.

Preferred method when Git is available:

```powershell
git apply --check .\patches\home-vehicle-section.diff
git apply .\patches\home-vehicle-section.diff
```

If the check fails, open `patches/home-vehicle-section.diff` and make only those vehicle-section wording/capacity changes manually.

## 4. Refresh autoload and frontend assets

```powershell
composer dump-autoload
npm install
npm run build
php artisan optimize:clear
```

`npm install` only needs to be repeated when `node_modules` is missing or Vite is unavailable.

## 5. No migration

Do not run a new migration for this patch. It continues using the existing `bookings` table for Custom Trips.

Read-only database check:

```powershell
php artisan migrate:status
```

## 6. Test the flow

1. Open `/custom-trip`.
2. Enter a group size and trip plan.
3. Confirm the suggested vehicle changes at 1–5, 6–12, and 13+ guests.
4. Choose a vehicle preference.
5. Enter contact details.
6. Review and submit.
7. Confirm the success page shows the reference and Pending status.
8. Open Admin → Custom Trips.
9. Confirm Vehicle Preference and Suggested Fit appear separately.
10. Confirm the SMTP email links to the Custom Trip detail.

Optional automated test:

```powershell
php artisan test --filter=CustomTripRequestV2Test
```

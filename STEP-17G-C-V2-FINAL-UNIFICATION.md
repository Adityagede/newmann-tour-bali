# Newman Tour Guide — Step 17g-c V2 Final Unification

This package is built directly from the original project archive supplied before Step 17g-b. It already includes the Step 17g-b admin/dashboard/SMTP work, so the previous ZIP does not need to be installed first.

## Canonical flows

### Tour Packages

Tour Packages now use only the V2 flow:

1. Tour detail
2. Availability
3. Tour Option and starting time
4. Review
5. Tour Booking Request V2
6. V2 success page
7. SMTP V2 notification
8. Admin `/admin/bookings`

The old package booking controller, model name, notification, views, and admin routes have been removed from active code.

### Custom Trips

Custom itinerary enquiries remain available because there is no Tour Option V2 equivalent for them. They are explicitly separated as Custom Trip Requests:

- Website: `/custom-trip`
- Admin: `/admin/custom-trip-requests`
- SMTP: `NewCustomTripRequestNotification`

They continue to use the existing `bookings` table through the `CustomTripRequest` model. This avoids any database migration or destructive table change.

## Database safety

- No migration was added, removed, or edited.
- No database table was dropped or renamed.
- `tour_booking_requests` remains the only active source for Tour Package V2 bookings.
- The existing `bookings` table is retained only for Custom Trip Requests and historical data.
- Historical package rows in `bookings` are preserved but are not exposed as an active package-booking flow.

## SMTP

Both active request flows use the existing Laravel mail settings and:

- `BOOKING_NOTIFICATION_EMAIL`
- `MAIL_MAILER`
- `MAIL_HOST`
- `MAIL_PORT`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS`
- `MAIL_FROM_NAME`

No `.env` file or credential is included.

## Important files added

- `app/Models/CustomTripRequest.php`
- `app/Http/Controllers/CustomTripRequestController.php`
- `app/Http/Controllers/Admin/AdminCustomTripRequestController.php`
- `app/Notifications/NewTourBookingRequestNotification.php`
- `app/Notifications/NewCustomTripRequestNotification.php`
- `resources/views/pages/custom-trip.blade.php`
- `resources/views/pages/custom-trip-success.blade.php`
- `resources/views/admin/custom-trip-requests/index.blade.php`
- `resources/views/admin/custom-trip-requests/show.blade.php`
- `tests/Feature/BookingFlowRoutingTest.php`

## Active old-package files removed

- `app/Http/Controllers/BookingController.php`
- `app/Http/Controllers/Admin/AdminBookingController.php`
- `app/Models/Booking.php`
- `app/Notifications/NewBookingNotification.php`
- `resources/views/pages/booking.blade.php`
- `resources/views/pages/booking-success.blade.php`
- `resources/views/admin/bookings/`
- old route/source backup copies that could cause confusion

## Installation into the main project

1. Back up the current main project and database.
2. Keep the main project's real `.env`; do not replace it.
3. Keep any newer uploaded files in `storage/app/public` if the live project contains uploads newer than this archive.
4. Replace the project code with this package.
5. Run:

```powershell
composer dump-autoload
php artisan optimize:clear
php artisan route:list --name=admin
```

No `php artisan migrate` command is required for this step.

## Expected routes

```text
GET|HEAD  admin                                 admin.dashboard
GET|HEAD  admin/bookings                        admin.tour-booking-requests.index
GET|HEAD  admin/bookings/{tourBookingRequest}   admin.tour-booking-requests.show
PATCH     admin/bookings/{tourBookingRequest}/status
GET|HEAD  admin/custom-trip-requests
GET|HEAD  custom-trip
POST      custom-trip
POST      tours/{slug}/booking-request
```

The old GET `/booking` URL is retained only as a permanent redirect to `/custom-trip`; no old booking logic remains behind it.

## Verification performed

- PHP syntax checked for `app`, `routes`, migrations, and the new test file.
- Route list loaded successfully.
- No duplicate route names.
- No duplicate method/URI combinations.
- Changed Blade files compiled and their compiled PHP passed syntax checks.
- V2 and Custom Trip notification subjects and admin action URLs were rendered.
- Public `/custom-trip` returned HTTP 200 in a temporary test environment.
- Old `/booking` returned HTTP 301 to `/custom-trip`.
- Database directory was verified unchanged against the supplied original archive.

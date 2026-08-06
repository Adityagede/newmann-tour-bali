# Step 18A — Tour Package V2 Unification

## Product boundary
- No payment gateway.
- No online checkout or automatic voucher.
- Final public action remains a Booking Request.
- Booking status starts as Pending and is confirmed manually by Newman.

## Changes
1. Public tour cards use the Adult price from the default active Tour Option.
2. Home and Tours eager-load the default option and prices to prevent N+1 queries.
3. Availability pricing uses the selected travel date, so date-based discounts stay consistent through review and submission.
4. Public availability UI reads the canonical top-level `options` response.
5. Obsolete Step 17F text was replaced with the actual booking-request confirmation flow.
6. Admin dashboard includes Tour Package health counters:
   - Active products
   - Draft products
   - Inactive products
   - Products without a default option
   - Active options without allowed participant prices
   - Active options without active schedules
7. Admin dashboard refreshes every 15 seconds while open.

## Database safety
- No migrations added.
- No migrations edited.
- No database tables or columns changed.
- Existing `.env` remains external to this package.

## Install
```powershell
composer install
php artisan optimize:clear
php artisan migrate:status
php artisan serve
```

Do not run `php artisan migrate` for this step because no schema changes are required.

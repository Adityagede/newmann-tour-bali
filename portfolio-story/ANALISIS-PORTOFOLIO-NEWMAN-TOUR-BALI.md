# Laporan Analisis Portofolio — Newman Tour Bali

**Tanggal snapshot audit:** 17 Agustus 2026  
**Jenis produk:** full-stack travel platform dan booking-request operations system  
**Arsitektur:** Laravel server-rendered monolith dengan MVC, service layer, Eloquent ORM, Blade, dan progressive enhancement.

## Ringkasan

Newman Tour Bali bukan hanya website promosi. Sistem ini menyatukan pengalaman pencarian tur, detail itinerary, pemeriksaan availability, kalkulasi harga, booking request, Custom Trip Builder, verified rating, notifikasi admin, dan CMS operasional dalam satu aplikasi Laravel.

Posisi yang cocok untuk portofolio:

> A full-stack Bali travel platform combining a responsive guest experience, booking-request engine, pricing and availability rules, verified reviews, and an operations CMS in one Laravel application.

## Statistik engineering

| Metrik | Jumlah |
|---|---:|
| Application routes | **96** |
| Public routes | **19** |
| Protected admin routes | **77** |
| Controllers | **22** |
| Admin controllers | **15** |
| Eloquent models | **13** |
| Business services | **4** |
| Form Requests | **14** |
| Migration files | **17** |
| Database tables | **20** |
| Blade files | **68** |
| Reusable Blade components | **10** |
| Authored test methods | **48** |
| Static assert calls | **223** |
| Image assets | **51** |
| Application source lines | **43.997 / ≈44K** |
| Source lines termasuk tests | **46.000** |

Statistik source code tidak menghitung `vendor`, `node_modules`, storage hasil runtime, atau generated build assets.

## Tech stack

### Backend

- PHP **8.3.30**.
- Laravel Framework **13.19.0**.
- Eloquent ORM.
- MySQL **8.4.3**.
- Laravel Blade server-side rendering.
- MVC dengan service layer dan model observers.
- Form Request validation.
- Laravel Notifications dan SMTP mail.
- Database sessions, cache, serta queue driver.

### Frontend

- Tailwind CSS **4.3.2**.
- Alpine.js **3.15.12**.
- Swiper **14.0.2**.
- AOS **2.3.4**.
- Vite **8.1.3**.
- Laravel Vite Plugin **3.1.0**.
- Custom responsive CSS dan reusable Blade components.

### Development dan quality tooling

- PHPUnit **12.5.31**.
- Laravel Pint **1.29.3**.
- Laravel Pao **1.1.2**.
- FakerPHP **1.24.1**.
- Mockery **1.6.12**.
- Collision **8.9.4**.
- Composer dan npm workflows.

## Ekosistem dan integrasi

- Leaflet **1.9.4** untuk peta interaktif.
- OpenStreetMap sebagai tile provider.
- Google Maps embed.
- WhatsApp deep links untuk conversion dan follow-up.
- Tripadvisor, Instagram, dan Facebook links.
- SMTP untuk notifikasi booking kepada admin.
- Public/private filesystem storage.
- Dynamic sitemap dan `robots.txt`.
- Canonical URL dan meta descriptions.
- Locale switch English/Indonesian pada navigasi.
- Timezone aplikasi: Asia/Makassar.

## Fitur publik

### Homepage

- Hero carousel tiga slide.
- Popular tours.
- Service marquee.
- About/guide section.
- Vehicle offering.
- Penjelasan booking dalam tiga langkah.
- Reasons/advantages section.
- Reviews.
- Gallery moments.
- FAQ.
- CTA, footer, dan floating WhatsApp button.

### Tour catalogue dan detail

- Tour catalogue dengan category filtering.
- Promotional price display.
- Verified rating statistics.
- Galeri lima foto dan modal viewer.
- Intro, highlights, description, dan trip story.
- Included dan excluded items.
- Roadmap/itinerary stops.
- Leaflet + OpenStreetMap interactive route map.
- Mobile sticky booking bar.

### Package booking request

- Pemilihan tanggal.
- Adult, child, dan infant counts.
- Pemilihan bahasa.
- Active tour option selection.
- Schedule dan departure-time validation.
- Availability window.
- Booking cutoff.
- Whole-day dan time-slot blackout.
- Capacity checks.
- Live price breakdown.
- Discount application.
- Vehicle-fit recommendation.
- Review-before-submit.
- Unique reference `NWM-yymmdd-XXXXXX`.
- Session-gated success page.
- Email notification kepada admin.

### Custom Trip Builder V2

Flow empat langkah:

1. Plan.
2. Vehicle.
3. Contact.
4. Review.

Sistem memberikan rekomendasi kendaraan berdasarkan ukuran grup:

- 1–5 tamu: Avanza/private car class.
- 6–12 tamu: Hiace/van class.
- 13+ tamu: kendaraan lebih besar atau kombinasi kendaraan.

Pilihan kendaraan yang tidak sesuai recommendation ditandai untuk manual review. Custom Trip memperoleh reference dengan prefix `NWM-CT-`.

### Verified rating

- Hanya tersedia bagi package booking berstatus completed.
- Menggunakan signed URL.
- Maksimal satu rating per booking.
- Rate limit 10 request per menit.
- Transaction dan row lock digunakan untuk mencegah duplicate submission.

## Business logic

### Availability engine

`TourAvailabilityService` menangani:

- Tour harus aktif.
- Tanggal tidak boleh berada di masa lalu.
- Minimal satu participant.
- Language support.
- Active option filtering.
- Day-of-week schedule.
- `available_from` dan `available_to`.
- Booking cutoff berdasarkan departure time.
- Whole-day dan time-slot blackout.
- Option dan schedule capacity limits.
- Infant ikut dihitung terhadap occupancy.
- Alasan unavailable dikirim kembali ke UI.

Kapasitas saat ini masih berdasarkan konfigurasi option/schedule. Reserved booking requests belum otomatis dikurangkan dari kapasitas.

### Pricing engine

`TourPricingService` menangani:

- Harga adult, child, dan infant.
- Participant category gratis atau tidak diizinkan.
- Min/max participant validation.
- Currency consistency.
- Fixed dan percentage discounts.
- Participant-scoped promotions.
- Start/end validity period.
- Highest-priority valid discount.
- Discount tidak dapat melebihi harga.
- Current currency: IDR.

Availability dan pricing dihitung ulang di server pada tahap review serta sebelum submission akhir. Nilai harga dari browser atau session tidak dipercaya sebagai source of truth.

### Publishing readiness

Sistem memeriksa kelengkapan sebelum tour atau option dapat diaktifkan:

- Title, slug, category, area, format, dan duration.
- Description, intro, dan story.
- Main image.
- Minimal tiga gallery images.
- Minimal tiga highlights.
- Minimal dua roadmap stops.
- Minimal satu option.
- Tepat satu default option.
- Active option harus memiliki harga dan future schedule yang valid.
- Adult, child, dan infant price rows.
- Pickup dan language configuration.
- Included item untuk all-inclusive option.

Observer memblokir activation yang belum memenuhi blocking checks. Admin masih dapat menggunakan draft preview tanpa mengubah status database.

## Admin dan operations CMS

### Dashboard

- Booking statistics.
- Latest bookings.
- Custom Trip statistics.
- Active/draft/inactive tour counters.
- Missing-default-option indicator.
- Active option without valid price/schedule indicators.
- Health information refresh setiap 15 detik.

### Booking management

- Search, filtering, dan pagination.
- Detail booking.
- Immutable tour, option, selection, transport, pricing, dan item snapshots.
- Status lifecycle: pending, contacted, confirmed, completed, cancelled.
- Completed atau rated booking dilindungi dari penghapusan.
- Signed rating link generation.

### Tour CMS

- Tour product CRUD.
- Tour options.
- Adult/child/infant prices.
- Included/excluded items.
- Operating schedules.
- Discounts.
- Blackout date/time.
- Roadmap stops dan map coordinates.
- Main image dan ordered gallery.
- Popular/featured controls.
- Draft preview dan readiness report.

### Content dan insights

- Custom Trip operations queue.
- Vehicle preference versus recommendation.
- Gallery moment management.
- Verified rating summaries.
- Search, filter, dan sorting rating.

## Database

### Domain/business tables

- `tour_packages`
- `tour_options`
- `tour_option_prices`
- `tour_option_items`
- `tour_option_schedules`
- `tour_option_discounts`
- `tour_option_blackout_dates`
- `tour_stops`
- `tour_booking_requests`
- `bookings`
- `tour_ratings`
- `gallery_moments`

### Framework/infrastructure tables

- `users`
- `password_reset_tokens`
- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`

## Security dan data integrity

Yang sudah diterapkan:

- CSRF protection.
- Form Request validation.
- Admin session regeneration dan logout invalidation.
- Protected admin middleware.
- Signed rating URLs.
- Rating rate limit.
- One-rating-per-booking constraint.
- Database transactions dan row locks.
- Server-side availability/price recomputation.
- Session-gated success page.
- Soft deletes untuk operational catalogue data.
- Immutable JSON booking snapshots.

Catatan peningkatan:

- Admin authentication masih custom dan membandingkan credential dari environment.
- Belum ada RBAC, login throttling yang eksplisit, atau 2FA.
- Leaflet CDN belum menggunakan SRI/CSP hardening.
- Queue driver tersedia, tetapi notifications belum menggunakan `ShouldQueue`.

## Performance, responsive design, dan SEO

- Blade SSR memberikan HTML siap tampil dan mudah diindeks.
- Swiper di-import hanya ketika selector terkait tersedia.
- Vite menggunakan hashed production assets.
- Eager loading digunakan pada daftar tur untuk mengurangi N+1 query.
- Admin lists menggunakan pagination.
- AOS mempunyai fail-open styling.
- Mobile audit 390 × 844 tidak menemukan horizontal overflow.
- Dynamic sitemap hanya memasukkan public pages dan active tours.
- Canonical URL dan meta description sudah tersedia.

Peningkatan yang disarankan:

- Open Graph dan Twitter card metadata.
- JSON-LD untuk LocalBusiness, TouristTrip/Product, FAQ, dan Review.
- Full bilingual content.
- Analytics dan conversion events.
- WebP/AVIF serta responsive image pipeline.
- Production Core Web Vitals audit.

## Test status

- Terdapat **48 authored test methods** dan **223 assert calls**.
- PHPUnit hanya menemukan 45 tests karena file `CostumTripRequestV2Test.php` tidak cocok dengan class `CustomTripRequestV2Test`.
- Pada environment audit, 22 tests dan 74 assertions selesai dengan pass.
- Full suite belum green karena PHP runtime tidak memiliki PDO SQLite, sedangkan test configuration menggunakan SQLite in-memory.

Karena itu statistik yang benar untuk portofolio adalah **48 authored test methods**, bukan “48 passing tests”.

## Snapshot database pada saat audit

| Data | Jumlah |
|---|---:|
| Tour products | **3** |
| Active / draft tours | **1 / 2** |
| Tour options | **3** |
| Active options | **2** |
| Price rows | **6** |
| Included/excluded item rows | **15** |
| Schedule rows | **8** |
| Discount rows | **1** |
| Blackout rows | **2** |
| Roadmap stops | **15** |
| Gallery moments | **26** |
| Package booking requests | **13** |
| Custom Trip requests | **1** |
| Verified ratings | **1** |
| Completed guests | **6** |

## Batas produk

Newman Tour Bali adalah booking-request platform:

- Tidak memiliki payment gateway.
- Tidak memiliki instant checkout atau voucher.
- Tidak memberikan automatic booking confirmation.
- Tidak memberikan automatic vehicle confirmation.
- Konfirmasi dilakukan secara manual oleh admin.

Batas ini sesuai untuk private/custom travel yang membutuhkan komunikasi sebelum konfirmasi akhir dan sekaligus mengurangi payment/PCI scope.

## Prioritas roadmap

1. Migrasikan admin ke Laravel Auth dengan hashed password.
2. Tambahkan login throttling, RBAC, 2FA, dan audit log.
3. Aktifkan PDO SQLite atau testing database di CI hingga suite green.
4. Implementasikan reserved-capacity aggregation atau slot locking.
5. Queue email notifications dan monitor failed jobs.
6. Selesaikan full localization serta structured SEO data.
7. Tambahkan analytics, error monitoring, dan performance monitoring.

## Narasi siap portofolio

Saya membangun Newman Tour Bali sebagai full-stack travel platform menggunakan Laravel 13, PHP 8.3, MySQL, Blade, Tailwind CSS 4, Alpine.js, Swiper, dan Vite. Aplikasi ini mencakup 96 application routes, 22 controllers, 13 Eloquent models, 20 database tables, 68 Blade files, dan hampir 44 ribu baris application source. Di balik UI responsifnya terdapat availability engine, participant-based pricing, discount priority rules, booking snapshots, vehicle-fit recommendation, verified ratings, publishing readiness guard, serta back-office CMS dengan 77 protected admin routes.

## Kesimpulan

Kekuatan utama Newman Tour Bali berada pada domain logic dan operational workflow di balik tampilan publiknya. Produk ini lebih tepat dipresentasikan sebagai **full-stack product engineering case study**, bukan sekadar landing page atau company profile.

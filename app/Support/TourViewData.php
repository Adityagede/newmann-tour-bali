<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class TourViewData
{
    /**
     * Menormalisasi data TourPackage agar seluruh tampilan public
     * membaca format yang sama. Statistik publik hanya memakai aggregate
     * booking selesai dan rating terverifikasi yang dimuat oleh query.
     */
    public static function make(mixed $tour): array
    {


        /* Basic Information */
        $title = self::text(
            data_get($tour, 'title') ?: data_get($tour, 'name'),
            'Private Bali Tour'
        );

        $slug = self::text(data_get($tour, 'slug'));

        $area = self::text(
            data_get($tour, 'area.name')
                ?: data_get($tour, 'area')
                ?: data_get($tour, 'location'),
            'Bali'
        );

        $duration = self::text(
            data_get($tour, 'duration') ?: data_get($tour, 'duration_text'),
            'Flexible duration'
        );

        /* Tour Type */ 
        $tourFormat = self::normaliseKey(
            self::text(data_get($tour, 'tour_format'), 'full_day')
        );

        $formatLabel = match ($tourFormat) {
            'full_day' => 'Full-day tour',
            'half_day' => 'Half-day tour',
            'activity_transfer' => 'Activity + transfer',
            'custom_trip' => 'Custom trip',
            default => 'Private Bali tour',
        };

        $tripType = self::text(
            data_get($tour, 'trip_type'),
            'Private tour'
        );

        $categoryName = self::text(
            data_get($tour, 'category.name') ?: data_get($tour, 'category'),
            $tripType
        );

        /*
         * Tidak memakai custom badge dari database.
         * Badge dibuat otomatis dari jenis tour agar semua card konsisten.
         */
        $badge = $formatLabel;

        /* Image */
        $imagePath = self::text(
            data_get($tour, 'main_image')
                ?: data_get($tour, 'image_path')
                ?: data_get($tour, 'image')
                ?: data_get($tour, 'thumbnail')
        );

        /* Verified booking and rating statistics */
        $ratingValue = data_get($tour, 'verified_rating_average');
        $ratingCount = max(
            0,
            (int) data_get($tour, 'verified_rating_count', 0)
        );

        $hasRating = $ratingCount > 0
            && is_numeric($ratingValue)
            && (float) $ratingValue > 0;
        $rating = $hasRating
            ? max(0, min(5, (float) $ratingValue))
            : null;

        $ratingText = $hasRating
            ? number_format($ratingCount)
                . ' verified '
                . ($ratingCount === 1 ? 'rating' : 'ratings')
            : null;

        $guestCount = max(
            0,
            (int) data_get($tour, 'verified_guest_count', 0)
        );

        $hostedGuestText = $guestCount > 0
            ? number_format($guestCount)
                . ' '
                . ($guestCount === 1 ? 'guest' : 'guests')
                . ' hosted'
            : null;

        /* Vehicle and Pickup */
        $vehicle = self::text(
            data_get($tour, 'default_vehicle') ?: data_get($tour, 'vehicle'),
            'Vehicle arranged by Newman'
        );

        $transportIncluded = filter_var(
            data_get($tour, 'transport_included', false),
            FILTER_VALIDATE_BOOL
        );

        $pickupText = self::text(
            data_get($tour, 'pickup_text') ?: data_get($tour, 'pickup_type')
        );

        if ($pickupText === '') {
            $pickupText = $transportIncluded
                ? 'Private pickup included'
                : 'Pickup can be arranged';
        }

        $confirmationText = self::text(
            data_get($tour, 'confirmation_text'),
            'Confirmation through WhatsApp'
        );

        $cancellationText = self::text(
            data_get($tour, 'cancellation_text')
                ?: data_get($tour, 'cancellation_policy')
        );

        /* Description */
        $descriptionSource = self::text(
            data_get($tour, 'short_description')
                ?: data_get($tour, 'description')
        );

        $description = Str::limit(strip_tags($descriptionSource), 135);

        /* Prices */
        $pricingType = self::pricingType(
            self::text(
                data_get($tour, 'pricing_type')
                    ?: data_get($tour, 'price_type')
            )
        );

        $defaultOption = data_get($tour, 'defaultActiveOption')
            ?: collect(data_get($tour, 'activeOptions', []))
                ->firstWhere('is_default', true)
            ?: collect(data_get($tour, 'activeOptions', []))->first();

        $optionPrices = collect(data_get($defaultOption, 'prices', []));
        $optionPrice = static function (string $type) use ($optionPrices): int {
            $price = $optionPrices->first(
                fn (mixed $row): bool =>
                    (string) data_get($row, 'participant_type') === $type
                    && (bool) data_get($row, 'is_allowed', false)
            );

            if (!$price || (bool) data_get($price, 'is_free', false)) {
                return 0;
            }

            return max(0, (int) data_get($price, 'base_price', 0));
        };

        $adultPrice = $optionPrice('adult')
            ?: self::money(data_get($tour, 'adult_price'));
        $childPrice = $optionPrice('child')
            ?: self::money(data_get($tour, 'child_price'));
        $infantPrice = $optionPrice('infant')
            ?: self::money(data_get($tour, 'infant_price'));
        $vehiclePrice = self::money(data_get($tour, 'vehicle_price'));
        $groupPrice = self::money(
            data_get($tour, 'group_price') ?: data_get($tour, 'package_price')
        );

        $legacyPrice = self::money(
            data_get($tour, 'starting_price')
                ?: data_get($tour, 'base_price')
                ?: data_get($tour, 'price_amount')
                ?: data_get($tour, 'price')
        );

        if ($pricingType === 'request_quote') {
            $pricingType = match (true) {
                $adultPrice > 0 => 'per_person',
                $vehiclePrice > 0 => 'per_vehicle',
                $groupPrice > 0 => 'per_group',
                $legacyPrice > 0 => 'per_person',
                default => 'request_quote',
            };
        }

        $priceAmount = match ($pricingType) {
            'per_person' => $adultPrice > 0 ? $adultPrice : $legacyPrice,
            'per_vehicle' => $vehiclePrice > 0 ? $vehiclePrice : $legacyPrice,
            'per_group' => $groupPrice > 0 ? $groupPrice : $legacyPrice,
            default => 0,
        };

        $priceSuffix = match ($pricingType) {
            'per_person' => 'per adult',
            'per_vehicle' => 'per vehicle',
            'per_group' => 'per group',
            default => '',
        };

        $priceAvailable = $priceAmount > 0;

        /* Capacity */
        $minGuests = max(1, self::integerFromText(data_get($tour, 'min_guests')) ?: 1);
        $maxGuestsValue = self::integerFromText(data_get($tour, 'max_guests'));


         $promotion = \App\Support\TourCardPromotion::make(
        $tour
    );
        return [
            'id' => data_get($tour, 'id'),
            'title' => $title,
            'slug' => $slug,
            'area' => $area,

            'category' => $categoryName,
            'category_key' => Str::slug($categoryName),
            'tour_format' => $tourFormat,
            'format_label' => $formatLabel,
            'trip_type' => $tripType,
            'badge' => $badge,

            'image_path' => $imagePath,
            'image_url' => self::imageUrl($imagePath),

            'duration' => $duration,
            'vehicle' => $vehicle,
            'pickup_text' => $pickupText,
            'confirmation_text' => $confirmationText,
            'cancellation_text' => $cancellationText,

            'has_rating' => $hasRating,
            'rating' => $rating,
            'rating_count' => $ratingCount,
            'rating_text' => $ratingText,
            'guest_count' => $guestCount,
            'hosted_guest_count' => $guestCount,
            'hosted_guest_text' => $hostedGuestText,
            'review_text' => $ratingText,
            'social_proof_text' => $ratingText,

            'description' => $description,

            'pricing_type' => $pricingType,
            'price_available' => $priceAvailable,
            'price_amount' => $priceAmount,
            'price_label' => $priceAvailable ? 'From' : 'Tailored price',
            'price_text' => $priceAvailable ? self::rupiah($priceAmount) : 'Contact for price',
            'price_suffix' => $priceSuffix,
            'price_note' => self::text(data_get($tour, 'price_note')),

            

            'adult_price' => $adultPrice,
            'child_price' => $childPrice,
            'infant_price' => $infantPrice,
            'child_price_text' => $childPrice > 0 ? self::rupiah($childPrice) : null,
            'infant_price_text' => $infantPrice > 0 ? self::rupiah($infantPrice) : null,
            'vehicle_price' => $vehiclePrice,
            'group_price' => $groupPrice,

            'min_guests' => $minGuests,
            'max_guests' => $maxGuestsValue > 0 ? $maxGuestsValue : null,
            'transport_included' => $transportIncluded,

            'promotion' => $promotion,
               
        ];
    }

    /** Membuat URL gambar dari public maupun storage. */
    public static function imageUrl(?string $path): string
    {
        $placeholder = asset('images/tour-placeholder.jpg');
        $path = trim((string) $path);

        if ($path === '') {
            return $placeholder;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        $normalised = str_replace('\\', '/', $path);
        $normalised = ltrim($normalised, '/');
        $normalised = Str::replaceStart('public/', '', $normalised);
        $normalised = Str::replaceStart('storage/app/public/', '', $normalised);

        if (Str::startsWith($normalised, 'storage/')) {
            $storagePath = Str::after($normalised, 'storage/');

            return Storage::disk('public')->exists($storagePath)
                ? asset('storage/' . $storagePath)
                : $placeholder;
        }

        if (file_exists(public_path($normalised))) {
            return asset($normalised);
        }

        if (Storage::disk('public')->exists($normalised)) {
            return Storage::disk('public')->url($normalised);
        }

        return $placeholder;
    }

    private static function pricingType(string $value): string
    {
        return match (self::normaliseKey($value)) {
            'per_person', 'person', 'per_adult' => 'per_person',
            'per_car', 'per_vehicle', 'vehicle', 'car' => 'per_vehicle',
            'per_group', 'group', 'package' => 'per_group',
            default => 'request_quote',
        };
    }

    /**
     * Mengubah harga database atau input berformat Rupiah menjadi integer.
     * Aman untuk 400000, 400000.00, 400.000, dan IDR 400.000.
     */
    private static function money(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_int($value) || is_float($value)) {
            return max(0, (int) round($value));
        }

        $value = trim((string) $value);
        $value = preg_replace('/[^0-9,.-]/', '', $value) ?? '';

        if ($value === '') {
            return 0;
        }

        /* Format Indonesia: 1.250.000 atau 1.250.000,00 */
        if (preg_match('/^-?\d{1,3}(?:\.\d{3})+(?:,\d{1,2})?$/', $value)) {
            $normalised = str_replace('.', '', $value);
            $normalised = str_replace(',', '.', $normalised);

            return max(0, (int) round((float) $normalised));
        }

        /* Format internasional: 1,250,000 atau 1,250,000.00 */
        if (preg_match('/^-?\d{1,3}(?:,\d{3})+(?:\.\d{1,2})?$/', $value)) {
            return max(0, (int) round((float) str_replace(',', '', $value)));
        }

        /* Nilai decimal database seperti 400000.00 */
        if (is_numeric($value)) {
            return max(0, (int) round((float) $value));
        }

        $digits = preg_replace('/[^0-9]/', '', $value) ?? '';

        return $digits !== '' ? max(0, (int) $digits) : 0;
    }

    private static function integerFromText(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return max(0, (int) $value);
        }

        $digits = preg_replace('/[^0-9]/', '', (string) $value) ?? '';

        return $digits !== '' ? max(0, (int) $digits) : 0;
    }

    private static function rupiah(int $amount): string
    {
        return 'IDR ' . number_format($amount, 0, ',', '.');
    }

    private static function normaliseKey(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->replace([' ', '-'], '_')
            ->replaceMatches('/_+/', '_')
            ->trim('_')
            ->toString();
    }

    private static function text(mixed $value, string $fallback = ''): string
    {
        if (is_array($value) || is_object($value)) {
            $value = data_get($value, 'name')
                ?: data_get($value, 'title')
                ?: '';
        }

        $value = trim(strip_tags((string) $value));

        return $value !== '' ? $value : $fallback;
    }
}

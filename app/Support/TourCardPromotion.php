<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

final class TourCardPromotion
{
    /**
     * Membentuk promo ringkas untuk card Tour Package.
     *
     * Promo card hanya menjadi preview berdasarkan:
     * - default Active Tour Option;
     * - Adult Participant Price;
     * - discount Active yang sedang valid;
     * - priority tertinggi.
     *
     * Availability V2 tetap menghitung ulang harga final.
     */
    public static function make(mixed $tour): array
    {
        $empty = self::emptyPromotion();

        if (
            ! is_object($tour)
            || ! method_exists($tour, 'options')
        ) {
            return $empty;
        }

        try {
            $option = $tour
                ->options()
                ->where('status', 'active')
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();
        } catch (Throwable) {
            return $empty;
        }

        if (! $option) {
            return $empty;
        }

        /*
        |--------------------------------------------------------------------------
        | Participant prices
        |--------------------------------------------------------------------------
        */

        $adultBasePrice = self::participantPrice(
            $option,
            'adult'
        );

        $childBasePrice = self::participantPrice(
            $option,
            'child'
        );

        /*
         * Fallback untuk project yang masih menyimpan ringkasan harga
         * pada Tour Product.
         */
        if ($adultBasePrice <= 0) {
            $adultBasePrice = self::money(
                data_get($tour, 'adult_price')
            );
        }

        if ($childBasePrice <= 0) {
            $childBasePrice = self::money(
                data_get($tour, 'child_price')
            );
        }

        if ($adultBasePrice <= 0) {
            return $empty;
        }

        /*
        |--------------------------------------------------------------------------
        | Currently valid discount
        |--------------------------------------------------------------------------
        */

        $discounts = self::relationCollection(
            $option,
            [
                'discounts',
                'tourOptionDiscounts',
            ]
        );

        $now = Carbon::now(
            config('app.timezone')
        );

        $discount = $discounts
            ->filter(
                fn (mixed $candidate): bool =>
                    self::discountIsActive($candidate)
                    && self::discountIsCurrentlyValid(
                        $candidate,
                        $now
                    )
                    && self::discountAppliesTo(
                        $candidate,
                        'adult'
                    )
            )
            ->sortByDesc(
                fn (mixed $candidate): int =>
                    (int) data_get(
                        $candidate,
                        'priority',
                        0
                    )
            )
            ->first();

        if (! $discount) {
            return $empty;
        }

        $discountType = strtolower(
            trim(
                (string) (
                    data_get($discount, 'discount_type')
                    ?? data_get($discount, 'type')
                    ?? ''
                )
            )
        );

        $discountValue = self::money(
            data_get($discount, 'discount_value')
                ?? data_get($discount, 'value')
                ?? data_get($discount, 'amount')
        );

        if ($discountValue <= 0) {
            return $empty;
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate teaser price
        |--------------------------------------------------------------------------
        */

        $adultPromoPrice = self::applyDiscount(
            $adultBasePrice,
            $discountType,
            $discountValue
        );

        if (
            $adultPromoPrice <= 0
            || $adultPromoPrice >= $adultBasePrice
        ) {
            return $empty;
        }

        $childPromoPrice = null;

        if (
            $childBasePrice > 0
            && self::discountAppliesTo(
                $discount,
                'child'
            )
        ) {
            $candidateChildPrice = self::applyDiscount(
                $childBasePrice,
                $discountType,
                $discountValue
            );

            if (
                $candidateChildPrice >= 0
                && $candidateChildPrice < $childBasePrice
            ) {
                $childPromoPrice = $candidateChildPrice;
            }
        }

        $savingAdult =
            $adultBasePrice - $adultPromoPrice;

        $badgeLabel = self::badgeLabel(
            $discountType,
            $discountValue,
            $savingAdult
        );

        return [
            'active' => true,

            'name' => trim(
                (string) (
                    data_get($discount, 'label')
                    ?? data_get($discount, 'name')
                    ?? 'Limited offer'
                )
            ),

            'label' => $badgeLabel,

            'discount_type' => $discountType,
            'discount_value' => $discountValue,

            'base_adult_price' => $adultBasePrice,
            'adult_price' => $adultPromoPrice,
            'saving_adult' => $savingAdult,

            'base_child_price' =>
                $childBasePrice > 0
                    ? $childBasePrice
                    : null,

            'child_price' => $childPromoPrice,

            'base_adult_price_text' =>
                self::rupiah($adultBasePrice),

            'adult_price_text' =>
                self::rupiah($adultPromoPrice),

            'saving_adult_text' =>
                self::rupiah($savingAdult),

            'base_child_price_text' =>
                $childBasePrice > 0
                    ? self::rupiah($childBasePrice)
                    : null,

            'child_price_text' =>
                $childPromoPrice !== null
                    ? self::rupiah($childPromoPrice)
                    : null,

            'starts_at' =>
                data_get($discount, 'starts_at'),

            'ends_at' =>
                data_get($discount, 'ends_at'),
        ];
    }

    private static function participantPrice(
        mixed $option,
        string $participantType
    ): int {
        $prices = self::relationCollection(
            $option,
            [
                'participantPrices',
                'prices',
            ]
        );

        $price = $prices
            ->first(function (
                mixed $candidate
            ) use ($participantType): bool {
                $type = strtolower(
                    trim(
                        (string) (
                            data_get(
                                $candidate,
                                'participant_type'
                            )
                            ?? data_get(
                                $candidate,
                                'participant_category'
                            )
                            ?? data_get(
                                $candidate,
                                'type'
                            )
                            ?? ''
                        )
                    )
                );

                if ($type !== $participantType) {
                    return false;
                }

                $allowed = data_get(
                    $candidate,
                    'is_allowed'
                );

                if (
                    $allowed !== null
                    && ! self::boolean(
                        $allowed,
                        true
                    )
                ) {
                    return false;
                }

                $active = data_get(
                    $candidate,
                    'is_active'
                );

                if (
                    $active !== null
                    && ! self::boolean(
                        $active,
                        true
                    )
                ) {
                    return false;
                }

                return true;
            });

        if (! $price) {
            return 0;
        }

        return self::money(
            data_get($price, 'base_price')
                ?? data_get($price, 'price')
                ?? data_get($price, 'amount')
        );
    }

    private static function relationCollection(
        mixed $model,
        array $relationNames
    ): Collection {
        if (! is_object($model)) {
            return collect();
        }

        foreach ($relationNames as $relationName) {
            if (! method_exists($model, $relationName)) {
                continue;
            }

            try {
                return $model
                    ->{$relationName}()
                    ->get();
            } catch (Throwable) {
                continue;
            }
        }

        return collect();
    }

    private static function discountIsActive(
        mixed $discount
    ): bool {
        $active = data_get(
            $discount,
            'is_active'
        );

        if ($active !== null) {
            return self::boolean(
                $active,
                false
            );
        }

        $active = data_get(
            $discount,
            'active'
        );

        if ($active !== null) {
            return self::boolean(
                $active,
                false
            );
        }

        $status = strtolower(
            trim(
                (string) data_get(
                    $discount,
                    'status',
                    'active'
                )
            )
        );

        return $status === 'active';
    }

    private static function discountIsCurrentlyValid(
        mixed $discount,
        Carbon $now
    ): bool {
        $startsAt = data_get(
            $discount,
            'starts_at'
        );

        $endsAt = data_get(
            $discount,
            'ends_at'
        );

        try {
            if (
                $startsAt
                && Carbon::parse(
                    $startsAt,
                    config('app.timezone')
                )->isAfter($now)
            ) {
                return false;
            }

            if (
                $endsAt
                && Carbon::parse(
                    $endsAt,
                    config('app.timezone')
                )->isBefore($now)
            ) {
                return false;
            }
        } catch (Throwable) {
            return false;
        }

        return true;
    }

    private static function discountAppliesTo(
        mixed $discount,
        string $participantType
    ): bool {
        $appliesToAll = data_get(
            $discount,
            'applies_to_all'
        );

        if (
            $appliesToAll !== null
            && self::boolean(
                $appliesToAll,
                false
            )
        ) {
            return true;
        }

        $target = strtolower(
            trim(
                (string) (
                    data_get(
                        $discount,
                        'participant_type'
                    )
                    ?? data_get(
                        $discount,
                        'applies_to'
                    )
                    ?? ''
                )
            )
        );

        if (
            $target === ''
            || in_array(
                $target,
                [
                    'all',
                    'all_paid',
                    'all_paid_participants',
                    'paid',
                ],
                true
            )
        ) {
            return true;
        }

        return $target === $participantType;
    }

    private static function applyDiscount(
        int $basePrice,
        string $discountType,
        int $discountValue
    ): int {
        if (
            in_array(
                $discountType,
                [
                    'percentage',
                    'percent',
                    'percentage_off',
                ],
                true
            )
        ) {
            $percentage = min(
                100,
                max(0, $discountValue)
            );

            return max(
                0,
                (int) round(
                    $basePrice
                    * (1 - ($percentage / 100))
                )
            );
        }

        if (
            in_array(
                $discountType,
                [
                    'fixed',
                    'fixed_amount',
                    'amount',
                ],
                true
            )
        ) {
            return max(
                0,
                $basePrice - $discountValue
            );
        }

        return $basePrice;
    }

    private static function badgeLabel(
        string $discountType,
        int $discountValue,
        int $savingAdult
    ): string {
        if (
            in_array(
                $discountType,
                [
                    'percentage',
                    'percent',
                    'percentage_off',
                ],
                true
            )
        ) {
            return $discountValue . '% OFF';
        }

        return 'SAVE '
            . self::rupiah($savingAdult);
    }

    private static function boolean(
        mixed $value,
        bool $fallback
    ): bool {
        if ($value === null) {
            return $fallback;
        }

        return filter_var(
            $value,
            FILTER_VALIDATE_BOOL,
            FILTER_NULL_ON_FAILURE
        ) ?? $fallback;
    }

    private static function money(
        mixed $value
    ): int {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_int($value) || is_float($value)) {
            return max(
                0,
                (int) round($value)
            );
        }

        $value = preg_replace(
            '/[^0-9,.-]/',
            '',
            trim((string) $value)
        ) ?? '';

        if ($value === '') {
            return 0;
        }

        if (
            preg_match(
                '/^\d{1,3}(?:\.\d{3})+(?:,\d{1,2})?$/',
                $value
            )
        ) {
            $value = str_replace(
                '.',
                '',
                $value
            );

            $value = str_replace(
                ',',
                '.',
                $value
            );
        } elseif (
            preg_match(
                '/^\d{1,3}(?:,\d{3})+(?:\.\d{1,2})?$/',
                $value
            )
        ) {
            $value = str_replace(
                ',',
                '',
                $value
            );
        }

        return is_numeric($value)
            ? max(
                0,
                (int) round((float) $value)
            )
            : 0;
    }

    private static function rupiah(
        int $amount
    ): string {
        return 'IDR '
            . number_format(
                $amount,
                0,
                ',',
                '.'
            );
    }

    private static function emptyPromotion(): array
    {
        return [
            'active' => false,
            'name' => null,
            'label' => null,

            'discount_type' => null,
            'discount_value' => null,

            'base_adult_price' => null,
            'adult_price' => null,
            'saving_adult' => null,

            'base_child_price' => null,
            'child_price' => null,

            'base_adult_price_text' => null,
            'adult_price_text' => null,
            'saving_adult_text' => null,

            'base_child_price_text' => null,
            'child_price_text' => null,

            'starts_at' => null,
            'ends_at' => null,
        ];
    }
}
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TourOption;
use App\Models\TourOptionDiscount;
use App\Models\TourOptionPrice;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class TourPricingService
{
    private const PARTICIPANT_TYPES = [
        TourOptionPrice::TYPE_ADULT,
        TourOptionPrice::TYPE_CHILD,
        TourOptionPrice::TYPE_INFANT,
    ];

    /**
     * Menghitung harga dasar, diskon, dan estimated total.
     *
     * Contoh participants:
     *
     * [
     *     'adult' => 2,
     *     'child' => 1,
     *     'infant' => 1,
     * ]
     */
    public function calculate(
        TourOption $option,
        array $participants,
        ?Carbon $moment = null
    ): array {
        $moment ??= now();

        $counts = $this->normalizeParticipantCounts(
            $participants
        );

        $totalParticipants = array_sum($counts);

        $this->validateTotalParticipants(
            $option,
            $totalParticipants
        );

        $prices = $this->getOptionPrices($option);

        if ($prices->isEmpty()) {
            throw new InvalidArgumentException(
                'This tour option does not have participant prices.'
            );
        }

        $pricesByType = $prices->keyBy(
            'participant_type'
        );

        $currency = null;
        $baseTotal = 0;
        $baseBreakdown = [];

        foreach (self::PARTICIPANT_TYPES as $type) {
            $count = $counts[$type];

            if ($count === 0) {
                continue;
            }

            /** @var TourOptionPrice|null $price */
            $price = $pricesByType->get($type);

            if (!$price) {
                throw new InvalidArgumentException(
                    $this->participantLabel($type)
                    . ' pricing is not available for this option.'
                );
            }

            if (!$price->is_allowed) {
                throw new InvalidArgumentException(
                    $this->participantLabel($type)
                    . ' participants are not allowed for this option.'
                );
            }

            $priceCurrency = strtoupper(
                trim((string) ($price->currency ?: 'IDR'))
            );

            if (
                $currency !== null
                && $currency !== $priceCurrency
            ) {
                throw new InvalidArgumentException(
                    'Participant prices must use the same currency.'
                );
            }

            $currency ??= $priceCurrency;

            $unitPrice = $price->is_free
                ? 0
                : max(0, (int) $price->base_price);

            $lineTotal = $unitPrice * $count;

            $baseTotal += $lineTotal;

            $baseBreakdown[] = [
                'participant_type' => $type,

                'label' => $price->label
                    ?: $this->participantLabel($type),

                'age_label' => $price->age_label,

                'count' => $count,

                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,

                'is_free' => (bool) $price->is_free,
                'currency' => $priceCurrency,
            ];
        }

        /*
         * Hanya satu diskon valid dan applicable
         * dengan priority tertinggi yang digunakan.
         */
        $discount = $this->selectDiscount(
            $option,
            $moment,
            $baseBreakdown
        );

        $discountResult = $this->applyDiscount(
            $baseBreakdown,
            $discount
        );

        $discountAmount = $discountResult[
            'discount_amount'
        ];

        $estimatedTotal = max(
            0,
            $baseTotal - $discountAmount
        );

        return [
            'tour_package_id' => $option->tour_package_id,
            'tour_option_id' => $option->id,

            'currency' => $currency ?? 'IDR',

            'participants' => $counts,
            'total_participants' => $totalParticipants,

            'breakdown' => $discountResult['breakdown'],

            'base_total' => $baseTotal,

            'discount' => $this->discountPayload(
                $discount
            ),

            'discount_amount' => $discountAmount,
            'estimated_total' => $estimatedTotal,

            'calculated_at' => $moment->toIso8601String(),
        ];
    }

    private function normalizeParticipantCounts(
        array $participants
    ): array {
        $unknownTypes = array_diff(
            array_keys($participants),
            self::PARTICIPANT_TYPES
        );

        if ($unknownTypes !== []) {
            throw new InvalidArgumentException(
                'Unknown participant type: '
                . implode(', ', $unknownTypes)
            );
        }

        $normalized = [];

        foreach (self::PARTICIPANT_TYPES as $type) {
            $normalized[$type] = $this->normalizeCount(
                $participants[$type] ?? 0,
                $type
            );
        }

        return $normalized;
    }

    private function normalizeCount(
        mixed $value,
        string $participantType
    ): int {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_int($value)) {
            $count = $value;
        } elseif (
            is_string($value)
            && preg_match('/^\d+$/', $value) === 1
        ) {
            $count = (int) $value;
        } else {
            throw new InvalidArgumentException(
                $this->participantLabel($participantType)
                . ' count must be a whole number.'
            );
        }

        if ($count < 0) {
            throw new InvalidArgumentException(
                $this->participantLabel($participantType)
                . ' count cannot be negative.'
            );
        }

        return $count;
    }

    /**
     * Adult + Child + Infant semuanya dihitung
     * untuk minimum, maksimum, dan kapasitas.
     */
    private function validateTotalParticipants(
        TourOption $option,
        int $totalParticipants
    ): void {
        if ($totalParticipants < 1) {
            throw new InvalidArgumentException(
                'At least one participant is required.'
            );
        }

        $minimumGuests = max(
            1,
            (int) ($option->min_guests ?: 1)
        );

        if ($totalParticipants < $minimumGuests) {
            throw new InvalidArgumentException(
                "This option requires at least "
                . "{$minimumGuests} participants."
            );
        }

        if (
            $option->max_guests !== null
            && $totalParticipants
                > (int) $option->max_guests
        ) {
            throw new InvalidArgumentException(
                "This option allows a maximum of "
                . "{$option->max_guests} participants."
            );
        }
    }

    private function getOptionPrices(
        TourOption $option
    ): Collection {
        if ($option->relationLoaded('prices')) {
            return $option->getRelation('prices');
        }

        return $option->prices()->get();
    }

    /**
     * Mengambil satu diskon:
     *
     * - aktif
     * - berada dalam periode berlaku
     * - berlaku untuk participant yang dipilih
     * - priority paling tinggi
     */
    private function selectDiscount(
        TourOption $option,
        Carbon $moment,
        array $breakdown
    ): ?TourOptionDiscount {
        $discounts = $this->getValidDiscounts(
            $option,
            $moment
        );

        $applicableDiscounts = $discounts->filter(
            fn (TourOptionDiscount $discount): bool =>
                $this->discountAppliesToBreakdown(
                    $discount,
                    $breakdown
                )
        );

        return $applicableDiscounts
            ->sort(
                function (
                    TourOptionDiscount $first,
                    TourOptionDiscount $second
                ): int {
                    $priorityComparison =
                        (int) $second->priority
                        <=> (int) $first->priority;

                    if ($priorityComparison !== 0) {
                        return $priorityComparison;
                    }

                    return (int) ($first->id ?? PHP_INT_MAX)
                        <=> (int) (
                            $second->id ?? PHP_INT_MAX
                        );
                }
            )
            ->first();
    }

    private function getValidDiscounts(
        TourOption $option,
        Carbon $moment
    ): Collection {
        if ($option->relationLoaded('discounts')) {
            return $option
                ->getRelation('discounts')
                ->filter(
                    fn (
                        TourOptionDiscount $discount
                    ): bool =>
                        $this->isDiscountValid(
                            $discount,
                            $moment
                        )
                )
                ->values();
        }

        return $option
            ->discounts()
            ->currentlyValid($moment)
            ->get();
    }

    private function isDiscountValid(
        TourOptionDiscount $discount,
        Carbon $moment
    ): bool {
        if (!$discount->is_active) {
            return false;
        }

        if (
            $discount->starts_at !== null
            && $discount->starts_at->gt($moment)
        ) {
            return false;
        }

        if (
            $discount->ends_at !== null
            && $discount->ends_at->lt($moment)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Diskon hanya dianggap applicable jika ada
     * participant berbayar yang dipilih.
     */
    private function discountAppliesToBreakdown(
        TourOptionDiscount $discount,
        array $breakdown
    ): bool {
        foreach ($breakdown as $row) {
            if (
                $row['count'] > 0
                && $row['unit_price'] > 0
                && !$row['is_free']
                && $discount->appliesToParticipant(
                    $row['participant_type']
                )
            ) {
                return true;
            }
        }

        return false;
    }

    private function applyDiscount(
        array $baseBreakdown,
        ?TourOptionDiscount $discount
    ): array {
        $discountAmount = 0;
        $finalBreakdown = [];

        if ($discount !== null) {
            $this->validateDiscount($discount);
        }

        foreach ($baseBreakdown as $row) {
            $discountPerUnit = 0;

            $canReceiveDiscount =
                $discount !== null
                && !$row['is_free']
                && $row['unit_price'] > 0
                && $discount->appliesToParticipant(
                    $row['participant_type']
                );

            if ($canReceiveDiscount) {
                $discountPerUnit =
                    $this->calculateDiscountPerUnit(
                        $row['unit_price'],
                        $discount
                    );
            }

            /*
             * Potongan tidak boleh melebihi harga peserta.
             */
            $discountPerUnit = min(
                $row['unit_price'],
                max(0, $discountPerUnit)
            );

            $lineDiscount =
                $discountPerUnit * $row['count'];

            $effectiveUnitPrice = max(
                0,
                $row['unit_price'] - $discountPerUnit
            );

            $effectiveLineTotal =
                $effectiveUnitPrice * $row['count'];

            $discountAmount += $lineDiscount;

            $row['discount_per_unit'] =
                $discountPerUnit;

            $row['discount_amount'] =
                $lineDiscount;

            $row['effective_unit_price'] =
                $effectiveUnitPrice;

            $row['effective_line_total'] =
                $effectiveLineTotal;

            $finalBreakdown[] = $row;
        }

        return [
            'breakdown' => $finalBreakdown,
            'discount_amount' => $discountAmount,
        ];
    }

    /**
     * Percentage dan fixed dihitung per peserta.
     */
    private function calculateDiscountPerUnit(
        int $unitPrice,
        TourOptionDiscount $discount
    ): int {
        $value = (int) $discount->discount_value;

        if (
            $discount->discount_type
            === TourOptionDiscount::TYPE_PERCENTAGE
        ) {
            return intdiv(
                $unitPrice * $value,
                100
            );
        }

        if (
            $discount->discount_type
            === TourOptionDiscount::TYPE_FIXED
        ) {
            return $value;
        }

        throw new InvalidArgumentException(
            'Unsupported discount type: '
            . $discount->discount_type
        );
    }

    private function validateDiscount(
        TourOptionDiscount $discount
    ): void {
        $value = (int) $discount->discount_value;

        if ($value <= 0) {
            throw new InvalidArgumentException(
                'Discount value must be greater than zero.'
            );
        }

        if (
            $discount->discount_type
            === TourOptionDiscount::TYPE_PERCENTAGE
            && $value > 100
        ) {
            throw new InvalidArgumentException(
                'Percentage discount cannot exceed 100%.'
            );
        }

        if (
            !in_array(
                $discount->discount_type,
                [
                    TourOptionDiscount::TYPE_PERCENTAGE,
                    TourOptionDiscount::TYPE_FIXED,
                ],
                true
            )
        ) {
            throw new InvalidArgumentException(
                'Unsupported discount type: '
                . $discount->discount_type
            );
        }
    }

    private function discountPayload(
        ?TourOptionDiscount $discount
    ): ?array {
        if ($discount === null) {
            return null;
        }

        return [
            'id' => $discount->id,
            'label' => $discount->label,

            'discount_type' =>
                $discount->discount_type,

            'discount_value' =>
                (int) $discount->discount_value,

            'formatted_value' =>
                $discount->formatted_value,

            'participant_types' =>
                $discount->participant_types ?? [],

            'priority' => (int) $discount->priority,

            'starts_at' =>
                $discount->starts_at?->toIso8601String(),

            'ends_at' =>
                $discount->ends_at?->toIso8601String(),
        ];
    }

    private function participantLabel(
        string $participantType
    ): string {
        return match ($participantType) {
            TourOptionPrice::TYPE_ADULT => 'Adult',
            TourOptionPrice::TYPE_CHILD => 'Child',
            TourOptionPrice::TYPE_INFANT => 'Infant',
            default => ucfirst($participantType),
        };
    }
}
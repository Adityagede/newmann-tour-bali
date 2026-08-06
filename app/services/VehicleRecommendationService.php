<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TourOptionPrice;
use InvalidArgumentException;

final class VehicleRecommendationService
{
    private const PARTICIPANT_TYPES = [
        TourOptionPrice::TYPE_ADULT,
        TourOptionPrice::TYPE_CHILD,
        TourOptionPrice::TYPE_INFANT,
    ];

    /**
     * Memberikan rekomendasi transport awal.
     *
     * Contoh participants:
     *
     * [
     *     'adult' => 2,
     *     'child' => 1,
     *     'infant' => 1,
     * ]
     *
     * Contoh requirements:
     *
     * [
     *     'large_luggage' => true,
     *     'baby_seat_count' => 1,
     *     'wheelchair_count' => 0,
     *     'accessibility_required' => false,
     *     'oversized_equipment' => false,
     * ]
     */
    public function recommend(
        array $participants,
        array $requirements = []
    ): array {
        $counts = $this->normalizeParticipantCounts(
            $participants
        );

        $totalPassengers = array_sum($counts);

        if ($totalPassengers < 1) {
            throw new InvalidArgumentException(
                'At least one passenger is required '
                . 'for a transport recommendation.'
            );
        }

        $normalizedRequirements =
            $this->normalizeRequirements(
                $requirements
            );

        $rule = $this->findMatchingRule(
            $totalPassengers
        );

        $specialReviewRequired =
            $this->hasSpecialRequirements(
                $normalizedRequirements
            );

        return [
            'key' => $rule['key'],
            'label' => $rule['label'],
            'description' =>
                $rule['description'] ?? null,

            'participants' => $counts,
            'total_passengers' =>
                $totalPassengers,

            'configured_capacity' => [
                'minimum' =>
                    (int) $rule['min_passengers'],

                'maximum' =>
                    $rule['max_passengers'] === null
                        ? null
                        : (int) $rule[
                            'max_passengers'
                        ],
            ],

            'requirements' =>
                $normalizedRequirements,

            /*
             * Selalu true karena rekomendasi awal
             * bukan konfirmasi kendaraan final.
             */
            'confirmation_required' => true,

            /*
             * Menjadi true ketika ada bagasi besar,
             * baby seat, wheelchair, accessibility,
             * atau equipment khusus.
             */
            'special_review_required' =>
                $specialReviewRequired,

            'confirmation_note' =>
                (string) config(
                    'tour.vehicle_recommendation'
                    . '.confirmation_note',
                    'Final transport will be confirmed '
                    . 'by Newman.'
                ),
        ];
    }

    private function findMatchingRule(
        int $totalPassengers
    ): array {
        $rules = config(
            'tour.vehicle_recommendation.rules',
            []
        );

        if (!is_array($rules) || $rules === []) {
            throw new InvalidArgumentException(
                'Vehicle recommendation rules '
                . 'have not been configured.'
            );
        }

        usort(
            $rules,
            static fn (
                array $first,
                array $second
            ): int =>
                (int) ($first['min_passengers'] ?? 0)
                <=>
                (int) ($second['min_passengers'] ?? 0)
        );

        foreach ($rules as $rule) {
            $this->validateRule($rule);

            $minimum = (int) $rule[
                'min_passengers'
            ];

            $maximum = $rule[
                'max_passengers'
            ];

            $matchesMinimum =
                $totalPassengers >= $minimum;

            $matchesMaximum =
                $maximum === null
                || $totalPassengers
                    <= (int) $maximum;

            if (
                $matchesMinimum
                && $matchesMaximum
            ) {
                return $rule;
            }
        }

        throw new InvalidArgumentException(
            'No transport recommendation is '
            . 'configured for this passenger count.'
        );
    }

    private function validateRule(
        array $rule
    ): void {
        foreach (
            [
                'key',
                'label',
                'min_passengers',
                'max_passengers',
            ] as $field
        ) {
            if (!array_key_exists($field, $rule)) {
                throw new InvalidArgumentException(
                    "Vehicle rule is missing: {$field}."
                );
            }
        }

        if (
            !is_string($rule['key'])
            || trim($rule['key']) === ''
        ) {
            throw new InvalidArgumentException(
                'Vehicle rule key must be valid.'
            );
        }

        if (
            !is_string($rule['label'])
            || trim($rule['label']) === ''
        ) {
            throw new InvalidArgumentException(
                'Vehicle rule label must be valid.'
            );
        }

        $minimum = (int) $rule[
            'min_passengers'
        ];

        if ($minimum < 1) {
            throw new InvalidArgumentException(
                'Vehicle minimum passengers '
                . 'must be at least one.'
            );
        }

        if (
            $rule['max_passengers'] !== null
            && (int) $rule['max_passengers']
                < $minimum
        ) {
            throw new InvalidArgumentException(
                'Vehicle maximum passengers cannot '
                . 'be smaller than its minimum.'
            );
        }
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
            $value = $participants[$type] ?? 0;

            $normalized[$type] =
                $this->normalizeWholeNumber(
                    $value,
                    $this->participantLabel($type)
                    . ' count'
                );
        }

        return $normalized;
    }

    private function normalizeRequirements(
        array $requirements
    ): array {
        $allowedKeys = [
            'large_luggage',
            'baby_seat_count',
            'wheelchair_count',
            'accessibility_required',
            'oversized_equipment',
        ];

        $unknownKeys = array_diff(
            array_keys($requirements),
            $allowedKeys
        );

        if ($unknownKeys !== []) {
            throw new InvalidArgumentException(
                'Unknown transport requirement: '
                . implode(', ', $unknownKeys)
            );
        }

        return [
            'large_luggage' =>
                $this->normalizeBoolean(
                    $requirements[
                        'large_luggage'
                    ] ?? false,
                    'Large luggage'
                ),

            'baby_seat_count' =>
                $this->normalizeWholeNumber(
                    $requirements[
                        'baby_seat_count'
                    ] ?? 0,
                    'Baby seat count'
                ),

            'wheelchair_count' =>
                $this->normalizeWholeNumber(
                    $requirements[
                        'wheelchair_count'
                    ] ?? 0,
                    'Wheelchair count'
                ),

            'accessibility_required' =>
                $this->normalizeBoolean(
                    $requirements[
                        'accessibility_required'
                    ] ?? false,
                    'Accessibility requirement'
                ),

            'oversized_equipment' =>
                $this->normalizeBoolean(
                    $requirements[
                        'oversized_equipment'
                    ] ?? false,
                    'Oversized equipment'
                ),
        ];
    }

    private function hasSpecialRequirements(
        array $requirements
    ): bool {
        return $requirements['large_luggage']
            || $requirements['baby_seat_count'] > 0
            || $requirements['wheelchair_count'] > 0
            || $requirements[
                'accessibility_required'
            ]
            || $requirements[
                'oversized_equipment'
            ];
    }

    private function normalizeWholeNumber(
        mixed $value,
        string $label
    ): int {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_int($value)) {
            $number = $value;
        } elseif (
            is_string($value)
            && preg_match('/^\d+$/', $value) === 1
        ) {
            $number = (int) $value;
        } else {
            throw new InvalidArgumentException(
                "{$label} must be a whole number."
            );
        }

        if ($number < 0) {
            throw new InvalidArgumentException(
                "{$label} cannot be negative."
            );
        }

        return $number;
    }

    private function normalizeBoolean(
        mixed $value,
        string $label
    ): bool {
        if (is_bool($value)) {
            return $value;
        }

        if (
            $value === 1
            || $value === '1'
            || $value === 'true'
            || $value === 'on'
        ) {
            return true;
        }

        if (
            $value === 0
            || $value === '0'
            || $value === 'false'
            || $value === 'off'
            || $value === null
            || $value === ''
        ) {
            return false;
        }

        throw new InvalidArgumentException(
            "{$label} must be true or false."
        );
    }

    private function participantLabel(
        string $participantType
    ): string {
        return match ($participantType) {
            TourOptionPrice::TYPE_ADULT =>
                'Adult',

            TourOptionPrice::TYPE_CHILD =>
                'Child',

            TourOptionPrice::TYPE_INFANT =>
                'Infant',

            default =>
                ucfirst($participantType),
        };
    }
}
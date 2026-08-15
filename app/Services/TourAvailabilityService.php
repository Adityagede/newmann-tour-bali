<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TourOption;
use App\Models\TourOptionBlackoutDate;
use App\Models\TourOptionPrice;
use App\Models\TourOptionSchedule;
use App\Models\TourPackage;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Throwable;

final class TourAvailabilityService
{
    private const PARTICIPANT_TYPES = [
        TourOptionPrice::TYPE_ADULT,
        TourOptionPrice::TYPE_CHILD,
        TourOptionPrice::TYPE_INFANT,
    ];

    public function __construct(
    private readonly TourPricingService $pricingService,
    private readonly VehicleRecommendationService
        $vehicleRecommendationService
) {
}
    /**
     * Memeriksa option yang tersedia untuk tanggal,
     * peserta, dan bahasa yang dipilih.
     *
     * Date harus berformat YYYY-MM-DD.
     */
    public function check(
        TourPackage $tour,
        string $date,
        array $participants,
        ?string $language = null,
        ?Carbon $now = null
    ): array {
        if ((string) $tour->status !== 'active') {
            throw new InvalidArgumentException(
                'This tour is not currently available.'
            );
        }

        $timezone = (string) config(
            'app.timezone',
            'Asia/Makassar'
        );

        $now = ($now ?? now($timezone))
            ->copy()
            ->setTimezone($timezone);

        $travelDate = $this->parseTravelDate(
            $date,
            $timezone
        );

        if (
            $travelDate->lt(
                $now->copy()->startOfDay()
            )
        ) {
            throw new InvalidArgumentException(
                'The selected date cannot be in the past.'
            );
        }

        $participantCounts =
            $this->normalizeParticipantCounts(
                $participants
            );

        $totalParticipants = array_sum(
            $participantCounts
        );

        if ($totalParticipants < 1) {
            throw new InvalidArgumentException(
                'At least one participant is required.'
            );
        }

        $vehicleRecommendation = $this->vehicleRecommendationService
            ->recommend($participantCounts);

        $requestedLanguage = $this->normalizeLanguage($language);

        $availableOptions = [];
        $unavailableOptions = [];

        foreach ($this->getActiveOptions($tour) as $option) {
            $languageResult =
                $this->checkLanguageAvailability(
                    $option,
                    $requestedLanguage
                );

            if (!$languageResult['supported']) {
                $unavailableOptions[] = [
                    'option_id' => $option->id,
                    'title' => $option->title,
                    'reason_code' =>
                        'language_not_supported',
                    'message' =>
                        'The selected language is not '
                        . 'available for this option.',
                    'available_languages' =>
                        $languageResult[
                            'available_languages'
                        ],
                ];

                continue;
            }

            try {
                $pricing = $this->pricingService
                    ->calculate(
                        $option,
                        $participantCounts,
                        $travelDate
                    );
            } catch (InvalidArgumentException $exception) {
                $unavailableOptions[] = [
                    'option_id' => $option->id,
                    'title' => $option->title,
                    'reason_code' =>
                        'pricing_or_participant_error',
                    'message' =>
                        $exception->getMessage(),
                ];

                continue;
            }

            $matchingSchedules =
                $this->getSchedulesForDate(
                    $option,
                    $travelDate
                );

            if ($matchingSchedules->isEmpty()) {
                $unavailableOptions[] = [
                    'option_id' => $option->id,
                    'title' => $option->title,
                    'reason_code' =>
                        'no_schedule_for_date',
                    'message' =>
                        'This option has no operating '
                        . 'schedule for the selected date.',
                ];

                continue;
            }

            $availableStartingTimes = [];
            $unavailableStartingTimes = [];

            foreach ($matchingSchedules as $schedule) {
                $scheduleResult =
                    $this->evaluateSchedule(
                        $option,
                        $schedule,
                        $travelDate,
                        $now,
                        $totalParticipants,
                        $timezone
                    );

                if ($scheduleResult['available']) {
                    $availableStartingTimes[] =
                        $scheduleResult;
                } else {
                    $unavailableStartingTimes[] =
                        $scheduleResult;
                }
            }

            if ($availableStartingTimes === []) {
                $unavailableOptions[] = [
                    'option_id' => $option->id,
                    'title' => $option->title,
                    'reason_code' =>
                        'no_available_start_time',
                    'message' =>
                        'No starting time is available '
                        . 'for the selected date.',
                    'unavailable_starting_times' =>
                        $unavailableStartingTimes,
                ];

                continue;
            }

            $availableOptions[] = [
                'option_id' => $option->id,
                'tour_package_id' =>
                    $option->tour_package_id,

                'title' => $option->title,
                'slug' => $option->slug,

                'short_description' =>
                    $option->short_description,

                'duration_minutes' =>
                    $option->duration_minutes,

                'languages' =>
                    $languageResult[
                        'available_languages'
                    ],

                'requested_language' =>
                    $requestedLanguage,

                /*
                 * true jika bahasa belum dikonfigurasi
                 * dan perlu dikonfirmasi Newman.
                 */
                'language_confirmation_required' =>
                    $languageResult[
                        'confirmation_required'
                    ],

                'pickup_type' =>
                    $option->pickup_type,

                'pickup_label' =>
                    $option->pickup_label,

                'is_all_inclusive' =>
                    (bool) $option->is_all_inclusive,

                'pricing' => $pricing,

                'included_items' =>
                    $this->itemPayload(
                        $this->getIncludedItems($option)
                    ),

                'excluded_items' =>
                    $this->itemPayload(
                        $this->getExcludedItems($option)
                    ),

                'starting_times' =>
                    $availableStartingTimes,

                /*
                 * Berguna untuk debugging dan admin.
                 * Nanti tidak harus ditampilkan semuanya
                 * kepada pengunjung.
                 */
                'unavailable_starting_times' =>
                    $unavailableStartingTimes,
            ];
        }

        return [
    'tour_package_id' => $tour->id,
    'tour_title' => $tour->title,

    'date' => $travelDate->toDateString(),
    'day_of_week' => $travelDate->dayOfWeek,
    'day_name' => $travelDate->format('l'),

    'participants' => $participantCounts,
    'total_participants' => $totalParticipants,

    'recommended_transport' =>
        $vehicleRecommendation,

    'requested_language' => $requestedLanguage,

    'available' => $availableOptions !== [],

    'available_options' => $availableOptions,
    'unavailable_options' => $unavailableOptions,

    'checked_at' => $now->toIso8601String(),
];
    }

    private function getActiveOptions(
        TourPackage $tour
    ): Collection {
        /*
         * Mendukung object sementara pada automated test.
         */
        if ($tour->relationLoaded('options')) {
            return $tour
                ->getRelation('options')
                ->filter(
                    fn (TourOption $option): bool =>
                        $option->status === 'active'
                )
                ->sortBy('sort_order')
                ->values();
        }

        return $tour
            ->activeOptions()
            ->with([
                'prices',
                'activeSchedules',
                'activeBlackoutDates',
                'discounts',
                'includedItems',
                'excludedItems',
            ])
            ->get();
    }

    private function getSchedulesForDate(
        TourOption $option,
        Carbon $travelDate
    ): Collection {
        $schedules = $option->relationLoaded(
            'activeSchedules'
        )
            ? $option->getRelation('activeSchedules')
            : $option->activeSchedules()->get();

        return $schedules
            ->filter(
                fn (
                    TourOptionSchedule $schedule
                ): bool =>
                    $this->scheduleMatchesDate(
                        $schedule,
                        $travelDate
                    )
            )
            ->sortBy(
                fn (
                    TourOptionSchedule $schedule
                ): string =>
                    $this->normalizeTime(
                        $schedule->start_time
                    ) ?? '99:99'
            )
            ->values();
    }

    private function scheduleMatchesDate(
        TourOptionSchedule $schedule,
        Carbon $travelDate
    ): bool {
        if (!$schedule->is_active) {
            return false;
        }

        if (
            (int) $schedule->day_of_week
            !== $travelDate->dayOfWeek
        ) {
            return false;
        }

        $date = $travelDate->toDateString();

        if (
            $schedule->available_from !== null
            && $date
                < $schedule->available_from
                    ->toDateString()
        ) {
            return false;
        }

        if (
            $schedule->available_until !== null
            && $date
                > $schedule->available_until
                    ->toDateString()
        ) {
            return false;
        }

        return true;
    }

    private function evaluateSchedule(
        TourOption $option,
        TourOptionSchedule $schedule,
        Carbon $travelDate,
        Carbon $now,
        int $totalParticipants,
        string $timezone
    ): array {
        $startTime = $this->normalizeTime(
            $schedule->start_time
        );

        if ($startTime === null) {
            return [
                'schedule_id' => $schedule->id,
                'available' => false,
                'reason_code' => 'invalid_start_time',
                'message' =>
                    'This schedule has an invalid '
                    . 'starting time.',
            ];
        }

        $blackout = $this->findBlockingBlackout(
            $option,
            $travelDate,
            $startTime
        );

        if ($blackout !== null) {
            return [
                'schedule_id' => $schedule->id,
                'start_time' => $startTime,
                'available' => false,
                'reason_code' =>
                    $blackout['reason_code'],
                'message' => $blackout['message'],
            ];
        }

        $departureAt = Carbon::parse(
            $travelDate->toDateString()
            . ' '
            . $startTime,
            $timezone
        );

        $cutoffHours = max(
            0,
            (int) $schedule->booking_cutoff_hours
        );

        $bookingClosesAt = $departureAt
            ->copy()
            ->subHours($cutoffHours);

        if ($now->gte($bookingClosesAt)) {
            return [
                'schedule_id' => $schedule->id,
                'start_time' => $startTime,
                'available' => false,
                'reason_code' =>
                    'booking_cutoff_passed',
                'message' =>
                    'The booking cutoff for this '
                    . 'starting time has passed.',
                'booking_closes_at' =>
                    $bookingClosesAt
                        ->toIso8601String(),
            ];
        }

        $capacityLimit = $this->capacityLimit(
            $option,
            $schedule
        );

        if (
            $capacityLimit !== null
            && $totalParticipants > $capacityLimit
        ) {
            return [
                'schedule_id' => $schedule->id,
                'start_time' => $startTime,
                'available' => false,
                'reason_code' =>
                    'capacity_exceeded',
                'message' =>
                    "This starting time allows a maximum "
                    . "of {$capacityLimit} participants.",
                'capacity_limit' => $capacityLimit,
            ];
        }

        return [
            'schedule_id' => $schedule->id,
            'available' => true,

            'start_time' => $startTime,

            'end_time' => $this->normalizeTime(
                $schedule->end_time
            ),

            'departure_at' =>
                $departureAt->toIso8601String(),

            'booking_cutoff_hours' =>
                $cutoffHours,

            'booking_closes_at' =>
                $bookingClosesAt->toIso8601String(),

            /*
             * Ini adalah batas kapasitas konfigurasi.
             * Pengurangan reserved booking ditambahkan
             * saat booking snapshot sudah memiliki
             * option dan starting time.
             */
            'capacity_limit' => $capacityLimit,
        ];
    }

    private function findBlockingBlackout(
        TourOption $option,
        Carbon $travelDate,
        string $startTime
    ): ?array {
        $blackouts = $option->relationLoaded(
            'activeBlackoutDates'
        )
            ? $option->getRelation(
                'activeBlackoutDates'
            )
            : $option->activeBlackoutDates()->get();

        foreach ($blackouts as $blackout) {
            if (!$blackout->is_active) {
                continue;
            }

            $blackoutDate =
                $this->dateString(
                    $blackout->blackout_date
                );

            if (
                $blackoutDate
                !== $travelDate->toDateString()
            ) {
                continue;
            }

            if ($blackout->blocks_entire_day) {
                return [
                    'reason_code' =>
                        'blackout_entire_day',
                    'message' =>
                        $blackout->reason
                        ?: 'This date is unavailable.',
                ];
            }

            if (
                $blackout->blocksStartTime(
                    $startTime
                )
            ) {
                return [
                    'reason_code' =>
                        'blackout_start_time',
                    'message' =>
                        $blackout->reason
                        ?: 'This starting time is '
                            . 'unavailable.',
                ];
            }
        }

        return null;
    }

    /**
     * Memakai batas paling kecil antara:
     * option.max_guests dan schedule.capacity.
     */
    private function capacityLimit(
        TourOption $option,
        TourOptionSchedule $schedule
    ): ?int {
        $limits = [];

        if ($option->max_guests !== null) {
            $limits[] = (int) $option->max_guests;
        }

        if ($schedule->capacity !== null) {
            $limits[] = (int) $schedule->capacity;
        }

        if ($limits === []) {
            return null;
        }

        return min($limits);
    }

    private function checkLanguageAvailability(
        TourOption $option,
        ?string $requestedLanguage
    ): array {
        $languages = collect(
            $option->languages ?? []
        )
            ->filter(
                fn (mixed $language): bool =>
                    is_string($language)
                    && trim($language) !== ''
            )
            ->map(
                fn (string $language): string =>
                    trim($language)
            )
            ->values()
            ->all();

        if ($requestedLanguage === null) {
            return [
                'supported' => true,
                'confirmation_required' => false,
                'available_languages' => $languages,
            ];
        }

        /*
         * Bahasa belum dikonfigurasi.
         * Option tidak langsung ditolak, tetapi
         * Newman harus mengonfirmasi bahasa tersebut.
         */
        if ($languages === []) {
            return [
                'supported' => true,
                'confirmation_required' => true,
                'available_languages' => [],
            ];
        }

        $normalizedLanguages = array_map(
            fn (string $language): string =>
                strtolower($language),
            $languages
        );

        return [
            'supported' => in_array(
                strtolower($requestedLanguage),
                $normalizedLanguages,
                true
            ),
            'confirmation_required' => false,
            'available_languages' => $languages,
        ];
    }

    private function getIncludedItems(
        TourOption $option
    ): Collection {
        if ($option->relationLoaded('includedItems')) {
            return $option->getRelation(
                'includedItems'
            );
        }

        return $option->includedItems()->get();
    }

    private function getExcludedItems(
        TourOption $option
    ): Collection {
        if ($option->relationLoaded('excludedItems')) {
            return $option->getRelation(
                'excludedItems'
            );
        }

        return $option->excludedItems()->get();
    }

    private function itemPayload(
        Collection $items
    ): array {
        return $items
            ->map(
                fn ($item): array => [
                    'id' => $item->id,
                    'category' => $item->category,
                    'label' => $item->label,
                    'details' => $item->details,
                    'is_highlighted' =>
                        (bool) $item->is_highlighted,
                ]
            )
            ->values()
            ->all();
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

            if ($value === null || $value === '') {
                $normalized[$type] = 0;
                continue;
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
                    $this->participantLabel($type)
                    . ' count must be a whole number.'
                );
            }

            if ($count < 0) {
                throw new InvalidArgumentException(
                    $this->participantLabel($type)
                    . ' count cannot be negative.'
                );
            }

            $normalized[$type] = $count;
        }

        return $normalized;
    }

    private function parseTravelDate(
        string $date,
        string $timezone
    ): Carbon {
        $date = trim($date);

        if (
            preg_match(
                '/^\d{4}-\d{2}-\d{2}$/',
                $date
            ) !== 1
        ) {
            throw new InvalidArgumentException(
                'The date must use YYYY-MM-DD format.'
            );
        }

        try {
            $parsed = Carbon::createFromFormat(
                'Y-m-d',
                $date,
                $timezone
            );
        } catch (Throwable) {
            throw new InvalidArgumentException(
                'The selected date is invalid.'
            );
        }

        if (
            !$parsed
            || $parsed->format('Y-m-d') !== $date
        ) {
            throw new InvalidArgumentException(
                'The selected date is invalid.'
            );
        }

        return $parsed->startOfDay();
    }

    private function normalizeTime(
        mixed $time
    ): ?string {
        if ($time === null) {
            return null;
        }

        $value = substr(
            trim((string) $time),
            0,
            5
        );

        if (
            preg_match(
                '/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                $value
            ) !== 1
        ) {
            return null;
        }

        return $value;
    }

    private function dateString(
        mixed $date
    ): string {
        if ($date instanceof DateTimeInterface) {
            return $date->format('Y-m-d');
        }

        return substr((string) $date, 0, 10);
    }

    private function normalizeLanguage(
        ?string $language
    ): ?string {
        if ($language === null) {
            return null;
        }

        $language = trim($language);

        return $language === ''
            ? null
            : $language;
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
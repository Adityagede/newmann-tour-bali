<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TourOption;
use App\Models\TourOptionPrice;
use App\Models\TourPackage;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final class TourPublishingReadinessService
{
    private const REQUIRED_PARTICIPANT_TYPES = [
        TourOptionPrice::TYPE_ADULT,
        TourOptionPrice::TYPE_CHILD,
        TourOptionPrice::TYPE_INFANT,
    ];

    public function inspect(
        TourPackage $tourPackage
    ): array {
        $tourPackage->loadMissing([
            'options' => fn ($query) =>
                $query
                    ->orderBy('sort_order')
                    ->orderBy('id'),

            'options.prices',
            'options.items',
            'options.schedules',
            'sharedStops',
        ]);

        $checks = [];

        $galleryImages = $this->arrayValue(
            $tourPackage->gallery_images
        );

        $highlights = $this->arrayValue(
            $tourPackage->highlights
        );

        $activeSharedStops = $tourPackage
            ->sharedStops
            ->where('is_active', true)
            ->values();

        $options = $tourPackage
            ->options
            ->sortBy('sort_order')
            ->values();

        $optionReports = $options
            ->map(
                fn (TourOption $option): array =>
                    $this->inspectOption($option)
            )
            ->values();

        $activeOptionReports = $optionReports
            ->where('status', 'active')
            ->values();

        $this->addCheck(
            $checks,
            'product_title',
            'Tour title',
            $this->hasText($tourPackage->title),
            'Add a clear tour title.'
        );

        $this->addCheck(
            $checks,
            'product_slug',
            'Tour slug',
            $this->hasText($tourPackage->slug),
            'The tour must have a valid URL slug.'
        );

        $this->addCheck(
            $checks,
            'product_category',
            'Category',
            $this->hasText($tourPackage->category),
            'Select or enter a tour category.'
        );

        $this->addCheck(
            $checks,
            'product_area',
            'Area',
            $this->hasText($tourPackage->area),
            'Add the main operating area.'
        );

        $this->addCheck(
            $checks,
            'product_format',
            'Tour format',
            $this->hasText($tourPackage->tour_format),
            'Select the tour format.'
        );

        $this->addCheck(
            $checks,
            'product_duration',
            'Display duration',
            $this->hasText($tourPackage->duration),
            'Add a guest-facing display duration.'
        );

        $this->addCheck(
            $checks,
            'product_description',
            'Card description',
            $this->hasText($tourPackage->description),
            'Add a concise card description.'
        );

        $this->addCheck(
            $checks,
            'product_intro',
            'Detail introduction',
            $this->hasText($tourPackage->intro),
            'Add an introduction for the detail page.'
        );

        $this->addCheck(
            $checks,
            'product_story',
            'Experience story',
            $this->hasText($tourPackage->story),
            'Add factual editorial content for the experience.'
        );

        $this->addCheck(
            $checks,
            'main_image',
            'Main image',
            $this->hasText($tourPackage->main_image),
            'Upload a main image for the tour.'
        );

        $this->addCheck(
            $checks,
            'gallery_images',
            'Tour gallery',
            count($galleryImages) >= 3,
            'Add at least three authentic tour gallery images.',
            [
                'current_count' =>
                    count($galleryImages),

                'minimum_count' => 3,
            ]
        );

        $this->addCheck(
            $checks,
            'tour_highlights',
            'Tour highlights',
            count($highlights) >= 3,
            'Add at least three clear tour highlights.',
            [
                'current_count' =>
                    count($highlights),

                'minimum_count' => 3,
            ]
        );

        $this->addCheck(
            $checks,
            'shared_roadmap',
            'Shared roadmap',
            $activeSharedStops->count() >= 2,
            'Add at least two active shared roadmap stops.',
            [
                'current_count' =>
                    $activeSharedStops->count(),

                'minimum_count' => 2,
            ]
        );

        $this->addCheck(
            $checks,
            'tour_options',
            'Tour options',
            $options->isNotEmpty(),
            'Create at least one Tour Option.'
        );

        $defaultOptionsCount = $options
            ->where('is_default', true)
            ->count();

        $this->addCheck(
            $checks,
            'default_option',
            'Default option',
            $defaultOptionsCount === 1,
            'Exactly one Tour Option must be marked as default.',
            [
                'current_count' =>
                    $defaultOptionsCount,
            ]
        );

        $this->addCheck(
            $checks,
            'active_option',
            'Active booking option',
            $activeOptionReports->isNotEmpty(),
            'Activate at least one fully configured Tour Option.'
        );

        $activeOptionsReady =
            $activeOptionReports->isNotEmpty()
            && $activeOptionReports->every(
                fn (array $report): bool =>
                    $report['configuration_ready']
            );

        $this->addCheck(
            $checks,
            'active_options_ready',
            'Active option configuration',
            $activeOptionsReady,
            'Every active option must pass its configuration checks.'
        );

        $publishingFlagsSafe =
            (string) $tourPackage->status === 'active'
            || (
                !(bool) $tourPackage->is_popular
                && !(bool) $tourPackage->is_featured
            );

        $this->addCheck(
            $checks,
            'publishing_flags',
            'Popular and featured flags',
            $publishingFlagsSafe,
            'Keep Popular and Featured disabled while the product is not active.',
            severity: 'warning'
        );

        $failedBlockingChecks = collect($checks)
            ->where('severity', 'blocking')
            ->where('passed', false)
            ->values();

        $failedWarnings = collect($checks)
            ->where('severity', 'warning')
            ->where('passed', false)
            ->values();

        $activeOptionBlockingCount =
            $activeOptionReports->sum(
                fn (array $report): int =>
                    $report['blocking_count']
            );

        $optionWarningCount = $optionReports->sum(
            fn (array $report): int =>
                $report['warning_count']
        );

        return [
            'tour_package_id' =>
                $tourPackage->id,

            'tour_title' =>
                $tourPackage->title,

            'tour_status' =>
                $tourPackage->status,

            /*
             * true berarti konfigurasi sudah cukup
             * untuk mempublikasikan Tour Product.
             */
            'ready_to_publish' =>
                $failedBlockingChecks->isEmpty()
                && $activeOptionsReady,

            'blocking_count' =>
                $failedBlockingChecks->count()
                + $activeOptionBlockingCount,

            'warning_count' =>
                $failedWarnings->count()
                + $optionWarningCount,

            'summary' => [
                'gallery_images' =>
                    count($galleryImages),

                'highlights' =>
                    count($highlights),

                'active_shared_stops' =>
                    $activeSharedStops->count(),

                'all_options' =>
                    $options->count(),

                'active_options' =>
                    $activeOptionReports->count(),

                'default_options' =>
                    $defaultOptionsCount,
            ],

            'checks' => $checks,

            'option_reports' =>
                $optionReports->all(),

            'inspected_at' =>
                now(config('app.timezone'))
                    ->toIso8601String(),
        ];
    }

    public function inspectOption(
        TourOption $tourOption
    ): array {
        $tourOption->loadMissing([
    'prices',
    'items',
    'schedules',
]);
        $checks = [];

        $prices = $tourOption
            ->prices
            ->keyBy('participant_type');

        $items = $tourOption->items;

        $activeIncludedItems = $items
            ->where('item_type', 'included')
            ->where('is_active', true)
            ->values();

        $activeExcludedItems = $items
            ->where('item_type', 'excluded')
            ->where('is_active', true)
            ->values();

        $activeSchedules = $tourOption
            ->schedules
            ->where('is_active', true)
            ->values();

        $today = now(
            config('app.timezone')
        )->startOfDay();

        $futureSchedules = $activeSchedules
            ->filter(function ($schedule) use (
                $today
            ): bool {
                if (
                    $schedule->available_until
                    === null
                ) {
                    return true;
                }

                return $this->dateValue(
                    $schedule->available_until
                )
                    ->endOfDay()
                    ->gte($today);
            })
            ->values();

        $languages = collect(
            $this->arrayValue(
                $tourOption->languages
            )
        )
            ->filter(
                fn (mixed $language): bool =>
                    is_string($language)
                    && trim($language) !== ''
            )
            ->values();

        $this->addCheck(
            $checks,
            'option_title',
            'Option title',
            $this->hasText($tourOption->title),
            'Add a clear Tour Option title.'
        );

        $this->addCheck(
            $checks,
            'option_duration',
            'Option duration',
            (int) $tourOption->duration_minutes > 0,
            'Set the Tour Option duration in minutes.'
        );

        $this->addCheck(
            $checks,
            'option_languages',
            'Available languages',
            $languages->isNotEmpty(),
            'Add at least one available language.'
        );

        $pickupIsValid =
            $this->hasText($tourOption->pickup_type)
            && (
                $tourOption->pickup_type === 'flexible'
                || $this->hasText(
                    $tourOption->pickup_label
                )
            );

        $this->addCheck(
            $checks,
            'option_pickup',
            'Pickup arrangement',
            $pickupIsValid,
            'Add a pickup label unless the pickup type is flexible.'
        );

        $minimumGuests =
            (int) $tourOption->min_guests;

        $maximumGuests =
            $tourOption->max_guests === null
                ? null
                : (int) $tourOption->max_guests;

        $groupLimitsValid =
            $minimumGuests >= 1
            && (
                $maximumGuests === null
                || $maximumGuests
                    >= $minimumGuests
            );

        $this->addCheck(
            $checks,
            'participant_limits',
            'Participant limits',
            $groupLimitsValid,
            'Correct the minimum and maximum participant limits.'
        );

        $missingParticipantTypes = collect(
            self::REQUIRED_PARTICIPANT_TYPES
        )
            ->reject(
                fn (string $participantType): bool =>
                    $prices->has($participantType)
            )
            ->values();

        $this->addCheck(
            $checks,
            'participant_price_rows',
            'Adult, Child, and Infant rows',
            $missingParticipantTypes->isEmpty(),
            'Configure Adult, Child, and Infant participant rows.',
            [
                'missing_types' =>
                    $missingParticipantTypes->all(),
            ]
        );

        $allowedPrices = $prices
            ->filter(
                fn ($price): bool =>
                    (bool) $price->is_allowed
            );

        $this->addCheck(
            $checks,
            'allowed_participants',
            'Allowed participant categories',
            $allowedPrices->isNotEmpty(),
            'At least one participant category must be allowed.'
        );

        $adultAllowed =
            $prices->has(
                TourOptionPrice::TYPE_ADULT
            )
            && (bool) $prices[
                TourOptionPrice::TYPE_ADULT
            ]->is_allowed;

        $this->addCheck(
            $checks,
            'adult_participant',
            'Adult participant',
            $adultAllowed,
            'Adult must be allowed for this Tour Option.'
        );

        $invalidPrices = $allowedPrices
            ->filter(function ($price): bool {
                if ((bool) $price->is_free) {
                    return (int) $price->base_price
                        !== 0;
                }

                return (int) $price->base_price
                    <= 0;
            })
            ->values();

        $this->addCheck(
            $checks,
            'participant_prices',
            'Participant price values',
            $invalidPrices->isEmpty(),
            'Paid participants need a price above zero and free participants must use zero.',
            [
                'invalid_types' =>
                    $invalidPrices
                        ->pluck('participant_type')
                        ->all(),
            ]
        );

        $invalidCurrencies = $allowedPrices
            ->filter(
                fn ($price): bool =>
                    strtoupper(
                        (string) $price->currency
                    ) !== 'IDR'
            )
            ->values();

        $this->addCheck(
            $checks,
            'price_currency',
            'Price currency',
            $invalidCurrencies->isEmpty(),
            'Every allowed participant price must use IDR.'
        );

        $this->addCheck(
            $checks,
            'active_schedule',
            'Active future schedule',
            $futureSchedules->isNotEmpty(),
            'Add at least one active schedule that has not expired.'
        );

        $includedRequirementPassed =
            !(bool) $tourOption->is_all_inclusive
            || $activeIncludedItems->isNotEmpty();

        $this->addCheck(
            $checks,
            'included_items',
            'Included items',
            $includedRequirementPassed,
            'An all-inclusive option must have at least one active Included item.'
        );

        $this->addCheck(
            $checks,
            'excluded_items',
            'Excluded items',
            $activeExcludedItems->isNotEmpty(),
            'Add at least one active Excluded item so guests understand the limitations.',
            severity: 'warning'
        );

        $capacityMismatch = $activeSchedules
            ->filter(function ($schedule) use (
                $maximumGuests
            ): bool {
                return $maximumGuests !== null
                    && $schedule->capacity !== null
                    && (int) $schedule->capacity
                        > $maximumGuests;
            })
            ->values();

        $this->addCheck(
            $checks,
            'schedule_capacity',
            'Schedule capacity consistency',
            $capacityMismatch->isEmpty(),
            'A schedule capacity is higher than the Tour Option maximum. The smaller limit will be used.',
            severity: 'warning'
        );

        $blockingCount = collect($checks)
            ->where('severity', 'blocking')
            ->where('passed', false)
            ->count();

        $warningCount = collect($checks)
            ->where('severity', 'warning')
            ->where('passed', false)
            ->count();

        return [
            'option_id' =>
                $tourOption->id,

            'title' =>
                $tourOption->title,

            'slug' =>
                $tourOption->slug,

            'status' =>
                $tourOption->status,

            'is_default' =>
                (bool) $tourOption->is_default,

            /*
             * Konfigurasi boleh siap walaupun option
             * masih berstatus Draft.
             */
            'configuration_ready' =>
                $blockingCount === 0,

            'blocking_count' =>
                $blockingCount,

            'warning_count' =>
                $warningCount,

            'summary' => [
                'prices' =>
                    $prices->count(),

                'allowed_prices' =>
                    $allowedPrices->count(),

                'languages' =>
                    $languages->count(),

                'active_included_items' =>
                    $activeIncludedItems->count(),

                'active_excluded_items' =>
                    $activeExcludedItems->count(),

                'active_schedules' =>
                    $activeSchedules->count(),

                'future_schedules' =>
                    $futureSchedules->count(),
            ],

            'checks' => $checks,
        ];
    }

        public function assertOptionCanActivate(
    TourOption $tourOption
): array {
    $report = $this->inspectOption(
        $tourOption
    );

    if ($report['configuration_ready']) {
        return $report;
    }

    $messages = collect(
        $report['checks']
    )
        ->where('severity', 'blocking')
        ->where('passed', false)
        ->pluck('message')
        ->filter()
        ->unique()
        ->values()
        ->all();

    if ($messages === []) {
        $messages = [
            'The Tour Option is not ready to be activated.',
        ];
    }

    throw ValidationException::withMessages([
        'status' => $messages,
    ]);
}

public function assertTourCanPublish(
    TourPackage $tourPackage
): array {
    $report = $this->inspect(
        $tourPackage
    );

    if ($report['ready_to_publish']) {
        return $report;
    }

    $messages = collect(
        $report['checks']
    )
        ->where('severity', 'blocking')
        ->where('passed', false)
        ->pluck('message')
        ->filter()
        ->values();

    foreach (
        $report['option_reports']
        as $optionReport
    ) {
        /*
         * Hanya active option yang menjadi bagian
         * dari publishing readiness produk.
         */
        if (
            (string) $optionReport['status']
            !== 'active'
        ) {
            continue;
        }

        $optionMessages = collect(
            $optionReport['checks']
        )
            ->where('severity', 'blocking')
            ->where('passed', false)
            ->pluck('message')
            ->filter()
            ->map(
                fn (string $message): string =>
                    $optionReport['title']
                    . ': '
                    . $message
            );

        $messages = $messages->concat(
            $optionMessages
        );
    }

    $messages = $messages
        ->unique()
        ->values()
        ->all();

    if ($messages === []) {
        $messages = [
            'The Tour Product is not ready to be published.',
        ];
    }

    throw ValidationException::withMessages([
        'status' => $messages,
    ]);
}

    private function addCheck(
        array &$checks,
        string $key,
        string $label,
        bool $passed,
        string $message,
        array $context = [],
        string $severity = 'blocking'
    ): void {
        $checks[] = [
            'key' => $key,
            'label' => $label,
            'passed' => $passed,
            'severity' => $severity,
            'message' => $message,
            'context' => $context,
        ];
    }

    private function hasText(
        mixed $value
    ): bool {
        return is_string($value)
            && trim($value) !== '';
    }

    private function arrayValue(
        mixed $value
    ): array {
        if ($value instanceof Collection) {
            return $value->values()->all();
        }

        if (is_array($value)) {
            return array_values($value);
        }

        if (
            is_string($value)
            && trim($value) !== ''
        ) {
            $decoded = json_decode(
                $value,
                true
            );

            return is_array($decoded)
                ? array_values($decoded)
                : [];
        }

        return [];
    }

    private function dateValue(
        mixed $value
    ): Carbon {
        if ($value instanceof Carbon) {
            return $value->copy();
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value);
        }

        return Carbon::parse(
            (string) $value,
            config('app.timezone')
        );
    }
}


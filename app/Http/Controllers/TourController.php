<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckTourAvailabilityRequest;
use App\Models\TourPackage;
use App\Services\TourAvailabilityService;
use App\Support\TourViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use InvalidArgumentException;

final class TourController extends Controller
{
    /**
     * Menampilkan seluruh Tour Product yang aktif.
     */
    public function index(): View
    {
        $tours = TourPackage::query()
            ->where('status', 'active')
            ->with(['defaultActiveOption.prices'])
            ->latest()
            ->get();

        $categories = $tours
            ->pluck('category')
            ->filter(
                fn (mixed $category): bool =>
                    is_string($category)
                    && trim($category) !== ''
            )
            ->map(
                fn (string $category): array => [
                    'key' => Str::slug($category),
                    'label' => Str::headline($category),
                ]
            )
            ->unique('key')
            ->values();

        return view('pages.tours', [
            'tours' => $tours,
            'categories' => $categories,
        ]);
    }

    /**
     * Menampilkan detail publik berdasarkan slug.
     * Produk Draft tetap tidak dapat dibuka dari route publik.
     */
    public function show(string $slug): View
    {
        $tourPackage = TourPackage::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->with(['defaultActiveOption.prices'])
            ->firstOrFail();

        return view('pages.tour-detail', [
            'tour' => $this->detailDataWithBooking(
                $tourPackage
            ),
            'isPreview' => false,
        ]);
    }

    /**
     * Preview admin untuk produk Draft tanpa mengubah status database.
     * Route method ini wajib ditempatkan di dalam group admin.
     */
    public function preview(
        TourPackage $tourPackage
    ): View {
        return view('pages.tour-detail', [
            'tour' => $this->detailDataWithBooking(
                $tourPackage
            ),
            'isPreview' => true,
        ]);
    }

    /**
     * Availability publik hanya untuk Tour Product aktif.
     */
    public function availability(
        CheckTourAvailabilityRequest $request,
        string $slug,
        TourAvailabilityService $availabilityService
    ): JsonResponse {
        $tourPackage = TourPackage::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        return $this->availabilityResponse(
            $request,
            $tourPackage,
            $availabilityService
        );
    }

    /**
     * Availability khusus preview admin.
     * Clone dibuat aktif hanya di memori agar service dapat dijalankan.
     */
    public function previewAvailability(
        CheckTourAvailabilityRequest $request,
        TourPackage $tourPackage,
        TourAvailabilityService $availabilityService
    ): JsonResponse {
        $candidate = clone $tourPackage;
        $candidate->unsetRelations();
        $candidate->status = 'active';

        return $this->availabilityResponse(
            $request,
            $candidate,
            $availabilityService
        );
    }

    /**
     * Menjalankan availability V2 dan membentuk response stabil untuk UI.
     */
    private function availabilityResponse(
        CheckTourAvailabilityRequest $request,
        TourPackage $tourPackage,
        TourAvailabilityService $availabilityService
    ): JsonResponse {
        $validated = $request->validated();

        $participants = [
            'adult' => (int) data_get(
                $validated,
                'participants.adult',
                0
            ),
            'child' => (int) data_get(
                $validated,
                'participants.child',
                0
            ),
            'infant' => (int) data_get(
                $validated,
                'participants.infant',
                0
            ),
        ];

        $language = $validated['language'] ?? null;

        if (is_string($language)) {
            $language = trim($language);
            $language = $language !== ''
                ? $language
                : null;
        }

        try {
            $availability = $availabilityService->check(
                $tourPackage,
                (string) $validated['travel_date'],
                $participants,
                $language
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'available' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

     /*
 * TourAvailabilityService menggunakan
 * available_options sebagai hasil option tersedia.
 *
 * Fallback options dipertahankan agar controller
 * tetap kompatibel bila struktur service berubah.
 */
$rawOptions = $availability['available_options']
    ?? $availability['options']
    ?? [];


    logger()->debug(
    'Tour availability service structure',
    [
        'keys' =>
            array_keys($availability),

        'available_options_count' =>
            is_countable(
                $availability[
                    'available_options'
                ] ?? null
            )
                ? count(
                    $availability[
                        'available_options'
                    ]
                )
                : null,

        'options_count' =>
            is_countable(
                $availability['options']
                ?? null
            )
                ? count(
                    $availability['options']
                )
                : null,
    ]
);


$options = $this->normalizeAvailabilityOptions(
    $tourPackage,
    $rawOptions
);

logger()->debug(
    'Tour availability option normalization',
    [
        'tour_id' =>
            $tourPackage->id,

        'raw_options_count' =>
            is_countable($rawOptions)
                ? count($rawOptions)
                : null,

        'normalized_options_count' =>
            count($options),

        'raw_options' =>
            $rawOptions,
    ]
);

$optionsCount = count($options);

/*
 * Untuk UI, available berarti minimal satu option
 * sudah berhasil dinormalisasi dan dapat ditampilkan.
 */
$available = $optionsCount > 0;
            

        $travelDate = Carbon::createFromFormat(
            'Y-m-d',
            (string) $validated['travel_date'],
            config('app.timezone')
        );

        return response()->json([
    'available' => $available,

    'message' => $available
    ? 'Available Tour Options were found.'
    : 'No Tour Options are available for this selection.',

    'tour' => [
        'id' => $tourPackage->id,
        'title' => $tourPackage->title,
        'slug' => $tourPackage->slug,
    ],

    'selection' => [
        'travel_date' =>
            $travelDate->toDateString(),

        'date_label' =>
            $travelDate->format('d M Y'),

        'language' => $language,

        'participants' => $participants,

        'total_participants' =>
            array_sum($participants),

        'participant_label' =>
            $this->participantLabel(
                $participants
            ),
    ],

    'options' => $options,

    'recommended_transport' =>
        $availability['recommended_transport']
        ?? null,

    'unavailable_options' =>
        $availability['unavailable_options']
        ?? [],

    'availability' => $availability,
]);
    }


    







    private function normalizeAvailabilityOptions(
    TourPackage $tourPackage,
    mixed $rawOptions
): array {
    $rawOptions = $this->toArray(
        $rawOptions
    );

    if ($rawOptions === []) {
        return [];
    }

    /*
     * Availability Service menentukan option mana
     * yang tersedia. Database hanya digunakan untuk
     * memperkaya detail statis option tersebut.
     */
    $optionModels = $tourPackage
        ->options()
        ->where('status', 'active')
        ->with([
            'items' => fn ($query) =>
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id'),
        ])
        ->orderByDesc('is_default')
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    return collect($rawOptions)
        ->map(function (
            mixed $rawOption
        ) use ($optionModels): ?array {


          if (
    is_object($rawOption)
    && method_exists(
        $rawOption,
        'toArray'
    )
) {
    $rawOption = $rawOption->toArray();
}

if (!is_array($rawOption)) {
    return null;
}

/*
 * Availability Service dapat mengirim Tour Option
 * sebagai array atau Eloquent model di dalam key
 * "option" atau "tour_option".
 */
$embeddedOption = $rawOption['option']
    ?? $rawOption['tour_option']
    ?? [];

if (
    is_object($embeddedOption)
    && method_exists(
        $embeddedOption,
        'toArray'
    )
) {
    $embeddedOption =
        $embeddedOption->toArray();
}

if (!is_array($embeddedOption)) {
    $embeddedOption = [];
}

$identity = array_merge(
    $embeddedOption,
    $rawOption
);


            $optionId = (int) (
    $rawOption['option_id']
    ?? $rawOption['tour_option_id']
    ?? $embeddedOption['id']
    ?? $embeddedOption['option_id']
    ?? 0
);

            $optionSlug = trim(
    (string) (
        $rawOption['option_slug']
        ?? $embeddedOption['slug']
        ?? $rawOption['slug']
        ?? ''
    )
);

            $optionTitle = trim(
    (string) (
        $rawOption['option_title']
        ?? $embeddedOption['title']
        ?? $rawOption['title']
        ?? ''
    )
);

            $optionModel = $optionId > 0
                ? $optionModels->firstWhere(
                    'id',
                    $optionId
                )
                : null;

            if (
                !$optionModel
                && $optionSlug !== ''
            ) {
                $optionModel = $optionModels
                    ->firstWhere(
                        'slug',
                        $optionSlug
                    );
            }

            if (
                !$optionModel
                && $optionTitle !== ''
            ) {
                $optionModel = $optionModels
                    ->first(
                        fn ($option): bool =>
                            strcasecmp(
                                (string) $option->title,
                                $optionTitle
                            ) === 0
                    );
            }

            /*
 * Bila produk hanya mempunyai satu active option,
 * gunakan option tersebut ketika response service
 * tidak menyertakan identitas lengkap.
 */
if (
    !$optionModel
    && $optionModels->count() === 1
) {
    $optionModel =
        $optionModels->first();
}

            if (!$optionModel) {
                return null;
            }

            $startingTimes =
                $this->normalizeStartingTimes(
                    $rawOption['starting_times']
                    ?? $rawOption['start_times']
                    ?? $rawOption['available_times']
                    ?? $rawOption['times']
                    ?? $rawOption['departures']
                    ?? $rawOption['schedules']
                    ?? []
                );

            $pricingSource =
                $rawOption['pricing']
                ?? $rawOption['price']
                ?? $rawOption['totals']
                ?? $rawOption;

            $pricing =
                $this->normalizeOptionPricing(
                    $pricingSource
                );

            $languages = collect(
                $this->toArray(
                    $optionModel->languages
                )
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

            $included = $optionModel
                ->items
                ->where(
                    'item_type',
                    'included'
                )
                ->map(
                    fn ($item): array => [
                        'label' =>
                            (string) $item->label,

                        'details' =>
                            $item->details
                                ?: null,

                        'category' =>
                            $item->category
                                ?: null,

                        'highlighted' =>
                            (bool) $item
                                ->is_highlighted,
                    ]
                )
                ->values()
                ->all();

            $excluded = $optionModel
                ->items
                ->where(
                    'item_type',
                    'excluded'
                )
                ->map(
                    fn ($item): array => [
                        'label' =>
                            (string) $item->label,

                        'details' =>
                            $item->details
                                ?: null,

                        'category' =>
                            $item->category
                                ?: null,
                    ]
                )
                ->values()
                ->all();

            return [
                'option_id' =>
                    $optionModel->id,

                'slug' =>
                    $optionModel->slug,

                'title' =>
                    $optionModel->title,

                'short_description' =>
                    $optionModel
                        ->short_description
                        ?: null,

                'duration_minutes' =>
                    $optionModel
                        ->duration_minutes !== null
                            ? (int) $optionModel
                                ->duration_minutes
                            : null,

                'duration_label' =>
                    $this->durationLabel(
                        $optionModel
                            ->duration_minutes
                    ),

                'languages' => $languages,

                'pickup_type' =>
                    $optionModel->pickup_type,

                'pickup_label' =>
                    $optionModel->pickup_label
                    ?: $optionModel
                        ->pickup_type,

                'confirmation_note' =>
                    $optionModel
                        ->confirmation_note
                        ?: null,

                'minimum_participants' =>
                    (int) $optionModel
                        ->min_guests,

                'maximum_participants' =>
                    $optionModel
                        ->max_guests !== null
                            ? (int) $optionModel
                                ->max_guests
                            : null,

                'is_all_inclusive' =>
                    (bool) $optionModel
                        ->is_all_inclusive,

                'is_default' =>
                    (bool) $optionModel
                        ->is_default,

                'starting_times' =>
                    $startingTimes,

                'pricing' => $pricing,

                'included' => $included,

                'excluded' => $excluded,

                /*
                 * Data asli option dari service tetap
                 * tersedia untuk debugging dan perluasan.
                 */
                'service_data' => $rawOption,
            ];
        })
        ->filter()
        ->values()
        ->all();
}

private function normalizeStartingTimes(
    mixed $value
): array {
    if (
        is_array($value)
        && (
            isset($value['start_time'])
            || isset($value['time'])
            || isset($value['value'])
        )
    ) {
        $value = [$value];
    }

    return collect(
        $this->toArray($value)
    )
        ->map(function (
            mixed $time
        ): ?array {
            $label = null;

            if (is_array($time)) {
                $label = $time['label']
                    ?? $time['time_label']
                    ?? null;

                $time = $time['value']
                    ?? $time['start_time']
                    ?? $time['time']
                    ?? null;
            }

            if (!is_scalar($time)) {
                return null;
            }

            $time = trim(
                (string) $time
            );

            if ($time === '') {
                return null;
            }

            /*
             * Mengubah 06:00:00 menjadi 06:00.
             */
            if (
                preg_match(
                    '/^\d{2}:\d{2}:\d{2}$/',
                    $time
                )
            ) {
                $time = substr(
                    $time,
                    0,
                    5
                );
            }

            return [
                'value' => $time,
                'label' => is_string($label)
                    && trim($label) !== ''
                        ? trim($label)
                        : $time,
            ];
        })
        ->filter()
        ->unique('value')
        ->values()
        ->all();
}

private function normalizeOptionPricing(
    mixed $value
): array {
    $pricing = is_array($value)
        ? $value
        : [];

    $baseTotal = $this->numericAmount(
        $pricing['base_total']
        ?? $pricing['subtotal']
        ?? $pricing['base_amount']
        ?? null
    );

    $discountAmount = $this->numericAmount(
        $pricing['discount_amount']
        ?? $pricing['discount_total']
        ?? $pricing['savings']
        ?? 0
    ) ?? 0;

    $estimatedTotal = $this->numericAmount(
        $pricing['estimated_total']
        ?? $pricing['final_total']
        ?? $pricing['grand_total']
        ?? $pricing['total']
        ?? null
    );

    if (
        $estimatedTotal === null
        && $baseTotal !== null
    ) {
        $estimatedTotal = max(
            0,
            $baseTotal
                - $discountAmount
        );
    }

    $currency = strtoupper(
        trim(
            (string) (
                $pricing['currency']
                ?? 'IDR'
            )
        )
    );

    if ($currency === '') {
        $currency = 'IDR';
    }

    return [
        'base_total' => $baseTotal,

        'discount_amount' =>
            $discountAmount,

        'estimated_total' =>
            $estimatedTotal,

        'currency' => $currency,

        'discount_label' =>
            $pricing['discount_label']
            ?? $pricing['discount_name']
            ?? null,

        'formatted_base_total' =>
            $baseTotal !== null
                ? $this->formatMoney(
                    $baseTotal,
                    $currency
                )
                : null,

        'formatted_discount' =>
            $discountAmount > 0
                ? $this->formatMoney(
                    $discountAmount,
                    $currency
                )
                : null,

        'formatted_estimated_total' =>
            $estimatedTotal !== null
                ? $this->formatMoney(
                    $estimatedTotal,
                    $currency
                )
                : null,

        'breakdown' => $this->toArray(
            $pricing['breakdown']
            ?? $pricing['lines']
            ?? $pricing[
                'participant_breakdown'
            ]
            ?? []
        ),
    ];
}

private function numericAmount(
    mixed $value
): ?int {
    if (
        is_int($value)
        || is_float($value)
    ) {
        return (int) round($value);
    }

    if (
        is_string($value)
        && trim($value) !== ''
    ) {
        if (is_numeric($value)) {
            return (int) round(
                (float) $value
            );
        }

        $numbers = preg_replace(
            '/[^\d-]/',
            '',
            $value
        );

        return is_string($numbers)
            && $numbers !== ''
                ? (int) $numbers
                : null;
    }

    return null;
}

private function formatMoney(
    int $amount,
    string $currency = 'IDR'
): string {
    return strtoupper($currency)
        . ' '
        . number_format(
            $amount,
            0,
            ',',
            '.'
        );
}

private function durationLabel(
    mixed $minutes
): ?string {
    if (
        !is_numeric($minutes)
        || (int) $minutes <= 0
    ) {
        return null;
    }

    $minutes = (int) $minutes;

    $hours = intdiv(
        $minutes,
        60
    );

    $remainingMinutes =
        $minutes % 60;

    if (
        $hours > 0
        && $remainingMinutes > 0
    ) {
        return $hours
            . ' hr '
            . $remainingMinutes
            . ' min';
    }

    if ($hours > 0) {
        return $hours
            . ($hours === 1
                ? ' hour'
                : ' hours');
    }

    return $remainingMinutes
        . ' minutes';
}



    private function participantLabel(
        array $participants
    ): string {
        $adult = (int) (
            $participants['adult'] ?? 0
        );

        $child = (int) (
            $participants['child'] ?? 0
        );

        $infant = (int) (
            $participants['infant'] ?? 0
        );

        return implode(' · ', [
            $adult
                . ' '
                . ($adult === 1
                    ? 'adult'
                    : 'adults'),
            $child
                . ' '
                . ($child === 1
                    ? 'child'
                    : 'children'),
            $infant
                . ' '
                . ($infant === 1
                    ? 'infant'
                    : 'infants'),
        ]);
    }

    /**
     * Menambahkan data booking V2 ke data detail lama.
     */
    private function detailDataWithBooking(
        TourPackage $tourPackage
    ): array {
        $tour = $this->detailData(
            $tourPackage
        );

        $activeOptions = $tourPackage
            ->options()
            ->where('status', 'active')
            ->with([
                'items' => fn ($query) =>
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('id'),
            ])
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $languages = $activeOptions
            ->flatMap(function ($option): array {
                $languages = $option->languages;

                if (is_string($languages)) {
                    $decoded = json_decode(
                        $languages,
                        true
                    );

                    $languages = is_array($decoded)
                        ? $decoded
                        : [];
                }

                return is_array($languages)
                    ? $languages
                    : [];
            })
            ->filter(
                fn (mixed $language): bool =>
                    is_string($language)
                    && trim($language) !== ''
            )
            ->map(
                fn (string $language): string =>
                    trim($language)
            )
            ->unique(
                fn (string $language): string =>
                    strtolower($language)
            )
            ->values()
            ->all();

        $defaultOption = $activeOptions
            ->firstWhere('is_default', true)
            ?? $activeOptions->first();

        $includedItems = $defaultOption
            ? $defaultOption->items
                ->where('item_type', 'included')
                ->pluck('label')
                ->filter()
                ->values()
                ->all()
            : [];

        $excludedItems = $defaultOption
            ? $defaultOption->items
                ->where('item_type', 'excluded')
                ->pluck('label')
                ->filter()
                ->values()
                ->all()
            : [];

        return array_merge($tour, [
            'available_languages' => $languages,
            'booking_min_guests' => max(
                1,
                (int) (
                    $defaultOption?->min_guests
                    ?? 1
                )
            ),
            'booking_max_guests' =>
                $defaultOption?->max_guests !== null
                    ? (int) $defaultOption->max_guests
                    : null,
            'active_options_count' =>
                $activeOptions->count(),
            'included' => $includedItems,
            'excluded' => $excludedItems,
            'goodToKnow' => $excludedItems,
            'good_to_know' => $excludedItems,
        ]);
    }

    /**
     * Menyiapkan data detail dengan format yang konsisten.
     */
    private function detailData(
        TourPackage $tourPackage
    ): array {
        $card = TourViewData::make(
            $tourPackage
        );

        $gallery = collect([
            $tourPackage->main_image,
        ])
            ->merge(
                $this->toArray(
                    $tourPackage->gallery_images
                )
            )
            ->map(function (mixed $image): ?string {
                if (is_array($image)) {
                    $image = $image['path']
                        ?? $image['image']
                        ?? null;
                }

                return is_string($image)
                    ? trim($image)
                    : null;
            })
            ->filter()
            ->map(
                fn (string $image): string =>
                    TourViewData::imageUrl(
                        $image
                    )
            )
            ->unique()
            ->values();

        if ($gallery->isEmpty()) {
            $gallery->push(
                $card['image_url']
            );
        }

        $highlights = $this->normalizeTextList(
            $tourPackage->highlights
        );

       /*
|--------------------------------------------------------------------------
| Structured roadmap
|--------------------------------------------------------------------------
|
| Roadmap admin V2 disimpan di tabel tour_stops.
| Field itinerary pada tour_packages hanya menjadi fallback legacy.
|
*/

$structuredItinerary = $tourPackage
    ->sharedStops()
    ->where('is_active', true)
    ->orderBy('day_number')
    ->orderBy('sort_order')
    ->orderBy('id')
    ->get()
    ->map(function ($stop): array {
        $time = trim(
            (string) ($stop->time_label ?? '')
        );

        if (
            $time === ''
            && $stop->scheduled_time !== null
        ) {
            $time = substr(
                (string) $stop->scheduled_time,
                0,
                5
            );
        }

        $durationMinutes = max(
            0,
            (int) ($stop->duration_minutes ?? 0)
        );

        $duration = '';

        if ($durationMinutes >= 60) {
            $hours = intdiv($durationMinutes, 60);
            $minutes = $durationMinutes % 60;

            $duration = $hours . ' hr';

            if ($hours > 1) {
                $duration .= 's';
            }

            if ($minutes > 0) {
                $duration .= ' ' . $minutes . ' min';
            }
        } elseif ($durationMinutes > 0) {
            $duration = $durationMinutes . ' min';
        }

        return [
            'id' => (int) $stop->id,

            'type' => trim(
                (string) ($stop->stop_type ?? 'stop')
            ),

            'time' => $time,

            'title' => trim(
                (string) ($stop->title ?? '')
            ),

            'text' => trim(
                (string) ($stop->description ?? '')
            ),

            'location' => trim(
                (string) ($stop->location_name ?? '')
            ),

            'address' => trim(
                (string) ($stop->address ?? '')
            ),

            'duration' => $duration,

            'duration_minutes' => $durationMinutes,

            'latitude' => is_numeric($stop->latitude)
                ? (float) $stop->latitude
                : null,

            'longitude' => is_numeric($stop->longitude)
                ? (float) $stop->longitude
                : null,

            'show_on_map' =>
                (bool) $stop->show_on_map,
        ];
    })
    ->filter(
        fn (array $item): bool =>
            $item['title'] !== ''
            || $item['text'] !== ''
            || $item['location'] !== ''
    )
    ->values()
    ->all();

$itinerary = $structuredItinerary !== []
    ? $structuredItinerary
    : $this->normalizeItinerary(
        $tourPackage->itinerary
    );


    return [
            'id' => $tourPackage->id,

            'title' => $card['title'],
            'slug' => $card['slug'],

            'category' => $card['category'],
            'category_label' => $card['category'],
            'badge' => $card['badge'],

            'area' => $card['area'],
            'duration' => $card['duration'],

            'type' => $card['trip_type'],
            'trip_type' => $card['trip_type'],
            'vehicle' => $card['vehicle'],

            'has_rating' => $card['has_rating'],
            'rating' => $card['rating'],

            'guest_count' => $card['guest_count'],
            'guests' => $card['social_proof_text'],
            'social_proof_text' =>
                $card['social_proof_text'],

            'pricing_type' => $card['pricing_type'],
            'price_available' =>
                $card['price_available'],
            'price_label' => $card['price_label'],
            'price_text' => $card['price_text'],
            'price_suffix' => $card['price_suffix'],
            'price_note' => $card['price_note'],
            'promotion' => $card['promotion'] ?? [
    'active' => false,
],
            /*
             * Nilai legacy dipertahankan agar view lama tidak rusak.
             * Availability V2 tidak lagi memakai nilai ini.
             */
            'adult_price' => $card['adult_price'],
            'child_price' => $card['child_price'],
            'vehicle_price' => $card['vehicle_price'],
            'child_price_text' =>
                $card['child_price_text'],

            'min_guests' => $card['min_guests'],
            'max_guests' => $card['max_guests'],

            'transport_included' =>
                $card['transport_included'],
            'pickup_text' => $card['pickup_text'],
            'confirmation_text' =>
                $card['confirmation_text'],

            'description' =>
    $tourPackage->description ?: '',

'intro' =>
    $tourPackage->intro
    ?: $tourPackage->description
    ?: '',

'about_heading' =>
    $tourPackage->intro
    ?: $tourPackage->title,

'story' =>
    $tourPackage->story
    ?: $tourPackage->description
    ?: '',

            'image' => $card['image_url'],
            'main_image' => $card['image_url'],

            'gallery' => $gallery->all(),
            'images' => $gallery->all(),
            'gallery_images' => $gallery->all(),

            'highlights' => $highlights,
            'itinerary' => $itinerary,

            /* Diisi dari default active option oleh detailDataWithBooking(). */
            'included' => [],
            'excluded' => [],
            'goodToKnow' => [],
            'good_to_know' => [],

            'is_popular' =>
                (bool) $tourPackage->is_popular,
            'is_featured' =>
                (bool) $tourPackage->is_featured,
            'status' => $tourPackage->status,
        ];
    }

    /**
     * Mengubah Collection, array, atau JSON menjadi array.
     */
    private function toArray(mixed $value): array
    {
        if ($value instanceof Collection) {
            return $value->values()->all();
        }

        if (is_array($value)) {
            return $value;
        }

        if (
            is_string($value)
            && trim($value) !== ''
        ) {
            $decoded = json_decode(
                $value,
                true
            );

            if (
                json_last_error()
                    === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                return $decoded;
            }

            return preg_split(
                '/\r\n|\r|\n/',
                trim($value)
            ) ?: [];
        }

        return [];
    }

    /**
     * Menormalkan highlights menjadi array teks.
     */
    private function normalizeTextList(
        mixed $value
    ): array {
        return collect(
            $this->toArray($value)
        )
            ->map(function (mixed $item): string {
                if (is_array($item)) {
                    return trim(
                        (string) (
                            $item['text']
                            ?? $item['title']
                            ?? $item['name']
                            ?? ''
                        )
                    );
                }

                return is_scalar($item)
                    ? trim((string) $item)
                    : '';
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Menormalkan itinerary lama menjadi time, title, dan text.
     */
    private function normalizeItinerary(
        mixed $value
    ): array {
        return collect(
            $this->toArray($value)
        )
            ->map(function (mixed $item): ?array {
                if (is_array($item)) {
                    return [
                        'time' => trim(
                            (string) (
                                $item['time'] ?? ''
                            )
                        ),
                        'title' => trim(
                            (string) (
                                $item['title'] ?? ''
                            )
                        ),
                        'text' => trim(
                            (string) (
                                $item['text'] ?? ''
                            )
                        ),
                    ];
                }

                if (!is_scalar($item)) {
                    return null;
                }

                $parts = array_map(
                    'trim',
                    explode('|', (string) $item)
                );

                return [
                    'time' => $parts[0] ?? '',
                    'title' => $parts[1] ?? '',
                    'text' => $parts[2] ?? '',
                ];
            })
            ->filter(
                fn (mixed $item): bool =>
                    is_array($item)
                    && (
                        $item['time'] !== ''
                        || $item['title'] !== ''
                        || $item['text'] !== ''
                    )
            )
            ->values()
            ->all();
    }
}
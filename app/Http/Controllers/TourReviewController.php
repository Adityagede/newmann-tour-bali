<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ReviewTourSelectionRequest;
use App\Models\TourOption;
use App\Models\TourPackage;
use App\Services\TourAvailabilityService;
use App\Services\TourPricingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

final class TourReviewController extends Controller
{
    public function __construct(
        private readonly TourAvailabilityService $availabilityService,
        private readonly TourPricingService $pricingService
    ) {
    }

    public function store(
        ReviewTourSelectionRequest $request,
        string $slug
    ): RedirectResponse {
        $tourPackage = TourPackage::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        return $this->storeReview(
            $request,
            $tourPackage,
            false
        );
    }

    public function show(
        Request $request,
        string $slug
    ): View|RedirectResponse {
        $tourPackage = TourPackage::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        return $this->showReview(
            $request,
            $tourPackage,
            false
        );
    }

    public function previewStore(
        ReviewTourSelectionRequest $request,
        TourPackage $tourPackage
    ): RedirectResponse {
        return $this->storeReview(
            $request,
            $tourPackage,
            true
        );
    }

    public function previewShow(
        Request $request,
        TourPackage $tourPackage
    ): View|RedirectResponse {
        return $this->showReview(
            $request,
            $tourPackage,
            true
        );
    }

    private function storeReview(
        ReviewTourSelectionRequest $request,
        TourPackage $tourPackage,
        bool $isPreview
    ): RedirectResponse {
        $validated = $request->validated();

        $participants = [
            'adult' => (int) $validated['adults'],
            'child' => (int) $validated['children'],
            'infant' => (int) $validated['infants'],
        ];

        $tourOption = TourOption::query()
            ->whereKey(
                $validated['tour_option_id']
            )
            ->where(
                'tour_package_id',
                $tourPackage->id
            )
            ->where('status', 'active')
            ->with([
                'items' => fn ($query) =>
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('id'),
            ])
            ->first();

        if (!$tourOption) {
            return back()
                ->withInput()
                ->withErrors([
                    'selection' =>
                        'The selected Tour Option is no longer active.',
                ]);
        }

        /*
         * Admin Preview boleh memeriksa produk Draft,
         * tetapi perubahan status hanya terjadi pada clone.
         */
        $availabilityTour = $tourPackage;

        if ($isPreview) {
            $availabilityTour =
                clone $tourPackage;

            $availabilityTour
                ->unsetRelations();

            $availabilityTour->setAttribute(
                'status',
                'active'
            );
        }

        try {
            $availability =
                $this->availabilityService->check(
                    $availabilityTour,
                    $validated['travel_date'],
                    $participants,
                    $validated['language']
                );
        } catch (Throwable $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'selection' =>
                        $exception->getMessage(),
                ]);
        }

        $availableOptions =
            $availability['available_options']
            ?? $availability['options']
            ?? [];

        $matchedOption = collect(
            is_array($availableOptions)
                ? $availableOptions
                : []
        )->first(
            fn (mixed $rawOption): bool =>
                $this->extractOptionId(
                    $rawOption
                ) === $tourOption->id
        );

        if ($matchedOption === null) {
            return back()
                ->withInput()
                ->withErrors([
                    'selection' =>
                        'This Tour Option is no longer available for the selected date and participants.',
                ]);
        }

        $availableTimes =
            $this->extractStartingTimes(
                $matchedOption
            );

        if (
            $availableTimes !== []
            && !in_array(
                $validated['starting_time'],
                $availableTimes,
                true
            )
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'starting_time' =>
                        'The selected starting time is no longer available.',
                ]);
        }

        $travelMoment = Carbon::createFromFormat(
            'Y-m-d',
            $validated['travel_date'],
            config('app.timezone')
        )->startOfDay();

        try {
            $pricing = $this->pricingService
                ->calculate(
                    $tourOption,
                    $participants,
                    $travelMoment
                );
        } catch (Throwable $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'pricing' =>
                        $exception->getMessage(),
                ]);
        }

        $included = $tourOption->items
            ->where('item_type', 'included')
            ->map(
                fn ($item): array => [
                    'label' =>
                        (string) $item->label,

                    'details' =>
                        $item->details ?: null,
                ]
            )
            ->values()
            ->all();

        $excluded = $tourOption->items
            ->where('item_type', 'excluded')
            ->map(
                fn ($item): array => [
                    'label' =>
                        (string) $item->label,

                    'details' =>
                        $item->details ?: null,
                ]
            )
            ->values()
            ->all();

        $review = [
            'is_preview' => $isPreview,

            'tour' => [
                'id' => $tourPackage->id,
                'title' => $tourPackage->title,
                'slug' => $tourPackage->slug,
            ],

            'option' => [
                'id' => $tourOption->id,
                'title' => $tourOption->title,
                'slug' => $tourOption->slug,

                'short_description' =>
                    $tourOption->short_description,

                'duration_minutes' =>
                    $tourOption->duration_minutes,

                'pickup_label' =>
                    $tourOption->pickup_label
                    ?: $tourOption->pickup_type,

                'is_all_inclusive' =>
                    (bool) $tourOption
                        ->is_all_inclusive,

                'included' => $included,
                'excluded' => $excluded,
            ],

            'selection' => [
                'travel_date' =>
                    $validated['travel_date'],

                'date_label' =>
                    $travelMoment->format(
                        'd M Y'
                    ),

                'starting_time' =>
                    $validated['starting_time'],

                'language' =>
                    $validated['language'],

                'participants' =>
                    $participants,

                'total_participants' =>
                    array_sum($participants),

                'participant_label' =>
                    $this->participantLabel(
                        $participants
                    ),
            ],

            'recommended_transport' =>
                $availability[
                    'recommended_transport'
                ]
                ?? null,

            'pricing' => [
                'base_total' => (int) (
                    $pricing['base_total']
                    ?? 0
                ),

                'discount_amount' => (int) (
                    $pricing['discount_amount']
                    ?? 0
                ),

                'estimated_total' => (int) (
                    $pricing['estimated_total']
                    ?? $pricing['total']
                    ?? 0
                ),

                'currency' => strtoupper(
                    (string) (
                        $pricing['currency']
                        ?? 'IDR'
                    )
                ),

                'breakdown' =>
                    $pricing['breakdown']
                    ?? [],
            ],
        ];

        $request->session()->put(
            'tour_review_selection',
            $review
        );

        return redirect()->route(
            $isPreview
                ? 'admin.tour-packages.preview.review'
                : 'tours.review',
            $isPreview
                ? [
                    'tourPackage' =>
                        $tourPackage,
                ]
                : [
                    'slug' =>
                        $tourPackage->slug,
                ]
        );
    }

    private function showReview(
        Request $request,
        TourPackage $tourPackage,
        bool $isPreview
    ): View|RedirectResponse {
        $review = $request->session()->get(
            'tour_review_selection'
        );

        $validReview =
            is_array($review)
            && (int) (
                $review['tour']['id']
                ?? 0
            ) === $tourPackage->id
            && (bool) (
                $review['is_preview']
                ?? false
            ) === $isPreview;

        if (!$validReview) {
            return redirect()
                ->route(
                    $isPreview
                        ? 'admin.tour-packages.preview'
                        : 'tours.detail',
                    $isPreview
                        ? [
                            'tourPackage' =>
                                $tourPackage,
                        ]
                        : [
                            'slug' =>
                                $tourPackage->slug,
                        ]
                )
                ->with(
                    'error',
                    'Please select an available Tour Option before opening the review page.'
                );
        }

        return view(
            'pages.tour-review',
            [
                'review' => $review,
                'isPreview' => $isPreview,
            ]
        );
    }

    private function extractOptionId(
        mixed $rawOption
    ): int {
        if (
            is_object($rawOption)
            && method_exists(
                $rawOption,
                'toArray'
            )
        ) {
            $rawOption =
                $rawOption->toArray();
        }

        if (!is_array($rawOption)) {
            return 0;
        }

        $embeddedOption =
            $rawOption['option']
            ?? $rawOption['tour_option']
            ?? null;

        if (is_object($embeddedOption)) {
            return (int) (
                $embeddedOption->id
                ?? 0
            );
        }

        if (is_array($embeddedOption)) {
            return (int) (
                $embeddedOption['id']
                ?? $embeddedOption['option_id']
                ?? 0
            );
        }

        return (int) (
            $rawOption['option_id']
            ?? $rawOption['tour_option_id']
            ?? $rawOption['id']
            ?? 0
        );
    }

    private function extractStartingTimes(
        mixed $rawOption
    ): array {
        if (
            is_object($rawOption)
            && method_exists(
                $rawOption,
                'toArray'
            )
        ) {
            $rawOption =
                $rawOption->toArray();
        }

        if (!is_array($rawOption)) {
            return [];
        }

        $source =
            $rawOption['starting_times']
            ?? $rawOption['start_times']
            ?? $rawOption['available_times']
            ?? $rawOption['times']
            ?? $rawOption['departures']
            ?? $rawOption['schedules']
            ?? [];

        if (
            is_array($source)
            && (
                isset($source['start_time'])
                || isset($source['time'])
                || isset($source['value'])
            )
        ) {
            $source = [$source];
        }

        return collect(
            is_array($source)
                ? $source
                : []
        )
            ->map(function (
                mixed $time
            ): ?string {
                if (is_object($time)) {
                    $time =
                        method_exists(
                            $time,
                            'toArray'
                        )
                            ? $time->toArray()
                            : [];
                }

                if (is_array($time)) {
                    $time =
                        $time['value']
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

                return $time !== ''
                    ? $time
                    : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function participantLabel(
        array $participants
    ): string {
        $adult = (int) (
            $participants['adult']
            ?? 0
        );

        $child = (int) (
            $participants['child']
            ?? 0
        );

        $infant = (int) (
            $participants['infant']
            ?? 0
        );

        return implode(
            ' · ',
            [
                $adult
                    . ' '
                    . (
                        $adult === 1
                            ? 'adult'
                            : 'adults'
                    ),

                $child
                    . ' '
                    . (
                        $child === 1
                            ? 'child'
                            : 'children'
                    ),

                $infant
                    . ' '
                    . (
                        $infant === 1
                            ? 'infant'
                            : 'infants'
                    ),
            ]
        );
    }
}
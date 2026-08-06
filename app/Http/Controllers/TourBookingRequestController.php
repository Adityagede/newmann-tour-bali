<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SubmitTourBookingRequest;
use App\Models\TourBookingRequest;
use App\Models\TourOption;
use App\Models\TourPackage;
use App\Notifications\NewTourBookingRequestNotification;
use App\Services\TourAvailabilityService;
use App\Services\TourPricingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

final class TourBookingRequestController extends Controller
{
    public function __construct(
        private readonly TourAvailabilityService $availabilityService,
        private readonly TourPricingService $pricingService
    ) {
    }

    public function store(
        SubmitTourBookingRequest $request,
        string $slug
    ): RedirectResponse {
        $review = $request
            ->session()
            ->get(
                'tour_review_selection'
            );

        if (
            !is_array($review)
            || (bool) (
                $review['is_preview']
                ?? false
            )
        ) {
            return redirect()
                ->route(
                    'tours.detail',
                    [
                        'slug' => $slug,
                    ]
                )
                ->with(
                    'error',
                    'Please select an available Tour Option before sending a Booking Request.'
                );
        }

        $tourPackage = TourPackage::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        if (
            (int) (
                $review['tour']['id']
                ?? 0
            ) !== $tourPackage->id
        ) {
            $request->session()->forget(
                'tour_review_selection'
            );

            return redirect()
                ->route(
                    'tours.detail',
                    [
                        'slug' =>
                            $tourPackage->slug,
                    ]
                )
                ->with(
                    'error',
                    'The stored Tour selection is no longer valid.'
                );
        }

        $selection = is_array(
            $review['selection']
                ?? null
        )
            ? $review['selection']
            : [];

        $optionData = is_array(
            $review['option']
                ?? null
        )
            ? $review['option']
            : [];

        $participants = is_array(
            $selection['participants']
                ?? null
        )
            ? [
                'adult' => (int) (
                    $selection[
                        'participants'
                    ]['adult']
                    ?? 0
                ),

                'child' => (int) (
                    $selection[
                        'participants'
                    ]['child']
                    ?? 0
                ),

                'infant' => (int) (
                    $selection[
                        'participants'
                    ]['infant']
                    ?? 0
                ),
            ]
            : [];

        $travelDate = (string) (
            $selection['travel_date']
            ?? ''
        );

        $startingTime = (string) (
            $selection['starting_time']
            ?? ''
        );

        $language = (string) (
            $selection['language']
            ?? ''
        );

        $tourOption = TourOption::query()
            ->whereKey(
                (int) (
                    $optionData['id']
                    ?? 0
                )
            )
            ->where(
                'tour_package_id',
                $tourPackage->id
            )
            ->where('status', 'active')
            ->with([
                'items' => fn ($query) =>
                    $query
                        ->where(
                            'is_active',
                            true
                        )
                        ->orderBy(
                            'sort_order'
                        )
                        ->orderBy('id'),
            ])
            ->first();

        if (!$tourOption) {
            return back()
                ->withInput()
                ->withErrors([
                    'booking' =>
                        'The selected Tour Option is no longer active.',
                ]);
        }

        try {
            /*
             * Availability dan harga selalu dihitung ulang.
             * Snapshot browser/session tidak dipercaya
             * sebagai nilai final.
             */
            $availability =
                $this->availabilityService
                    ->check(
                        $tourPackage,
                        $travelDate,
                        $participants,
                        $language
                    );

            $matchedOption =
                $this->findAvailableOption(
                    $availability,
                    $tourOption->id
                );

            if ($matchedOption === null) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'booking' =>
                            'This Tour Option is no longer available for the selected date.',
                    ]);
            }

            $availableTimes =
                $this->extractStartingTimes(
                    $matchedOption
                );

            if (
                $availableTimes !== []
                && !in_array(
                    $startingTime,
                    $availableTimes,
                    true
                )
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'booking' =>
                            'The selected starting time is no longer available.',
                    ]);
            }

            $travelMoment =
                Carbon::createFromFormat(
                    'Y-m-d',
                    $travelDate,
                    config('app.timezone')
                )->startOfDay();

            $pricing =
                $this->pricingService
                    ->calculate(
                        $tourOption,
                        $participants,
                        $travelMoment
                    );

            $booking = DB::transaction(
                function () use (
                    $request,
                    $tourPackage,
                    $tourOption,
                    $selection,
                    $participants,
                    $travelDate,
                    $startingTime,
                    $language,
                    $availability,
                    $pricing
                ): TourBookingRequest {
                    $included =
                        $tourOption
                            ->items
                            ->where(
                                'item_type',
                                'included'
                            )
                            ->map(
                                fn ($item): array => [
                                    'label' =>
                                        (string) $item
                                            ->label,

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

                    $excluded =
                        $tourOption
                            ->items
                            ->where(
                                'item_type',
                                'excluded'
                            )
                            ->map(
                                fn ($item): array => [
                                    'label' =>
                                        (string) $item
                                            ->label,

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

                    $baseTotal = (int) (
                        $pricing['base_total']
                        ?? 0
                    );

                    $discountAmount = (int) (
                        $pricing[
                            'discount_amount'
                        ]
                        ?? 0
                    );

                    $estimatedTotal = (int) (
                        $pricing[
                            'estimated_total'
                        ]
                        ?? $pricing['total']
                        ?? max(
                            0,
                            $baseTotal
                                - $discountAmount
                        )
                    );

                    return TourBookingRequest::query()
                        ->create([
                            'booking_reference' =>
                                $this
                                    ->generateReference(),

                            'tour_package_id' =>
                                $tourPackage->id,

                            'tour_option_id' =>
                                $tourOption->id,

                            'status' => 'pending',

                            'source' =>
                                'website_v2',

                            'guest_name' =>
                                $request->validated(
                                    'full_name'
                                ),

                            'guest_whatsapp' =>
                                $request->validated(
                                    'whatsapp'
                                ),

                            'guest_email' =>
                                $request->validated(
                                    'email'
                                )
                                ?: null,

                            'pickup_address' =>
                                $request->validated(
                                    'pickup_address'
                                ),

                            'special_requests' =>
                                $request->validated(
                                    'special_requests'
                                )
                                ?: null,

                            'travel_date' =>
                                $travelDate,

                            'starting_time' =>
                                $startingTime,

                            'language' =>
                                $language,

                            'adult_count' =>
                                $participants[
                                    'adult'
                                ],

                            'child_count' =>
                                $participants[
                                    'child'
                                ],

                            'infant_count' =>
                                $participants[
                                    'infant'
                                ],

                            'total_participants' =>
                                array_sum(
                                    $participants
                                ),

                            'currency' =>
                                strtoupper(
                                    (string) (
                                        $pricing[
                                            'currency'
                                        ]
                                        ?? 'IDR'
                                    )
                                ),

                            'base_total' =>
                                $baseTotal,

                            'discount_amount' =>
                                $discountAmount,

                            'estimated_total' =>
                                $estimatedTotal,

                            'tour_snapshot' => [
                                'id' =>
                                    $tourPackage->id,

                                'title' =>
                                    $tourPackage->title,

                                'slug' =>
                                    $tourPackage->slug,

                                'category' =>
                                    $tourPackage
                                        ->category,

                                'area' =>
                                    $tourPackage->area,

                                'duration' =>
                                    $tourPackage
                                        ->duration,

                                'tour_format' =>
                                    $tourPackage
                                        ->tour_format,
                            ],

                            'option_snapshot' => [
                                'id' =>
                                    $tourOption->id,

                                'title' =>
                                    $tourOption->title,

                                'slug' =>
                                    $tourOption->slug,

                                'duration_minutes' =>
                                    $tourOption
                                        ->duration_minutes,

                                'languages' =>
                                    $tourOption
                                        ->languages,

                                'pickup_type' =>
                                    $tourOption
                                        ->pickup_type,

                                'pickup_label' =>
                                    $tourOption
                                        ->pickup_label,

                                'is_all_inclusive' =>
                                    (bool) $tourOption
                                        ->is_all_inclusive,
                            ],

                            'selection_snapshot' => [
                                'travel_date' =>
                                    $travelDate,

                                'starting_time' =>
                                    $startingTime,

                                'language' =>
                                    $language,

                                'participants' =>
                                    $participants,

                                'participant_label' =>
                                    $selection[
                                        'participant_label'
                                    ]
                                    ?? null,
                            ],

                            'transport_snapshot' =>
                                $availability[
                                    'recommended_transport'
                                ]
                                ?? null,

                            'pricing_snapshot' =>
                                $pricing,

                            'items_snapshot' => [
                                'included' =>
                                    $included,

                                'excluded' =>
                                    $excluded,
                            ],

                            'requested_at' =>
                                now(),
                        ]);
                }
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'booking' =>
                        'The Booking Request could not be submitted. Please check the selected trip details and try again.',
                ]);
        }

        /*
         * SMTP notification is sent only after the V2 booking
         * transaction has completed successfully. A mail failure
         * is reported without cancelling the guest's booking.
         */
        $this->sendAdminNotification($booking);

        $request->session()->forget(
            'tour_review_selection'
        );

        $request->session()->put(
            'booking_success_reference',
            $booking->booking_reference
        );

        return redirect()->route(
            'booking-requests.success',
            [
                'reference' =>
                    $booking
                        ->booking_reference,
            ]
        );
    }

    public function success(
        Request $request,
        string $reference
    ): View|RedirectResponse {
        $sessionReference =
            $request
                ->session()
                ->get(
                    'booking_success_reference'
                );

        if (
            !is_string($sessionReference)
            || !hash_equals(
                $sessionReference,
                $reference
            )
        ) {
            return redirect()
                ->route('tours')
                ->with(
                    'error',
                    'The Booking Request success page is no longer available.'
                );
        }

        $booking = TourBookingRequest::query()
            ->where(
                'booking_reference',
                $reference
            )
            ->firstOrFail();

        return view(
            'pages.booking-request-success',
            [
                'booking' => $booking,
            ]
        );
    }

    private function findAvailableOption(
        array $availability,
        int $optionId
    ): mixed {
        $options =
            $availability[
                'available_options'
            ]
            ?? $availability['options']
            ?? [];

        return collect(
            is_array($options)
                ? $options
                : []
        )->first(
            fn (mixed $rawOption): bool =>
                $this->extractOptionId(
                    $rawOption
                ) === $optionId
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

        $embedded =
            $rawOption['option']
            ?? $rawOption['tour_option']
            ?? null;

        if (is_object($embedded)) {
            return (int) (
                $embedded->id
                ?? 0
            );
        }

        if (is_array($embedded)) {
            return (int) (
                $embedded['id']
                ?? $embedded['option_id']
                ?? 0
            );
        }

        return (int) (
            $rawOption['option_id']
            ?? $rawOption[
                'tour_option_id'
            ]
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
            ?? $rawOption[
                'available_times'
            ]
            ?? $rawOption['times']
            ?? $rawOption['departures']
            ?? $rawOption['schedules']
            ?? [];

        if (
            is_array($source)
            && (
                isset(
                    $source['start_time']
                )
                || isset(
                    $source['time']
                )
                || isset(
                    $source['value']
                )
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
                        ?? $time[
                            'start_time'
                        ]
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

    private function sendAdminNotification(
        TourBookingRequest $booking
    ): void {
        $adminEmail = config(
            'newman.booking_notification_email'
        );

        if (!is_string($adminEmail)) {
            return;
        }

        $adminEmail = trim($adminEmail);

        if ($adminEmail === '') {
            return;
        }

        try {
            $booking->loadMissing([
                'tourPackage:id,title,slug',
                'tourOption:id,title,slug',
            ]);

            Notification::route(
                'mail',
                $adminEmail
            )->notify(
                new NewTourBookingRequestNotification(
                    $booking
                )
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function generateReference(): string
    {
        for (
            $attempt = 0;
            $attempt < 10;
            $attempt++
        ) {
            $reference =
                'NWM-'
                . now()->format('ymd')
                . '-'
                . Str::upper(
                    Str::random(6)
                );

            $exists =
                TourBookingRequest::query()
                    ->where(
                        'booking_reference',
                        $reference
                    )
                    ->exists();

            if (!$exists) {
                return $reference;
            }
        }

        throw new RuntimeException(
            'A unique booking reference could not be generated.'
        );
    }
}
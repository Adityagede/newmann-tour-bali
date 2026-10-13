<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SubmitCustomTripRequest;
use App\Models\CustomTripRequest;
use App\Services\BookingNotificationDispatcher;
use App\Support\BookingSubmission;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class CustomTripRequestController extends Controller
{
    public function __construct(
        private readonly BookingSubmission $submissions,
        private readonly BookingNotificationDispatcher $notifications,
    ) {}

    private const VEHICLE_OPTIONS = [
        'Not sure, please recommend',
        'Toyota Avanza',
        'Toyota Hiace',
        'Another Car',
    ];

    private const VEHICLE_RECOMMENDATIONS = [
        [
            'minimum' => 1,
            'maximum' => 5,
            'vehicle' => 'Toyota Avanza',
            'label' => 'Private car',
            'description' => 'Best starting point for one to five guests with normal luggage.',
        ],
        [
            'minimum' => 6,
            'maximum' => 12,
            'vehicle' => 'Toyota Hiace',
            'label' => 'Passenger van',
            'description' => 'Recommended for six to twelve guests or groups needing more space.',
        ],
        [
            'minimum' => 13,
            'maximum' => null,
            'vehicle' => 'Another Car',
            'label' => 'Larger arrangement',
            'description' => 'A larger vehicle or multiple vehicles must be arranged manually.',
        ],
    ];

    public function create(Request $request): View
    {
        $requestedVehicle = trim(
            (string) $request->query('vehicle', '')
        );

        $selectedVehicle = in_array(
            $requestedVehicle,
            self::VEHICLE_OPTIONS,
            true
        )
            ? $requestedVehicle
            : 'Not sure, please recommend';

        return view('pages.custom-trip', [
            'submissionToken' => old('submission_token', (string) Str::uuid()),
            'vehicleOptions' => self::VEHICLE_OPTIONS,
            'vehicleRecommendations' => self::VEHICLE_RECOMMENDATIONS,
            'selectedVehicle' => old(
                'selected_vehicle',
                $selectedVehicle
            ),
        ]);
    }

    public function store(
        SubmitCustomTripRequest $request
    ): RedirectResponse {
        $validated = $request->validated();
        $submissionKey = $this->submissions->key($request, 'custom');
        $submissionHash = $this->submissions->fingerprint($validated);
        $customTripRequest = CustomTripRequest::query()->customOnly()->where('submission_key', $submissionKey)->first();

        if (! $customTripRequest) {
            try {
                $customTripRequest = CustomTripRequest::query()->create([
                    'submission_key' => $submissionKey,
                    'submission_hash' => $submissionHash,
                    'admin_notification_pending' => $this->notifications->enabled(),
                    'booking_code' => $this->generateReference(),
                    'tour_package_id' => null,
                    'name' => $validated['name'],
                    'whatsapp' => $validated['whatsapp'],
                    'email' => $validated['email'] ?? null,
                    'selected_tour' => 'Custom Bali Private Tour',
                    'trip_date' => $validated['trip_date'] ?? null,
                    'adult_count' => null,
                    'child_count' => null,
                    'people_count' => $validated['people_count'],
                    'pricing_type' => null,
                    'adult_unit_price' => null,
                    'child_unit_price' => null,
                    'vehicle_unit_price' => null,
                    'estimated_total' => null,
                    'currency' => 'IDR',
                    'selected_vehicle' => $validated['selected_vehicle'],
                    'custom_vehicle' => $validated['custom_vehicle'] ?? null,
                    'pickup_area' => $validated['pickup_area'] ?? null,
                    'message' => $validated['message'],
                    'status' => 'pending',
                ]);
            } catch (UniqueConstraintViolationException $exception) {
                // A simultaneous submit may have committed this exact form first.
                $customTripRequest = CustomTripRequest::query()->customOnly()->where('submission_key', $submissionKey)->first();
                if (! $customTripRequest) {
                    throw $exception;
                }
            }
        }

        $this->submissions->assertSamePayload($customTripRequest->submission_hash, $submissionHash);
        $this->submissions->rememberCustomTrip($request, $customTripRequest->booking_code);
        $this->notifications->dispatch($customTripRequest, 'custom');

        return redirect()->route(
            'custom-trip.success',
            $customTripRequest->booking_code
        );
    }

    public function success(Request $request, string $bookingCode): Response
    {
        // Check ownership before lookup, so the response cannot reveal valid codes.
        abort_unless($this->submissions->ownsCustomTrip($request, $bookingCode), 404);

        $customTripRequest = CustomTripRequest::query()
            ->customOnly()
            ->where('booking_code', $bookingCode)
            ->firstOrFail();

        return response()->view('pages.custom-trip-success', [
            'customTripRequest' => $customTripRequest,
        ])->header('Cache-Control', 'private, no-store');
    }

    private function generateReference(): string
    {
        do {
            $reference = 'NWM-CT-'
                .now()->format('ymd')
                .'-'
                .Str::upper(Str::random(10));
        } while (
            CustomTripRequest::query()
                ->where('booking_code', $reference)
                ->exists()
        );

        return $reference;
    }
}

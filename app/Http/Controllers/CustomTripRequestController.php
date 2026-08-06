<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SubmitCustomTripRequest;
use App\Models\CustomTripRequest;
use App\Notifications\NewCustomTripRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

final class CustomTripRequestController extends Controller
{
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

        $customTripRequest = CustomTripRequest::query()->create([
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

        $this->sendAdminNotification($customTripRequest);

        return redirect()->route(
            'custom-trip.success',
            $customTripRequest->booking_code
        );
    }

    public function success(string $bookingCode): View
    {
        $customTripRequest = CustomTripRequest::query()
            ->customOnly()
            ->where('booking_code', $bookingCode)
            ->firstOrFail();

        return view('pages.custom-trip-success', [
            'customTripRequest' => $customTripRequest,
        ]);
    }

    private function generateReference(): string
    {
        do {
            $reference = 'NWM-CT-'
                . now()->format('ymd')
                . '-'
                . Str::upper(Str::random(5));
        } while (
            CustomTripRequest::query()
                ->where('booking_code', $reference)
                ->exists()
        );

        return $reference;
    }

    private function sendAdminNotification(
        CustomTripRequest $customTripRequest
    ): void {
        $adminEmail = config(
            'newman.booking_notification_email'
        );

        if (! is_string($adminEmail)) {
            return;
        }

        $adminEmail = trim($adminEmail);

        if ($adminEmail === '') {
            return;
        }

        try {
            Notification::route('mail', $adminEmail)
                ->notify(
                    new NewCustomTripRequestNotification(
                        $customTripRequest
                    )
                );
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}

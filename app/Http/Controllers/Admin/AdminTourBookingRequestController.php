<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomTripRequest;
use App\Models\TourBookingRequest;
use App\Models\TourOption;
use App\Models\TourPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class AdminTourBookingRequestController extends Controller
{
    private const STATUSES = [
        'pending',
        'contacted',
        'confirmed',
        'cancelled',
        'completed',
    ];

    public function dashboard(): View
{
    $stats = [
        'total' => TourBookingRequest::query()
            ->count(),

        'pending' => TourBookingRequest::query()
            ->where('status', 'pending')
            ->count(),

        'contacted' => TourBookingRequest::query()
            ->where('status', 'contacted')
            ->count(),

        'confirmed' => TourBookingRequest::query()
            ->where('status', 'confirmed')
            ->count(),

        'cancelled' => TourBookingRequest::query()
            ->where('status', 'cancelled')
            ->count(),

        'completed' => TourBookingRequest::query()
            ->where('status', 'completed')
            ->count(),
    ];

    $latestBookings = TourBookingRequest::query()
        ->with([
            'tourPackage:id,title,slug',
            'tourOption:id,title,slug',
        ])
        ->orderByDesc('requested_at')
        ->orderByDesc('id')
        ->limit(5)
        ->get();

    $customTripStats = [
        'total' => CustomTripRequest::query()
            ->customOnly()
            ->count(),
        'pending' => CustomTripRequest::query()
            ->customOnly()
            ->where('status', 'pending')
            ->count(),
    ];

    $tourHealth = [
        'total' => TourPackage::query()->count(),
        'active' => TourPackage::query()->where('status', 'active')->count(),
        'draft' => TourPackage::query()->where('status', 'draft')->count(),
        'inactive' => TourPackage::query()->where('status', 'inactive')->count(),
        'without_default_option' => TourPackage::query()
            ->whereDoesntHave('options', fn (Builder $query) =>
                $query->where('is_default', true)
            )
            ->count(),
        'options_without_price' => TourOption::query()
            ->where('status', 'active')
            ->whereDoesntHave('allowedPrices')
            ->count(),
        'options_without_schedule' => TourOption::query()
            ->where('status', 'active')
            ->whereDoesntHave('activeSchedules')
            ->count(),
    ];

    return view('admin.dashboard', [
        'stats' => $stats,
        'latestBookings' => $latestBookings,
        'customTripStats' => $customTripStats,
        'tourHealth' => $tourHealth,
    ]);
}

    public function index(
        Request $request
    ): View {
        $validated = $request->validate([
            'status' => [
                'nullable',
                'string',
                Rule::in(self::STATUSES),
            ],

            'q' => [
                'nullable',
                'string',
                'max:120',
            ],
        ]);

        $status = $validated['status']
            ?? null;

        $search = trim(
            (string) (
                $validated['q']
                ?? ''
            )
        );

        $tourBookingRequests =
            TourBookingRequest::query()
                ->with([
                    'tourPackage:id,title,slug',
                    'tourOption:id,title,slug',
                ])
                ->when(
                    $status,
                    fn (Builder $query) =>
                        $query->where(
                            'status',
                            $status
                        )
                )
                ->when(
                    $search !== '',
                    function (
                        Builder $query
                    ) use ($search): void {
                        $query->where(
                            function (
                                Builder $innerQuery
                            ) use ($search): void {
                                $innerQuery
                                    ->where(
                                        'booking_reference',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'guest_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'guest_whatsapp',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'guest_email',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                    }
                )
                ->orderByDesc('requested_at')
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString();

        $counts = [
            'all' =>
                TourBookingRequest::query()
                    ->count(),

            'pending' =>
                TourBookingRequest::query()
                    ->where(
                        'status',
                        'pending'
                    )
                    ->count(),

            'contacted' =>
                TourBookingRequest::query()
                    ->where(
                        'status',
                        'contacted'
                    )
                    ->count(),

            'confirmed' =>
                TourBookingRequest::query()
                    ->where(
                        'status',
                        'confirmed'
                    )
                    ->count(),

            'cancelled' =>
                TourBookingRequest::query()
                    ->where(
                        'status',
                        'cancelled'
                    )
                    ->count(),

            'completed' =>
                TourBookingRequest::query()
                    ->where(
                        'status',
                        'completed'
                    )
                    ->count(),
        ];

        return view(
            'admin.tour-booking-requests.index',
            [
                'tourBookingRequests' =>
                    $tourBookingRequests,

                'counts' => $counts,
                'status' => $status,
                'search' => $search,

                'availableStatuses' =>
                    self::STATUSES,
            ]
        );
    }

    public function show(
        TourBookingRequest $tourBookingRequest
    ): View {
        $tourBookingRequest->load([
            'tourPackage:id,title,slug',
            'tourOption:id,title,slug',
        ]);

        return view(
            'admin.tour-booking-requests.show',
            [
                'tourBookingRequest' =>
                    $tourBookingRequest,

                'availableStatuses' =>
                    self::STATUSES,
            ]
        );
    }

    public function updateStatus(
        Request $request,
        TourBookingRequest $tourBookingRequest
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(self::STATUSES),
            ],
        ]);

        $oldStatus =
            $tourBookingRequest->status;

        $newStatus =
            $validated['status'];

        if ($oldStatus === $newStatus) {
            return back()->with(
                'success',
                'Tour Booking Request status is already '
                . ucfirst($newStatus)
                . '.'
            );
        }

        $tourBookingRequest->update([
            'status' => $newStatus,
        ]);

        return back()->with(
            'success',
            'Tour Booking Request '
            . $tourBookingRequest
                ->booking_reference
            . ' updated from '
            . ucfirst($oldStatus)
            . ' to '
            . ucfirst($newStatus)
            . '.'
        );
    }
}
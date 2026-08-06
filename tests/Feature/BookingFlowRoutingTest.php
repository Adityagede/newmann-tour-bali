<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminTourBookingRequestController;
use App\Http\Controllers\CustomTripRequestController;
use App\Http\Controllers\TourBookingRequestController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class BookingFlowRoutingTest extends TestCase
{
    public function test_tour_package_booking_routes_use_v2_controllers(): void
    {
        $this->assertSame(
            TourBookingRequestController::class . '@store',
            Route::getRoutes()
                ->getByName('tours.booking-request.store')
                ?->getActionName()
        );

        $this->assertSame(
            AdminTourBookingRequestController::class . '@index',
            Route::getRoutes()
                ->getByName('admin.tour-booking-requests.index')
                ?->getActionName()
        );

        $this->assertSame(
            'admin/bookings',
            Route::getRoutes()
                ->getByName('admin.tour-booking-requests.index')
                ?->uri()
        );
    }

    public function test_removed_legacy_booking_route_names_do_not_exist(): void
    {
        $this->assertNull(
            Route::getRoutes()->getByName('booking.store')
        );

        $this->assertNull(
            Route::getRoutes()->getByName('booking.success')
        );

        $this->assertNull(
            Route::getRoutes()->getByName('admin.bookings.index')
        );
    }

    public function test_custom_trip_flow_is_separate_from_package_v2(): void
    {
        $this->assertSame(
            CustomTripRequestController::class . '@store',
            Route::getRoutes()
                ->getByName('custom-trip.store')
                ?->getActionName()
        );

        $this->assertSame(
            'custom-trip',
            Route::getRoutes()
                ->getByName('custom-trip.store')
                ?->uri()
        );
    }
}

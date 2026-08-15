<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminTourBookingRequestController;
use App\Http\Middleware\EnsureAdminIsLoggedIn;
use App\Models\CustomTripRequest;
use App\Models\TourBookingRequest;
use App\Models\TourPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class TourBookingLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_status_update_is_visible_on_the_same_authorized_public_view(): void
    {
        $booking = $this->createBooking(TourBookingRequest::STATUS_PENDING);
        $successRoute = route('booking-requests.success', $booking->booking_reference);

        $this->withSession([
            'booking_success_reference' => $booking->booking_reference,
        ])->get($successRoute)
            ->assertOk()
            ->assertSeeText('Pending');

        $this->withSession([
            'admin_logged_in' => true,
            'booking_success_reference' => $booking->booking_reference,
        ])->patch(
            route('admin.tour-booking-requests.status.update', $booking),
            ['status' => TourBookingRequest::STATUS_COMPLETED]
        )->assertRedirect();

        $this->assertDatabaseHas('tour_booking_requests', [
            'id' => $booking->id,
            'status' => TourBookingRequest::STATUS_COMPLETED,
        ]);

        $this->withSession([
            'booking_success_reference' => $booking->booking_reference,
        ])->get($successRoute)
            ->assertOk()
            ->assertSeeText('Completed')
            ->assertDontSeeText('Pending confirmation');
    }

    public function test_public_view_renders_every_supported_status_from_the_booking_record(): void
    {
        foreach (TourBookingRequest::STATUSES as $status) {
            $booking = $this->createBooking($status);

            $this->withSession([
                'booking_success_reference' => $booking->booking_reference,
            ])->get(route('booking-requests.success', $booking->booking_reference))
                ->assertOk()
                ->assertSeeText(ucfirst($status));
        }
    }

    public function test_public_status_view_still_requires_the_matching_session_reference(): void
    {
        $booking = $this->createBooking(TourBookingRequest::STATUS_PENDING);
        $url = route('booking-requests.success', $booking->booking_reference);

        $this->get($url)
            ->assertRedirect(route('tours'));

        $this->withSession([
            'booking_success_reference' => 'NWM-DIFFERENT-REFERENCE',
        ])->get($url)
            ->assertRedirect(route('tours'));
    }

    public function test_admin_can_delete_one_eligible_booking_without_affecting_other_records(): void
    {
        $tour = $this->createTour();
        $deletable = $this->createBooking(
            TourBookingRequest::STATUS_PENDING,
            $tour
        );
        $otherBooking = $this->createBooking(
            TourBookingRequest::STATUS_CONTACTED,
            $tour
        );
        $ratedBooking = $this->createBooking(
            TourBookingRequest::STATUS_COMPLETED,
            $tour
        );
        $unrelatedRating = $ratedBooking->ratingRecord()->create([
            'tour_package_id' => $tour->id,
            'rating' => 4,
        ]);
        $customTrip = $this->createCustomTrip();

        $this->withSession(['admin_logged_in' => true])
            ->delete(route('admin.tour-booking-requests.destroy', $deletable))
            ->assertRedirect(route('admin.tour-booking-requests.index'))
            ->assertSessionHas('success', 'Booking deleted successfully.');

        $this->assertDatabaseMissing('tour_booking_requests', [
            'id' => $deletable->id,
        ]);
        $this->assertDatabaseHas('tour_booking_requests', [
            'id' => $otherBooking->id,
        ]);
        $this->assertDatabaseHas('tour_booking_requests', [
            'id' => $ratedBooking->id,
        ]);
        $this->assertDatabaseHas('tour_ratings', [
            'id' => $unrelatedRating->id,
        ]);
        $this->assertDatabaseHas('tour_packages', ['id' => $tour->id]);
        $this->assertDatabaseHas('bookings', ['id' => $customTrip->id]);
    }

    public function test_eligible_booking_detail_has_an_identified_confirmed_delete_action(): void
    {
        $booking = $this->createBooking(TourBookingRequest::STATUS_CANCELLED);

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.tour-booking-requests.show', $booking))
            ->assertOk()
            ->assertSeeText('Danger zone')
            ->assertSeeText('Delete booking')
            ->assertSee(
                "Delete booking {$booking->booking_reference}? This action cannot be undone."
            );
    }

    public function test_rated_booking_cannot_be_deleted_and_rating_statistics_remain(): void
    {
        $tour = $this->createTour();
        $booking = $this->createBooking(
            TourBookingRequest::STATUS_COMPLETED,
            $tour
        );
        $rating = $booking->ratingRecord()->create([
            'tour_package_id' => $tour->id,
            'rating' => 5,
            'feedback' => 'A memorable day.',
        ]);

        $this->withSession(['admin_logged_in' => true])
            ->delete(route('admin.tour-booking-requests.destroy', $booking))
            ->assertRedirect()
            ->assertSessionHas(
                'error',
                'This booking has a verified guest rating and cannot be deleted.'
            );

        $this->assertDatabaseHas('tour_booking_requests', ['id' => $booking->id]);
        $this->assertDatabaseHas('tour_ratings', ['id' => $rating->id]);

        $statistics = TourPackage::query()
            ->withVerifiedStatistics()
            ->findOrFail($tour->id);

        $this->assertSame(1, $statistics->verified_rating_count);
        $this->assertSame(2, $statistics->verified_guest_count);
        $this->assertSame(5.0, (float) $statistics->verified_rating_average);
    }

    public function test_completed_unrated_booking_cannot_be_deleted_or_removed_from_guest_history(): void
    {
        $tour = $this->createTour();
        $booking = $this->createBooking(
            TourBookingRequest::STATUS_COMPLETED,
            $tour
        );

        $this->withSession(['admin_logged_in' => true])
            ->delete(route('admin.tour-booking-requests.destroy', $booking))
            ->assertRedirect()
            ->assertSessionHas(
                'error',
                'This completed booking is part of the guest history and cannot be deleted.'
            );

        $this->assertDatabaseHas('tour_booking_requests', ['id' => $booking->id]);

        $statistics = TourPackage::query()
            ->withVerifiedStatistics()
            ->findOrFail($tour->id);

        $this->assertSame(2, $statistics->verified_guest_count);
    }

    public function test_non_admin_cannot_delete_a_booking(): void
    {
        $booking = $this->createBooking(TourBookingRequest::STATUS_PENDING);

        $this->delete(route('admin.tour-booking-requests.destroy', $booking))
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseHas('tour_booking_requests', ['id' => $booking->id]);
    }

    public function test_destroy_route_is_admin_only_delete_and_never_get(): void
    {
        $route = Route::getRoutes()
            ->getByName('admin.tour-booking-requests.destroy');

        $this->assertNotNull($route);
        $this->assertSame(['DELETE'], $route->methods());
        $this->assertSame(
            AdminTourBookingRequestController::class . '@destroy',
            $route->getActionName()
        );
        $this->assertContains(
            EnsureAdminIsLoggedIn::class,
            $route->middleware()
        );
        $this->assertNotContains('GET', $route->methods());
    }

    private function createTour(): TourPackage
    {
        return TourPackage::query()->create([
            'title' => 'Private Lifecycle Tour',
            'slug' => 'private-lifecycle-tour-' . uniqid(),
            'status' => 'draft',
        ]);
    }

    private function createBooking(
        string $status,
        ?TourPackage $tour = null
    ): TourBookingRequest {
        $tour ??= $this->createTour();

        return TourBookingRequest::query()->create([
            'booking_reference' => 'NWM-' . strtoupper(uniqid()),
            'tour_package_id' => $tour->id,
            'status' => $status,
            'guest_name' => 'Lifecycle Guest',
            'guest_whatsapp' => '+628123456789',
            'pickup_address' => 'Sanur, Bali',
            'travel_date' => '2026-08-20',
            'starting_time' => '08:00',
            'language' => 'English',
            'adult_count' => 2,
            'child_count' => 0,
            'infant_count' => 0,
            'total_participants' => 2,
        ]);
    }

    private function createCustomTrip(): CustomTripRequest
    {
        return CustomTripRequest::query()->create([
            'booking_code' => 'CT-' . strtoupper(uniqid()),
            'name' => 'Custom Guest',
            'whatsapp' => '+628987654321',
            'selected_tour' => 'Custom Bali Trip',
            'trip_date' => '2026-08-21',
            'people_count' => 3,
            'selected_vehicle' => 'Toyota Avanza',
            'status' => 'pending',
        ]);
    }
}

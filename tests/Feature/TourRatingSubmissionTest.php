<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TourBookingRequest;
use App\Models\TourPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

final class TourRatingSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_booking_can_submit_one_verified_rating(): void
    {
        $booking = $this->createBooking(TourBookingRequest::STATUS_COMPLETED);
        $url = URL::signedRoute('tour-ratings.store', [
            'bookingReference' => $booking->booking_reference,
        ]);

        $response = $this->post($url, [
            'rating' => 5,
            'feedback' => 'A relaxed and memorable day.',
        ]);

        $response->assertRedirect();

        $this->get((string) $response->headers->get('Location'))
            ->assertOk()
            ->assertSee('Your verified rating')
            ->assertSee('5/5')
            ->assertSee('Your private review')
            ->assertSee('A relaxed and memorable day.');

        $this->assertDatabaseHas('tour_ratings', [
            'tour_booking_request_id' => $booking->id,
            'tour_package_id' => $booking->tour_package_id,
            'rating' => 5,
            'feedback' => 'A relaxed and memorable day.',
        ]);

        $this->post($url, ['rating' => 4])
            ->assertSessionHasErrors('rating');

        $this->assertDatabaseCount('tour_ratings', 1);
    }

    public function test_admin_can_find_the_rating_action_and_received_review(): void
    {
        $booking = $this->createBooking(TourBookingRequest::STATUS_COMPLETED);

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.tour-booking-requests.index'))
            ->assertOk()
            ->assertSee('Send rating link');

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.tour-booking-requests.show', $booking))
            ->assertOk()
            ->assertSee('waiting for a verified rating')
            ->assertSee('Send rating link');

        $booking->ratingRecord()->create([
            'tour_package_id' => $booking->tour_package_id,
            'rating' => 4,
            'feedback' => 'Helpful private feedback.',
        ]);

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.tour-booking-requests.index'))
            ->assertOk()
            ->assertSee('4/5 received');

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.tour-booking-requests.show', $booking))
            ->assertOk()
            ->assertSee('4/5')
            ->assertSee('Helpful private feedback.');
    }

    public function test_marking_a_trip_completed_reveals_the_rating_action(): void
    {
        $booking = $this->createBooking(TourBookingRequest::STATUS_CONFIRMED);

        $response = $this
            ->withSession(['admin_logged_in' => true])
            ->patch(
                route('admin.tour-booking-requests.status.update', $booking),
                ['status' => TourBookingRequest::STATUS_COMPLETED]
            );

        $response
            ->assertRedirect(route(
                'admin.tour-booking-requests.show',
                $booking
            ) . '#verified-trip-rating')
            ->assertSessionHas(
                'success',
                fn (string $message): bool => str_contains(
                    $message,
                    'verified rating link is ready'
                )
            );

        $this->assertSame(
            TourBookingRequest::STATUS_COMPLETED,
            $booking->fresh()->status
        );
    }

    public function test_unfinished_booking_cannot_rate(): void
    {
        foreach ([
            TourBookingRequest::STATUS_PENDING,
            TourBookingRequest::STATUS_CONFIRMED,
        ] as $status) {
            $booking = $this->createBooking($status);
            $url = URL::signedRoute('tour-ratings.store', [
                'bookingReference' => $booking->booking_reference,
            ]);

            $this->post($url, ['rating' => 5])->assertForbidden();
        }

        $this->assertDatabaseCount('tour_ratings', 0);
    }

    public function test_rating_value_and_signed_url_are_validated(): void
    {
        $booking = $this->createBooking(TourBookingRequest::STATUS_COMPLETED);
        $url = URL::signedRoute('tour-ratings.store', [
            'bookingReference' => $booking->booking_reference,
        ]);

        $this->post($url, ['rating' => 0])
            ->assertSessionHasErrors('rating');

        $this->post($url, ['rating' => 6])
            ->assertSessionHasErrors('rating');

        $this->post($url . '&signature=changed', ['rating' => 5])
            ->assertForbidden();

        $this->assertDatabaseCount('tour_ratings', 0);
    }

    private function createBooking(string $status): TourBookingRequest
    {
        $tour = TourPackage::query()->create([
            'title' => 'Private Ubud Day',
            'slug' => 'private-ubud-day-' . uniqid(),
            'status' => 'draft',
        ]);

        return TourBookingRequest::query()->create([
            'booking_reference' => 'NWM-' . strtoupper(uniqid()),
            'tour_package_id' => $tour->id,
            'status' => $status,
            'guest_name' => 'Test Guest',
            'guest_whatsapp' => '+628123456789',
            'pickup_address' => 'Ubud, Bali',
            'travel_date' => '2026-08-10',
            'starting_time' => '08:00',
            'language' => 'English',
            'adult_count' => 2,
            'child_count' => 0,
            'infant_count' => 0,
            'total_participants' => 2,
        ]);
    }
}

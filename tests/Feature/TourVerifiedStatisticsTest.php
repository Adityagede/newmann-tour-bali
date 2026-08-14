<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TourBookingRequest;
use App\Models\TourPackage;
use App\Models\TourRating;
use App\Support\TourViewData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TourVerifiedStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_total_is_derived_from_completed_bookings_only(): void
    {
        $tour = $this->createTour();
        $completed = $this->createBooking(
            $tour,
            TourBookingRequest::STATUS_COMPLETED,
            4
        );
        $confirmed = $this->createBooking(
            $tour,
            TourBookingRequest::STATUS_CONFIRMED,
            3
        );
        $this->createBooking(
            $tour,
            TourBookingRequest::STATUS_CANCELLED,
            8
        );

        $this->assertSame(4, $this->freshStatistics($tour)->verified_guest_count);

        $confirmed->update(['status' => TourBookingRequest::STATUS_COMPLETED]);
        $this->assertSame(7, $this->freshStatistics($tour)->verified_guest_count);

        $completed->update([
            'adult_count' => 1,
            'child_count' => 1,
            'infant_count' => 0,
            'total_participants' => 2,
        ]);
        $this->assertSame(5, $this->freshStatistics($tour)->verified_guest_count);

        $confirmed->delete();
        $this->assertSame(2, $this->freshStatistics($tour)->verified_guest_count);
    }

    public function test_public_rating_uses_verified_records_and_ignores_legacy_values(): void
    {
        $tour = $this->createTour([
            'rating' => 5,
            'guests' => '999',
        ]);

        $zeroState = TourViewData::make($this->freshStatistics($tour));
        $this->assertFalse($zeroState['has_rating']);
        $this->assertSame(0, $zeroState['guest_count']);

        foreach ([5, 5, 4] as $value) {
            $booking = $this->createBooking(
                $tour,
                TourBookingRequest::STATUS_COMPLETED,
                1
            );

            TourRating::query()->create([
                'tour_booking_request_id' => $booking->id,
                'tour_package_id' => $tour->id,
                'rating' => $value,
            ]);
        }

        $statistics = $this->freshStatistics($tour);
        $viewData = TourViewData::make($statistics);

        $this->assertEqualsWithDelta(4.6667, (float) $statistics->verified_rating_average, 0.001);
        $this->assertSame(3, $statistics->verified_rating_count);
        $this->assertSame(3, $statistics->verified_guest_count);
        $this->assertSame('3 verified ratings', $viewData['rating_text']);
        $this->assertSame('3 guests hosted', $viewData['hosted_guest_text']);
    }

    public function test_tour_detail_exposes_a_complete_zero_statistics_contract(): void
    {
        $tour = $this->createTour([
            'slug' => 'tour-without-statistics',
            'status' => 'active',
            'guests' => 'Up to 6 guests',
            'max_guests' => 6,
        ]);

        $response = $this->get(route('tours.detail', $tour->slug));

        $response
            ->assertOk()
            ->assertSee('New tour')
            ->assertDontSee('0 guests hosted');

        $viewTour = $response->viewData('tour');

        $this->assertIsArray($viewTour);
        $this->assertArrayHasKey('rating_count', $viewTour);
        $this->assertArrayHasKey('rating_text', $viewTour);
        $this->assertArrayHasKey('hosted_guest_count', $viewTour);
        $this->assertArrayHasKey('hosted_guest_text', $viewTour);
        $this->assertSame(0, $viewTour['rating_count']);
        $this->assertNull($viewTour['rating_text']);
        $this->assertSame(0, $viewTour['hosted_guest_count']);
        $this->assertNull($viewTour['hosted_guest_text']);
        $this->assertSame('Up to 6 guests', $viewTour['guests']);
        $this->assertSame(6, $viewTour['max_guests']);
    }

    public function test_verified_statistics_render_across_public_tour_pages(): void
    {
        $tour = $this->createTour([
            'slug' => 'tour-with-verified-statistics',
            'status' => 'active',
            'is_popular' => true,
        ]);
        $completed = $this->createBooking(
            $tour,
            TourBookingRequest::STATUS_COMPLETED,
            3
        );
        $this->createBooking(
            $tour,
            TourBookingRequest::STATUS_CONFIRMED,
            7
        );

        TourRating::query()->create([
            'tour_booking_request_id' => $completed->id,
            'tour_package_id' => $tour->id,
            'rating' => 5,
        ]);

        $detailResponse = $this->get(route('tours.detail', $tour->slug));

        $detailResponse
            ->assertOk()
            ->assertSee('5.0')
            ->assertSee('1 verified rating')
            ->assertSee('3 guests hosted')
            ->assertDontSee('10 guests hosted');

        $viewTour = $detailResponse->viewData('tour');

        $this->assertSame(1, $viewTour['rating_count']);
        $this->assertSame('1 verified rating', $viewTour['rating_text']);
        $this->assertSame(3, $viewTour['hosted_guest_count']);
        $this->assertSame('3 guests hosted', $viewTour['hosted_guest_text']);

        $this->get(route('tours'))
            ->assertOk()
            ->assertSee('5.0')
            ->assertSee('1 verified rating');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('5.0')
            ->assertSee('1 verified rating');
    }

    private function createTour(array $overrides = []): TourPackage
    {
        return TourPackage::query()->create(array_merge([
            'title' => 'Private East Bali Day',
            'slug' => 'private-east-bali-day-' . uniqid(),
            'status' => 'draft',
        ], $overrides));
    }

    private function createBooking(
        TourPackage $tour,
        string $status,
        int $participants
    ): TourBookingRequest {
        return TourBookingRequest::query()->create([
            'booking_reference' => 'NWM-' . strtoupper(uniqid()),
            'tour_package_id' => $tour->id,
            'status' => $status,
            'guest_name' => 'Test Guest',
            'guest_whatsapp' => '+628123456789',
            'pickup_address' => 'Sanur, Bali',
            'travel_date' => '2026-08-10',
            'starting_time' => '08:00',
            'language' => 'English',
            'adult_count' => max(1, $participants - 2),
            'child_count' => $participants > 1 ? 1 : 0,
            'infant_count' => $participants > 2 ? 1 : 0,
            'total_participants' => $participants,
        ]);
    }

    private function freshStatistics(TourPackage $tour): TourPackage
    {
        return TourPackage::query()
            ->withVerifiedStatistics()
            ->findOrFail($tour->id);
    }
}

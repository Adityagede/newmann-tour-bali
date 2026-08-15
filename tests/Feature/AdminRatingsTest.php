<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TourBookingRequest;
use App\Models\TourPackage;
use App\Models\TourRating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

final class AdminRatingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_access_is_protected_and_routes_are_read_only(): void
    {
        $tour = $this->createTour('Protected Tour');
        $rating = $this->createRating(
            $tour,
            TourBookingRequest::STATUS_COMPLETED,
            5,
            'Protected Guest',
            'Protected feedback.'
        );

        $this->get(route('admin.ratings.index'))
            ->assertRedirect(route('admin.login'));

        $this->get(route('admin.ratings.show', $rating))
            ->assertRedirect(route('admin.login'));

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.ratings.index'))
            ->assertOk()
            ->assertSee('Ratings &amp; Reviews', false);

        $this->assertSame(
            ['GET', 'HEAD'],
            app('router')->getRoutes()
                ->getByName('admin.ratings.index')
                ?->methods()
        );
        $this->assertSame(
            ['GET', 'HEAD'],
            app('router')->getRoutes()
                ->getByName('admin.ratings.show')
                ?->methods()
        );
    }

    public function test_index_only_lists_and_summarizes_completed_booking_ratings(): void
    {
        $ubud = $this->createTour('Private Ubud Day');
        $eastBali = $this->createTour('East Bali Journey');

        $first = $this->createRating(
            $ubud,
            TourBookingRequest::STATUS_COMPLETED,
            5,
            'Verified Guest One',
            'A thoughtful and relaxed day.'
        );
        $this->createRating(
            $eastBali,
            TourBookingRequest::STATUS_COMPLETED,
            4,
            'Verified Guest Two',
            null
        );
        $this->createRating(
            $ubud,
            TourBookingRequest::STATUS_PENDING,
            1,
            'Pending Guest',
            'Should not be visible.'
        );
        $this->createRating(
            $eastBali,
            TourBookingRequest::STATUS_CANCELLED,
            2,
            'Cancelled Guest',
            'Should not be visible either.'
        );

        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.ratings.index'));

        $response
            ->assertOk()
            ->assertSee('Verified Guest One')
            ->assertSee('Verified Guest Two')
            ->assertSee($first->bookingRequest->booking_reference)
            ->assertSee('Private Ubud Day')
            ->assertSee('10 Aug 2026')
            ->assertSee('A thoughtful and relaxed day.')
            ->assertSee('No written feedback.')
            ->assertDontSee('Pending Guest')
            ->assertDontSee('Cancelled Guest')
            ->assertDontSee('Should not be visible.');

        $summary = $response->viewData('summary');

        $this->assertEqualsWithDelta(4.5, $summary['average_rating'], 0.001);
        $this->assertSame(2, $summary['verified_reviews']);
        $this->assertSame(2, $summary['tours_reviewed']);
        $this->assertSame(2, $response->viewData('ratings')->total());
    }

    public function test_filters_search_and_sort_are_applied_to_verified_ratings(): void
    {
        $ubud = $this->createTour('Private Ubud Day');
        $north = $this->createTour('North Bali Escape');

        Carbon::setTestNow('2026-08-10 09:00:00');
        $older = $this->createRating(
            $ubud,
            TourBookingRequest::STATUS_COMPLETED,
            5,
            'Older Guest',
            'Outstanding Ubud trip.'
        );

        Carbon::setTestNow('2026-08-12 09:00:00');
        $this->createRating(
            $north,
            TourBookingRequest::STATUS_COMPLETED,
            3,
            'Newer Guest',
            'A calm northern route.'
        );
        Carbon::setTestNow();

        $filtered = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.ratings.index', [
                'tour' => $ubud->id,
                'rating' => 5,
                'sort' => 'oldest',
            ]));

        $filtered
            ->assertOk()
            ->assertSee('Older Guest')
            ->assertDontSee('Newer Guest');

        $search = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.ratings.index', [
                'q' => $older->bookingRequest->booking_reference,
            ]));

        $search
            ->assertOk()
            ->assertSee('Older Guest')
            ->assertDontSee('Newer Guest');

        $sorted = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.ratings.index', ['sort' => 'lowest']));

        $sorted
            ->assertOk()
            ->assertSeeInOrder(['Newer Guest', 'Older Guest']);
    }

    public function test_admin_can_view_full_verified_rating_detail(): void
    {
        $tour = $this->createTour('Private Ubud Day');
        $verified = $this->createRating(
            $tour,
            TourBookingRequest::STATUS_COMPLETED,
            5,
            'Detail Guest',
            'The full private review remains visible on the detail page.'
        );
        $unverified = $this->createRating(
            $tour,
            TourBookingRequest::STATUS_PENDING,
            4,
            'Pending Detail Guest',
            'Not a verified review.'
        );

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.ratings.show', $verified))
            ->assertOk()
            ->assertSee('Verified Trip Rating')
            ->assertSee('5.0 out of 5')
            ->assertSee('The full private review remains visible on the detail page.')
            ->assertSee('Private Ubud Day')
            ->assertSee('Detail Guest')
            ->assertSee($verified->bookingRequest->booking_reference)
            ->assertSee('View Booking');

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.ratings.show', $unverified))
            ->assertNotFound();
    }

    private function createTour(string $title): TourPackage
    {
        return TourPackage::query()->create([
            'title' => $title,
            'slug' => str($title)->slug() . '-' . uniqid(),
            'status' => 'draft',
        ]);
    }

    private function createRating(
        TourPackage $tour,
        string $status,
        int $score,
        string $guestName,
        ?string $feedback
    ): TourRating {
        $booking = TourBookingRequest::query()->create([
            'booking_reference' => 'NWM-' . strtoupper(uniqid()),
            'tour_package_id' => $tour->id,
            'status' => $status,
            'guest_name' => $guestName,
            'guest_whatsapp' => '+628123456789',
            'guest_email' => strtolower(str_replace(' ', '.', $guestName)) . '@example.test',
            'pickup_address' => 'Ubud, Bali',
            'travel_date' => '2026-08-10',
            'starting_time' => '08:00',
            'language' => 'English',
            'adult_count' => 2,
            'child_count' => 0,
            'infant_count' => 0,
            'total_participants' => 2,
        ]);

        return TourRating::query()->create([
            'tour_booking_request_id' => $booking->id,
            'tour_package_id' => $tour->id,
            'rating' => $score,
            'feedback' => $feedback,
        ]);
    }
}

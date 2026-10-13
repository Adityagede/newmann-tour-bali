<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendBookingNotification;
use App\Models\CustomTripRequest;
use App\Models\TourBookingRequest;
use App\Models\TourOption;
use App\Models\TourPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

final class BookingProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Notification::fake();
        config(['newman.booking_notification_email' => 'admin@example.com']);
        $this->withCredentials();
        $this->newVisitor();
    }

    public function test_repeated_custom_form_creates_one_order_and_one_notification_job(): void
    {
        $payload = $this->customPayload();
        $first = $this->post('/custom-trip', $payload)->assertRedirect();
        $this->post('/custom-trip', $payload)->assertRedirect($first->headers->get('Location'));

        $this->assertDatabaseCount('bookings', 1);
        Queue::assertPushed(SendBookingNotification::class, 1);
        Notification::assertNothingSent();
        $this->assertNotNull(CustomTripRequest::firstOrFail()->admin_notification_queued_at);
    }

    public function test_rendered_forms_use_fresh_tokens_and_preserve_token_after_validation_error(): void
    {
        $first = $this->get('/custom-trip')->assertOk()->assertSee('name="submission_token"', false)->viewData('submissionToken');
        $second = $this->get('/custom-trip')->assertOk()->viewData('submissionToken');
        $this->assertTrue(Str::isUuid($first));
        $this->assertNotSame($first, $second);
        $payload = $this->customPayload();
        unset($payload['message']);
        $this->from('/custom-trip')->post('/custom-trip', $payload)->assertSessionHasErrors('message');
        $this->get('/custom-trip')->assertViewHas('submissionToken', $payload['submission_token']);

        [$tour, $review] = $this->tourReview();
        $this->post('/tours/'.$tour->slug.'/review', [
            'tour_option_id' => $review['option']['id'], 'travel_date' => $review['selection']['travel_date'],
            'starting_time' => '09:00', 'language' => 'English', 'adults' => 2, 'children' => 0, 'infants' => 0,
        ])->assertSessionDoesntHaveErrors()->assertRedirect();
        $this->get('/tours/'.$tour->slug.'/review')
            ->assertOk()->assertSee('name="submission_token"', false)
            ->assertViewHas('submissionToken', fn ($token) => Str::isUuid($token));
    }

    public function test_changed_payload_cannot_reuse_a_submitted_token(): void
    {
        $payload = $this->customPayload();
        $this->post('/custom-trip', $payload)->assertRedirect();
        $payload['people_count'] = 4;
        $this->from('/custom-trip')->post('/custom-trip', $payload)->assertSessionHasErrors('booking');

        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseHas('bookings', ['people_count' => 2]);
        Queue::assertPushed(SendBookingNotification::class, 1);
    }

    public function test_distinct_forms_and_distinct_visitors_can_place_identical_orders(): void
    {
        $payload = $this->customPayload();
        $this->post('/custom-trip', $payload)->assertRedirect();
        $this->post('/custom-trip', array_replace($payload, ['submission_token' => (string) Str::uuid()]))->assertRedirect();
        $this->newVisitor();
        $this->post('/custom-trip', $payload)->assertRedirect();

        $this->assertDatabaseCount('bookings', 3);
        Queue::assertPushed(SendBookingNotification::class, 3);
    }

    public function test_legacy_forms_still_submit_and_replay_safely(): void
    {
        $payload = $this->customPayload();
        unset($payload['submission_token']);
        $this->post('/custom-trip', $payload)->assertRedirect();
        $this->post('/custom-trip', $payload)->assertRedirect();
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_custom_success_is_private_and_multiple_references_remain_accessible_to_owner(): void
    {
        $first = $this->post('/custom-trip', $this->customPayload())->headers->get('Location');
        $second = $this->post('/custom-trip', $this->customPayload())->headers->get('Location');
        $response = $this->get($first)->assertOk()->assertSee('Protection Guest');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->get($second)->assertOk();

        $this->newVisitor();
        $this->get($first)->assertNotFound()->assertDontSee('Protection Guest');
        $this->get('/custom-trip/success/NWM-CT-NOT-A-REAL-CODE')->assertNotFound();
    }

    public function test_expired_custom_success_access_is_rejected(): void
    {
        config(['newman.booking_protection.success_access_minutes' => 1]);
        $url = $this->post('/custom-trip', $this->customPayload())->headers->get('Location');
        $this->get($url)->assertOk();
        $this->travel(61)->seconds();
        $this->get($url)->assertNotFound();
    }

    public function test_booking_limit_preserves_input_and_resumes_after_window(): void
    {
        config(['newman.booking_protection.session_per_minute' => 2]);
        $this->post('/custom-trip', $this->customPayload())->assertRedirect();
        $this->post('/custom-trip', $this->customPayload())->assertRedirect();
        $payload = $this->customPayload();
        $response = $this->post('/custom-trip', $payload)->assertRedirect('/custom-trip')
            ->assertSessionHasErrors('booking')->assertSessionHasInput('name', $payload['name'])
            ->assertSessionHasInput('submission_token', $payload['submission_token']);
        $this->assertGreaterThan(0, (int) $response->headers->get('Retry-After'));
        $this->assertDatabaseCount('bookings', 2);
        $this->travel(61)->seconds();
        $this->post('/custom-trip', $payload)->assertRedirect();
        $this->assertDatabaseCount('bookings', 3);
    }

    public function test_guests_on_same_ip_have_separate_session_quotas(): void
    {
        config(['newman.booking_protection.session_per_minute' => 1, 'newman.booking_protection.ip_per_minute' => 100]);
        $this->post('/custom-trip', $this->customPayload())->assertRedirect();
        $this->post('/custom-trip', $this->customPayload())->assertSessionHasErrors('booking');
        $this->newVisitor();
        $this->post('/custom-trip', $this->customPayload())->assertSessionDoesntHaveErrors();
        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_ip_backstop_applies_even_when_session_is_changed(): void
    {
        config(['newman.booking_protection.session_per_minute' => 10, 'newman.booking_protection.ip_per_minute' => 1]);
        $this->post('/custom-trip', $this->customPayload())->assertRedirect();
        $this->newVisitor();
        $this->postJson('/custom-trip', $this->customPayload())->assertStatus(429)->assertJsonStructure(['message']);
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_language_switch_does_not_reset_booking_quota(): void
    {
        config(['newman.booking_protection.session_per_minute' => 1]);
        $this->post('/custom-trip', $this->customPayload())->assertRedirect();
        $this->post('/id/custom-trip', $this->customPayload())->assertRedirect('/id/custom-trip')
            ->assertSessionHasErrors('booking')->assertSessionHas('error', fn ($value) => str_contains($value, 'Mohon tunggu'));
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_availability_has_its_own_larger_quota_and_localized_json_errors(): void
    {
        config(['newman.booking_protection.session_per_minute' => 1, 'newman.booking_protection.availability_session_per_minute' => 2]);
        [$tour] = $this->tourReview();
        $payload = ['travel_date' => now()->addWeek()->toDateString(), 'participants' => ['adult' => 2, 'child' => 0, 'infant' => 0], 'language' => 'English'];
        $this->post('/custom-trip', $this->customPayload())->assertRedirect();
        $this->postJson('/tours/'.$tour->slug.'/availability', $payload)->assertOk();
        $this->postJson('/id/tours/'.$tour->slug.'/availability', $payload)->assertOk();
        $response = $this->postJson('/id/tours/'.$tour->slug.'/availability', $payload)->assertStatus(429);
        $this->assertStringContainsString('Mohon tunggu', $response->json('message'));
        $this->assertGreaterThan(0, (int) $response->headers->get('Retry-After'));
    }

    public function test_review_post_cannot_bypass_availability_quota(): void
    {
        config(['newman.booking_protection.availability_session_per_minute' => 1]);
        $this->postJson('/tours/missing-tour/availability', ['travel_date' => now()->addWeek()->toDateString()])->assertNotFound();
        $this->post('/tours/missing-tour/review', [])->assertRedirect('/tours/missing-tour')
            ->assertSessionHasErrors('booking');
    }

    public function test_invalid_token_or_array_fields_do_not_create_orders(): void
    {
        $this->post('/custom-trip', array_replace($this->customPayload(), ['submission_token' => 'invalid']))
            ->assertSessionHasErrors('submission_token');
        $this->post('/custom-trip', array_replace($this->customPayload(), ['name' => ['invalid']]))
            ->assertSessionHasErrors('name');
        $this->assertDatabaseCount('bookings', 0);
        Queue::assertNothingPushed();
    }

    public function test_tour_booking_recalculates_price_and_replay_survives_cleared_review(): void
    {
        [$tour, $review] = $this->tourReview();
        $payload = $this->tourPayload();
        $first = $this->withSession(['tour_review_selection' => $review])
            ->post('/tours/'.$tour->slug.'/booking-request', $payload)->assertSessionDoesntHaveErrors()->assertRedirect();
        $booking = TourBookingRequest::firstOrFail();
        $this->assertSame(800000, (int) $booking->estimated_total);
        $this->assertNull(session('tour_review_selection'));
        $this->post('/tours/'.$tour->slug.'/booking-request', $payload)->assertRedirect($first->headers->get('Location'));
        $this->assertDatabaseCount('tour_booking_requests', 1);
        Queue::assertPushed(SendBookingNotification::class, 1);
        $this->get($first->headers->get('Location'))->assertOk();
    }

    public function test_tour_replay_rejects_changed_details_or_changed_selection(): void
    {
        [$tour, $review] = $this->tourReview();
        $payload = $this->tourPayload();
        $url = '/tours/'.$tour->slug.'/booking-request';
        $this->withSession(['tour_review_selection' => $review])->post($url, $payload)->assertSessionDoesntHaveErrors();
        $this->post($url, array_replace($payload, ['full_name' => 'Changed Guest']))->assertSessionHasErrors('booking');
        $review['selection']['participants']['adult'] = 3;
        $this->withSession(['tour_review_selection' => $review])->post($url, $payload)->assertSessionHasErrors('booking');
        $this->assertDatabaseCount('tour_booking_requests', 1);
        Queue::assertPushed(SendBookingNotification::class, 1);
        $this->assertSame(3, session('tour_review_selection.selection.participants.adult'));
    }

    public function test_new_tour_form_can_intentionally_book_same_trip_again(): void
    {
        [$tour, $review] = $this->tourReview();
        $url = '/tours/'.$tour->slug.'/booking-request';
        $this->withSession(['tour_review_selection' => $review])->post($url, $this->tourPayload())->assertSessionDoesntHaveErrors();
        $this->withSession(['tour_review_selection' => $review])->post($url, $this->tourPayload())->assertSessionDoesntHaveErrors();
        $this->assertDatabaseCount('tour_booking_requests', 2);
        Queue::assertPushed(SendBookingNotification::class, 2);
    }

    private function newVisitor(): void
    {
        $this->app['session']->driver()->invalidate();
        $this->withCookie(config('session.cookie'), Str::random(40));
    }

    private function customPayload(): array
    {
        return [
            'submission_token' => (string) Str::uuid(), 'trip_date' => now()->addWeek()->toDateString(),
            'people_count' => 2, 'pickup_area' => 'Ubud hotel', 'message' => 'A relaxed private tour of temples and rice terraces.',
            'selected_vehicle' => 'Toyota Avanza', 'name' => 'Protection Guest', 'whatsapp' => '+628123456789',
            'email' => 'guest@example.com',
        ];
    }

    private function tourPayload(): array
    {
        return [
            'submission_token' => (string) Str::uuid(), 'full_name' => 'Tour Protection Guest', 'whatsapp' => '+628123456789',
            'email' => 'guest@example.com', 'pickup_address' => 'Ubud hotel, Bali', 'special_requests' => null, 'agreement' => '1',
        ];
    }

    private function tourReview(): array
    {
        $date = now()->addWeek();
        $tour = TourPackage::withoutEvents(fn () => TourPackage::create([
            'title' => 'Protection Test Tour', 'slug' => 'protection-test-tour', 'status' => 'active',
        ]));
        $option = TourOption::withoutEvents(fn () => $tour->options()->create([
            'title' => 'Private Tour', 'slug' => 'private-tour', 'languages' => ['English'], 'status' => 'active',
            'min_guests' => 1, 'max_guests' => 10,
        ]));
        foreach (['adult' => 400000, 'child' => 250000, 'infant' => 0] as $type => $price) {
            $option->prices()->create(['participant_type' => $type, 'label' => ucfirst($type), 'base_price' => $price, 'currency' => 'IDR',
                'is_allowed' => true, 'is_free' => $type === 'infant']);
        }
        $option->schedules()->create(['day_of_week' => $date->dayOfWeek, 'start_time' => '09:00:00',
            'capacity' => 10, 'booking_cutoff_hours' => 0, 'is_active' => true]);

        return [$tour, [
            'tour' => ['id' => $tour->id], 'option' => ['id' => $option->id], 'is_preview' => false,
            'selection' => ['travel_date' => $date->toDateString(), 'starting_time' => '09:00', 'language' => 'English',
                'participants' => ['adult' => 2, 'child' => 0, 'infant' => 0]],
            'pricing' => ['estimated_total' => 1],
        ]];
    }
}

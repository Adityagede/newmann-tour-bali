<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendBookingNotification;
use App\Models\CustomTripRequest;
use App\Models\TourBookingRequest;
use App\Models\TourPackage;
use App\Notifications\NewCustomTripRequestNotification;
use App\Notifications\NewTourBookingRequestNotification;
use App\Services\BookingNotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

final class BookingNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        config(['newman.booking_notification_email' => 'admin@example.com']);
        $this->withCookie(config('session.cookie'), Str::random(40));
    }

    public function test_notification_is_persisted_in_database_queue_without_synchronous_email(): void
    {
        $booking = $this->submitCustom();
        $this->assertDatabaseCount('jobs', 1);
        $job = DB::table('jobs')->first();
        $this->assertSame('booking-notifications', $job->queue);
        $this->assertStringNotContainsString('Notification Guest', $job->payload);
        $this->assertTrue($booking->admin_notification_pending);
        $this->assertNotNull($booking->admin_notification_queued_at);
        Notification::assertNothingSent();
        app(BookingNotificationDispatcher::class)->dispatch($booking, 'custom');
        $this->assertDatabaseCount('jobs', 1);
    }

    public function test_completed_job_and_duplicate_job_only_notify_once(): void
    {
        $booking = $this->submitCustom();
        $job = new SendBookingNotification('custom', $booking->id);
        $job->handle();
        $job->handle();
        Notification::assertSentOnDemandTimes(NewCustomTripRequestNotification::class, 1);
        $this->assertFalse($booking->fresh()->admin_notification_pending);
        $this->assertNotNull($booking->fresh()->admin_notified_at);
    }

    public function test_enqueue_failure_does_not_lose_order_and_recovery_queues_once(): void
    {
        $realBus = Bus::getFacadeRoot();
        Bus::shouldReceive('dispatch')->once()->andThrow(new RuntimeException('Simulated enqueue failure'));
        $booking = $this->submitCustom();
        Bus::swap($realBus);
        $this->assertTrue($booking->admin_notification_pending);
        $this->assertNull($booking->admin_notification_queued_at);
        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('jobs', 0);
        $this->artisan('booking-notifications:dispatch')->assertSuccessful();
        $this->artisan('booking-notifications:dispatch')->assertSuccessful();
        $this->assertDatabaseCount('jobs', 1);
        $this->assertNotNull($booking->fresh()->admin_notification_queued_at);
        Notification::assertNothingSent();
    }

    public function test_queue_insert_rolls_back_when_queued_marker_cannot_be_saved(): void
    {
        $event = 'eloquent.saving: '.CustomTripRequest::class;
        Event::listen($event, function (CustomTripRequest $booking): void {
            if ($booking->isDirty('admin_notification_queued_at')) {
                throw new RuntimeException('Simulated marker write failure');
            }
        });
        try {
            $booking = $this->submitCustom();
        } finally {
            Event::forget($event);
        }
        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('jobs', 0);
        $this->assertNull($booking->admin_notification_queued_at);
        $this->artisan('booking-notifications:dispatch')->assertSuccessful();
        $this->assertDatabaseCount('jobs', 1);
    }

    public function test_smtp_failure_leaves_saved_order_pending_and_can_be_retried(): void
    {
        $booking = $this->submitCustom();
        Notification::shouldReceive('sendNow')->once()->andThrow(new RuntimeException('Simulated SMTP failure'));
        try {
            (new SendBookingNotification('custom', $booking->id))->handle();
            $this->fail('SMTP failure must reach the queue retry handler.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated SMTP failure', $exception->getMessage());
        }
        $this->assertDatabaseCount('bookings', 1);
        $this->assertTrue($booking->fresh()->admin_notification_pending);
        $this->assertNull($booking->fresh()->admin_notified_at);
        Notification::fake();
        (new SendBookingNotification('custom', $booking->id))->handle();
        Notification::assertSentOnDemandTimes(NewCustomTripRequestNotification::class, 1);
    }

    public function test_email_rate_limit_delays_notification_without_rejecting_orders(): void
    {
        config(['newman.booking_protection.notification_emails_per_minute' => 1]);
        $first = $this->submitCustom();
        $second = $this->submitCustom();
        $middleware = new RateLimited('booking-notifications');
        $firstJob = (new SendBookingNotification('custom', $first->id))->withFakeQueueInteractions();
        $secondJob = (new SendBookingNotification('custom', $second->id))->withFakeQueueInteractions();
        $middleware->handle($firstJob, fn ($job) => $job->handle());
        $middleware->handle($secondJob, fn ($job) => $job->handle());
        $secondJob->assertReleased()->assertNotFailed();
        $this->assertDatabaseCount('bookings', 2);
        $this->assertTrue($second->fresh()->admin_notification_pending);
        Notification::assertSentOnDemandTimes(NewCustomTripRequestNotification::class, 1);
        $this->travel(61)->seconds();
        $middleware->handle($secondJob, fn ($job) => $job->handle());
        Notification::assertSentOnDemandTimes(NewCustomTripRequestNotification::class, 2);
        $this->assertFalse($second->fresh()->admin_notification_pending);
    }

    public function test_worker_processes_persisted_job_and_leaves_other_queues_untouched(): void
    {
        $booking = $this->submitCustom();
        DB::table('jobs')->insert(['queue' => 'default', 'payload' => '{}', 'attempts' => 0,
            'available_at' => now()->timestamp, 'created_at' => now()->timestamp]);
        $this->artisan('booking-notifications:work', ['--max-time' => 1])->assertSuccessful();
        Notification::assertSentOnDemandTimes(NewCustomTripRequestNotification::class, 1);
        $this->assertFalse($booking->fresh()->admin_notification_pending);
        $this->assertSame(1, DB::table('jobs')->where('queue', 'default')->count());
        $this->assertSame(0, DB::table('jobs')->where('queue', 'booking-notifications')->count());
    }

    public function test_cron_worker_skips_overlapping_execution(): void
    {
        $this->submitCustom();
        $lock = Cache::lock('booking-notifications-cron', 120);
        $this->assertTrue($lock->get());
        try {
            $this->artisan('booking-notifications:work', ['--max-time' => 1])->assertSuccessful();
            Notification::assertNothingSent();
            $this->assertDatabaseCount('jobs', 1);
        } finally {
            $lock->release();
        }
    }

    public function test_existing_bookings_without_pending_flag_are_not_sent_by_recovery(): void
    {
        CustomTripRequest::create(['booking_code' => 'LEGACY-CUSTOM', 'name' => 'Legacy Guest', 'whatsapp' => '+628123456789',
            'selected_tour' => 'Custom Bali Private Tour', 'selected_vehicle' => 'Toyota Avanza', 'people_count' => 2, 'status' => 'pending']);
        $this->artisan('booking-notifications:dispatch')->assertSuccessful();
        $this->assertDatabaseCount('jobs', 0);
        Notification::assertNothingSent();
    }

    public function test_tour_notification_uses_existing_template_and_marks_sent_once(): void
    {
        $tour = TourPackage::create(['title' => 'Notification Tour', 'slug' => 'notification-tour', 'status' => 'draft']);
        $booking = TourBookingRequest::create([
            'booking_reference' => 'NOTIFICATION-TOUR', 'tour_package_id' => $tour->id, 'status' => 'pending',
            'guest_name' => 'Tour Notification Guest', 'guest_whatsapp' => '+628123456789', 'pickup_address' => 'Ubud hotel, Bali',
            'travel_date' => now()->addWeek()->toDateString(), 'starting_time' => '09:00', 'language' => 'English',
            'adult_count' => 2, 'child_count' => 0, 'infant_count' => 0, 'total_participants' => 2, 'admin_notification_pending' => true,
        ]);
        app(BookingNotificationDispatcher::class)->dispatch($booking, 'tour');
        (new SendBookingNotification('tour', $booking->id))->handle();
        (new SendBookingNotification('tour', $booking->id))->handle();
        Notification::assertSentOnDemandTimes(NewTourBookingRequestNotification::class, 1);
        $this->assertNotNull($booking->fresh()->admin_notified_at);
    }

    private function submitCustom(): CustomTripRequest
    {
        $response = $this->post('/custom-trip', [
            'submission_token' => (string) Str::uuid(), 'trip_date' => now()->addWeek()->toDateString(),
            'people_count' => 2, 'selected_vehicle' => 'Toyota Avanza', 'name' => 'Notification Guest',
            'whatsapp' => '+628123456789', 'message' => 'A relaxed private trip visiting temples and rice terraces.',
        ])->assertSessionDoesntHaveErrors()->assertRedirect();
        $booking = CustomTripRequest::latest('id')->firstOrFail();
        $this->assertStringContainsString($booking->booking_code, $response->headers->get('Location'));

        return $booking;
    }
}

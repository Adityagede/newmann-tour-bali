<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Notifications\NewCustomTripRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class CustomTripRequestV2Test extends TestCase
{
    use RefreshDatabase;

    public function test_custom_trip_page_explains_the_request_flow(): void
    {
        $response = $this->get(route('custom-trip.create'));

        $response
            ->assertOk()
            ->assertSee('Custom Trip Request V2')
            ->assertSee('Vehicle preference')
            ->assertSee('No website payment');
    }

    public function test_custom_trip_request_is_saved_without_package_booking_data(): void
    {
        Notification::fake();

        config()->set(
            'newman.booking_notification_email',
            'admin@example.com'
        );

        $response = $this->post(route('custom-trip.store'), [
            'trip_date' => now()->addWeek()->toDateString(),
            'people_count' => 7,
            'pickup_area' => 'Ubud hotel',
            'message' => 'Ubud rice terrace, temple, waterfall, and a relaxed lunch stop.',
            'selected_vehicle' => 'Toyota Hiace',
            'custom_vehicle' => null,
            'name' => 'Test Guest',
            'whatsapp' => '+628123456789',
            'email' => 'guest@example.com',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'tour_package_id' => null,
            'name' => 'Test Guest',
            'people_count' => 7,
            'selected_vehicle' => 'Toyota Hiace',
            'status' => 'pending',
        ]);

        Notification::assertSentOnDemand(
            NewCustomTripRequestNotification::class
        );
    }

    public function test_people_count_and_trip_plan_are_required(): void
    {
        $response = $this->from(route('custom-trip.create'))
            ->post(route('custom-trip.store'), [
                'selected_vehicle' => 'Not sure, please recommend',
                'name' => 'Test Guest',
                'whatsapp' => '+628123456789',
            ]);

        $response
            ->assertRedirect(route('custom-trip.create'))
            ->assertSessionHasErrors([
                'people_count',
                'message',
            ]);
    }
}

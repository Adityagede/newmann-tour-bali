<?php

namespace Tests\Feature;

use App\Models\TourOption;
use App\Models\TourOptionBlackoutDate;
use App\Models\TourOptionPrice;
use App\Models\TourOptionSchedule;
use App\Models\TourPackage;
use App\Services\TourAvailabilityService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class TourAvailabilityServiceTest extends TestCase
{
    public function test_it_returns_an_available_option(): void
    {
        $now = Carbon::parse(
            '2026-07-17 08:00:00',
            config('app.timezone')
        );

        $tour = $this->makeTourGraph(
            travelDate: '2026-07-20',
            capacity: 12
        );

        $result = app(
            TourAvailabilityService::class
        )->check(
            $tour,
            '2026-07-20',
            [
                'adult' => 2,
                'child' => 1,
                'infant' => 1,
            ],
            'English',
            $now
        );

        $this->assertTrue($result['available']);

        $this->assertSame(
    'private_car',
    $result['recommended_transport']['key']
);

$this->assertSame(
    4,
    $result['recommended_transport'][
        'total_passengers'
    ]
);

$this->assertTrue(
    $result['recommended_transport'][
        'confirmation_required'
    ]
);

        $this->assertCount(
            1,
            $result['available_options']
        );

        $option = $result['available_options'][0];

        $this->assertSame(
            1050000,
            $option['pricing']['base_total']
        );

        $this->assertSame(
            1050000,
            $option['pricing']['estimated_total']
        );

        $this->assertSame(
            '06:00',
            $option['starting_times'][0][
                'start_time'
            ]
        );
    }

    public function test_blackout_date_blocks_the_option(): void
    {
        $now = Carbon::parse(
            '2026-07-17 08:00:00',
            config('app.timezone')
        );

        $tour = $this->makeTourGraph(
            travelDate: '2026-07-20',
            blackoutEntireDay: true
        );

        $result = app(
            TourAvailabilityService::class
        )->check(
            $tour,
            '2026-07-20',
            [
                'adult' => 2,
                'child' => 0,
                'infant' => 0,
            ],
            'English',
            $now
        );

        $this->assertFalse($result['available']);

        $unavailable =
            $result['unavailable_options'][0];

        $this->assertSame(
            'blackout_entire_day',
            $unavailable[
                'unavailable_starting_times'
            ][0]['reason_code']
        );
    }

    public function test_capacity_includes_infants(): void
    {
        $now = Carbon::parse(
            '2026-07-17 08:00:00',
            config('app.timezone')
        );

        $tour = $this->makeTourGraph(
            travelDate: '2026-07-20',
            capacity: 3
        );

        $result = app(
            TourAvailabilityService::class
        )->check(
            $tour,
            '2026-07-20',
            [
                'adult' => 2,
                'child' => 1,
                'infant' => 1,
            ],
            'English',
            $now
        );

        $this->assertFalse($result['available']);

        $unavailable =
            $result['unavailable_options'][0];

        $this->assertSame(
            'capacity_exceeded',
            $unavailable[
                'unavailable_starting_times'
            ][0]['reason_code']
        );
    }

    private function makeTourGraph(
        string $travelDate,
        ?int $capacity = 12,
        bool $blackoutEntireDay = false
    ): TourPackage {
        $timezone = config('app.timezone');

        $date = Carbon::parse(
            $travelDate,
            $timezone
        );

        $tour = new TourPackage([
            'title' => 'Availability Test Tour',
            'slug' => 'availability-test-tour',
            'status' => 'active',
        ]);

        $tour->id = 999;

        $option = new TourOption([
            'tour_package_id' => 999,
            'title' => 'All-Inclusive Private Tour',
            'slug' => 'all-inclusive-private-tour',
            'duration_minutes' => 600,
            'languages' => [
                'English',
                'Indonesian',
            ],
            'pickup_type' => 'hotel_pickup',
            'min_guests' => 1,
            'max_guests' => 12,
            'is_all_inclusive' => true,
            'is_default' => true,
            'sort_order' => 0,
            'status' => 'active',
        ]);

        $option->id = 999;

        $adult = new TourOptionPrice([
            'tour_option_id' => 999,
            'participant_type' => 'adult',
            'label' => 'Adult',
            'age_min' => 12,
            'age_max' => null,
            'base_price' => 400000,
            'currency' => 'IDR',
            'is_free' => false,
            'is_allowed' => true,
            'sort_order' => 1,
        ]);

        $child = new TourOptionPrice([
            'tour_option_id' => 999,
            'participant_type' => 'child',
            'label' => 'Child',
            'age_min' => 3,
            'age_max' => 11,
            'base_price' => 250000,
            'currency' => 'IDR',
            'is_free' => false,
            'is_allowed' => true,
            'sort_order' => 2,
        ]);

        $infant = new TourOptionPrice([
            'tour_option_id' => 999,
            'participant_type' => 'infant',
            'label' => 'Infant',
            'age_min' => 0,
            'age_max' => 2,
            'base_price' => 0,
            'currency' => 'IDR',
            'is_free' => true,
            'is_allowed' => true,
            'sort_order' => 3,
        ]);

        $schedule = new TourOptionSchedule([
            'tour_option_id' => 999,
            'day_of_week' => $date->dayOfWeek,
            'start_time' => '06:00:00',
            'end_time' => '16:00:00',
            'available_from' => null,
            'available_until' => null,
            'capacity' => $capacity,
            'booking_cutoff_hours' => 12,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $schedule->id = 999;

        $blackouts = [];

        if ($blackoutEntireDay) {
            $blackout = new TourOptionBlackoutDate([
                'tour_option_id' => 999,
                'blackout_date' => $travelDate,
                'blocks_entire_day' => true,
                'start_time' => null,
                'reason' => 'Test closure',
                'is_active' => true,
            ]);

            $blackout->id = 999;
            $blackouts[] = $blackout;
        }

        $option->setRelation(
            'prices',
            new Collection([
                $adult,
                $child,
                $infant,
            ])
        );

        $option->setRelation(
            'discounts',
            new Collection()
        );

        $option->setRelation(
            'activeSchedules',
            new Collection([$schedule])
        );

        $option->setRelation(
            'activeBlackoutDates',
            new Collection($blackouts)
        );

        $option->setRelation(
            'includedItems',
            new Collection()
        );

        $option->setRelation(
            'excludedItems',
            new Collection()
        );

        $tour->setRelation(
            'options',
            new Collection([$option])
        );

        return $tour;
    }
}
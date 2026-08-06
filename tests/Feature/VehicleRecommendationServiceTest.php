<?php

namespace Tests\Feature;

use App\Services\VehicleRecommendationService;
use InvalidArgumentException;
use Tests\TestCase;

class VehicleRecommendationServiceTest extends TestCase
{
    public function test_it_recommends_private_car_for_five_passengers(): void
    {
        $result = app(
            VehicleRecommendationService::class
        )->recommend([
            'adult' => 2,
            'child' => 2,
            'infant' => 1,
        ]);

        $this->assertSame(
            'private_car',
            $result['key']
        );

        $this->assertSame(
            5,
            $result['total_passengers']
        );

        $this->assertTrue(
            $result['confirmation_required']
        );

        $this->assertFalse(
            $result['special_review_required']
        );
    }

    public function test_it_recommends_passenger_van_for_six_passengers(): void
    {
        $result = app(
            VehicleRecommendationService::class
        )->recommend([
            'adult' => 4,
            'child' => 1,
            'infant' => 1,
        ]);

        $this->assertSame(
            'passenger_van',
            $result['key']
        );

        $this->assertSame(
            6,
            $result['total_passengers']
        );
    }

    public function test_it_recommends_larger_transport_for_thirteen_passengers(): void
    {
        $result = app(
            VehicleRecommendationService::class
        )->recommend([
            'adult' => 10,
            'child' => 2,
            'infant' => 1,
        ]);

        $this->assertSame(
            'larger_or_multiple',
            $result['key']
        );

        $this->assertSame(
            13,
            $result['total_passengers']
        );

        $this->assertNull(
            $result['configured_capacity'][
                'maximum'
            ]
        );
    }

    public function test_infants_are_counted_as_passengers(): void
    {
        $result = app(
            VehicleRecommendationService::class
        )->recommend([
            'adult' => 5,
            'child' => 0,
            'infant' => 1,
        ]);

        /*
         * 5 adults + 1 infant = 6,
         * sehingga hasilnya passenger van.
         */
        $this->assertSame(
            'passenger_van',
            $result['key']
        );

        $this->assertSame(
            6,
            $result['total_passengers']
        );
    }

    public function test_special_requirements_need_manual_review(): void
    {
        $result = app(
            VehicleRecommendationService::class
        )->recommend(
            [
                'adult' => 2,
                'child' => 1,
                'infant' => 1,
            ],
            [
                'large_luggage' => true,
                'baby_seat_count' => 1,
                'wheelchair_count' => 0,
                'accessibility_required' => false,
                'oversized_equipment' => false,
            ]
        );

        $this->assertTrue(
            $result['special_review_required']
        );

        $this->assertSame(
            1,
            $result['requirements'][
                'baby_seat_count'
            ]
        );
    }

    public function test_it_rejects_zero_passengers(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'At least one passenger is required'
        );

        app(
            VehicleRecommendationService::class
        )->recommend([
            'adult' => 0,
            'child' => 0,
            'infant' => 0,
        ]);
    }

    public function test_it_rejects_unknown_participant_types(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'Unknown participant type: senior'
        );

        app(
            VehicleRecommendationService::class
        )->recommend([
            'adult' => 1,
            'senior' => 1,
        ]);
    }
}
<?php

namespace Tests\Feature;

use App\Models\TourOption;
use App\Models\TourOptionItem;
use App\Models\TourOptionPrice;
use App\Models\TourOptionSchedule;
use App\Models\TourPackage;
use App\Models\TourStop;
use App\Services\TourPublishingReadinessService;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;


class TourPublishingReadinessServiceTest extends TestCase
{
    public function test_complete_tour_is_ready_to_publish(): void
    {
        $tour = $this->makeCompleteTourGraph();

        $report = app(
            TourPublishingReadinessService::class
        )->inspect($tour);

        $this->assertTrue(
            $report['ready_to_publish']
        );

        $this->assertSame(
            0,
            $report['blocking_count']
        );

        $this->assertTrue(
            $report['option_reports'][0][
                'configuration_ready'
            ]
        );
    }

    public function test_draft_option_does_not_make_product_publishable(): void
    {
        $tour = $this->makeCompleteTourGraph();

        $tour->options[0]->status = 'draft';

        $report = app(
            TourPublishingReadinessService::class
        )->inspect($tour);

        $this->assertFalse(
            $report['ready_to_publish']
        );

        $this->assertTrue(
            $report['option_reports'][0][
                'configuration_ready'
            ]
        );

        $activeOptionCheck = collect(
            $report['checks']
        )->firstWhere(
            'key',
            'active_option'
        );

        $this->assertFalse(
            $activeOptionCheck['passed']
        );
    }

    public function test_missing_gallery_and_schedule_are_blocking(): void
    {
        $tour = $this->makeCompleteTourGraph();

        $tour->gallery_images = [];

        $tour->options[0]->setRelation(
            'schedules',
            new Collection()
        );

        $report = app(
            TourPublishingReadinessService::class
        )->inspect($tour);

        $this->assertFalse(
            $report['ready_to_publish']
        );

        $galleryCheck = collect(
            $report['checks']
        )->firstWhere(
            'key',
            'gallery_images'
        );

        $this->assertFalse(
            $galleryCheck['passed']
        );

        $scheduleCheck = collect(
            $report['option_reports'][0][
                'checks'
            ]
        )->firstWhere(
            'key',
            'active_schedule'
        );

        $this->assertFalse(
            $scheduleCheck['passed']
        );
    }

    private function makeCompleteTourGraph(): TourPackage
    {
        $tour = new TourPackage();

        $tour->forceFill([
            'id' => 900,
            'title' => 'Bali Instagram Highlights',
            'slug' => 'bali-instagram-highlights',
            'category' => 'Photography',
            'area' => 'Bali',
            'tour_format' => 'full_day',
            'duration' => 'Full day',
            'description' => 'Card description.',
            'intro' => 'Detail introduction.',
            'story' => 'Experience story.',
            'main_image' =>
                'storage/tour-packages/main.webp',

            'gallery_images' => [
                'storage/tour-packages/gallery-1.webp',
                'storage/tour-packages/gallery-2.webp',
                'storage/tour-packages/gallery-3.webp',
            ],

            'highlights' => [
                'First highlight',
                'Second highlight',
                'Third highlight',
            ],

            'status' => 'draft',
            'is_popular' => false,
            'is_featured' => false,
        ]);

        $option = new TourOption();

        $option->forceFill([
            'id' => 901,
            'tour_package_id' => 900,
            'title' =>
                'All-Inclusive Private Tour',

            'slug' =>
                'all-inclusive-private-tour',

            'duration_minutes' => 600,

            'languages' => [
                'English',
                'Indonesian',
            ],

            'pickup_type' =>
                'hotel_pickup',

            'pickup_label' =>
                'Pickup from selected Bali areas',

            'min_guests' => 1,
            'max_guests' => 12,
            'is_all_inclusive' => true,
            'is_default' => true,
            'sort_order' => 10,
            'status' => 'active',
        ]);

        $adult = new TourOptionPrice();

        $adult->forceFill([
            'participant_type' => 'adult',
            'label' => 'Adult',
            'base_price' => 750000,
            'currency' => 'IDR',
            'is_free' => false,
            'is_allowed' => true,
            'sort_order' => 10,
        ]);

        $child = new TourOptionPrice();

        $child->forceFill([
            'participant_type' => 'child',
            'label' => 'Child',
            'base_price' => 500000,
            'currency' => 'IDR',
            'is_free' => false,
            'is_allowed' => true,
            'sort_order' => 20,
        ]);

        $infant = new TourOptionPrice();

        $infant->forceFill([
            'participant_type' => 'infant',
            'label' => 'Infant',
            'base_price' => 0,
            'currency' => 'IDR',
            'is_free' => true,
            'is_allowed' => true,
            'sort_order' => 30,
        ]);

        $included = new TourOptionItem();

        $included->forceFill([
            'item_type' => 'included',
            'category' => 'pickup',
            'label' =>
                'Hotel pickup and drop-off',

            'is_active' => true,
            'is_highlighted' => true,
            'sort_order' => 10,
        ]);

        $excluded = new TourOptionItem();

        $excluded->forceFill([
            'item_type' => 'excluded',
            'category' => 'personal_expense',
            'label' => 'Personal expenses',
            'is_active' => true,
            'is_highlighted' => false,
            'sort_order' => 10,
        ]);

        $schedule = new TourOptionSchedule();

        $schedule->forceFill([
            'day_of_week' => 1,
            'start_time' => '06:00:00',
            'end_time' => '16:00:00',
            'available_from' => null,
            'available_until' => null,
            'capacity' => 12,
            'booking_cutoff_hours' => 12,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $firstStop = new TourStop();

        $firstStop->forceFill([
            'tour_package_id' => 900,
            'tour_option_id' => null,
            'day_number' => 1,
            'stop_type' => 'pickup',
            'title' => 'Hotel pickup',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $secondStop = new TourStop();

        $secondStop->forceFill([
            'tour_package_id' => 900,
            'tour_option_id' => null,
            'day_number' => 1,
            'stop_type' => 'destination',
            'title' => 'Scenic destination',
            'is_active' => true,
            'sort_order' => 20,
        ]);

        $option->setRelation(
            'prices',
            new Collection([
                $adult,
                $child,
                $infant,
            ])
        );

        $option->setRelation(
            'items',
            new Collection([
                $included,
                $excluded,
            ])
        );

        $option->setRelation(
            'schedules',
            new Collection([
                $schedule,
            ])
        );

        $tour->setRelation(
            'options',
            new Collection([
                $option,
            ])
        );

        $tour->setRelation(
            'sharedStops',
            new Collection([
                $firstStop,
                $secondStop,
            ])
        );

        return $tour;
    }
}
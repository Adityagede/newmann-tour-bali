<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TourPackage;
use Illuminate\Support\Str;
class TourPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $tours = [
            [
                'title' => 'Ubud Rice Terrace, Waterfall & Art Village Tour',
                'category' => 'culture',
                'badge' => 'Most requested',
                'area' => 'Ubud Area',
                'duration' => '8 hours',
                'trip_type' => 'Private tour',
                'vehicle' => 'Avanza / Hiace available',
                'rating' => 4.9,
                'guests' => '120+ guests',
                'price_text' => 'Request price',
                'description' => 'A calm private route through Ubud’s rice terrace, waterfall, traditional village, local art stops, and green countryside atmosphere.',
                'intro' => 'A calm private route through Ubud’s rice terrace, waterfall, traditional village, local art stops, and green countryside atmosphere.',
                'story' => 'This route is made for travelers who want to enjoy Ubud without rushing. The day can start from your hotel, then continue through nature, culture, and simple local stops that make Bali feel warm and personal.',
                'main_image' => 'images/tour-ubud.jpg',
                'gallery_images' => [
                    'images/tour-ubud.jpg',
                    'images/gallery-rice.jpg',
                    'images/gallery-waterfall.jpg',
                    'images/gallery-temple.jpg',
                ],
                'highlights' => [
                    'Rice Terrace',
                    'Waterfall',
                    'Art Village',
                ],
                'itinerary' => [
                    [
                        'time' => 'Morning',
                        'title' => 'Hotel pickup',
                        'text' => 'Start from your hotel with a private car and flexible timing.',
                    ],
                    [
                        'time' => 'Late morning',
                        'title' => 'Rice terrace and local scenery',
                        'text' => 'Enjoy green views, photo stops, and a slower Ubud atmosphere.',
                    ],
                    [
                        'time' => 'Midday',
                        'title' => 'Waterfall or village stop',
                        'text' => 'Visit a nature stop or local village depending on your route and energy.',
                    ],
                    [
                        'time' => 'Afternoon',
                        'title' => 'Art village and return',
                        'text' => 'End the day with a relaxed cultural stop before heading back.',
                    ],
                ],
                'is_popular' => true,
                'is_featured' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Uluwatu Temple, Melasti Beach & Sunset Trip',
                'category' => 'beach',
                'badge' => 'Sunset favorite',
                'area' => 'South Bali',
                'duration' => '10 hours',
                'trip_type' => 'Private tour',
                'vehicle' => 'Best with Avanza',
                'rating' => 4.8,
                'guests' => '90+ guests',
                'price_text' => 'Request price',
                'description' => 'A relaxed south Bali route with beach stops, cliff temple, sunset view, and flexible time for photos and dinner.',
                'intro' => 'A relaxed south Bali route with beach stops, cliff temple, sunset view, and flexible time for photos and dinner.',
                'story' => 'This route is perfect for travelers who want beaches, ocean views, and a beautiful sunset without moving too fast from one place to another.',
                'main_image' => 'images/tour-beach.jpg',
                'gallery_images' => [
                    'images/tour-beach.jpg',
                    'images/gallery-beach.jpg',
                    'images/gallery-local.jpg',
                    'images/gallery-car.jpg',
                ],
                'highlights' => [
                    'Melasti Beach',
                    'Uluwatu',
                    'Sunset',
                ],
                'itinerary' => [
                    [
                        'time' => 'Morning',
                        'title' => 'Hotel pickup',
                        'text' => 'Start with a comfortable pickup from your hotel area.',
                    ],
                    [
                        'time' => 'Midday',
                        'title' => 'Beach route',
                        'text' => 'Visit selected beaches with enough time to enjoy the view.',
                    ],
                    [
                        'time' => 'Afternoon',
                        'title' => 'Uluwatu Temple',
                        'text' => 'Continue to the cliff temple area and enjoy the ocean atmosphere.',
                    ],
                    [
                        'time' => 'Evening',
                        'title' => 'Sunset and return',
                        'text' => 'End the trip with sunset before heading back to your hotel.',
                    ],
                ],
                'is_popular' => true,
                'is_featured' => false,
                'status' => 'active',
            ],
            [
                'title' => 'Kintamani Volcano, Coffee Plantation & Temple Tour',
                'category' => 'nature',
                'badge' => 'Nature route',
                'area' => 'Kintamani',
                'duration' => '9 hours',
                'trip_type' => 'Private tour',
                'vehicle' => 'Avanza / Hiace available',
                'rating' => 4.9,
                'guests' => '80+ guests',
                'price_text' => 'Request price',
                'description' => 'A scenic Bali day trip with volcano view, coffee plantation, temple stop, and countryside roads.',
                'intro' => 'A scenic Bali day trip with volcano view, coffee plantation, temple stop, and countryside roads.',
                'story' => 'A good choice for travelers who want cooler air, mountain scenery, and a mix of nature and local stops in one private route.',
                'main_image' => 'images/tour-kintamani.jpg',
                'gallery_images' => [
                    'images/tour-kintamani.jpg',
                    'images/gallery-local.jpg',
                    'images/gallery-temple.jpg',
                    'images/gallery-rice.jpg',
                ],
                'highlights' => [
                    'Volcano View',
                    'Coffee Stop',
                    'Temple',
                ],
                'itinerary' => [
                    [
                        'time' => 'Morning',
                        'title' => 'Hotel pickup',
                        'text' => 'Drive from your hotel toward the mountain area.',
                    ],
                    [
                        'time' => 'Late morning',
                        'title' => 'Coffee plantation',
                        'text' => 'Stop for a relaxed local coffee experience.',
                    ],
                    [
                        'time' => 'Midday',
                        'title' => 'Kintamani volcano view',
                        'text' => 'Enjoy the mountain view and cooler atmosphere.',
                    ],
                    [
                        'time' => 'Afternoon',
                        'title' => 'Temple or village route',
                        'text' => 'Close the trip with a cultural stop before returning.',
                    ],
                ],
                'is_popular' => true,
                'is_featured' => false,
                'status' => 'active',
            ],
            [
                'title' => 'Waterfall, Village Road & Countryside Bali Tour',
                'category' => 'nature',
                'badge' => 'Calm trip',
                'area' => 'Central Bali',
                'duration' => '8 hours',
                'trip_type' => 'Private tour',
                'vehicle' => 'Best with Avanza',
                'rating' => 4.8,
                'guests' => '70+ guests',
                'price_text' => 'Request price',
                'description' => 'A softer nature route for travelers who want fresh air, local village roads, and peaceful Bali scenery.',
                'intro' => 'A softer nature route for travelers who want fresh air, local village roads, and peaceful Bali scenery.',
                'story' => 'This route is for guests who enjoy quiet places, nature, small roads, and a slower Bali day away from the busiest tourist spots.',
                'main_image' => 'images/tour-waterfall.jpg',
                'gallery_images' => [
                    'images/tour-waterfall.jpg',
                    'images/gallery-waterfall.jpg',
                    'images/gallery-local.jpg',
                    'images/gallery-rice.jpg',
                ],
                'highlights' => [
                    'Waterfall',
                    'Village Road',
                    'Nature',
                ],
                'itinerary' => [
                    [
                        'time' => 'Morning',
                        'title' => 'Hotel pickup',
                        'text' => 'Start with a flexible pickup from your hotel.',
                    ],
                    [
                        'time' => 'Late morning',
                        'title' => 'Village road',
                        'text' => 'Drive through quieter roads and local scenery.',
                    ],
                    [
                        'time' => 'Midday',
                        'title' => 'Waterfall stop',
                        'text' => 'Enjoy a fresh nature stop with time for photos.',
                    ],
                    [
                        'time' => 'Afternoon',
                        'title' => 'Return with optional stop',
                        'text' => 'Add a small local stop if time and energy fit.',
                    ],
                ],
                'is_popular' => true,
                'is_featured' => false,
                'status' => 'active',
            ],
        ];

        foreach ($tours as $tour) {
            TourPackage::updateOrCreate(
                ['slug' => Str::slug($tour['title'])],
                array_merge($tour, [
                    'slug' => Str::slug($tour['title']),
                ])
            );
        }
    
    }
}

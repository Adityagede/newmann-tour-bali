<?php

namespace App\Http\Controllers;

use App\Models\GalleryMoment;
use App\Models\TourPackage;

class HomeController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Popular Tours
        |--------------------------------------------------------------------------
        |
        | Popular Tours harus tetap berupa model TourPackage.
        |
        | Komponen resources/views/components/tour-card.blade.php
        | akan menangani:
        |
        | - TourViewData::make($tour)
        | - TourCardPromotion::make($tour)
        |
        | Jangan mengubah model menjadi array di controller karena
        | TourCardPromotion memerlukan relationship model TourPackage.
        |
        */

        $popularTours = TourPackage::query()
            ->where('status', 'active')
            ->where('is_popular', true)
            ->with([
                'defaultActiveOption.prices',
            ])
            ->latest()
            ->take(6)
            ->get();

        // Fallback kalau belum ada tour yang ditandai popular.
        if ($popularTours->isEmpty()) {
            $popularTours = TourPackage::query()
                ->where('status', 'active')
                ->with([
                    'defaultActiveOption.prices',
                ])
                ->latest()
                ->take(6)
                ->get();
        }

        /*
         * Jangan gunakan map TourViewData di sini.
         *
         * Biarkan collection tetap berisi:
         * App\Models\TourPackage
         */
        $popularTours = $popularTours->values();

        /*
        |--------------------------------------------------------------------------
        | Homepage Guest Moments
        |--------------------------------------------------------------------------
        */

        $homepageMoments = GalleryMoment::query()
            ->where('status', 'active')
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        // Fallback kalau belum ada gallery yang ditandai featured.
        if ($homepageMoments->isEmpty()) {
            $homepageMoments = GalleryMoment::query()
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->take(6)
                ->get();
        }

        $guestMoments = $homepageMoments
            ->map(
                fn (GalleryMoment $moment) =>
                    $this->formatGalleryMoment($moment)
            )
            ->values()
            ->all();

        return view('pages.home', [
            'popularTours' => $popularTours,
            'guestMoments' => $guestMoments,
        ]);
    }

    /**
     * Mengubah GalleryMoment menjadi data yang dipakai homepage.
     */
    private function formatGalleryMoment(
        GalleryMoment $moment
    ): array {
        $category = strtolower(
            $moment->category ?: 'other'
        );

        $title = $moment->title
            ?: 'Guest moment in Bali';

        $image = $moment->image_path
            ?: 'images/gallery-placeholder.jpg';

        return [
            'id' => $moment->id,

            'title' => $title,
            'caption' => $moment->caption ?: '',
            'description' => $moment->caption ?: '',

            'category' => $category,

            'location' => $moment->location ?: 'Bali',

            'tag' => $moment->location
                ?: ucwords(
                    str_replace(
                        ['-', '_'],
                        ' ',
                        $category
                    )
                ),

            'image' => $image,
            'image_path' => $image,

            'alt' => $moment->alt_text ?: $title,
            'alt_text' => $moment->alt_text ?: $title,

            'size' => $moment->display_size === 'large'
                ? 'large'
                : 'regular',

            'display_size' => $moment->display_size,

            'is_featured' =>
                (bool) $moment->is_featured,
        ];
    }
}
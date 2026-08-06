<?php

namespace App\Http\Controllers;

use App\Models\GalleryMoment;

class GalleryController extends Controller
{
    private function formatMoment(GalleryMoment $moment): array
    {
        $category = strtolower($moment->category ?: 'other');
        $title = $moment->title ?: 'Guest moment in Bali';
        $image = $moment->image_path ?: 'images/gallery-placeholder.jpg';

        return [
            'id' => $moment->id,

            'title' => $title,
            'caption' => $moment->caption ?: '',
            'description' => $moment->caption ?: '',

            'category' => $category,
            'category_label' => ucwords(
                str_replace(['-', '_'], ' ', $category)
            ),

            'location' => $moment->location ?: 'Bali',
            'tag' => $moment->location
                ?: ucwords(str_replace('-', ' ', $category)),

            'image' => $image,
            'image_path' => $image,

            'alt' => $moment->alt_text ?: $title,
            'alt_text' => $moment->alt_text ?: $title,

            'size' => $moment->display_size === 'large'
                ? 'large'
                : 'regular',
            'display_size' => $moment->display_size,
            'sort_order' => $moment->sort_order,
            'is_featured' => (bool) $moment->is_featured,
        ];
    }

    public function index()
    {
        $guestMoments = GalleryMoment::query()
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(fn (GalleryMoment $moment) => $this->formatMoment($moment))
            ->values()
            ->all();

        $categories = GalleryMoment::query()
            ->where('status', 'active')
            ->whereNotNull('category')
            ->pluck('category')
            ->map(fn ($category) => strtolower($category))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return view('pages.gallery', [
            'guestMoments' => $guestMoments,
            'categories' => $categories,
        ]);
    }
}   
<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\TourPackage;

final class TourPackageEditor
{
    public static function packageCheckUrl(TourPackage $package, string $check): string
    {
        $route = match ($check) {
            'gallery_images' => 'gallery.edit',
            'shared_roadmap' => 'roadmap.index',
            'tour_options' => 'options.create',
            'default_option' => 'options.index',
            'active_option', 'active_options_ready' => 'readiness.show',
            default => 'edit',
        };

        $anchor = match ($check) {
            'product_title', 'product_slug' => '#title',
            'product_category' => '#category',
            'product_area' => '#area',
            'product_format' => '#tour_format',
            'product_duration' => '#duration',
            'product_description' => '#description',
            'product_intro' => '#intro',
            'product_story' => '#story',
            'tour_highlights' => '#highlights_text',
            'main_image' => '#main_image',
            'publishing_flags' => '#homepage-placement',
            'active_option', 'active_options_ready' => '#booking-options',
            default => '',
        };

        return route('admin.tour-packages.'.$route, $package).$anchor;
    }

    public static function optionCheckUrl(TourPackage $package, int $optionId, string $check): string
    {
        $section = match ($check) {
            'participant_price_rows', 'allowed_participants', 'adult_participant',
            'participant_prices', 'price_currency' => 'prices.edit',
            'active_schedule', 'schedule_capacity' => 'schedules.index',
            'included_items', 'excluded_items' => 'items.index',
            default => 'edit',
        };

        return route('admin.tour-packages.options.'.$section, [
            'tourPackage' => $package,
            'tourOption' => $optionId,
        ]);
    }
}

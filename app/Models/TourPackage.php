<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TourPackage extends Model
{
     protected $fillable = [
        'title',
        'slug',
        'category',
        'badge',
        'area',
        'duration',
        'trip_type',
        'vehicle',
        'rating',
        'guests',
        'price_text',
        'description',
        'intro',
        'story',
        'main_image',
        'gallery_images',
        'highlights',
        'itinerary',
        'is_popular',
        'is_featured',
        'status',
        'tour_format',
        'pricing_type',
        'adult_price',
        'child_price',
        'vehicle_price',
        'min_guests',
        'max_guests',
        'default_vehicle',
        'transport_included',
        'price_note',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'highlights' => 'array',
        'itinerary' => 'array',
        'is_popular' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'decimal:1',
         'adult_price' => 'integer',
        'child_price' => 'integer',
        'vehicle_price' => 'integer',
        'min_guests' => 'integer',
        'max_guests' => 'integer',
        'transport_included' => 'boolean',
    ];


    public function options(): HasMany
{
    return $this->hasMany(TourOption::class)
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function defaultActiveOption(): HasOne
{
    return $this->hasOne(TourOption::class)
        ->where('status', 'active')
        ->where('is_default', true)
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function activeOptions(): HasMany
{
    return $this->hasMany(TourOption::class)
        ->where('status', 'active')
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function stops(): HasMany
{
    return $this->hasMany(TourStop::class)
        ->orderBy('day_number')
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function activeStops(): HasMany
{
    return $this->hasMany(TourStop::class)
        ->where('is_active', true)
        ->orderBy('day_number')
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function sharedStops(): HasMany
{
    return $this->hasMany(TourStop::class)
        ->whereNull('tour_option_id')
        ->where('is_active', true)
        ->orderBy('day_number')
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function bookingRequests(): HasMany
{
    return $this->hasMany(TourBookingRequest::class);
}

public function completedBookingRequests(): HasMany
{
    return $this->bookingRequests()
        ->where('status', TourBookingRequest::STATUS_COMPLETED);
}

public function ratings(): HasMany
{
    return $this->hasMany(TourRating::class);
}

public function verifiedRatings(): HasMany
{
    return $this->ratings()
        ->whereHas(
            'bookingRequest',
            fn (Builder $query): Builder => $query->where(
                'status',
                TourBookingRequest::STATUS_COMPLETED
            )
        );
}

public function scopeWithVerifiedStatistics(Builder $query): Builder
{
    return $query
        ->withAvg(
            'verifiedRatings as verified_rating_average',
            'rating'
        )
        ->withCount(
            'verifiedRatings as verified_rating_count'
        )
        ->addSelect([
            'verified_guest_count' => TourBookingRequest::query()
                ->selectRaw(
                    'COALESCE(SUM(adult_count + child_count + infant_count), 0)'
                )
                ->whereColumn(
                    'tour_package_id',
                    'tour_packages.id'
                )
                ->where(
                    'status',
                    TourBookingRequest::STATUS_COMPLETED
                ),
        ]);
}
}

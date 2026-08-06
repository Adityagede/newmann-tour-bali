<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class TourOption extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tour_package_id',
        'title',
        'slug',
        'short_description',
        'duration_minutes',
        'languages',
        'pickup_type',
        'pickup_label',
        'confirmation_note',
        'min_guests',
        'max_guests',
        'is_all_inclusive',
        'is_default',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'languages' => 'array',
        'min_guests' => 'integer',
        'max_guests' => 'integer',
        'is_all_inclusive' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function prices(): HasMany
{
    return $this->hasMany(TourOptionPrice::class)
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function allowedPrices(): HasMany
{
    return $this->hasMany(TourOptionPrice::class)
        ->where('is_allowed', true)
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function items(): HasMany
{
    return $this->hasMany(TourOptionItem::class)
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function activeItems(): HasMany
{
    return $this->hasMany(TourOptionItem::class)
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function includedItems(): HasMany
{
    return $this->hasMany(TourOptionItem::class)
        ->where('item_type', TourOptionItem::TYPE_INCLUDED)
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function excludedItems(): HasMany
{
    return $this->hasMany(TourOptionItem::class)
        ->where('item_type', TourOptionItem::TYPE_EXCLUDED)
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function schedules(): HasMany
{
    return $this->hasMany(TourOptionSchedule::class)
        ->orderBy('day_of_week')
        ->orderBy('start_time')
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function activeSchedules(): HasMany
{
    return $this->hasMany(TourOptionSchedule::class)
        ->where('is_active', true)
        ->orderBy('day_of_week')
        ->orderBy('start_time')
        ->orderBy('sort_order')
        ->orderBy('id');
}


public function discounts(): HasMany
{
    return $this->hasMany(TourOptionDiscount::class)
        ->orderByDesc('priority')
        ->orderBy('id');
}

public function activeDiscounts(): HasMany
{
    return $this->hasMany(TourOptionDiscount::class)
        ->where('is_active', true)
        ->orderByDesc('priority')
        ->orderBy('id');
}



public function blackoutDates(): HasMany
{
    return $this->hasMany(
        TourOptionBlackoutDate::class
    )
        ->orderBy('blackout_date')
        ->orderBy('start_time')
        ->orderBy('id');
}

public function activeBlackoutDates(): HasMany
{
    return $this->hasMany(
        TourOptionBlackoutDate::class
    )
        ->where('is_active', true)
        ->orderBy('blackout_date')
        ->orderBy('start_time')
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
}
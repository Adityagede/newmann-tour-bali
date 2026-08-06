<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourStop extends Model
{
    use SoftDeletes;

    public const TYPE_PICKUP = 'pickup';
    public const TYPE_ATTRACTION = 'attraction';
    public const TYPE_ACTIVITY = 'activity';
    public const TYPE_MEAL = 'meal';
    public const TYPE_BREAK = 'break';
    public const TYPE_DROPOFF = 'dropoff';
    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'tour_package_id',
        'tour_option_id',
        'day_number',
        'stop_type',
        'title',
        'description',
        'location_name',
        'address',
        'scheduled_time',
        'time_label',
        'duration_minutes',
        'latitude',
        'longitude',
        'show_on_map',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'day_number' => 'integer',
        'duration_minutes' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'show_on_map' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function tourOption(): BelongsTo
    {
        return $this->belongsTo(TourOption::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay(
        Builder $query,
        int $dayNumber
    ): Builder {
        return $query->where('day_number', $dayNumber);
    }

    public function scopeShared(Builder $query): Builder
    {
        return $query->whereNull('tour_option_id');
    }

    public function scopeVisibleOnMap(Builder $query): Builder
    {
        return $query
            ->where('show_on_map', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null
            && $this->longitude !== null;
    }

    public function getFormattedTimeAttribute(): ?string
    {
        if ($this->time_label) {
            return $this->time_label;
        }

        if ($this->scheduled_time === null) {
            return null;
        }

        return substr(
            (string) $this->scheduled_time,
            0,
            5
        );
    }

    public function getFormattedDurationAttribute(): ?string
    {
        if ($this->duration_minutes === null) {
            return null;
        }

        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} hr {$minutes} min";
        }

        if ($hours > 0) {
            return "{$hours} hr";
        }

        return "{$minutes} min";
    }
}

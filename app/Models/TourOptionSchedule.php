<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourOptionSchedule extends Model
{
    use SoftDeletes;

    public const DAY_SUNDAY = 0;
    public const DAY_MONDAY = 1;
    public const DAY_TUESDAY = 2;
    public const DAY_WEDNESDAY = 3;
    public const DAY_THURSDAY = 4;
    public const DAY_FRIDAY = 5;
    public const DAY_SATURDAY = 6;

    protected $fillable = [
        'tour_option_id',
        'day_of_week',
        'start_time',
        'end_time',
        'available_from',
        'available_until',
        'capacity',
        'booking_cutoff_hours',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'available_from' => 'date',
        'available_until' => 'date',
        'capacity' => 'integer',
        'booking_cutoff_hours' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

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
        int $dayOfWeek
    ): Builder {
        return $query->where('day_of_week', $dayOfWeek);
    }

    public function getDayNameAttribute(): string
    {
        return match ($this->day_of_week) {
            self::DAY_SUNDAY => 'Sunday',
            self::DAY_MONDAY => 'Monday',
            self::DAY_TUESDAY => 'Tuesday',
            self::DAY_WEDNESDAY => 'Wednesday',
            self::DAY_THURSDAY => 'Thursday',
            self::DAY_FRIDAY => 'Friday',
            self::DAY_SATURDAY => 'Saturday',
            default => 'Unknown',
        };
    }

    public function getFormattedStartTimeAttribute(): string
    {
        return substr((string) $this->start_time, 0, 5);
    }
}
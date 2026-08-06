<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourOptionBlackoutDate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tour_option_id',
        'blackout_date',
        'blocks_entire_day',
        'start_time',
        'reason',
        'internal_note',
        'is_active',
    ];

    protected $casts = [
        'blackout_date' => 'date',
        'blocks_entire_day' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(
            function (TourOptionBlackoutDate $blackout): void {
                /*
                 * Blackout satu hari penuh tidak perlu
                 * menyimpan start_time.
                 */
                if ($blackout->blocks_entire_day) {
                    $blackout->start_time = null;
                }
            }
        );
    }

    public function tourOption(): BelongsTo
    {
        return $this->belongsTo(TourOption::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForDate(
        Builder $query,
        string $date
    ): Builder {
        return $query->whereDate(
            'blackout_date',
            $date
        );
    }

    public function scopeEntireDay(
        Builder $query
    ): Builder {
        return $query->where(
            'blocks_entire_day',
            true
        );
    }

    public function scopeForStartTime(
        Builder $query,
        string $startTime
    ): Builder {
        return $query
            ->where('blocks_entire_day', false)
            ->whereTime('start_time', $startTime);
    }

    public function blocksStartTime(
        ?string $startTime
    ): bool {
        if ($this->blocks_entire_day) {
            return true;
        }

        if (
            $this->start_time === null
            || $startTime === null
        ) {
            return false;
        }

        return substr(
            (string) $this->start_time,
            0,
            5
        ) === substr(
            $startTime,
            0,
            5
        );
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->blackout_date
            ? $this->blackout_date->format('d M Y')
            : '';
    }

    public function getFormattedStartTimeAttribute(): ?string
    {
        if (
            $this->blocks_entire_day
            || $this->start_time === null
        ) {
            return null;
        }

        return substr(
            (string) $this->start_time,
            0,
            5
        );
    }
}
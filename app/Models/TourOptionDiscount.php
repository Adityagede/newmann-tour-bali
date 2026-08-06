<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourOptionDiscount extends Model
{
    use SoftDeletes;

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'tour_option_id',
        'label',
        'discount_type',
        'discount_value',
        'participant_types',
        'starts_at',
        'ends_at',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'integer',
        'participant_types' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    public function tourOption(): BelongsTo
    {
        return $this->belongsTo(TourOption::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrentlyValid(
        Builder $query,
        ?Carbon $moment = null
    ): Builder {
        $moment ??= now();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) use ($moment) {
                $query
                    ->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $moment);
            })
            ->where(function (Builder $query) use ($moment) {
                $query
                    ->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $moment);
            });
    }

    public function appliesToParticipant(
        string $participantType
    ): bool {
        $participantTypes = $this->participant_types ?? [];

        /*
         * Array kosong berarti diskon diterapkan
         * ke semua kategori peserta berbayar.
         */
        if ($participantTypes === []) {
            return true;
        }

        return in_array(
            $participantType,
            $participantTypes,
            true
        );
    }

    public function getFormattedValueAttribute(): string
    {
        if ($this->discount_type === self::TYPE_PERCENTAGE) {
            return $this->discount_value . '%';
        }

        return 'IDR ' . number_format(
            $this->discount_value,
            0,
            ',',
            '.'
        );
    }
}
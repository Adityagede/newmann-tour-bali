<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourOptionPrice extends Model
{
    public const TYPE_ADULT = 'adult';
    public const TYPE_CHILD = 'child';
    public const TYPE_INFANT = 'infant';

    protected $fillable = [
        'tour_option_id',
        'participant_type',
        'label',
        'age_min',
        'age_max',
        'base_price',
        'currency',
        'is_free',
        'is_allowed',
        'sort_order',
    ];

    protected $casts = [
        'age_min' => 'integer',
        'age_max' => 'integer',
        'base_price' => 'integer',
        'is_free' => 'boolean',
        'is_allowed' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tourOption(): BelongsTo
    {
        return $this->belongsTo(TourOption::class);
    }

    public function scopeAllowed(Builder $query): Builder
    {
        return $query->where('is_allowed', true);
    }

    public function getAgeLabelAttribute(): string
    {
        if (
            $this->age_min !== null
            && $this->age_max !== null
        ) {
            return "Age {$this->age_min}–{$this->age_max}";
        }

        if ($this->age_min !== null) {
            return "Age {$this->age_min}+";
        }

        if ($this->age_max !== null) {
            return "Up to age {$this->age_max}";
        }

        return 'All ages';
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->is_free) {
            return 'Free';
        }

        return $this->currency . ' ' . number_format(
            $this->base_price,
            0,
            ',',
            '.'
        );
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourOptionItem extends Model
{
    public const TYPE_INCLUDED = 'included';
    public const TYPE_EXCLUDED = 'excluded';

    public const CATEGORY_TRANSPORT = 'transport';
    public const CATEGORY_PICKUP = 'pickup';
    public const CATEGORY_TICKET = 'ticket';
    public const CATEGORY_GUIDE = 'guide';
    public const CATEGORY_MEAL = 'meal';
    public const CATEGORY_DRINK = 'drink';
    public const CATEGORY_EQUIPMENT = 'equipment';
    public const CATEGORY_INSURANCE = 'insurance';
    public const CATEGORY_PERSONAL_EXPENSE = 'personal_expense';
    public const CATEGORY_OTHER = 'other';

    protected $fillable = [
        'tour_option_id',
        'item_type',
        'category',
        'label',
        'details',
        'is_highlighted',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_highlighted' => 'boolean',
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

    public function scopeIncluded(Builder $query): Builder
    {
        return $query->where(
            'item_type',
            self::TYPE_INCLUDED
        );
    }

    public function scopeExcluded(Builder $query): Builder
    {
        return $query->where(
            'item_type',
            self::TYPE_EXCLUDED
        );
    }

    public function scopeHighlighted(Builder $query): Builder
    {
        return $query->where('is_highlighted', true);
    }
}
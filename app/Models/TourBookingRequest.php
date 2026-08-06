<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TourBookingRequest extends Model
{
    protected $fillable = [
        'booking_reference',
        'tour_package_id',
        'tour_option_id',
        'status',
        'source',

        'guest_name',
        'guest_whatsapp',
        'guest_email',
        'pickup_address',
        'special_requests',

        'travel_date',
        'starting_time',
        'language',

        'adult_count',
        'child_count',
        'infant_count',
        'total_participants',

        'currency',
        'base_total',
        'discount_amount',
        'estimated_total',

        'tour_snapshot',
        'option_snapshot',
        'selection_snapshot',
        'transport_snapshot',
        'pricing_snapshot',
        'items_snapshot',

        'requested_at',
    ];
    

    protected function casts(): array
    {
        return [
            'travel_date' => 'date:Y-m-d',

            'adult_count' => 'integer',
            'child_count' => 'integer',
            'infant_count' => 'integer',

            'total_participants' =>
                'integer',

            'base_total' => 'integer',

            'discount_amount' =>
                'integer',

            'estimated_total' =>
                'integer',

            'tour_snapshot' => 'array',
            'option_snapshot' => 'array',
            'selection_snapshot' => 'array',
            'transport_snapshot' => 'array',
            'pricing_snapshot' => 'array',
            'items_snapshot' => 'array',

            'requested_at' => 'datetime',
        ];
    }

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(
            TourPackage::class
        );
    }

    public function tourOption(): BelongsTo
    {
        return $this->belongsTo(
            TourOption::class
        );
    }
}
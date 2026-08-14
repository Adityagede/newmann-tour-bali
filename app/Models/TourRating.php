<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TourRating extends Model
{
    protected $fillable = [
        'tour_booking_request_id',
        'tour_package_id',
        'rating',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(
            TourBookingRequest::class,
            'tour_booking_request_id'
        );
    }

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Custom Trip tetap menggunakan tabel `bookings`.
 *
 * Tour Package V2 menggunakan tabel `tour_booking_requests`,
 * sehingga kedua flow tetap terpisah dan tidak mencampur data.
 */
final class CustomTripRequest extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'booking_code',
        'tour_package_id',
        'name',
        'whatsapp',
        'email',
        'selected_tour',
        'trip_date',
        'adult_count',
        'child_count',
        'people_count',
        'pricing_type',
        'adult_unit_price',
        'child_unit_price',
        'vehicle_unit_price',
        'estimated_total',
        'currency',
        'selected_vehicle',
        'custom_vehicle',
        'pickup_area',
        'message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'trip_date' => 'date:Y-m-d',
            'people_count' => 'integer',
        ];
    }

    /**
     * Hanya mengambil data Custom Trip,
     * bukan booking Tour Package lama.
     */
    public function scopeCustomOnly(Builder $query): Builder
    {
        return $query->whereNull('tour_package_id');
    }

    /**
     * Label kendaraan yang dipilih pelanggan.
     */
    public function vehiclePreferenceLabel(): string
    {
        if (
            $this->selected_vehicle === 'Another Car'
            && is_string($this->custom_vehicle)
            && trim($this->custom_vehicle) !== ''
        ) {
            return trim($this->custom_vehicle);
        }

        if (
            is_string($this->selected_vehicle)
            && trim($this->selected_vehicle) !== ''
        ) {
            return trim($this->selected_vehicle);
        }

        return 'Not sure, please recommend';
    }

    /**
     * Rekomendasi awal berdasarkan jumlah tamu.
     */
    public function suggestedVehicleLabel(): string
    {
        $guests = (int) ($this->people_count ?? 0);

        if ($guests < 1) {
            return 'Newman will recommend after reviewing the request';
        }

        if ($guests <= 5) {
            return 'Toyota Avanza or an equivalent private car';
        }

        if ($guests <= 12) {
            return 'Toyota Hiace or an equivalent passenger van';
        }

        return 'Larger vehicle or multiple vehicles';
    }

    /**
     * Menentukan apakah pilihan kendaraan perlu diperiksa admin.
     */
    public function preferenceNeedsManualReview(): bool
    {
        $preference = trim((string) $this->selected_vehicle);
        $guests = (int) ($this->people_count ?? 0);

        if (
            $guests < 1
            || $preference === ''
            || $preference === 'Not sure, please recommend'
            || $preference === 'Another Car'
        ) {
            return true;
        }

        if ($preference === 'Toyota Avanza') {
            return $guests > 5;
        }

        if ($preference === 'Toyota Hiace') {
            return $guests > 12;
        }

        return true;
    }
}
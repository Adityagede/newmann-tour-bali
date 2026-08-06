<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table
                ->foreignId('tour_package_id')
                ->nullable()
                ->after('booking_code')
                ->constrained('tour_packages')
                ->nullOnDelete();

            $table
                ->unsignedInteger('adult_count')
                ->nullable()
                ->after('trip_date');

            $table
                ->unsignedInteger('child_count')
                ->nullable()
                ->after('adult_count');

            $table
                ->string('pricing_type')
                ->nullable()
                ->after('people_count');

            $table
                ->unsignedBigInteger('adult_unit_price')
                ->nullable()
                ->after('pricing_type');

            $table
                ->unsignedBigInteger('child_unit_price')
                ->nullable()
                ->after('adult_unit_price');

            $table
                ->unsignedBigInteger('vehicle_unit_price')
                ->nullable()
                ->after('child_unit_price');

            $table
                ->unsignedBigInteger('estimated_total')
                ->nullable()
                ->after('vehicle_unit_price');

            $table
                ->string('currency', 3)
                ->default('IDR')
                ->after('estimated_total');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['tour_package_id']);

            $table->dropColumn([
                'tour_package_id',
                'adult_count',
                'child_count',
                'pricing_type',
                'adult_unit_price',
                'child_unit_price',
                'vehicle_unit_price',
                'estimated_total',
                'currency',
            ]);
        });
    }
};
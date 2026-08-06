<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->string('tour_format', 40)
                ->default('full_day');

            $table->string('pricing_type', 40)
                ->default('request_quote');

            $table->unsignedBigInteger('adult_price')
                ->nullable();

            $table->unsignedBigInteger('child_price')
                ->nullable();

            $table->unsignedBigInteger('vehicle_price')
                ->nullable();

            $table->unsignedInteger('min_guests')
                ->default(1);

            $table->unsignedInteger('max_guests')
                ->nullable();

            $table->string('default_vehicle', 120)
                ->nullable();

            $table->boolean('transport_included')
                ->default(true);

            $table->string('price_note', 180)
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn([
                'tour_format',
                'pricing_type',
                'adult_price',
                'child_price',
                'vehicle_price',
                'min_guests',
                'max_guests',
                'default_vehicle',
                'transport_included',
                'price_note',
            ]);
        });
    }
};
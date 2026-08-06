<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_stops', function (Blueprint $table) {
            $table->id();

            /*
             * Roadmap utama dimiliki oleh Tour Package.
             */
            $table->foreignId('tour_package_id')
                ->constrained('tour_packages')
                ->cascadeOnDelete();

            /*
             * Opsional:
             * digunakan jika sebuah option memiliki
             * roadmap berbeda dari roadmap utama tour.
             *
             * Contoh:
             * Sunrise option mempunyai urutan berbeda.
             */
            $table->foreignId('tour_option_id')
                ->nullable()
                ->constrained('tour_options')
                ->cascadeOnDelete();

            /*
             * Untuk mendukung kemungkinan multi-day tour.
             * Full-day biasa menggunakan day_number = 1.
             */
            $table->unsignedTinyInteger('day_number')
                ->default(1);

            /*
             * Nilai awal:
             * pickup
             * attraction
             * activity
             * meal
             * break
             * dropoff
             * other
             */
            $table->string('stop_type', 30)
                ->default('attraction');

            $table->string('title');

            /*
             * Penjelasan yang muncul pada timeline.
             */
            $table->text('description')->nullable();

            /*
             * Nama lokasi dapat berbeda dari title.
             *
             * Contoh:
             * title: Temple visit
             * location_name: Lempuyang Temple
             */
            $table->string('location_name')->nullable();

            $table->text('address')->nullable();

            /*
             * scheduled_time digunakan untuk waktu pasti.
             * Contoh: 06:00:00
             *
             * time_label digunakan untuk waktu fleksibel.
             * Contoh: Morning, Around 08:00.
             */
            $table->time('scheduled_time')->nullable();
            $table->string('time_label', 60)->nullable();

            /*
             * Contoh:
             * 90 berarti estimasi durasi 1 jam 30 menit.
             */
            $table->unsignedSmallInteger('duration_minutes')
                ->nullable();

            /*
             * Koordinat untuk marker pada map.
             */
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            /*
             * Stop tetap dapat tampil pada timeline,
             * tetapi tidak harus tampil pada map.
             */
            $table->boolean('show_on_map')
                ->default(true);

            $table->boolean('is_active')
                ->default(true);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                [
                    'tour_package_id',
                    'day_number',
                    'is_active',
                    'sort_order',
                ],
                'tour_stops_package_lookup_index'
            );

            $table->index(
                [
                    'tour_option_id',
                    'day_number',
                    'is_active',
                    'sort_order',
                ],
                'tour_stops_option_lookup_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_stops');
    }
};
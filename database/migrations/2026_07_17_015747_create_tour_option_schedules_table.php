<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_option_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_option_id')
                ->constrained('tour_options')
                ->cascadeOnDelete();

            /*
             * Day of week:
             * 0 = Sunday
             * 1 = Monday
             * 2 = Tuesday
             * 3 = Wednesday
             * 4 = Thursday
             * 5 = Friday
             * 6 = Saturday
             */
            $table->unsignedTinyInteger('day_of_week');

            /*
             * Waktu mulai tour.
             * Contoh: 06:00:00
             */
            $table->time('start_time');

            /*
             * Waktu selesai opsional.
             * Bisa kosong untuk tour dengan durasi fleksibel.
             */
            $table->time('end_time')->nullable();

            /*
             * Periode schedule ini berlaku.
             * Kosong berarti tidak dibatasi tanggal.
             */
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();

            /*
             * Jumlah peserta maksimum pada schedule ini.
             * Jika kosong, gunakan max_guests dari tour_options.
             */
            $table->unsignedSmallInteger('capacity')
                ->nullable();

            /*
             * Batas booking sebelum jam keberangkatan.
             * Contoh:
             * 12 berarti booking ditutup 12 jam sebelumnya.
             */
            $table->unsignedSmallInteger('booking_cutoff_hours')
                ->default(12);

            $table->boolean('is_active')
                ->default(true);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                [
                    'tour_option_id',
                    'day_of_week',
                    'is_active',
                ],
                'tour_option_schedule_day_index'
            );

            $table->index(
                [
                    'tour_option_id',
                    'available_from',
                    'available_until',
                ],
                'tour_option_schedule_period_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_option_schedules');
    }
};
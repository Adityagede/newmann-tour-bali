<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'tour_option_blackout_dates',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('tour_option_id')
                    ->constrained('tour_options')
                    ->cascadeOnDelete();

                /*
                 * Tanggal yang tidak tersedia.
                 *
                 * Contoh:
                 * 2026-12-25
                 */
                $table->date('blackout_date');

                /*
                 * true:
                 * Semua jam keberangkatan pada tanggal
                 * tersebut ditutup.
                 *
                 * false:
                 * Hanya start_time tertentu yang ditutup.
                 */
                $table->boolean('blocks_entire_day')
                    ->default(true);

                /*
                 * Digunakan jika blocks_entire_day = false.
                 *
                 * Contoh:
                 * 06:00:00
                 *
                 * Null berarti tidak menargetkan jam
                 * keberangkatan tertentu.
                 */
                $table->time('start_time')->nullable();

                /*
                 * Alasan yang boleh ditampilkan ke admin
                 * atau pengguna jika diperlukan.
                 *
                 * Contoh:
                 * Fully booked
                 * Public holiday closure
                 */
                $table->string('reason')->nullable();

                /*
                 * Catatan internal tidak perlu ditampilkan
                 * kepada pengguna.
                 */
                $table->text('internal_note')->nullable();

                $table->boolean('is_active')
                    ->default(true);

                $table->timestamps();
                $table->softDeletes();

                $table->index(
                    [
                        'tour_option_id',
                        'blackout_date',
                        'is_active',
                    ],
                    'tour_option_blackout_lookup_index'
                );

                $table->index(
                    [
                        'tour_option_id',
                        'blackout_date',
                        'start_time',
                    ],
                    'tour_option_blackout_time_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'tour_option_blackout_dates'
        );
    }
};
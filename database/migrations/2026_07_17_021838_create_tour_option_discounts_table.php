<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_option_discounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_option_id')
                ->constrained('tour_options')
                ->cascadeOnDelete();

            /*
             * Nama promosi yang terlihat oleh user.
             *
             * Contoh:
             * Early Booking Offer
             * July Special
             */
            $table->string('label');

            /*
             * Nilai yang digunakan:
             * percentage
             * fixed
             */
            $table->string('discount_type', 30);

            /*
             * Untuk percentage:
             * 10 berarti diskon 10%.
             *
             * Untuk fixed:
             * 50000 berarti diskon IDR 50.000
             * per peserta yang memenuhi syarat.
             */
            $table->unsignedBigInteger('discount_value');

            /*
             * Contoh:
             * ["adult"]
             * ["adult", "child"]
             *
             * Null atau array kosong berarti semua
             * kategori peserta berbayar.
             */
            $table->json('participant_types')
                ->nullable();

            /*
             * Periode berlakunya diskon.
             *
             * Null berarti tidak memiliki batas
             * awal atau akhir.
             */
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();

            /*
             * Jika beberapa diskon aktif bersamaan,
             * priority terbesar akan dipilih.
             */
            $table->unsignedInteger('priority')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                [
                    'tour_option_id',
                    'is_active',
                    'priority',
                ],
                'tour_option_discounts_lookup_index'
            );

            $table->index(
                [
                    'starts_at',
                    'ends_at',
                ],
                'tour_option_discounts_period_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_option_discounts');
    }
};
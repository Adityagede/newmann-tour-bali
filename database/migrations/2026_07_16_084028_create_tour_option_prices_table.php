<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_option_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_option_id')
                ->constrained('tour_options')
                ->cascadeOnDelete();

            /*
             * Nilai awal:
             * adult
             * child
             * infant
             */
            $table->string('participant_type', 30);

            /*
             * Label yang ditampilkan kepada user.
             * Contoh: Adult, Child, Infant.
             */
            $table->string('label', 60);

            /*
             * Rentang usia.
             *
             * Adult:
             * age_min = 12
             * age_max = null
             *
             * Child:
             * age_min = 3
             * age_max = 11
             *
             * Infant:
             * age_min = 0
             * age_max = 2
             */
            $table->unsignedTinyInteger('age_min')->nullable();
            $table->unsignedTinyInteger('age_max')->nullable();

            /*
             * Harga disimpan sebagai integer.
             * Contoh:
             * IDR 550.000 disimpan menjadi 550000.
             */
            $table->unsignedBigInteger('base_price')
                ->default(0);

            $table->string('currency', 3)
                ->default('IDR');

            /*
             * is_free:
             * Peserta tidak menambah harga, tetapi tetap
             * dihitung untuk kapasitas kendaraan.
             *
             * is_allowed:
             * Kategori peserta diperbolehkan untuk option ini.
             */
            $table->boolean('is_free')
                ->default(false);

            $table->boolean('is_allowed')
                ->default(true);

            $table->unsignedTinyInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->unique([
                'tour_option_id',
                'participant_type',
            ], 'tour_option_participant_unique');

            $table->index([
                'tour_option_id',
                'is_allowed',
                'sort_order',
            ], 'tour_option_prices_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_option_prices');
    }
};
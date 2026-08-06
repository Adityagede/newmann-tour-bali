<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_option_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_option_id')
                ->constrained('tour_options')
                ->cascadeOnDelete();

            /*
             * Nilai awal:
             * included
             * excluded
             */
            $table->string('item_type', 30);

            /*
             * Membantu UI memahami jenis fasilitas.
             *
             * Contoh:
             * transport
             * pickup
             * ticket
             * guide
             * meal
             * drink
             * equipment
             * insurance
             * personal_expense
             * other
             */
            $table->string('category', 50)
                ->default('other');

            /*
             * Teks utama yang terlihat oleh pengguna.
             *
             * Contoh:
             * Hotel pickup and drop-off
             * Entrance tickets listed in the itinerary
             */
            $table->string('label');

            /*
             * Penjelasan tambahan opsional.
             */
            $table->text('details')->nullable();

            /*
             * Item penting dapat ditampilkan lebih menonjol
             * pada card atau ringkasan option.
             */
            $table->boolean('is_highlighted')
                ->default(false);

            $table->boolean('is_active')
                ->default(true);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->index(
                [
                    'tour_option_id',
                    'item_type',
                    'is_active',
                    'sort_order',
                ],
                'tour_option_items_lookup_index'
            );

            $table->index([
                'tour_option_id',
                'category',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_option_items');
    }
};
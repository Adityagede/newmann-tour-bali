<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_package_id')
                ->constrained('tour_packages')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('slug');

            $table->text('short_description')->nullable();

            /*
             * Durasi disimpan dalam menit agar mudah dihitung.
             * Contoh:
             * 10 jam = 600
             * 5 jam  = 300
             */
            $table->unsignedSmallInteger('duration_minutes')
                ->nullable();

            /*
             * Contoh:
             * ["English", "Indonesian"]
             */
            $table->json('languages')->nullable();

            /*
             * Nilai awal yang digunakan:
             * hotel_pickup
             * meeting_point
             * flexible
             */
            $table->string('pickup_type')
                ->default('hotel_pickup');

            $table->string('pickup_label')->nullable();

            $table->text('confirmation_note')->nullable();

            $table->unsignedSmallInteger('min_guests')
                ->default(1);

            $table->unsignedSmallInteger('max_guests')
                ->nullable();

            $table->boolean('is_all_inclusive')
                ->default(true);

            $table->boolean('is_default')
                ->default(false);

            $table->unsignedInteger('sort_order')
                ->default(0);

            /*
             * active
             * inactive
             * draft
             */
            $table->string('status')
                ->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->unique([
                'tour_package_id',
                'slug',
            ]);

            $table->index([
                'tour_package_id',
                'status',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_options');
    }
};
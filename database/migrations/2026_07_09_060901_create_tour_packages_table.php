<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
             $table->id();

        $table->string('title');
        $table->string('slug')->unique();

        $table->string('category')->nullable();
        $table->string('badge')->nullable();
        $table->string('area')->nullable();
        $table->string('duration')->nullable();
        $table->string('trip_type')->nullable();
        $table->string('vehicle')->nullable();

        $table->decimal('rating', 2, 1)->default(5.0);
        $table->string('guests')->nullable();
        $table->string('price_text')->default('Request price');

        $table->text('description')->nullable();
        $table->text('intro')->nullable();
        $table->text('story')->nullable();

        $table->string('main_image')->nullable();
        $table->json('gallery_images')->nullable();
        $table->json('highlights')->nullable();
        $table->json('itinerary')->nullable();

        $table->boolean('is_popular')->default(false);
        $table->boolean('is_featured')->default(false);
        $table->string('status')->default('active');

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};

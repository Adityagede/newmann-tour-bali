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
        Schema::create('gallery_moments', function (Blueprint $table) {
            $table->id();

        $table->string('title')->nullable();
        $table->text('caption')->nullable();

        $table->string('category')->nullable();
        $table->string('location')->nullable();
        $table->string('alt_text')->nullable();

        $table->string('image_path');

        $table->string('display_size')->default('regular');
        $table->unsignedInteger('sort_order')->default(0);

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
        Schema::dropIfExists('gallery_moments');
    }
};

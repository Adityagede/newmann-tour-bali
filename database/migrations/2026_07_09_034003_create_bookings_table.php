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
       Schema::create('bookings', function (Blueprint $table) {
        $table->id();

        $table->string('booking_code')->unique();

        $table->string('name');
        $table->string('whatsapp');
        $table->string('email')->nullable();

        $table->string('selected_tour');
        $table->date('trip_date')->nullable();
        $table->unsignedInteger('people_count')->nullable();

        $table->string('selected_vehicle');
        $table->string('custom_vehicle')->nullable();

        $table->string('pickup_area')->nullable();
        $table->text('message')->nullable();

        $table->string('status')->default('pending');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

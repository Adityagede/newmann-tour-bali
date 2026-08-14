<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_ratings', function (Blueprint $table): void {
            $table->id();

            $table
                ->foreignId('tour_booking_request_id')
                ->unique()
                ->constrained('tour_booking_requests')
                ->cascadeOnDelete();

            $table
                ->foreignId('tour_package_id')
                ->constrained('tour_packages')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        Schema::table(
            'tour_booking_requests',
            function (Blueprint $table): void {
                $table->index(
                    ['tour_package_id', 'status'],
                    'tour_booking_requests_package_status_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'tour_booking_requests',
            function (Blueprint $table): void {
                $table->dropIndex(
                    'tour_booking_requests_package_status_index'
                );
            }
        );

        Schema::dropIfExists('tour_ratings');
    }
};

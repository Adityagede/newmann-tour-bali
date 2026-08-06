<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'tour_booking_requests',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->string(
                        'booking_reference',
                        40
                    )
                    ->unique();

                $table
                    ->foreignId(
                        'tour_package_id'
                    )
                    ->nullable()
                    ->constrained(
                        'tour_packages'
                    )
                    ->nullOnDelete();

                $table
                    ->foreignId(
                        'tour_option_id'
                    )
                    ->nullable()
                    ->constrained(
                        'tour_options'
                    )
                    ->nullOnDelete();

                $table
                    ->string(
                        'status',
                        30
                    )
                    ->default('pending');

                $table
                    ->string(
                        'source',
                        40
                    )
                    ->default('website_v2');

                $table->string(
                    'guest_name',
                    180
                );

                $table->string(
                    'guest_whatsapp',
                    50
                );

                $table
                    ->string(
                        'guest_email',
                        180
                    )
                    ->nullable();

                $table->text(
                    'pickup_address'
                );

                $table
                    ->text(
                        'special_requests'
                    )
                    ->nullable();

                $table->date(
                    'travel_date'
                );

                $table->time(
                    'starting_time'
                );

                $table->string(
                    'language',
                    80
                );

                $table
                    ->unsignedSmallInteger(
                        'adult_count'
                    )
                    ->default(0);

                $table
                    ->unsignedSmallInteger(
                        'child_count'
                    )
                    ->default(0);

                $table
                    ->unsignedSmallInteger(
                        'infant_count'
                    )
                    ->default(0);

                $table
                    ->unsignedSmallInteger(
                        'total_participants'
                    )
                    ->default(0);

                $table
                    ->char(
                        'currency',
                        3
                    )
                    ->default('IDR');

                $table
                    ->unsignedBigInteger(
                        'base_total'
                    )
                    ->nullable();

                $table
                    ->unsignedBigInteger(
                        'discount_amount'
                    )
                    ->default(0);

                $table
                    ->unsignedBigInteger(
                        'estimated_total'
                    )
                    ->nullable();

                /*
                 * Snapshots menjaga data booking tetap utuh
                 * walaupun data Tour Product berubah nanti.
                 */
                $table
                    ->json(
                        'tour_snapshot'
                    )
                    ->nullable();

                $table
                    ->json(
                        'option_snapshot'
                    )
                    ->nullable();

                $table
                    ->json(
                        'selection_snapshot'
                    )
                    ->nullable();

                $table
                    ->json(
                        'transport_snapshot'
                    )
                    ->nullable();

                $table
                    ->json(
                        'pricing_snapshot'
                    )
                    ->nullable();

                $table
                    ->json(
                        'items_snapshot'
                    )
                    ->nullable();

                $table
                    ->timestamp(
                        'requested_at'
                    )
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'status',
                    'requested_at',
                ]);

                $table->index(
                    'travel_date'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'tour_booking_requests'
        );
    }
};
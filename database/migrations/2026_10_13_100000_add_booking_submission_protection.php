<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['bookings', 'tour_booking_requests'] as $name) {
            Schema::table($name, function (Blueprint $table) use ($name): void {
                // Nullable keys leave existing bookings and references intact.
                $table->string('submission_key', 64)->nullable()->unique();
                $table->string('submission_hash', 64)->nullable();
                $table->boolean('admin_notification_pending')->default(false);
                $table->timestamp('admin_notification_queued_at')->nullable();
                $table->timestamp('admin_notified_at')->nullable();
                $table->index(['admin_notification_pending', 'admin_notification_queued_at'], $name.'_notification_pending_idx');
            });
        }
    }

    public function down(): void
    {
        foreach (['bookings', 'tour_booking_requests'] as $name) {
            Schema::table($name, function (Blueprint $table) use ($name): void {
                $table->dropUnique(['submission_key']);
                $table->dropIndex($name.'_notification_pending_idx');
                $table->dropColumn(['submission_key', 'submission_hash', 'admin_notification_pending', 'admin_notification_queued_at', 'admin_notified_at']);
            });
        }
    }
};

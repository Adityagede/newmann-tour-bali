<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\SendBookingNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Throwable;

final class BookingNotificationDispatcher
{
    public function enabled(): bool
    {
        $email = config('newman.booking_notification_email');

        return is_string($email) && trim($email) !== '';
    }

    public function dispatch(Model $booking, string $flow): void
    {
        try {
            DB::transaction(function () use ($booking, $flow): void {
                $fresh = $booking->newQuery()->lockForUpdate()->find($booking->getKey());
                if (! $fresh || ! $fresh->admin_notification_pending || $fresh->admin_notification_queued_at !== null) {
                    return;
                }

                // A persistent database job and its queued marker commit together.
                Bus::dispatch((new SendBookingNotification($flow, (int) $fresh->getKey()))
                    ->onConnection('booking-notifications')->onQueue('booking-notifications'));
                $fresh->forceFill(['admin_notification_queued_at' => now()])->save();
            });
        } catch (Throwable $exception) {
            // The order has already committed. The recovery command can enqueue it later.
            report($exception);
        }
    }
}

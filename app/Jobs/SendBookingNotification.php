<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\CustomTripRequest;
use App\Models\TourBookingRequest;
use App\Notifications\NewCustomTripRequestNotification;
use App\Notifications\NewTourBookingRequestNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

final class SendBookingNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    // Rate-limit releases do not exhaust SMTP retries during an order burst.
    public int $tries = 0;

    public int $maxExceptions = 5;

    public int $timeout = 45;

    public function __construct(public string $flow, public int $bookingId) {}

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('booking-mail:'.$this->flow.':'.$this->bookingId))->releaseAfter(5)->expireAfter(90),
            new RateLimited('booking-notifications'),
        ];
    }

    public function backoff(): array
    {
        return [30, 120, 300, 900];
    }

    public function handle(): void
    {
        $booking = match ($this->flow) {
            'custom' => CustomTripRequest::query()->customOnly()->find($this->bookingId),
            'tour' => TourBookingRequest::find($this->bookingId),
            default => throw new RuntimeException('Unknown booking notification flow.'),
        };

        if (! $booking || ! $booking->admin_notification_pending || $booking->admin_notified_at !== null) {
            return;
        }

        $email = config('newman.booking_notification_email');
        if (! is_string($email) || trim($email) === '') {
            throw new RuntimeException('Booking notification recipient is not configured.');
        }

        if ($booking instanceof TourBookingRequest) {
            $booking->loadMissing(['tourPackage:id,title,slug', 'tourOption:id,title,slug']);
            $notification = new NewTourBookingRequestNotification($booking);
        } else {
            $notification = new NewCustomTripRequestNotification($booking);
        }

        Notification::route('mail', trim($email))->notifyNow($notification);
        $booking->forceFill(['admin_notification_pending' => false, 'admin_notified_at' => now()])->save();
    }
}

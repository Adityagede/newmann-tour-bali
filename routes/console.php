<?php

use App\Models\CustomTripRequest;
use App\Models\TourBookingRequest;
use App\Services\BookingNotificationDispatcher;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('booking-notifications:dispatch', function (BookingNotificationDispatcher $dispatcher) {
    // Recover enqueue failures only. Already queued jobs are not added again.
    foreach (['custom' => CustomTripRequest::class, 'tour' => TourBookingRequest::class] as $flow => $model) {
        $model::query()->where('admin_notification_pending', true)
            ->whereNull('admin_notification_queued_at')->limit(100)->chunkById(100, function ($bookings) use ($dispatcher, $flow): void {
                foreach ($bookings as $booking) {
                    $dispatcher->dispatch($booking, $flow);
                }
            });
    }
    $this->info('Pending booking notifications checked.');
})->purpose('Queue saved booking notifications that could not be enqueued initially');

Artisan::command('booking-notifications:work {--max-time=50}', function () {
    $lock = Cache::lock('booking-notifications-cron', 120);
    if (! $lock->get()) {
        $this->info('A booking notification worker is already running.');

        return;
    }
    try {
        $this->call('booking-notifications:dispatch');
        $this->call('queue:work', [
            'connection' => 'booking-notifications', '--queue' => 'booking-notifications', '--stop-when-empty' => true,
            '--max-time' => max(1, min(50, (int) $this->option('max-time'))), '--timeout' => 45, '--sleep' => 1,
        ]);
    } finally {
        $lock->release();
    }
})->purpose('Run a bounded, non-overlapping notification worker from hosting cron');

<?php

declare(strict_types=1);

use App\Models\CustomTripRequest;
use App\Models\TourOption;
use App\Models\TourPackage;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

require dirname(__DIR__, 2).'/vendor/autoload.php';

$directory = $argv[1];
$mode = $argv[2];
$isTour = str_ends_with($mode, '-tour');
$mode = $isTour ? substr($mode, 0, -5) : $mode;
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(ConsoleKernel::class)->bootstrap();
$app['env'] = 'testing';
config([
    'app.env' => 'testing', 'app.debug' => false,
    'database.default' => 'sqlite', 'database.connections.sqlite.database' => $directory.'/race.sqlite',
    'database.connections.sqlite.url' => null, 'database.connections.sqlite.busy_timeout' => 5000,
    'session.driver' => 'database', 'session.connection' => 'sqlite', 'cache.default' => 'array',
    'mail.default' => 'array', 'newman.booking_notification_email' => 'admin@example.com',
]);
if (DB::connection()->getDriverName() !== 'sqlite' || DB::connection()->getDatabaseName() !== $directory.'/race.sqlite') {
    throw new RuntimeException('Concurrency test requires its isolated SQLite database.');
}

if ($mode === 'init') {
    Artisan::call('migrate', ['--force' => true]);
    $session = $app['session']->driver();
    $session->setId(str_repeat('r', 40));
    $session->start();
    if ($isTour) {
        $date = now()->addWeek();
        $tour = TourPackage::withoutEvents(fn () => TourPackage::create([
            'title' => 'Concurrent Test Tour', 'slug' => 'concurrent-test-tour', 'status' => 'active',
        ]));
        $option = TourOption::withoutEvents(fn () => $tour->options()->create([
            'title' => 'Private Tour', 'slug' => 'private-tour', 'status' => 'active', 'languages' => ['English'],
            'min_guests' => 1, 'max_guests' => 10,
        ]));
        foreach (['adult' => 400000, 'child' => 250000, 'infant' => 0] as $type => $price) {
            $option->prices()->create(['participant_type' => $type, 'label' => ucfirst($type), 'base_price' => $price,
                'currency' => 'IDR', 'is_allowed' => true, 'is_free' => $type === 'infant']);
        }
        $option->schedules()->create(['day_of_week' => $date->dayOfWeek, 'start_time' => '09:00:00',
            'capacity' => 10, 'booking_cutoff_hours' => 0, 'is_active' => true]);
        $session->put('tour_review_selection', [
            'tour' => ['id' => $tour->id], 'option' => ['id' => $option->id], 'is_preview' => false,
            'selection' => ['travel_date' => $date->toDateString(), 'starting_time' => '09:00', 'language' => 'English',
                'participants' => ['adult' => 2, 'child' => 0, 'infant' => 0]],
        ]);
    }
    $session->save();
    exit;
}

if ($mode === 'inspect') {
    echo json_encode(['orders' => DB::table($isTour ? 'tour_booking_requests' : 'bookings')->count(), 'jobs' => DB::table('jobs')->count()]);
    exit;
}

// Both HTTP requests pass the initial duplicate lookup before either may save.
$reachedBarrier = false;
$event = $isTour ? 'eloquent.retrieved: '.TourOption::class : 'eloquent.creating: '.CustomTripRequest::class;
Event::listen($event, function () use ($directory, $mode, $isTour, &$reachedBarrier): void {
    if ($reachedBarrier) {
        return;
    }
    $reachedBarrier = true;
    file_put_contents($directory.'/'.$mode.'.ready', 'ready');
    $other = $mode === 'first' ? 'second' : 'first';
    $deadline = microtime(true) + 10;
    while (! file_exists($directory.'/'.$other.'.ready')) {
        if (microtime(true) > $deadline) {
            throw new RuntimeException('Concurrent booking test barrier timed out.');
        }
        usleep(10000);
    }
    // The second tour transaction starts after the competing order committed.
    if ($isTour && $mode === 'second') {
        while (! file_exists($directory.'/first.finished')) {
            if (microtime(true) > $deadline) {
                throw new RuntimeException('Concurrent tour commit barrier timed out.');
            }
            usleep(10000);
        }
    }
});

$cookieName = config('session.cookie');
$encrypter = $app['encrypter'];
$cookie = $encrypter->encrypt(CookieValuePrefix::create($cookieName, $encrypter->getKey()).str_repeat('r', 40), false);
$payload = $isTour ? [
    'submission_token' => '11111111-1111-4111-8111-111111111111', 'full_name' => 'Concurrent Tour Guest',
    'whatsapp' => '+628123456789', 'pickup_address' => 'Ubud hotel, Bali', 'agreement' => '1',
] : [
    'submission_token' => '11111111-1111-4111-8111-111111111111', 'people_count' => 2,
    'name' => 'Concurrent Test Guest', 'whatsapp' => '+628123456789', 'selected_vehicle' => 'Toyota Avanza',
    'message' => 'An isolated test request visiting temples and rice terraces.',
];
$request = Request::create($isTour ? '/tours/concurrent-test-tour/booking-request' : '/custom-trip', 'POST', $payload, [$cookieName => $cookie]);
$kernel = $app->make(HttpKernel::class);
$response = $kernel->handle($request);
$kernel->terminate($request, $response);
file_put_contents($directory.'/'.$mode.'.finished', 'finished');
echo json_encode(['status' => $response->getStatusCode(), 'location' => $response->headers->get('Location')]);

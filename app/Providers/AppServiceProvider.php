<?php

namespace App\Providers;

use App\Http\Middleware\SetLocale;
use App\Models\TourOption;
use App\Models\TourPackage;
use App\Observers\TourOptionPublishingObserver;
use App\Observers\TourPackagePublishingObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TourOption::observe(TourOptionPublishingObserver::class);
        TourPackage::observe(TourPackagePublishingObserver::class);

        foreach (['public-booking' => 'booking', 'public-availability' => 'availability'] as $name => $kind) {
            RateLimiter::for($name, function (Request $request) use ($kind): array {
                $prefix = $kind === 'availability' ? 'availability_' : '';
                $response = function (Request $request, array $headers) use ($kind) {
                    // Throttling can run before the locale middleware.
                    $locale = $request->is('id', 'id/*') ? 'id' : 'en';
                    $message = __('security.'.$kind.'_wait', ['seconds' => max(1, (int) ($headers['Retry-After'] ?? 60))], $locale);
                    if ($request->expectsJson() || $request->routeIs('tours.availability', 'localized.tours.availability')) {
                        return response()->json(['message' => $message], 429, $headers);
                    }

                    $url = $kind === 'availability'
                        ? route('tours.detail', ['slug' => $request->route('slug')])
                        : ($request->routeIs('custom-trip.store', 'localized.custom-trip.store')
                            ? route('custom-trip.create')
                            : route('tours.review', ['slug' => $request->route('slug')]));

                    return redirect()->to(SetLocale::languageUrl($url, $locale))->withHeaders($headers)
                        ->withInput($request->except('_token'))
                        ->with('error', $message)->withErrors(['booking' => $message]);
                };

                return [
                    Limit::perMinute(max(1, (int) config('newman.booking_protection.'.$prefix.'session_per_minute', $kind === 'availability' ? 60 : 10)))
                        ->by($kind.'-session:'.hash('sha256', $request->session()->getId()))->response($response),
                    // The wider IP backstop accommodates guests sharing hotel Wi-Fi.
                    Limit::perMinute(max(1, (int) config('newman.booking_protection.'.$prefix.'ip_per_minute', $kind === 'availability' ? 1200 : 300)))
                        ->by($kind.'-ip:'.$request->ip())->response($response),
                ];
            });
        }

        RateLimiter::for('booking-notifications', fn () => Limit::perMinute(
            max(1, (int) config('newman.booking_protection.notification_emails_per_minute', 20))
        )->by('booking-mail:'.hash('sha256', (string) config('newman.booking_notification_email'))));
    }
}

<?php

namespace App\Providers;

use App\Models\TourOption;
use App\Models\TourPackage;
use App\Observers\TourOptionPublishingObserver;
use App\Observers\TourPackagePublishingObserver;
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
    TourOption::observe(
        TourOptionPublishingObserver::class
    );

    TourPackage::observe(
        TourPackagePublishingObserver::class
    );
}
}

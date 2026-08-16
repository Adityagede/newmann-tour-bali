<?php
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomTripRequestController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminCustomTripRequestController;
use App\Http\Controllers\Admin\AdminRatingController;
use App\Http\Controllers\Admin\AdminTourPackageController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\EnsureAdminIsLoggedIn;
use App\Http\Controllers\TourController;
use App\Http\Controllers\Admin\AdminGalleryMomentController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\AdminTourPackageGalleryController;
use App\Http\Controllers\Admin\AdminTourPackageRoadmapController;
use App\Http\Controllers\Admin\AdminTourOptionController;
use App\Http\Controllers\Admin\AdminTourOptionPriceController;
use App\Http\Controllers\Admin\AdminTourOptionItemController;
use App\Http\Controllers\Admin\AdminTourOptionScheduleController;
use App\Http\Controllers\Admin\AdminTourOptionDiscountController;
use App\Http\Controllers\Admin\AdminTourOptionBlackoutDateController;
use App\Http\Controllers\Admin\AdminTourPublishingReadinessController;
use App\Http\Controllers\TourReviewController;
use App\Http\Controllers\TourBookingRequestController;
use App\Http\Controllers\Admin\AdminTourBookingRequestController;
use App\Http\Controllers\GuestTourRatingController;

Route::post('/language', function (Request $request) {
    $validated = $request->validate([
        'locale' => [
            'required',
            'string',
            'in:'.implode(
                ',',
                config('app.supported_locales', ['en', 'id']),
            ),
        ],
    ]);

    $request->session()->put(
        'locale',
        $validated['locale'],
    );

    return redirect()->back(303);
})->name('language.update');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/tours', [TourController::class, 'index'])->name('tours');

Route::get('/tours/{slug}', [TourController::class, 'show'])->name('tours.detail');
Route::post(
    '/tours/{slug}/availability',
    [TourController::class, 'availability']
)->name('tours.availability');

Route::post(
    '/tours/{slug}/review',
    [
        TourReviewController::class,
        'store',
    ]
)
    ->name('tours.review.store');

Route::get(
    '/tours/{slug}/review',
    [
        TourReviewController::class,
        'show',
    ]
)
    ->name('tours.review');


Route::get(
    '/gallery',
    [GalleryController::class, 'index']
)->name('gallery');

Route::post(
    '/tours/{slug}/booking-request',
    [
        TourBookingRequestController::class,
        'store',
    ]
)
    ->name(
        'tours.booking-request.store'
    );

Route::middleware('signed')->group(function (): void {
    Route::get(
        '/rate-your-trip/{bookingReference}',
        [GuestTourRatingController::class, 'show']
    )->name('tour-ratings.show');

    Route::post(
        '/rate-your-trip/{bookingReference}',
        [GuestTourRatingController::class, 'store']
    )
        ->middleware('throttle:10,1')
        ->name('tour-ratings.store');
});

Route::get(
    '/booking-requests/{reference}/success',
    [
        TourBookingRequestController::class,
        'success',
    ]
)
    ->name(
        'booking-requests.success'
    );

/*
|--------------------------------------------------------------------------
| Custom Trip Requests
|--------------------------------------------------------------------------
|
| Published Tour Packages use the V2 flow above. This separate form only
| handles custom itinerary enquiries and keeps the existing database table.
|
*/
Route::get(
    '/custom-trip',
    [CustomTripRequestController::class, 'create']
)->name('custom-trip.create');

Route::post(
    '/custom-trip',
    [CustomTripRequestController::class, 'store']
)->name('custom-trip.store');

Route::get(
    '/custom-trip/success/{bookingCode}',
    [CustomTripRequestController::class, 'success']
)->name('custom-trip.success');

// Safe compatibility redirect only. The old booking implementation is gone.
Route::redirect('/booking', '/custom-trip', 301);

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');


Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware(EnsureAdminIsLoggedIn::class)
    ->prefix('admin')
    ->name('admin.')


    ->group(function () {

    Route::post(
    '/tour-packages/{tourPackage}/preview/review',
    [
        TourReviewController::class,
        'previewStore',
    ]
)
    ->whereNumber('tourPackage')
    ->name(
        'tour-packages.preview.review.store'
    );

Route::get(
    '/tour-packages/{tourPackage}/preview/review',
    [
        TourReviewController::class,
        'previewShow',
    ]
)
    ->whereNumber('tourPackage')
    ->name(
        'tour-packages.preview.review'
    );

        Route::get(
    '/tour-packages/{tourPackage}/preview',
    [
        TourController::class,
        'preview',
    ]
)
    ->whereNumber('tourPackage')
    ->name('tour-packages.preview');

Route::post(
    '/tour-packages/{tourPackage}/preview/availability',
    [
        TourController::class,
        'previewAvailability',
    ]
)
    ->whereNumber('tourPackage')
    ->name('tour-packages.preview.availability');
            

        Route::get('/', [AdminTourBookingRequestController::class, 'dashboard'])->name('dashboard');

        /*
        |------------------------------------------------------------------
        | Tour Package Booking Requests V2 — canonical admin booking flow
        |------------------------------------------------------------------
        */
        Route::get(
            '/bookings',
            [AdminTourBookingRequestController::class, 'index']
        )->name('tour-booking-requests.index');

        Route::get(
            '/bookings/{tourBookingRequest}',
            [AdminTourBookingRequestController::class, 'show']
        )
            ->whereNumber('tourBookingRequest')
            ->name('tour-booking-requests.show');

        Route::patch(
            '/bookings/{tourBookingRequest}/status',
            [AdminTourBookingRequestController::class, 'updateStatus']
        )
            ->whereNumber('tourBookingRequest')
            ->name('tour-booking-requests.status.update');

        Route::delete(
            '/bookings/{tourBookingRequest}',
            [AdminTourBookingRequestController::class, 'destroy']
        )
            ->whereNumber('tourBookingRequest')
            ->name('tour-booking-requests.destroy');

        /*
        |------------------------------------------------------------------
        | Custom Trip Requests — separate, non-package enquiry flow
        |------------------------------------------------------------------
        */
        Route::get(
            '/custom-trip-requests',
            [AdminCustomTripRequestController::class, 'index']
        )->name('custom-trip-requests.index');

        Route::get(
            '/custom-trip-requests/{customTripRequest}',
            [AdminCustomTripRequestController::class, 'show']
        )
            ->whereNumber('customTripRequest')
            ->name('custom-trip-requests.show');

        Route::patch(
            '/custom-trip-requests/{customTripRequest}/status',
            [AdminCustomTripRequestController::class, 'updateStatus']
        )
            ->whereNumber('customTripRequest')
            ->name('custom-trip-requests.status.update');

        Route::delete(
            '/custom-trip-requests/{customTripRequest}',
            [AdminCustomTripRequestController::class, 'destroy']
        )
            ->whereNumber('customTripRequest')
            ->name('custom-trip-requests.destroy');

        Route::get(
            '/ratings',
            [AdminRatingController::class, 'index']
        )->name('ratings.index');

        Route::get(
            '/ratings/{tourRating}',
            [AdminRatingController::class, 'show']
        )
            ->whereNumber('tourRating')
            ->name('ratings.show');

         Route::get('/tour-packages', [AdminTourPackageController::class, 'index'])->name('tour-packages.index');
        Route::get('/tour-packages/create', [AdminTourPackageController::class, 'create'])->name('tour-packages.create');
        Route::post('/tour-packages', [AdminTourPackageController::class, 'store'])->name('tour-packages.store');

        Route::get('/tour-packages/{tourPackage}/edit', [AdminTourPackageController::class, 'edit'])->name('tour-packages.edit');
        Route::patch('/tour-packages/{tourPackage}', [AdminTourPackageController::class, 'update'])->name('tour-packages.update');
        Route::delete('/tour-packages/{tourPackage}', [AdminTourPackageController::class, 'destroy'])->name('tour-packages.destroy');    
        Route::get(
    '/tour-packages/{tourPackage}/gallery',
    [
        AdminTourPackageGalleryController::class,
        'edit',
    ]
)->name('tour-packages.gallery.edit');

Route::put(
    '/tour-packages/{tourPackage}/gallery',
    [
        AdminTourPackageGalleryController::class,
        'update',
    ]
)->name('tour-packages.gallery.update');

Route::patch(
    '/tour-packages/{tourPackage}/gallery/{imageIndex}/move',
    [
        AdminTourPackageGalleryController::class,
        'move',
    ]
)
    ->whereNumber('imageIndex')
    ->name('tour-packages.gallery.move');

Route::delete(
    '/tour-packages/{tourPackage}/gallery/{imageIndex}',
    [
        AdminTourPackageGalleryController::class,
        'destroy',
    ]
)
    ->whereNumber('imageIndex')
    ->name('tour-packages.gallery.destroy');


    



    Route::get(
    '/tour-packages/{tourPackage}/roadmap',
    [
        AdminTourPackageRoadmapController::class,
        'index',
    ]
)->name('tour-packages.roadmap.index');

Route::get(
    '/tour-packages/{tourPackage}/roadmap/create',
    [
        AdminTourPackageRoadmapController::class,
        'create',
    ]
)->name('tour-packages.roadmap.create');

Route::post(
    '/tour-packages/{tourPackage}/roadmap',
    [
        AdminTourPackageRoadmapController::class,
        'store',
    ]
)->name('tour-packages.roadmap.store');

Route::get(
    '/tour-packages/{tourPackage}/roadmap/{tourStop}/edit',
    [
        AdminTourPackageRoadmapController::class,
        'edit',
    ]
)->name('tour-packages.roadmap.edit');

Route::patch(
    '/tour-packages/{tourPackage}/roadmap/{tourStop}',
    [
        AdminTourPackageRoadmapController::class,
        'update',
    ]
)->name('tour-packages.roadmap.update');

Route::patch(
    '/tour-packages/{tourPackage}/roadmap/{tourStop}/move',
    [
        AdminTourPackageRoadmapController::class,
        'move',
    ]
)->name('tour-packages.roadmap.move');

Route::delete(
    '/tour-packages/{tourPackage}/roadmap/{tourStop}',
    [
        AdminTourPackageRoadmapController::class,
        'destroy',
    ]
)->name('tour-packages.roadmap.destroy');





Route::get(
    '/tour-packages/{tourPackage}/options',
    [
        AdminTourOptionController::class,
        'index',
    ]
)->name('tour-packages.options.index');

Route::get(
    '/tour-packages/{tourPackage}/options/create',
    [
        AdminTourOptionController::class,
        'create',
    ]
)->name('tour-packages.options.create');

Route::post(
    '/tour-packages/{tourPackage}/options',
    [
        AdminTourOptionController::class,
        'store',
    ]
)->name('tour-packages.options.store');

Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/edit',
    [
        AdminTourOptionController::class,
        'edit',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.edit');

Route::patch(
    '/tour-packages/{tourPackage}/options/{tourOption}',
    [
        AdminTourOptionController::class,
        'update',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.update');

Route::patch(
    '/tour-packages/{tourPackage}/options/{tourOption}/move',
    [
        AdminTourOptionController::class,
        'move',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.move');

Route::delete(
    '/tour-packages/{tourPackage}/options/{tourOption}',
    [
        AdminTourOptionController::class,
        'destroy',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.destroy');




    Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/prices',
    [
        AdminTourOptionPriceController::class,
        'edit',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.prices.edit');

Route::put(
    '/tour-packages/{tourPackage}/options/{tourOption}/prices',
    [
        AdminTourOptionPriceController::class,
        'update',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.prices.update');




    Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/items',
    [
        AdminTourOptionItemController::class,
        'index',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.items.index');

Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/items/create',
    [
        AdminTourOptionItemController::class,
        'create',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.items.create');

Route::post(
    '/tour-packages/{tourPackage}/options/{tourOption}/items',
    [
        AdminTourOptionItemController::class,
        'store',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.items.store');

Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/items/{tourOptionItem}/edit',
    [
        AdminTourOptionItemController::class,
        'edit',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionItem')
    ->name('tour-packages.options.items.edit');

Route::patch(
    '/tour-packages/{tourPackage}/options/{tourOption}/items/{tourOptionItem}',
    [
        AdminTourOptionItemController::class,
        'update',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionItem')
    ->name('tour-packages.options.items.update');

Route::patch(
    '/tour-packages/{tourPackage}/options/{tourOption}/items/{tourOptionItem}/move',
    [
        AdminTourOptionItemController::class,
        'move',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionItem')
    ->name('tour-packages.options.items.move');

Route::delete(
    '/tour-packages/{tourPackage}/options/{tourOption}/items/{tourOptionItem}',
    [
        AdminTourOptionItemController::class,
        'destroy',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionItem')
    ->name('tour-packages.options.items.destroy');
    
    

    Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/schedules',
    [
        AdminTourOptionScheduleController::class,
        'index',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.schedules.index');

Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/schedules/create',
    [
        AdminTourOptionScheduleController::class,
        'create',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.schedules.create');

Route::post(
    '/tour-packages/{tourPackage}/options/{tourOption}/schedules',
    [
        AdminTourOptionScheduleController::class,
        'store',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.schedules.store');

Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/schedules/{tourOptionSchedule}/edit',
    [
        AdminTourOptionScheduleController::class,
        'edit',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionSchedule')
    ->name('tour-packages.options.schedules.edit');

Route::patch(
    '/tour-packages/{tourPackage}/options/{tourOption}/schedules/{tourOptionSchedule}',
    [
        AdminTourOptionScheduleController::class,
        'update',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionSchedule')
    ->name('tour-packages.options.schedules.update');

Route::patch(
    '/tour-packages/{tourPackage}/options/{tourOption}/schedules/{tourOptionSchedule}/move',
    [
        AdminTourOptionScheduleController::class,
        'move',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionSchedule')
    ->name('tour-packages.options.schedules.move');

Route::delete(
    '/tour-packages/{tourPackage}/options/{tourOption}/schedules/{tourOptionSchedule}',
    [
        AdminTourOptionScheduleController::class,
        'destroy',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionSchedule')
    ->name('tour-packages.options.schedules.destroy');





Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/discounts',
    [
        AdminTourOptionDiscountController::class,
        'index',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.discounts.index');

Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/discounts/create',
    [
        AdminTourOptionDiscountController::class,
        'create',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.discounts.create');

Route::post(
    '/tour-packages/{tourPackage}/options/{tourOption}/discounts',
    [
        AdminTourOptionDiscountController::class,
        'store',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.discounts.store');

Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/discounts/{tourOptionDiscount}/edit',
    [
        AdminTourOptionDiscountController::class,
        'edit',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionDiscount')
    ->name('tour-packages.options.discounts.edit');

Route::patch(
    '/tour-packages/{tourPackage}/options/{tourOption}/discounts/{tourOptionDiscount}',
    [
        AdminTourOptionDiscountController::class,
        'update',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionDiscount')
    ->name('tour-packages.options.discounts.update');

Route::delete(
    '/tour-packages/{tourPackage}/options/{tourOption}/discounts/{tourOptionDiscount}',
    [
        AdminTourOptionDiscountController::class,
        'destroy',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionDiscount')
    ->name('tour-packages.options.discounts.destroy');





    Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/blackouts',
    [
        AdminTourOptionBlackoutDateController::class,
        'index',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.blackouts.index');

Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/blackouts/create',
    [
        AdminTourOptionBlackoutDateController::class,
        'create',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.blackouts.create');

Route::post(
    '/tour-packages/{tourPackage}/options/{tourOption}/blackouts',
    [
        AdminTourOptionBlackoutDateController::class,
        'store',
    ]
)
    ->whereNumber('tourOption')
    ->name('tour-packages.options.blackouts.store');

Route::get(
    '/tour-packages/{tourPackage}/options/{tourOption}/blackouts/{tourOptionBlackoutDate}/edit',
    [
        AdminTourOptionBlackoutDateController::class,
        'edit',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionBlackoutDate')
    ->name('tour-packages.options.blackouts.edit');

Route::patch(
    '/tour-packages/{tourPackage}/options/{tourOption}/blackouts/{tourOptionBlackoutDate}',
    [
        AdminTourOptionBlackoutDateController::class,
        'update',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionBlackoutDate')
    ->name('tour-packages.options.blackouts.update');

Route::delete(
    '/tour-packages/{tourPackage}/options/{tourOption}/blackouts/{tourOptionBlackoutDate}',
    [
        AdminTourOptionBlackoutDateController::class,
        'destroy',
    ]
)
    ->whereNumber('tourOption')
    ->whereNumber('tourOptionBlackoutDate')
    ->name('tour-packages.options.blackouts.destroy');
    



    Route::get(
    '/tour-packages/{tourPackage}/readiness',
    [
        AdminTourPublishingReadinessController::class,
        'show',
    ]
)
    ->name('tour-packages.readiness.show');



        Route::get(
    '/gallery',
    [AdminGalleryMomentController::class, 'index']
)->name('gallery.index');

Route::get(
    '/gallery/create',
    [AdminGalleryMomentController::class, 'create']
)->name('gallery.create');

Route::post(
    '/gallery',
    [AdminGalleryMomentController::class, 'store']
)->name('gallery.store');

Route::get(
    '/gallery/{galleryMoment}/edit',
    [AdminGalleryMomentController::class, 'edit']
)->name('gallery.edit');

Route::patch(
    '/gallery/{galleryMoment}',
    [AdminGalleryMomentController::class, 'update']
)->name('gallery.update');

Route::delete(
    '/gallery/{galleryMoment}',
    [AdminGalleryMomentController::class, 'destroy']
)->name('gallery.destroy');
           });




Route::get('/sitemap.xml', function () {
    $tours = TourPackage::where('status', 'active')->get();

    return response()
        ->view('sitemap', compact('tours'))
        ->header('Content-Type', 'application/xml');
});
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SubmitTourRatingRequest;
use App\Models\TourBookingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class GuestTourRatingController extends Controller
{
    public function show(string $bookingReference): View
    {
        $booking = TourBookingRequest::query()
            ->with([
                'tourPackage:id,title,slug,main_image',
                'ratingRecord:id,tour_booking_request_id,rating,feedback,created_at',
            ])
            ->where('booking_reference', $bookingReference)
            ->firstOrFail();

        abort_unless(
            $booking->isRatingEligible(),
            Response::HTTP_FORBIDDEN,
            'This trip is not eligible for a rating yet.'
        );

        return view('pages.tour-rating', [
            'booking' => $booking,
            'rating' => $booking->ratingRecord,
        ]);
    }

    public function store(
        SubmitTourRatingRequest $request,
        string $bookingReference
    ): RedirectResponse {
        $booking = DB::transaction(
            function () use ($request, $bookingReference): TourBookingRequest {
                $booking = TourBookingRequest::query()
                    ->where('booking_reference', $bookingReference)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_unless(
                    $booking->isRatingEligible(),
                    Response::HTTP_FORBIDDEN,
                    'This trip is not eligible for a rating yet.'
                );

                if ($booking->ratingRecord()->exists()) {
                    throw ValidationException::withMessages([
                        'rating' => 'A verified rating has already been received for this trip.',
                    ]);
                }

                $booking->ratingRecord()->create([
                    'tour_package_id' => $booking->tour_package_id,
                    'rating' => $request->integer('rating'),
                    'feedback' => $request->validated('feedback'),
                ]);

                return $booking;
            }
        );

        return redirect()
            ->to(URL::signedRoute('tour-ratings.show', [
                'bookingReference' => $booking->booking_reference,
            ]))
            ->with('rating_submitted', true);
    }
}

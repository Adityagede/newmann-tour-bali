<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourBookingRequest;
use App\Models\TourPackage;
use App\Models\TourRating;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class AdminRatingController extends Controller
{
    private const SORTS = [
        'newest',
        'oldest',
        'highest',
        'lowest',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'tour' => [
                'nullable',
                'integer',
                'exists:tour_packages,id',
            ],
            'rating' => [
                'nullable',
                'integer',
                Rule::in([1, 2, 3, 4, 5]),
            ],
            'sort' => [
                'nullable',
                'string',
                Rule::in(self::SORTS),
            ],
            'q' => [
                'nullable',
                'string',
                'max:120',
            ],
        ]);

        $tourId = isset($validated['tour'])
            ? (int) $validated['tour']
            : null;
        $ratingValue = isset($validated['rating'])
            ? (int) $validated['rating']
            : null;
        $sort = (string) ($validated['sort'] ?? 'newest');
        $search = trim((string) ($validated['q'] ?? ''));

        $summaryRow = $this->verifiedRatingsQuery()
            ->selectRaw(
                'COUNT(*) as verified_reviews, '
                . 'AVG(rating) as average_rating, '
                . 'COUNT(DISTINCT tour_package_id) as tours_reviewed'
            )
            ->first();

        $summary = [
            'average_rating' => $summaryRow?->average_rating !== null
                ? (float) $summaryRow->average_rating
                : null,
            'verified_reviews' => (int) ($summaryRow?->verified_reviews ?? 0),
            'tours_reviewed' => (int) ($summaryRow?->tours_reviewed ?? 0),
        ];

        $ratings = $this->verifiedRatingsQuery()
            ->with([
                'bookingRequest:id,booking_reference,tour_package_id,status,guest_name,travel_date',
                'tourPackage:id,title,slug',
            ])
            ->when(
                $tourId !== null,
                fn (Builder $query): Builder => $query->where(
                    'tour_package_id',
                    $tourId
                )
            )
            ->when(
                $ratingValue !== null,
                fn (Builder $query): Builder => $query->where(
                    'rating',
                    $ratingValue
                )
            )
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(
                        function (Builder $searchQuery) use ($search): void {
                            $searchQuery
                                ->whereHas(
                                    'bookingRequest',
                                    function (Builder $bookingQuery) use ($search): void {
                                        $bookingQuery->where(
                                            function (Builder $identityQuery) use ($search): void {
                                                $identityQuery
                                                    ->where(
                                                        'guest_name',
                                                        'like',
                                                        "%{$search}%"
                                                    )
                                                    ->orWhere(
                                                        'booking_reference',
                                                        'like',
                                                        "%{$search}%"
                                                    );
                                            }
                                        );
                                    }
                                )
                                ->orWhereHas(
                                    'tourPackage',
                                    fn (Builder $tourQuery): Builder => $tourQuery->where(
                                        'title',
                                        'like',
                                        "%{$search}%"
                                    )
                                );
                        }
                    );
                }
            );

        match ($sort) {
            'oldest' => $ratings
                ->orderBy('created_at')
                ->orderBy('id'),
            'highest' => $ratings
                ->orderByDesc('rating')
                ->orderByDesc('created_at')
                ->orderByDesc('id'),
            'lowest' => $ratings
                ->orderBy('rating')
                ->orderByDesc('created_at')
                ->orderByDesc('id'),
            default => $ratings
                ->orderByDesc('created_at')
                ->orderByDesc('id'),
        };

        $ratings = $ratings
            ->paginate(15)
            ->withQueryString();

        $tourPackages = TourPackage::query()
            ->select(['id', 'title'])
            ->whereHas(
                'ratings',
                fn (Builder $ratingQuery): Builder => $ratingQuery->whereHas(
                    'bookingRequest',
                    fn (Builder $bookingQuery): Builder => $bookingQuery->where(
                        'status',
                        TourBookingRequest::STATUS_COMPLETED
                    )
                )
            )
            ->orderBy('title')
            ->get();

        return view('admin.ratings.index', [
            'ratings' => $ratings,
            'summary' => $summary,
            'tourPackages' => $tourPackages,
            'tourId' => $tourId,
            'ratingValue' => $ratingValue,
            'sort' => $sort,
            'search' => $search,
        ]);
    }

    public function show(TourRating $tourRating): View
    {
        $tourRating->load([
            'bookingRequest',
            'tourPackage:id,title,slug',
        ]);

        abort_unless(
            $tourRating->bookingRequest?->status
                === TourBookingRequest::STATUS_COMPLETED,
            404
        );

        return view('admin.ratings.show', [
            'rating' => $tourRating,
        ]);
    }

    private function verifiedRatingsQuery(): Builder
    {
        return TourRating::query()
            ->whereHas(
                'bookingRequest',
                fn (Builder $query): Builder => $query->where(
                    'status',
                    TourBookingRequest::STATUS_COMPLETED
                )
            );
    }
}

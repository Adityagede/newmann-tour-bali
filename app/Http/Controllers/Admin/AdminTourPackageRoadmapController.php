<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTourStopRequest;
use App\Models\TourPackage;
use App\Models\TourStop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class AdminTourPackageRoadmapController extends Controller
{
    public function index(
        TourPackage $tourPackage
    ) {
        $stops = $this->sharedStopsQuery(
            $tourPackage
        )
            ->orderBy('day_number')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'admin.tour-packages.roadmap.index',
            [
                'tourPackage' => $tourPackage,
                'stops' => $stops,
                'stopsByDay' => $stops->groupBy(
                    'day_number'
                ),
                'stopTypes' =>
                    SaveTourStopRequest::stopTypes(),
                'activeCount' =>
                    $stops->where(
                        'is_active',
                        true
                    )->count(),
                'mapCount' =>
                    $stops->where(
                        'show_on_map',
                        true
                    )->count(),
            ]
        );
    }

    public function create(
        TourPackage $tourPackage
    ) {
        return view(
            'admin.tour-packages.roadmap.create',
            [
                'tourPackage' => $tourPackage,
                'tourStop' => null,
                'stopTypes' =>
                    SaveTourStopRequest::stopTypes(),
            ]
        );
    }

    public function store(
        SaveTourStopRequest $request,
        TourPackage $tourPackage
    ) {
        $validated = $request->validated();

        $nextSortOrder = (int) (
            $this->sharedStopsQuery(
                $tourPackage
            )
                ->where(
                    'day_number',
                    $validated['day_number']
                )
                ->max('sort_order')
            ?? 0
        ) + 10;

        $tourStop = new TourStop();

        $tourStop->forceFill(
            $this->payload(
                $request,
                $validated,
                $tourPackage,
                $nextSortOrder
            )
        );

        $tourStop->save();

        return redirect()
            ->route(
                'admin.tour-packages.roadmap.index',
                $tourPackage
            )
            ->with(
                'success',
                'Roadmap stop has been added.'
            );
    }

    public function edit(
        TourPackage $tourPackage,
        TourStop $tourStop
    ) {
        $this->assertSharedStopBelongsToTour(
            $tourPackage,
            $tourStop
        );

        return view(
            'admin.tour-packages.roadmap.edit',
            [
                'tourPackage' => $tourPackage,
                'tourStop' => $tourStop,
                'stopTypes' =>
                    SaveTourStopRequest::stopTypes(),
            ]
        );
    }

    public function update(
        SaveTourStopRequest $request,
        TourPackage $tourPackage,
        TourStop $tourStop
    ) {
        $this->assertSharedStopBelongsToTour(
            $tourPackage,
            $tourStop
        );

        $validated = $request->validated();
        $oldDayNumber = (int) $tourStop->day_number;
        $newDayNumber = (int) $validated['day_number'];

        DB::transaction(function () use (
            $request,
            $validated,
            $tourPackage,
            $tourStop,
            $oldDayNumber,
            $newDayNumber
        ): void {
            $sortOrder = (int) $tourStop->sort_order;

            if ($oldDayNumber !== $newDayNumber) {
                $sortOrder = (int) (
                    $this->sharedStopsQuery(
                        $tourPackage
                    )
                        ->where(
                            'day_number',
                            $newDayNumber
                        )
                        ->max('sort_order')
                    ?? 0
                ) + 10;
            }

            $tourStop->forceFill(
                $this->payload(
                    $request,
                    $validated,
                    $tourPackage,
                    $sortOrder
                )
            );

            $tourStop->save();

            $this->normalizeDayOrder(
                $tourPackage,
                $oldDayNumber
            );

            if ($newDayNumber !== $oldDayNumber) {
                $this->normalizeDayOrder(
                    $tourPackage,
                    $newDayNumber
                );
            }
        });

        return redirect()
            ->route(
                'admin.tour-packages.roadmap.index',
                $tourPackage
            )
            ->with(
                'success',
                'Roadmap stop has been updated.'
            );
    }

    public function move(
        Request $request,
        TourPackage $tourPackage,
        TourStop $tourStop
    ) {
        $this->assertSharedStopBelongsToTour(
            $tourPackage,
            $tourStop
        );

        $validated = $request->validate([
            'direction' => [
                'required',
                'in:up,down',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $tourPackage,
            $tourStop
        ): void {
            $stops = $this->sharedStopsQuery(
                $tourPackage
            )
                ->where(
                    'day_number',
                    $tourStop->day_number
                )
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->values();

            $currentIndex = $stops->search(
                fn (TourStop $stop): bool =>
                    (int) $stop->id
                    === (int) $tourStop->id
            );

            if ($currentIndex === false) {
                return;
            }

            $targetIndex =
                $validated['direction'] === 'up'
                    ? $currentIndex - 1
                    : $currentIndex + 1;

            if (
                $targetIndex < 0
                || $targetIndex >= $stops->count()
            ) {
                return;
            }

            $orderedStops = $stops->all();

            [
                $orderedStops[$currentIndex],
                $orderedStops[$targetIndex],
            ] = [
                $orderedStops[$targetIndex],
                $orderedStops[$currentIndex],
            ];

            foreach (
                $orderedStops as $index => $stop
            ) {
                TourStop::query()
                    ->whereKey($stop->id)
                    ->update([
                        'sort_order' =>
                            ($index + 1) * 10,
                    ]);
            }
        });

        return redirect()
            ->route(
                'admin.tour-packages.roadmap.index',
                $tourPackage
            )
            ->with(
                'success',
                'Roadmap order has been updated.'
            );
    }

    public function destroy(
        TourPackage $tourPackage,
        TourStop $tourStop
    ) {
        $this->assertSharedStopBelongsToTour(
            $tourPackage,
            $tourStop
        );

        $dayNumber = (int) $tourStop->day_number;

        DB::transaction(function () use (
            $tourPackage,
            $tourStop,
            $dayNumber
        ): void {
            $tourStop->delete();

            $this->normalizeDayOrder(
                $tourPackage,
                $dayNumber
            );
        });

        return redirect()
            ->route(
                'admin.tour-packages.roadmap.index',
                $tourPackage
            )
            ->with(
                'success',
                'Roadmap stop has been removed.'
            );
    }

    private function payload(
        SaveTourStopRequest $request,
        array $validated,
        TourPackage $tourPackage,
        int $sortOrder
    ): array {
        return [
            'tour_package_id' => $tourPackage->id,

            /*
             * null berarti stop ini digunakan sebagai
             * roadmap bersama untuk seluruh options.
             */
            'tour_option_id' => null,

            'day_number' =>
                (int) $validated['day_number'],

            'stop_type' =>
                $validated['stop_type'],

            'title' =>
                $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'location_name' =>
                $validated['location_name'] ?? null,

            'address' =>
                $validated['address'] ?? null,

            'scheduled_time' =>
                $this->databaseTime(
                    $validated['scheduled_time']
                    ?? null
                ),

            'time_label' =>
                $validated['time_label'] ?? null,

            'duration_minutes' =>
                isset($validated['duration_minutes'])
                    ? (int) $validated[
                        'duration_minutes'
                    ]
                    : null,

            'latitude' =>
                $validated['latitude'] ?? null,

            'longitude' =>
                $validated['longitude'] ?? null,

            'show_on_map' =>
                $request->boolean(
                    'show_on_map'
                ),

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),

            'sort_order' => $sortOrder,
        ];
    }

    private function sharedStopsQuery(
        TourPackage $tourPackage
    ) {
        return TourStop::query()
            ->where(
                'tour_package_id',
                $tourPackage->id
            )
            ->whereNull('tour_option_id');
    }

    private function assertSharedStopBelongsToTour(
        TourPackage $tourPackage,
        TourStop $tourStop
    ): void {
        abort_unless(
            (int) $tourStop->tour_package_id
                === (int) $tourPackage->id
            && $tourStop->tour_option_id === null,
            404
        );
    }

    private function normalizeDayOrder(
        TourPackage $tourPackage,
        int $dayNumber
    ): void {
        $stops = $this->sharedStopsQuery(
            $tourPackage
        )
            ->where(
                'day_number',
                $dayNumber
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($stops as $index => $stop) {
            TourStop::query()
                ->whereKey($stop->id)
                ->update([
                    'sort_order' =>
                        ($index + 1) * 10,
                ]);
        }
    }

    private function databaseTime(
        ?string $time
    ): ?string {
        if ($time === null || $time === '') {
            return null;
        }

        return strlen($time) === 5
            ? $time . ':00'
            : $time;
    }
}
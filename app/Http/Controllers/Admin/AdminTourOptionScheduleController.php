<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTourOptionScheduleRequest;
use App\Models\TourOption;
use App\Models\TourOptionSchedule;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class AdminTourOptionScheduleController extends Controller
{
    public function index(
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $schedules = $tourOption
            ->schedules()
            ->orderBy('day_of_week')
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        return view(
            'admin.tour-packages.options.schedules.index',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'schedules' => $schedules,

                'schedulesByDay' => $schedules
                    ->groupBy('day_of_week'),

                'days' =>
                    SaveTourOptionScheduleRequest::days(),

                'activeCount' => $schedules
                    ->where('is_active', true)
                    ->count(),
            ]
        );
    }

    public function create(
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        return view(
            'admin.tour-packages.options.schedules.create',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionSchedule' => null,

                'days' =>
                    SaveTourOptionScheduleRequest::days(),
            ]
        );
    }

    public function store(
        SaveTourOptionScheduleRequest $request,
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $validated = $request->validated();

        $startTime = $this->databaseTime(
            $validated['start_time']
        );

        if (
            $this->duplicateExists(
                $tourOption,
                (int) $validated['day_of_week'],
                $startTime
            )
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' =>
                        'A schedule with this day and starting '
                        . 'time already exists for the option.',
                ]);
        }

        $nextSortOrder = (
            (int) (
                $tourOption
                    ->schedules()
                    ->where(
                        'day_of_week',
                        $validated['day_of_week']
                    )
                    ->max('sort_order')
                ?? 0
            )
        ) + 10;

        $schedule = new TourOptionSchedule();

        $schedule->forceFill(
            $this->payload(
                $request,
                $validated,
                $tourOption,
                $nextSortOrder
            )
        );

        $schedule->save();

        return redirect()
            ->route(
                'admin.tour-packages.options.schedules.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Operating schedule has been added.'
            );
    }

    public function edit(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionSchedule $tourOptionSchedule
    ) {
        $this->assertScheduleBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionSchedule
        );

        return view(
            'admin.tour-packages.options.schedules.edit',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionSchedule' =>
                    $tourOptionSchedule,

                'days' =>
                    SaveTourOptionScheduleRequest::days(),
            ]
        );
    }

    public function update(
        SaveTourOptionScheduleRequest $request,
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionSchedule $tourOptionSchedule
    ) {
        $this->assertScheduleBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionSchedule
        );

        $validated = $request->validated();

        $startTime = $this->databaseTime(
            $validated['start_time']
        );

        if (
            $this->duplicateExists(
                $tourOption,
                (int) $validated['day_of_week'],
                $startTime,
                $tourOptionSchedule->id
            )
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' =>
                        'A schedule with this day and starting '
                        . 'time already exists for the option.',
                ]);
        }

        /*
         * Jangan membiarkan option Active kehilangan
         * seluruh schedule aktifnya.
         */
        if (
            $tourOption->status === 'active'
            && !$request->boolean('is_active')
            && (bool) $tourOptionSchedule->is_active
            && !$tourOption
                ->activeSchedules()
                ->whereKeyNot($tourOptionSchedule->id)
                ->exists()
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'is_active' =>
                        'An active option must retain at least '
                        . 'one active operating schedule.',
                ]);
        }

        $oldDay = (int) $tourOptionSchedule->day_of_week;
        $newDay = (int) $validated['day_of_week'];

        DB::transaction(function () use (
            $request,
            $validated,
            $tourOption,
            $tourOptionSchedule,
            $oldDay,
            $newDay
        ): void {
            $sortOrder =
                (int) $tourOptionSchedule->sort_order;

            if ($oldDay !== $newDay) {
                $sortOrder = (
                    (int) (
                        $tourOption
                            ->schedules()
                            ->where(
                                'day_of_week',
                                $newDay
                            )
                            ->max('sort_order')
                        ?? 0
                    )
                ) + 10;
            }

            $tourOptionSchedule->forceFill(
                $this->payload(
                    $request,
                    $validated,
                    $tourOption,
                    $sortOrder
                )
            );

            $tourOptionSchedule->save();

            $this->normalizeDayOrder(
                $tourOption,
                $oldDay
            );

            if ($newDay !== $oldDay) {
                $this->normalizeDayOrder(
                    $tourOption,
                    $newDay
                );
            }
        });

        return redirect()
            ->route(
                'admin.tour-packages.options.schedules.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Operating schedule has been updated.'
            );
    }

    public function move(
        Request $request,
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionSchedule $tourOptionSchedule
    ) {
        $this->assertScheduleBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionSchedule
        );

        $validated = $request->validate([
            'direction' => [
                'required',
                'in:up,down',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $tourOption,
            $tourOptionSchedule
        ): void {
            $schedules = $tourOption
                ->schedules()
                ->where(
                    'day_of_week',
                    $tourOptionSchedule->day_of_week
                )
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->values();

            $currentIndex = $schedules->search(
                fn (
                    TourOptionSchedule $schedule
                ): bool =>
                    (int) $schedule->id
                    === (int) $tourOptionSchedule->id
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
                || $targetIndex
                    >= $schedules->count()
            ) {
                return;
            }

            $orderedSchedules =
                $schedules->all();

            [
                $orderedSchedules[$currentIndex],
                $orderedSchedules[$targetIndex],
            ] = [
                $orderedSchedules[$targetIndex],
                $orderedSchedules[$currentIndex],
            ];

            foreach (
                $orderedSchedules
                as $index => $schedule
            ) {
                TourOptionSchedule::query()
                    ->whereKey($schedule->id)
                    ->update([
                        'sort_order' =>
                            ($index + 1) * 10,
                    ]);
            }
        });

        return redirect()
            ->route(
                'admin.tour-packages.options.schedules.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Schedule order has been updated.'
            );
    }

    public function destroy(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionSchedule $tourOptionSchedule
    ) {
        $this->assertScheduleBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionSchedule
        );

        if (
            $tourOption->status === 'active'
            && (bool) $tourOptionSchedule->is_active
            && !$tourOption
                ->activeSchedules()
                ->whereKeyNot($tourOptionSchedule->id)
                ->exists()
        ) {
            return back()->with(
                'error',
                'The final active schedule cannot be removed '
                . 'from an active option.'
            );
        }

        $dayOfWeek =
            (int) $tourOptionSchedule->day_of_week;

        DB::transaction(function () use (
            $tourOption,
            $tourOptionSchedule,
            $dayOfWeek
        ): void {
            $tourOptionSchedule->delete();

            $this->normalizeDayOrder(
                $tourOption,
                $dayOfWeek
            );
        });

        return redirect()
            ->route(
                'admin.tour-packages.options.schedules.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Operating schedule has been removed.'
            );
    }

    private function payload(
        SaveTourOptionScheduleRequest $request,
        array $validated,
        TourOption $tourOption,
        int $sortOrder
    ): array {
        return [
            'tour_option_id' => $tourOption->id,

            'day_of_week' =>
                (int) $validated['day_of_week'],

            'start_time' =>
                $this->databaseTime(
                    $validated['start_time']
                ),

            'end_time' =>
                $this->databaseTime(
                    $validated['end_time']
                    ?? null
                ),

            'available_from' =>
                $validated['available_from']
                ?? null,

            'available_until' =>
                $validated['available_until']
                ?? null,

            'capacity' =>
                isset($validated['capacity'])
                    ? (int) $validated['capacity']
                    : null,

            'booking_cutoff_hours' =>
                (int) $validated[
                    'booking_cutoff_hours'
                ],

            'is_active' =>
                $request->boolean('is_active'),

            'sort_order' => $sortOrder,
        ];
    }

    private function duplicateExists(
        TourOption $tourOption,
        int $dayOfWeek,
        string $startTime,
        ?int $ignoreId = null
    ): bool {
        return $tourOption
            ->schedules()
            ->where(
                'day_of_week',
                $dayOfWeek
            )
            ->where(
                'start_time',
                $startTime
            )
            ->when(
                $ignoreId !== null,
                fn ($query) =>
                    $query->where(
                        'id',
                        '!=',
                        $ignoreId
                    )
            )
            ->exists();
    }

    private function assertOptionBelongsToTour(
        TourPackage $tourPackage,
        TourOption $tourOption
    ): void {
        abort_unless(
            (int) $tourOption->tour_package_id
                === (int) $tourPackage->id,
            404
        );
    }

    private function assertScheduleBelongsToOption(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionSchedule $tourOptionSchedule
    ): void {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        abort_unless(
            (int) $tourOptionSchedule->tour_option_id
                === (int) $tourOption->id,
            404
        );
    }

    private function normalizeDayOrder(
        TourOption $tourOption,
        int $dayOfWeek
    ): void {
        $schedules = $tourOption
            ->schedules()
            ->where(
                'day_of_week',
                $dayOfWeek
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach (
            $schedules as $index => $schedule
        ) {
            TourOptionSchedule::query()
                ->whereKey($schedule->id)
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
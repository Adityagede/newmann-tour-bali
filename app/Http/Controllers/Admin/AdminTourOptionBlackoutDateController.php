<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTourOptionBlackoutDateRequest;
use App\Models\TourOption;
use App\Models\TourOptionBlackoutDate;
use App\Models\TourPackage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

final class AdminTourOptionBlackoutDateController extends Controller
{
    public function index(
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $blackouts = $tourOption
            ->blackoutDates()
            ->orderBy('blackout_date')
            ->orderByDesc('blocks_entire_day')
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        $today = now(
            config('app.timezone')
        )->startOfDay();

        $activeCount = $blackouts
            ->where('is_active', true)
            ->count();

        $upcomingCount = $blackouts
            ->filter(
                fn (
                    TourOptionBlackoutDate $blackout
                ): bool =>
                    (bool) $blackout->is_active
                    && $blackout->blackout_date
                        ->startOfDay()
                        ->gte($today)
            )
            ->count();

        $pastCount = $blackouts
            ->filter(
                fn (
                    TourOptionBlackoutDate $blackout
                ): bool =>
                    $blackout->blackout_date
                        ->startOfDay()
                        ->lt($today)
            )
            ->count();

        return view(
            'admin.tour-packages.options.blackouts.index',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'blackouts' => $blackouts,
                'today' => $today,
                'activeCount' => $activeCount,
                'upcomingCount' => $upcomingCount,
                'pastCount' => $pastCount,
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
            'admin.tour-packages.options.blackouts.create',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionBlackoutDate' => null,
            ]
        );
    }

    public function store(
        SaveTourOptionBlackoutDateRequest $request,
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $validated = $request->validated();

        $blocksEntireDay = $request->boolean(
            'blocks_entire_day'
        );

        $isActive = $request->boolean(
            'is_active'
        );

        $startTime = $blocksEntireDay
            ? null
            : $this->databaseTime(
                $validated['start_time'] ?? null
            );

        if (
            !$blocksEntireDay
            && !$this->scheduleExistsForDateAndTime(
                $tourOption,
                $validated['blackout_date'],
                $startTime
            )
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' =>
                        'No active operating schedule matches '
                        . 'the selected date and starting time.',
                ]);
        }

        if (
            $isActive
            && $this->hasActiveConflict(
                $tourOption,
                $validated['blackout_date'],
                $blocksEntireDay,
                $startTime
            )
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'blackout_date' =>
                        'An active blackout already covers '
                        . 'this date or starting time.',
                ]);
        }

        $blackout = new TourOptionBlackoutDate();

        $blackout->forceFill(
            $this->payload(
                $request,
                $validated,
                $tourOption
            )
        );

        $blackout->save();

        return redirect()
            ->route(
                'admin.tour-packages.options.blackouts.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Blackout date has been added.'
            );
    }

    public function edit(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionBlackoutDate $tourOptionBlackoutDate
    ) {
        $this->assertBlackoutBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionBlackoutDate
        );

        return view(
            'admin.tour-packages.options.blackouts.edit',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionBlackoutDate' =>
                    $tourOptionBlackoutDate,
            ]
        );
    }

    public function update(
        SaveTourOptionBlackoutDateRequest $request,
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionBlackoutDate $tourOptionBlackoutDate
    ) {
        $this->assertBlackoutBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionBlackoutDate
        );

        $validated = $request->validated();

        $blocksEntireDay = $request->boolean(
            'blocks_entire_day'
        );

        $isActive = $request->boolean(
            'is_active'
        );

        $startTime = $blocksEntireDay
            ? null
            : $this->databaseTime(
                $validated['start_time'] ?? null
            );

        if (
            !$blocksEntireDay
            && !$this->scheduleExistsForDateAndTime(
                $tourOption,
                $validated['blackout_date'],
                $startTime
            )
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' =>
                        'No active operating schedule matches '
                        . 'the selected date and starting time.',
                ]);
        }

        if (
            $isActive
            && $this->hasActiveConflict(
                $tourOption,
                $validated['blackout_date'],
                $blocksEntireDay,
                $startTime,
                $tourOptionBlackoutDate->id
            )
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'blackout_date' =>
                        'Another active blackout already covers '
                        . 'this date or starting time.',
                ]);
        }

        $tourOptionBlackoutDate->forceFill(
            $this->payload(
                $request,
                $validated,
                $tourOption
            )
        );

        $tourOptionBlackoutDate->save();

        return redirect()
            ->route(
                'admin.tour-packages.options.blackouts.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Blackout date has been updated.'
            );
    }

    public function destroy(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionBlackoutDate $tourOptionBlackoutDate
    ) {
        $this->assertBlackoutBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionBlackoutDate
        );

        $tourOptionBlackoutDate->delete();

        return redirect()
            ->route(
                'admin.tour-packages.options.blackouts.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Blackout date has been removed.'
            );
    }

    private function payload(
        SaveTourOptionBlackoutDateRequest $request,
        array $validated,
        TourOption $tourOption
    ): array {
        $blocksEntireDay = $request->boolean(
            'blocks_entire_day'
        );

        return [
            'tour_option_id' =>
                $tourOption->id,

            'blackout_date' =>
                $validated['blackout_date'],

            'blocks_entire_day' =>
                $blocksEntireDay,

            /*
             * Full-day blackout tidak mempunyai
             * starting time tertentu.
             */
            'start_time' => $blocksEntireDay
                ? null
                : $this->databaseTime(
                    $validated['start_time']
                    ?? null
                ),

            'reason' =>
                $validated['reason'] ?? null,

            'internal_note' =>
                $validated['internal_note']
                ?? null,

            'is_active' =>
                $request->boolean('is_active'),
        ];
    }

    private function scheduleExistsForDateAndTime(
        TourOption $tourOption,
        string $date,
        ?string $startTime
    ): bool {
        if ($startTime === null) {
            return false;
        }

        $travelDate = Carbon::createFromFormat(
            'Y-m-d',
            $date,
            config('app.timezone')
        );

        return $tourOption
            ->schedules()
            ->where('is_active', true)
            ->where(
                'day_of_week',
                $travelDate->dayOfWeek
            )
            ->where(
                'start_time',
                $startTime
            )
            ->where(function (
                Builder $query
            ) use ($date): void {
                $query
                    ->whereNull('available_from')
                    ->orWhereDate(
                        'available_from',
                        '<=',
                        $date
                    );
            })
            ->where(function (
                Builder $query
            ) use ($date): void {
                $query
                    ->whereNull('available_until')
                    ->orWhereDate(
                        'available_until',
                        '>=',
                        $date
                    );
            })
            ->exists();
    }

    private function hasActiveConflict(
        TourOption $tourOption,
        string $date,
        bool $blocksEntireDay,
        ?string $startTime,
        ?int $ignoreId = null
    ): bool {
        $query = $tourOption
            ->blackoutDates()
            ->where('is_active', true)
            ->whereDate(
                'blackout_date',
                $date
            )
            ->when(
                $ignoreId !== null,
                fn (Builder $query) =>
                    $query->where(
                        'id',
                        '!=',
                        $ignoreId
                    )
            );

        /*
         * Full-day blackout bertabrakan dengan seluruh
         * blackout aktif lain pada tanggal yang sama.
         */
        if ($blocksEntireDay) {
            return $query->exists();
        }

        /*
         * Time-specific blackout bertabrakan dengan:
         * - full-day blackout pada tanggal yang sama
         * - time-specific blackout pada jam yang sama
         */
        return $query
            ->where(function (
                Builder $query
            ) use ($startTime): void {
                $query
                    ->where(
                        'blocks_entire_day',
                        true
                    )
                    ->orWhere(function (
                        Builder $query
                    ) use ($startTime): void {
                        $query
                            ->where(
                                'blocks_entire_day',
                                false
                            )
                            ->where(
                                'start_time',
                                $startTime
                            );
                    });
            })
            ->exists();
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

    private function assertBlackoutBelongsToOption(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionBlackoutDate $tourOptionBlackoutDate
    ): void {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        abort_unless(
            (int) $tourOptionBlackoutDate->tour_option_id
                === (int) $tourOption->id,
            404
        );
    }
}
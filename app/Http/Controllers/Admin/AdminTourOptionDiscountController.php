<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTourOptionDiscountRequest;
use App\Models\TourOption;
use App\Models\TourOptionDiscount;
use App\Models\TourPackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class AdminTourOptionDiscountController extends Controller
{
    public function index(
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $discounts = $tourOption
            ->discounts()
            ->orderByDesc('is_active')
            ->orderByDesc('priority')
            ->orderBy('id')
            ->get();

        $now = now(
            config('app.timezone')
        );



        $currentCount = $discounts
            ->filter(
                fn (
                    TourOptionDiscount $discount
                ): bool =>
                    $this->isCurrentlyValid(
                        $discount,
                        $now
                    )
            )
            ->count();

        return view(
            'admin.tour-packages.options.discounts.index',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'discounts' => $discounts,
                'now' => $now,

                'activeCount' => $discounts
                    ->where('is_active', true)
                    ->count(),

                'currentCount' => $currentCount,

                'participantTypes' =>
                    SaveTourOptionDiscountRequest
                        ::participantTypes(),
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
            'admin.tour-packages.options.discounts.create',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionDiscount' => null,

                'discountTypes' =>
                    SaveTourOptionDiscountRequest
                        ::discountTypes(),

                'participantTypes' =>
                    SaveTourOptionDiscountRequest
                        ::participantTypes(),
            ]
        );
    }

    public function store(
        SaveTourOptionDiscountRequest $request,
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $validated = $request->validated();

        $discount = new TourOptionDiscount();

        $discount->forceFill(
            $this->payload(
                $request,
                $validated,
                $tourOption
            )
        );

        $discount->save();

        return redirect()
            ->route(
                'admin.tour-packages.options.discounts.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Tour option discount has been added.'
            );
    }

    public function edit(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionDiscount $tourOptionDiscount
    ) {
        $this->assertDiscountBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionDiscount
        );

        return view(
            'admin.tour-packages.options.discounts.edit',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionDiscount' =>
                    $tourOptionDiscount,

                'discountTypes' =>
                    SaveTourOptionDiscountRequest
                        ::discountTypes(),

                'participantTypes' =>
                    SaveTourOptionDiscountRequest
                        ::participantTypes(),
            ]
        );
    }

    public function update(
        SaveTourOptionDiscountRequest $request,
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionDiscount $tourOptionDiscount
    ) {
        $this->assertDiscountBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionDiscount
        );

        $validated = $request->validated();

        DB::transaction(function () use (
            $request,
            $validated,
            $tourOption,
            $tourOptionDiscount
        ): void {
            $tourOptionDiscount->forceFill(
                $this->payload(
                    $request,
                    $validated,
                    $tourOption
                )
            );

            $tourOptionDiscount->save();
        });

        return redirect()
            ->route(
                'admin.tour-packages.options.discounts.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Tour option discount has been updated.'
            );
    }

    public function destroy(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionDiscount $tourOptionDiscount
    ) {
        $this->assertDiscountBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionDiscount
        );

        $tourOptionDiscount->delete();

        return redirect()
            ->route(
                'admin.tour-packages.options.discounts.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Tour option discount has been removed.'
            );
    }

    private function payload(
        SaveTourOptionDiscountRequest $request,
        array $validated,
        TourOption $tourOption
    ): array {
        $participantTypes = null;

        if (!$request->boolean('applies_to_all')) {
            $participantTypes = array_values(
                array_unique(
                    $validated[
                        'participant_types'
                    ] ?? []
                )
            );
        }

        return [
            'tour_option_id' => $tourOption->id,

            'label' =>
                $validated['label'],

            'discount_type' =>
                $validated['discount_type'],

            'discount_value' =>
                (int) $validated[
                    'discount_value'
                ],

            /*
             * null berarti berlaku untuk seluruh
             * peserta berbayar yang diperbolehkan.
             */
            'participant_types' =>
                $participantTypes,

            'starts_at' =>
                $this->databaseDateTime(
                    $validated['starts_at']
                    ?? null
                ),

            'ends_at' =>
                $this->databaseDateTime(
                    $validated['ends_at']
                    ?? null
                ),

            'priority' =>
                (int) $validated['priority'],

            'is_active' =>
                $request->boolean('is_active'),
        ];
    }

    private function databaseDateTime(
        ?string $value
    ): ?string {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::createFromFormat(
            'Y-m-d\TH:i',
            $value,
            config('app.timezone')
        )->format('Y-m-d H:i:s');
    }

    private function isCurrentlyValid(
        TourOptionDiscount $discount,
        Carbon $now
    ): bool {
        if (!$discount->is_active) {
            return false;
        }

        if (
            $discount->starts_at !== null
            && $discount->starts_at->gt($now)
        ) {
            return false;
        }

        if (
            $discount->ends_at !== null
            && $discount->ends_at->lt($now)
        ) {
            return false;
        }

        return true;
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

    private function assertDiscountBelongsToOption(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionDiscount $tourOptionDiscount
    ): void {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        abort_unless(
            (int) $tourOptionDiscount->tour_option_id
                === (int) $tourOption->id,
            404
        );
    }
}
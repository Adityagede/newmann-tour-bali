<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTourOptionPricesRequest;
use App\Models\TourOption;
use App\Models\TourOptionPrice;
use App\Models\TourPackage;
use Illuminate\Support\Facades\DB;

final class AdminTourOptionPriceController extends Controller
{
    public function edit(
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $pricesByType = $tourOption
            ->prices()
            ->orderBy('sort_order')
            ->get()
            ->keyBy('participant_type');

        return view(
            'admin.tour-packages.options.prices.edit',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,

                'pricesByType' => $pricesByType,

                'participantDefinitions' =>
                    UpdateTourOptionPricesRequest
                        ::participantDefinitions(),
            ]
        );
    }

    public function update(
        UpdateTourOptionPricesRequest $request,
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $validated = $request->validated();

        $participantDefinitions =
            UpdateTourOptionPricesRequest
                ::participantDefinitions();

        DB::transaction(function () use (
            $request,
            $validated,
            $tourOption,
            $participantDefinitions
        ): void {
            foreach (
                $participantDefinitions
                as $participantType => $definition
            ) {
                $row = $validated['prices'][
                    $participantType
                ];

                $isAllowed = $request->boolean(
                    "prices.{$participantType}.is_allowed"
                );

                $isFree = $request->boolean(
                    "prices.{$participantType}.is_free"
                );

                /*
                 * Kategori yang tidak diperbolehkan
                 * tidak dianggap gratis.
                 */
                if (!$isAllowed) {
                    $isFree = false;
                }

                /*
                 * Free participant selalu memiliki
                 * base_price nol.
                 */
                $basePrice = $isFree
                    ? 0
                    : max(
                        0,
                        (int) (
                            $row['base_price']
                            ?? 0
                        )
                    );

                /*
                 * Menyimpan ketiga kategori resmi.
                 * firstOrNew tidak bergantung pada fillable.
                 */
                $price = $tourOption
                    ->prices()
                    ->firstOrNew([
                        'participant_type' =>
                            $participantType,
                    ]);

                $price->forceFill([
                    'tour_option_id' =>
                        $tourOption->id,

                    'participant_type' =>
                        $participantType,

                    'label' =>
                        $definition['label'],

                    'age_min' =>
                        $definition['age_min'],

                    'age_max' =>
                        $definition['age_max'],

                    'base_price' =>
                        $basePrice,

                    'currency' => 'IDR',

                    'is_free' =>
                        $isFree,

                    'is_allowed' =>
                        $isAllowed,

                    'sort_order' =>
                        $definition['sort_order'],
                ]);

                $price->save();
            }
        });

        return redirect()
            ->route(
                'admin.tour-packages.options.prices.edit',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Participant prices have been updated.'
            );
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
}
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTourOptionItemRequest;
use App\Models\TourOption;
use App\Models\TourOptionItem;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class AdminTourOptionItemController extends Controller
{
    public function index(
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $includedItems = $tourOption
            ->items()
            ->where('item_type', 'included')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $excludedItems = $tourOption
            ->items()
            ->where('item_type', 'excluded')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'admin.tour-packages.options.items.index',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,

                'includedItems' => $includedItems,
                'excludedItems' => $excludedItems,

                'activeIncludedCount' =>
                    $includedItems
                        ->where('is_active', true)
                        ->count(),

                'activeExcludedCount' =>
                    $excludedItems
                        ->where('is_active', true)
                        ->count(),

                'itemTypes' =>
                    SaveTourOptionItemRequest::itemTypes(),

                'categories' =>
                    SaveTourOptionItemRequest::categories(),
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
            'admin.tour-packages.options.items.create',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionItem' => null,

                'itemTypes' =>
                    SaveTourOptionItemRequest::itemTypes(),

                'categories' =>
                    SaveTourOptionItemRequest::categories(),
            ]
        );
    }

    public function store(
        SaveTourOptionItemRequest $request,
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $validated = $request->validated();

        $nextSortOrder = (
            (int) (
                $tourOption
                    ->items()
                    ->where(
                        'item_type',
                        $validated['item_type']
                    )
                    ->max('sort_order')
                ?? 0
            )
        ) + 10;

        $item = new TourOptionItem();

        $item->forceFill(
            $this->payload(
                $request,
                $validated,
                $tourOption,
                $nextSortOrder
            )
        );

        $item->save();

        return redirect()
            ->route(
                'admin.tour-packages.options.items.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Option item has been added.'
            );
    }

    public function edit(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionItem $tourOptionItem
    ) {
        $this->assertItemBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionItem
        );

        return view(
            'admin.tour-packages.options.items.edit',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionItem' => $tourOptionItem,

                'itemTypes' =>
                    SaveTourOptionItemRequest::itemTypes(),

                'categories' =>
                    SaveTourOptionItemRequest::categories(),
            ]
        );
    }

    public function update(
        SaveTourOptionItemRequest $request,
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionItem $tourOptionItem
    ) {
        $this->assertItemBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionItem
        );

        $validated = $request->validated();

        $oldType = (string) $tourOptionItem->item_type;
        $newType = (string) $validated['item_type'];

        DB::transaction(function () use (
            $request,
            $validated,
            $tourOption,
            $tourOptionItem,
            $oldType,
            $newType
        ): void {
            $sortOrder = (int) $tourOptionItem->sort_order;

            if ($oldType !== $newType) {
                $sortOrder = (
                    (int) (
                        $tourOption
                            ->items()
                            ->where(
                                'item_type',
                                $newType
                            )
                            ->max('sort_order')
                        ?? 0
                    )
                ) + 10;
            }

            $tourOptionItem->forceFill(
                $this->payload(
                    $request,
                    $validated,
                    $tourOption,
                    $sortOrder
                )
            );

            $tourOptionItem->save();

            $this->normalizeTypeOrder(
                $tourOption,
                $oldType
            );

            if ($newType !== $oldType) {
                $this->normalizeTypeOrder(
                    $tourOption,
                    $newType
                );
            }
        });

        return redirect()
            ->route(
                'admin.tour-packages.options.items.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Option item has been updated.'
            );
    }

    public function move(
        Request $request,
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionItem $tourOptionItem
    ) {
        $this->assertItemBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionItem
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
            $tourOptionItem
        ): void {
            $items = $tourOption
                ->items()
                ->where(
                    'item_type',
                    $tourOptionItem->item_type
                )
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->values();

            $currentIndex = $items->search(
                fn (TourOptionItem $item): bool =>
                    (int) $item->id
                    === (int) $tourOptionItem->id
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
                || $targetIndex >= $items->count()
            ) {
                return;
            }

            $orderedItems = $items->all();

            [
                $orderedItems[$currentIndex],
                $orderedItems[$targetIndex],
            ] = [
                $orderedItems[$targetIndex],
                $orderedItems[$currentIndex],
            ];

            foreach (
                $orderedItems as $index => $item
            ) {
                TourOptionItem::query()
                    ->whereKey($item->id)
                    ->update([
                        'sort_order' =>
                            ($index + 1) * 10,
                    ]);
            }
        });

        return redirect()
            ->route(
                'admin.tour-packages.options.items.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Item order has been updated.'
            );
    }

    public function destroy(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionItem $tourOptionItem
    ) {
        $this->assertItemBelongsToOption(
            $tourPackage,
            $tourOption,
            $tourOptionItem
        );

        $itemType = (string) $tourOptionItem->item_type;

        DB::transaction(function () use (
            $tourOption,
            $tourOptionItem,
            $itemType
        ): void {
            $tourOptionItem->delete();

            $this->normalizeTypeOrder(
                $tourOption,
                $itemType
            );
        });

        return redirect()
            ->route(
                'admin.tour-packages.options.items.index',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Option item has been removed.'
            );
    }

    private function payload(
        SaveTourOptionItemRequest $request,
        array $validated,
        TourOption $tourOption,
        int $sortOrder
    ): array {
        return [
            'tour_option_id' => $tourOption->id,

            'item_type' =>
                $validated['item_type'],

            'category' =>
                $validated['category'],

            'label' =>
                $validated['label'],

            'details' =>
                $validated['details'] ?? null,

            'is_highlighted' =>
                $request->boolean(
                    'is_highlighted'
                ),

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),

            'sort_order' => $sortOrder,
        ];
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

    private function assertItemBelongsToOption(
        TourPackage $tourPackage,
        TourOption $tourOption,
        TourOptionItem $tourOptionItem
    ): void {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        abort_unless(
            (int) $tourOptionItem->tour_option_id
                === (int) $tourOption->id,
            404
        );
    }

    private function normalizeTypeOrder(
        TourOption $tourOption,
        string $itemType
    ): void {
        $items = $tourOption
            ->items()
            ->where('item_type', $itemType)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($items as $index => $item) {
            TourOptionItem::query()
                ->whereKey($item->id)
                ->update([
                    'sort_order' =>
                        ($index + 1) * 10,
                ]);
        }
    }
}
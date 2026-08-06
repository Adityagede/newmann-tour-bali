<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTourOptionRequest;
use App\Models\TourOption;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class AdminTourOptionController extends Controller
{
    public function index(
        TourPackage $tourPackage
    ) {
        $options = $tourPackage
            ->options()
            ->withCount([
                'prices',
                'items',
                'activeSchedules',
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'admin.tour-packages.options.index',
            [
                'tourPackage' => $tourPackage,
                'options' => $options,

                'activeCount' => $options
                    ->where('status', 'active')
                    ->count(),

                'draftCount' => $options
                    ->where('status', 'draft')
                    ->count(),
            ]
        );
    }

    public function create(
        TourPackage $tourPackage
    ) {
        return view(
            'admin.tour-packages.options.create',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => null,

                'pickupTypes' =>
                    SaveTourOptionRequest::pickupTypes(),
            ]
        );
    }

    public function store(
        SaveTourOptionRequest $request,
        TourPackage $tourPackage
    ) {
        $validated = $request->validated();

        /*
         * Option baru belum mempunyai prices,
         * schedules, dan included items.
         */
        if ($validated['status'] === 'active') {
            return back()
                ->withInput()
                ->withErrors([
                    'status' =>
                        'Create the option as Draft first. '
                        . 'Add participant prices, schedules, '
                        . 'and inclusions before activating it.',
                ]);
        }

        $tourOption = DB::transaction(
            function () use (
                $request,
                $validated,
                $tourPackage
            ): TourOption {
                $nextSortOrder = (int) (
                    $tourPackage
                        ->options()
                        ->max('sort_order')
                    ?? 0
                ) + 10;

                if ($request->boolean('is_default')) {
                    $tourPackage
                        ->options()
                        ->update([
                            'is_default' => false,
                        ]);
                }

                $tourOption = new TourOption();

                $tourOption->forceFill(
                    $this->payload(
                        $request,
                        $validated,
                        $tourPackage,
                        null,
                        $nextSortOrder
                    )
                );

                $tourOption->save();

                return $tourOption;
            }
        );

        return redirect()
            ->route(
                'admin.tour-packages.options.edit',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Tour option has been created as '
                . $tourOption->status
                . '. Configure its prices and availability next.'
            );
    }

    public function edit(
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $tourOption->loadCount([
            'prices',
            'items',
            'activeSchedules',
            'discounts',
            'activeBlackoutDates',
        ]);

        return view(
            'admin.tour-packages.options.edit',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,

                'pickupTypes' =>
                    SaveTourOptionRequest::pickupTypes(),
            ]
        );
    }

    public function update(
        SaveTourOptionRequest $request,
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        $validated = $request->validated();

        /*
         * Option hanya boleh diaktifkan setelah
         * konfigurasi pentingnya tersedia.
         */
        if ($validated['status'] === 'active') {
            $activationErrors =
                $this->activationErrors(
                    $tourOption,
                    $request->boolean(
                        'is_all_inclusive'
                    )
                );

            if ($activationErrors !== []) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'status' => implode(
                            ' ',
                            $activationErrors
                        ),
                    ]);
            }
        }

        DB::transaction(function () use (
            $request,
            $validated,
            $tourPackage,
            $tourOption
        ): void {
            if ($request->boolean('is_default')) {
                $tourPackage
                    ->options()
                    ->whereKeyNot(
                        $tourOption->id
                    )
                    ->update([
                        'is_default' => false,
                    ]);
            }

            $tourOption->forceFill(
                $this->payload(
                    $request,
                    $validated,
                    $tourPackage,
                    $tourOption,
                    (int) $tourOption->sort_order
                )
            );

            $tourOption->save();
        });

        return redirect()
            ->route(
                'admin.tour-packages.options.edit',
                [
                    'tourPackage' => $tourPackage,
                    'tourOption' => $tourOption,
                ]
            )
            ->with(
                'success',
                'Tour option has been updated.'
            );
    }

    public function move(
        Request $request,
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
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
            $tourOption
        ): void {
            $options = $tourPackage
                ->options()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->values();

            $currentIndex = $options->search(
                fn (TourOption $option): bool =>
                    (int) $option->id
                    === (int) $tourOption->id
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
                || $targetIndex >= $options->count()
            ) {
                return;
            }

            $orderedOptions = $options->all();

            [
                $orderedOptions[$currentIndex],
                $orderedOptions[$targetIndex],
            ] = [
                $orderedOptions[$targetIndex],
                $orderedOptions[$currentIndex],
            ];

            foreach (
                $orderedOptions as $index => $option
            ) {
                TourOption::query()
                    ->whereKey($option->id)
                    ->update([
                        'sort_order' =>
                            ($index + 1) * 10,
                    ]);
            }
        });

        return redirect()
            ->route(
                'admin.tour-packages.options.index',
                $tourPackage
            )
            ->with(
                'success',
                'Tour option order has been updated.'
            );
    }

    public function destroy(
        TourPackage $tourPackage,
        TourOption $tourOption
    ) {
        $this->assertOptionBelongsToTour(
            $tourPackage,
            $tourOption
        );

        if ($tourOption->status === 'active') {
            return back()->with(
                'error',
                'An active option cannot be archived. '
                . 'Change its status to Inactive first.'
            );
        }

        DB::transaction(function () use (
            $tourPackage,
            $tourOption
        ): void {
            $wasDefault =
                (bool) $tourOption->is_default;

            $tourOption->delete();

            $this->normalizeOptionOrder(
                $tourPackage
            );

            if ($wasDefault) {
                $nextOption = $tourPackage
                    ->options()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->first();

                if ($nextOption !== null) {
                    $nextOption->forceFill([
                        'is_default' => true,
                    ])->save();
                }
            }
        });

        return redirect()
            ->route(
                'admin.tour-packages.options.index',
                $tourPackage
            )
            ->with(
                'success',
                'Tour option has been archived.'
            );
    }

    private function payload(
        SaveTourOptionRequest $request,
        array $validated,
        TourPackage $tourPackage,
        ?TourOption $tourOption,
        int $sortOrder
    ): array {
        return [
            'tour_package_id' =>
                $tourPackage->id,

            'title' =>
                $validated['title'],

            'slug' => $this->uniqueSlug(
                $tourPackage,
                $validated['title'],
                $tourOption?->id
            ),

            'short_description' =>
                $validated[
                    'short_description'
                ] ?? null,

            'duration_minutes' =>
                isset($validated[
                    'duration_minutes'
                ])
                    ? (int) $validated[
                        'duration_minutes'
                    ]
                    : null,

            'languages' =>
                $this->parseLanguages(
                    $validated[
                        'languages_text'
                    ] ?? null
                ),

            'pickup_type' =>
                $validated['pickup_type'],

            'pickup_label' =>
                $validated[
                    'pickup_label'
                ] ?? null,

            'confirmation_note' =>
                $validated[
                    'confirmation_note'
                ] ?? null,

            'min_guests' =>
                (int) $validated['min_guests'],

            'max_guests' =>
                isset($validated['max_guests'])
                    ? (int) $validated[
                        'max_guests'
                    ]
                    : null,

            'is_all_inclusive' =>
                $request->boolean(
                    'is_all_inclusive'
                ),

            'is_default' =>
                $request->boolean(
                    'is_default'
                ),

            'sort_order' => $sortOrder,

            'status' =>
                $validated['status'],
        ];
    }

    private function parseLanguages(
        ?string $value
    ): array {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $languages = preg_split(
            '/[\r\n,]+/',
            $value
        ) ?: [];

        $result = [];

        foreach ($languages as $language) {
            $language = trim($language);

            if ($language === '') {
                continue;
            }

            $normalized = strtolower($language);

            if (array_key_exists(
                $normalized,
                $result
            )) {
                continue;
            }

            $result[$normalized] = $language;
        }

        return array_values($result);
    }

    private function uniqueSlug(
        TourPackage $tourPackage,
        string $title,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($title);

        if ($baseSlug === '') {
            $baseSlug = 'tour-option';
        }

        $slug = $baseSlug;
        $suffix = 2;

        while (
            TourOption::withTrashed()
                ->where(
                    'tour_package_id',
                    $tourPackage->id
                )
                ->where('slug', $slug)
                ->when(
                    $ignoreId !== null,
                    fn ($query) =>
                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        )
                )
                ->exists()
        ) {
            $slug = $baseSlug
                . '-'
                . $suffix;

            $suffix++;
        }

        return $slug;
    }

    private function activationErrors(
        TourOption $tourOption,
        bool $isAllInclusive
    ): array {
        $errors = [];

        if (
            !$tourOption
                ->prices()
                ->where('is_allowed', true)
                ->exists()
        ) {
            $errors[] =
                'Add at least one allowed participant price.';
        }

        if (
            !$tourOption
                ->activeSchedules()
                ->exists()
        ) {
            $errors[] =
                'Add at least one active operating schedule.';
        }

        if (
            $isAllInclusive
            && !$tourOption
                ->includedItems()
                ->exists()
        ) {
            $errors[] =
                'An all-inclusive option must have included items.';
        }

        return $errors;
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

    private function normalizeOptionOrder(
        TourPackage $tourPackage
    ): void {
        $options = $tourPackage
            ->options()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($options as $index => $option) {
            TourOption::query()
                ->whereKey($option->id)
                ->update([
                    'sort_order' =>
                        ($index + 1) * 10,
                ]);
        }
    }
}
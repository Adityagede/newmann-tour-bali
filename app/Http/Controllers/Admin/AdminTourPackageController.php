<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTourPackageRequest;
use App\Models\CustomTripRequest;
use App\Models\TourBookingRequest;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class AdminTourPackageController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $category = $request->query('category');

        $tourPackages = TourPackage::query()
            ->withCount('options')
            ->when(
                $status,
                fn ($query) =>
                    $query->where('status', $status)
            )
            ->when(
                $category,
                fn ($query) =>
                    $query->where(
                        'category',
                        $category
                    )
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => TourPackage::query()->count(),

            'active' => TourPackage::query()
                ->where('status', 'active')
                ->count(),

            'draft' => TourPackage::query()
                ->where('status', 'draft')
                ->count(),

            'popular' => TourPackage::query()
                ->where('is_popular', true)
                ->count(),

            'featured' => TourPackage::query()
                ->where('is_featured', true)
                ->count(),
        ];

        $categories = TourPackage::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view(
            'admin.tour-packages.index',
            [
                'tourPackages' => $tourPackages,
                'stats' => $stats,
                'status' => $status,
                'category' => $category,
                'categories' => $categories,
            ]
        );
    }

    public function create()
    {
        return view(
            'admin.tour-packages.create'
        );
    }

    public function store(
        SaveTourPackageRequest $request
    ) {
        $validated = $request->validated();

        $mainImagePath = $this->storeMainImage(
            $request->file('main_image')
        );

        try {
            $tourPackage = TourPackage::query()
                ->create(
                    $this->createPayload(
                        $request,
                        $validated,
                        $mainImagePath
                    )
                );
        } catch (Throwable $exception) {
            /*
             * Jangan meninggalkan file yatim jika
             * penyimpanan database gagal.
             */
            $this->deleteManagedImage(
                $mainImagePath
            );

            throw $exception;
        }

        return redirect()
            ->route(
                'admin.tour-packages.edit',
                $tourPackage
            )
            ->with(
                'success',
                'Tour product has been created as '
                . $tourPackage->status
                . '. Add its gallery and booking options next.'
            );
    }

    public function edit(
        TourPackage $tourPackage
    ) {
        $tourPackage->loadCount([
            'options',
            'activeOptions',
        ]);

        return view(
            'admin.tour-packages.edit',
            [
                'tourPackage' => $tourPackage,
            ]
        );
    }

    public function update(
        SaveTourPackageRequest $request,
        TourPackage $tourPackage
    ) {
        $validated = $request->validated();

        $oldImagePath = $tourPackage->main_image;
        $newImagePath = null;

        if ($request->hasFile('main_image')) {
            $newImagePath = $this->storeMainImage(
                $request->file('main_image')
            );
        }

        $mainImagePath = $newImagePath
            ?: $oldImagePath;

        try {
            $tourPackage->update(
                $this->updatePayload(
                    $request,
                    $validated,
                    $tourPackage,
                    $mainImagePath
                )
            );
        } catch (Throwable $exception) {
            /*
             * Image lama belum dihapus, sehingga update
             * yang gagal tidak merusak media sebelumnya.
             */
            $this->deleteManagedImage(
                $newImagePath
            );

            throw $exception;
        }

        if (
            $newImagePath !== null
            && $oldImagePath !== null
            && $oldImagePath !== $newImagePath
        ) {
            $this->deleteManagedImage(
                $oldImagePath
            );
        }

        return redirect()
            ->route(
                'admin.tour-packages.edit',
                $tourPackage
            )
            ->with(
                'success',
                'Tour product has been updated.'
            );
    }

    public function destroy(
        TourPackage $tourPackage
    ) {
        $hasV2Bookings = TourBookingRequest::query()
            ->where(
                'tour_package_id',
                $tourPackage->id
            )
            ->exists();

        /*
         * Preserve historical package records that may still exist in the
         * unchanged `bookings` table. New package bookings never write there.
         */
        $hasHistoricalPackageBookings =
            CustomTripRequest::query()
                ->where(
                    'tour_package_id',
                    $tourPackage->id
                )
                ->exists();

        if ($hasV2Bookings || $hasHistoricalPackageBookings) {
            return back()->with(
                'error',
                'This tour has booking records and cannot '
                . 'be deleted. Change its status to inactive.'
            );
        }

        if ($tourPackage->options()->exists()) {
            return back()->with(
                'error',
                'This tour already has booking options and '
                . 'cannot be deleted. Change its status to inactive.'
            );
        }

        $tourTitle = $tourPackage->title;
        $mainImagePath = $tourPackage->main_image;

        $tourPackage->delete();

        $this->deleteManagedImage(
            $mainImagePath
        );

        return redirect()
            ->route('admin.tour-packages.index')
            ->with(
                'success',
                "Tour product {$tourTitle} has been deleted."
            );
    }

    private function createPayload(
        SaveTourPackageRequest $request,
        array $validated,
        ?string $mainImagePath
    ): array {
        return array_merge(
            $this->baseProductPayload(
                $request,
                $validated,
                null,
                $mainImagePath
            ),
            [
                /*
                 * Nilai legacy aman untuk produk V2 baru.
                 * Harga asli nantinya berada di Tour Options.
                 */
                'gallery_images' => [],

                'vehicle' => null,
                'default_vehicle' => null,

                /*
                 * Nilai nol berarti belum mempunyai
                 * verified rating.
                 */
                'rating' => 0,
                'guests' => null,

                'price_text' => 'Request price',
                'pricing_type' => 'request_quote',

                'adult_price' => null,
                'child_price' => null,
                'vehicle_price' => null,

                'min_guests' => 1,
                'max_guests' => null,

                'transport_included' => false,
                'price_note' => null,
            ]
        );
    }

    private function updatePayload(
        SaveTourPackageRequest $request,
        array $validated,
        TourPackage $tourPackage,
        ?string $mainImagePath
    ): array {
        /*
         * Hanya field Tour Product yang diperbarui.
         *
         * Harga legacy, kendaraan legacy, dan data
         * pricing tour lama tidak ditimpa.
         */
        return $this->baseProductPayload(
            $request,
            $validated,
            $tourPackage,
            $mainImagePath
        );
    }

    private function baseProductPayload(
        SaveTourPackageRequest $request,
        array $validated,
        ?TourPackage $tourPackage,
        ?string $mainImagePath
    ): array {
        return [
            'title' => $validated['title'],

            'slug' => $this->uniqueSlug(
                $validated['title'],
                $tourPackage?->id
            ),

            'category' =>
                $validated['category'] ?? null,

            'badge' =>
                $validated['badge'] ?? null,

            'area' =>
                $validated['area'] ?? null,

            'duration' =>
                $validated['duration'] ?? null,

            'trip_type' =>
                $validated['trip_type'] ?? null,

            'tour_format' =>
                $validated['tour_format'],

            'description' =>
                $validated['description'] ?? null,

            'intro' =>
                $validated['intro'] ?? null,

            'story' =>
                $validated['story'] ?? null,

            'main_image' => $mainImagePath,

            'highlights' => $this->parseHighlights(
                $request->input(
                    'highlights_text',
                    ''
                )
            ),

            /*
             * Dipertahankan sementara sebagai fallback.
             * Step 13 mengelola TourStop terstruktur.
             */
            'itinerary' => $this->parseLegacyItinerary(
                $request->input(
                    'itinerary_text',
                    ''
                )
            ),

            'is_popular' =>
                $request->boolean('is_popular'),

            'is_featured' =>
                $request->boolean('is_featured'),

            'status' => $validated['status'],
        ];
    }

    private function uniqueSlug(
        string $title,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($title);

        if ($baseSlug === '') {
            $baseSlug = 'tour';
        }

        $slug = $baseSlug;
        $suffix = 2;

        while (
            TourPackage::query()
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

    private function parseHighlights(
        mixed $value
    ): array {
        return collect(
            preg_split(
                '/\r\n|\r|\n/',
                (string) $value
            ) ?: []
        )
            ->map(
                fn (string $line): string =>
                    trim($line)
            )
            ->filter()
            ->values()
            ->all();
    }

    private function parseLegacyItinerary(
        mixed $value
    ): array {
        return collect(
            preg_split(
                '/\r\n|\r|\n/',
                (string) $value
            ) ?: []
        )
            ->map(function (string $line): array {
                $parts = array_map(
                    'trim',
                    explode('|', $line, 3)
                );

                return [
                    'time' =>
                        $parts[0] ?? '',

                    'title' =>
                        $parts[1] ?? '',

                    'text' =>
                        $parts[2] ?? '',
                ];
            })
            ->filter(
                fn (array $item): bool =>
                    $item['time'] !== ''
                    || $item['title'] !== ''
                    || $item['text'] !== ''
            )
            ->values()
            ->all();
    }

    private function storeMainImage(
        ?UploadedFile $image
    ): ?string {
        if ($image === null) {
            return null;
        }

        $path = $image->store(
            'tour-packages',
            'public'
        );

        return 'storage/' . $path;
    }

    private function deleteManagedImage(
        ?string $imagePath
    ): void {
        if (
            $imagePath === null
            || !str_starts_with(
                $imagePath,
                'storage/'
            )
        ) {
            return;
        }

        Storage::disk('public')->delete(
            Str::after(
                $imagePath,
                'storage/'
            )
        );
    }
}
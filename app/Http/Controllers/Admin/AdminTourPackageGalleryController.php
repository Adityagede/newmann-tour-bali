<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTourGalleryRequest;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

final class AdminTourPackageGalleryController extends Controller
{
    private const MAX_GALLERY_IMAGES = 12;

    public function edit(
        TourPackage $tourPackage
    ) {
        return view(
            'admin.tour-packages.gallery',
            [
                'tourPackage' => $tourPackage,

                'galleryImages' =>
                    $this->normalizeGallery(
                        $tourPackage->gallery_images
                    ),

                'maximumImages' =>
                    self::MAX_GALLERY_IMAGES,
            ]
        );
    }

    public function update(
    UpdateTourGalleryRequest $request,
    TourPackage $tourPackage
) {
    $existingImages = $this->normalizeGallery(
        $tourPackage->gallery_images
    );

    $uploadedFiles = $request->file(
        'gallery_images',
        []
    );

    $uploadedFiles = array_values(
        array_filter($uploadedFiles)
    );

    if ($uploadedFiles === []) {
        return back()->withErrors([
            'gallery_images' =>
                'No gallery image reached the server. '
                . 'Please select the images again and retry.',
        ]);
    }

    if (
        count($existingImages)
        + count($uploadedFiles)
        > self::MAX_GALLERY_IMAGES
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'gallery_images' =>
                    'This tour gallery may contain a maximum of '
                    . self::MAX_GALLERY_IMAGES
                    . ' images.',
            ]);
    }

    // lanjutkan kode lama...

        $storedImages = [];

        try {
            foreach ($uploadedFiles as $image) {
                $path = $image->store(
                    'tour-packages/'
                    . $tourPackage->id
                    . '/gallery',
                    'public'
                );

                $storedImages[] =
                    'storage/' . $path;
            }

            $updatedGallery = array_values(
                array_merge(
                    $existingImages,
                    $storedImages
                )
            );

            /*
             * forceFill hanya digunakan pada field eksplisit
             * gallery_images agar tidak bergantung pada fillable.
             */
            $tourPackage->forceFill([
                'gallery_images' =>
                    $updatedGallery,
            ])->save();
        } catch (Throwable $exception) {
            foreach ($storedImages as $storedImage) {
                $this->deleteManagedImage(
                    $storedImage
                );
            }

            throw $exception;
        }

        return redirect()
            ->route(
                'admin.tour-packages.gallery.edit',
                $tourPackage
            )
            ->with(
                'success',
                count($storedImages)
                . ' gallery image(s) uploaded.'
            );
    }

    public function move(
        Request $request,
        TourPackage $tourPackage,
        string $imageIndex
    ) {
        $validated = $request->validate([
            'direction' => [
                'required',
                'in:up,down',
            ],
        ]);

        if (!ctype_digit($imageIndex)) {
            return back()->with(
                'error',
                'The selected gallery position is invalid.'
            );
        }

        $galleryImages = $this->normalizeGallery(
            $tourPackage->gallery_images
        );

        $currentIndex = (int) $imageIndex;

        if (!array_key_exists(
            $currentIndex,
            $galleryImages
        )) {
            return back()->with(
                'error',
                'The selected gallery image was not found.'
            );
        }

        $targetIndex =
            $validated['direction'] === 'up'
                ? $currentIndex - 1
                : $currentIndex + 1;

        if (!array_key_exists(
            $targetIndex,
            $galleryImages
        )) {
            return back();
        }

        [
            $galleryImages[$currentIndex],
            $galleryImages[$targetIndex],
        ] = [
            $galleryImages[$targetIndex],
            $galleryImages[$currentIndex],
        ];

        $tourPackage->forceFill([
            'gallery_images' =>
                array_values($galleryImages),
        ])->save();

        return redirect()
            ->route(
                'admin.tour-packages.gallery.edit',
                $tourPackage
            )
            ->with(
                'success',
                'Gallery order has been updated.'
            );
    }

    public function destroy(
        TourPackage $tourPackage,
        string $imageIndex
    ) {
        if (!ctype_digit($imageIndex)) {
            return back()->with(
                'error',
                'The selected gallery position is invalid.'
            );
        }

        $galleryImages = $this->normalizeGallery(
            $tourPackage->gallery_images
        );

        $index = (int) $imageIndex;

        if (!array_key_exists(
            $index,
            $galleryImages
        )) {
            return back()->with(
                'error',
                'The selected gallery image was not found.'
            );
        }

        $deletedImage = $galleryImages[$index];

        unset($galleryImages[$index]);

        /*
         * Database diperbarui dahulu.
         * File baru dihapus setelah database berhasil.
         */
        $tourPackage->forceFill([
            'gallery_images' =>
                array_values($galleryImages),
        ])->save();

        if (
            $deletedImage
            !== $tourPackage->main_image
        ) {
            $this->deleteManagedImage(
                $deletedImage
            );
        }

        return redirect()
            ->route(
                'admin.tour-packages.gallery.edit',
                $tourPackage
            )
            ->with(
                'success',
                'Gallery image has been removed.'
            );
    }

    private function normalizeGallery(
        mixed $value
    ): array {
        if (
            is_string($value)
            && trim($value) !== ''
        ) {
            $decoded = json_decode(
                $value,
                true
            );

            $value = is_array($decoded)
                ? $decoded
                : [];
        }

        if (!is_array($value)) {
            return [];
        }

        $images = [];

        foreach ($value as $item) {
            if (is_array($item)) {
                $item =
                    $item['path']
                    ?? $item['image']
                    ?? null;
            }

            if (!is_string($item)) {
                continue;
            }

            $path = trim($item);

            if ($path === '') {
                continue;
            }

            $images[] = $path;
        }

        return array_values(
            array_unique($images)
        );
    }

    private function deleteManagedImage(
        ?string $imagePath
    ): void {
        if (
            $imagePath === null
            || !str_starts_with(
                $imagePath,
                'storage/tour-packages/'
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
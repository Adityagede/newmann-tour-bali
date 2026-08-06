<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryMoment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminGalleryMomentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $category = $request->query('category');
        $search = $request->query('search');

        $galleryMoments = GalleryMoment::query()
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('caption', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => GalleryMoment::count(),
            'active' => GalleryMoment::where('status', 'active')->count(),
            'featured' => GalleryMoment::where('is_featured', true)->count(),
            'draft' => GalleryMoment::where('status', 'draft')->count(),
        ];

        $categories = GalleryMoment::query()
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.gallery.index', [
            'galleryMoments' => $galleryMoments,
            'stats' => $stats,
            'status' => $status,
            'category' => $category,
            'search' => $search,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:180'],
            'caption' => ['nullable', 'string', 'max:1200'],
            'category' => ['nullable', 'string', 'max:80'],
            'location' => ['nullable', 'string', 'max:120'],
            'alt_text' => ['nullable', 'string', 'max:180'],

            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'display_size' => ['required', 'in:regular,large'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', 'in:active,draft,inactive'],
            'is_featured' => ['nullable'],
        ]);

        $storedPath = $request
            ->file('image')
            ->store('gallery-moments', 'public');

        GalleryMoment::create([
            'title' => $validated['title'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'category' => $validated['category'] ?? null,
            'location' => $validated['location'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,

            'image_path' => 'storage/' . $storedPath,

            'display_size' => $validated['display_size'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Gallery moment has been created.');
    }

    public function edit(GalleryMoment $galleryMoment)
    {
        return view('admin.gallery.edit', [
            'galleryMoment' => $galleryMoment,
        ]);
    }

    public function update(
        Request $request,
        GalleryMoment $galleryMoment
    ) {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:180'],
            'caption' => ['nullable', 'string', 'max:1200'],
            'category' => ['nullable', 'string', 'max:80'],
            'location' => ['nullable', 'string', 'max:120'],
            'alt_text' => ['nullable', 'string', 'max:180'],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'display_size' => ['required', 'in:regular,large'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', 'in:active,draft,inactive'],
            'is_featured' => ['nullable'],
        ]);

        $imagePath = $galleryMoment->image_path;

        if ($request->hasFile('image')) {
            if (
                $galleryMoment->image_path &&
                str_starts_with(
                    $galleryMoment->image_path,
                    'storage/'
                )
            ) {
                Storage::disk('public')->delete(
                    str_replace(
                        'storage/',
                        '',
                        $galleryMoment->image_path
                    )
                );
            }

            $storedPath = $request
                ->file('image')
                ->store('gallery-moments', 'public');

            $imagePath = 'storage/' . $storedPath;
        }

        $galleryMoment->update([
            'title' => $validated['title'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'category' => $validated['category'] ?? null,
            'location' => $validated['location'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,

            'image_path' => $imagePath,

            'display_size' => $validated['display_size'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Gallery moment has been updated.');
    }

    public function destroy(GalleryMoment $galleryMoment)
    {
        $title = $galleryMoment->title ?: 'Gallery moment';

        if (
            $galleryMoment->image_path &&
            str_starts_with(
                $galleryMoment->image_path,
                'storage/'
            )
        ) {
            Storage::disk('public')->delete(
                str_replace(
                    'storage/',
                    '',
                    $galleryMoment->image_path
                )
            );
        }

        $galleryMoment->delete();

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', "{$title} has been deleted.");
    }
}
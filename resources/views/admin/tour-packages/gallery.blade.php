@extends('admin.layouts.app')

@section('content')
    @php
        /*
        |--------------------------------------------------------------------------
        | Gallery state
        |--------------------------------------------------------------------------
        */

        $galleryImages = is_array($galleryImages)
            ? array_values($galleryImages)
            : [];

        $currentCount = count($galleryImages);

        $remainingSlots = max(
            0,
            (int) $maximumImages - $currentCount
        );

        $placeholderImage = asset(
            'images/tour-placeholder.jpg'
        );
    @endphp

    <div class="mb-7 flex min-w-0 flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-newman-gold sm:tracking-[0.35em]">
                Tour Gallery
            </p>

            <h1 class="mt-3 max-w-4xl break-words text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                {{ $tourPackage->title }}
            </h1>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                These images appear inside this Tour Package detail page.
                The image shown on Home and Browse Tours remains the
                <strong>Main Image</strong> from the Tour Product form.
            </p>
        </div>

        <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
            <a
                href="{{ route('admin.tour-packages.edit', $tourPackage) }}"
                class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.14em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:border-newman-gold hover:bg-newman-gold"
            >
                Back to product
            </a>

            <a
                href="{{ route('admin.tour-packages.index') }}"
                class="bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.14em] text-white transition duration-300 hover:-translate-y-0.5 hover:bg-newman-gold hover:text-newman-navy"
            >
                All tours
            </a>
        </div>
    </div>

    {{-- Success message --}}
    @if (session('success'))
        <div class="mb-6 border border-emerald-200 bg-emerald-50 p-4 text-sm leading-6 text-emerald-800">
            <p class="font-semibold">
                Gallery updated successfully.
            </p>

            <p class="mt-1">
                {{ session('success') }}
            </p>
        </div>
    @endif

    {{-- Error message --}}
    @if (session('error'))
        <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm leading-6 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">
                Gallery images could not be uploaded.
            </p>

            <ul class="mt-3 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid min-w-0 items-start gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
        {{-- Upload sidebar --}}
        <aside class="min-w-0 space-y-6 xl:sticky xl:top-28">
            <section class="min-w-0 overflow-hidden border border-newman-gold/25 bg-newman-navy p-5 text-white shadow-2xl shadow-newman-navy/15 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-newman-gold sm:tracking-[0.3em]">
                    Upload images
                </p>

                <h2 class="mt-3 break-words text-2xl font-semibold tracking-[-0.03em]">
                    Add to this gallery
                </h2>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="min-w-0 border border-white/10 bg-white/5 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-white/45">
                            Current
                        </p>

                        <p class="mt-2 text-2xl font-semibold">
                            {{ $currentCount }}
                        </p>
                    </div>

                    <div class="min-w-0 border border-white/10 bg-white/5 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-white/45">
                            Remaining
                        </p>

                        <p class="mt-2 text-2xl font-semibold">
                            {{ $remainingSlots }}
                        </p>
                    </div>
                </div>

                @if ($remainingSlots > 0)
                    <form
                        action="{{ route(
                            'admin.tour-packages.gallery.update',
                            $tourPackage
                        ) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="mt-6 min-w-0"
                        x-data="{
                            selectedFiles: [],
                            isSubmitting: false,
                            maximumFiles: {{ $remainingSlots }},

                            formatSize(bytes) {
                                if (bytes < 1024) {
                                    return `${bytes} B`;
                                }

                                if (bytes < 1024 * 1024) {
                                    return `${(bytes / 1024).toFixed(1)} KB`;
                                }

                                return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
                            },

                            handleFiles(event) {
                                const files = Array.from(
                                    event.target.files || []
                                );

                                if (files.length > this.maximumFiles) {
                                    window.alert(
                                        `Only ${this.maximumFiles} gallery slot(s) remain.`
                                    );

                                    event.target.value = '';
                                    this.selectedFiles = [];

                                    return;
                                }

                                this.selectedFiles = files.map(
                                    (file) => ({
                                        name: file.name,
                                        size: this.formatSize(file.size),
                                    })
                                );
                            },

                            submitUpload(event) {
                                if (this.selectedFiles.length < 1) {
                                    event.preventDefault();

                                    window.alert(
                                        'Please select at least one gallery image.'
                                    );

                                    return;
                                }

                                this.isSubmitting = true;
                            },
                        }"
                        x-on:submit="submitUpload($event)"
                    >
                        @csrf
                        @method('PUT')

                        <label
                            for="gallery_images"
                            class="block text-sm font-semibold"
                        >
                            Gallery images
                        </label>

                        <input
                            id="gallery_images"
                            x-ref="galleryInput"
                            x-on:change="handleFiles($event)"
                            type="file"
                            name="gallery_images[]"
                            multiple
                            required
                            accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                            class="mt-3 block w-full min-w-0 text-xs text-white/65 file:mr-3 file:cursor-pointer file:border-0 file:bg-newman-gold file:px-4 file:py-3 file:text-xs file:font-bold file:uppercase file:tracking-[0.1em] file:text-newman-navy"
                        >

                        <p class="mt-3 break-words text-xs leading-6 text-white/55">
                            Select JPG, PNG, or WebP images. Each image may
                            be a maximum of 8 MB. You currently have
                            {{ $remainingSlots }} available gallery slot(s).
                        </p>

                        {{-- Selected file information --}}
                        <div
                            x-show="selectedFiles.length > 0"
                            x-cloak
                            class="mt-4 min-w-0 border border-white/10 bg-white/5 p-4"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-newman-gold">
                                    Selected files
                                </p>

                                <p class="shrink-0 text-xs text-white/55">
                                    <span x-text="selectedFiles.length"></span>
                                    file(s)
                                </p>
                            </div>

                            <div class="mt-3 space-y-2">
                                <template
                                    x-for="(file, fileIndex) in selectedFiles"
                                    :key="`${file.name}-${fileIndex}`"
                                >
                                    <div class="min-w-0 border border-white/10 bg-newman-navy/40 p-3">
                                        <p
                                            class="break-words text-xs font-semibold text-white [overflow-wrap:anywhere]"
                                            x-text="file.name"
                                        ></p>

                                        <p
                                            class="mt-1 text-[10px] text-white/45"
                                            x-text="file.size"
                                        ></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <button
                            type="submit"
                            x-bind:disabled="
                                selectedFiles.length === 0
                                || isSubmitting
                            "
                            class="mt-5 flex w-full items-center justify-center bg-newman-gold px-5 py-4 text-center text-sm font-bold uppercase tracking-[0.14em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:bg-white disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <span
                                x-show="!isSubmitting"
                            >
                                Upload gallery images
                            </span>

                            <span
                                x-show="isSubmitting"
                                x-cloak
                            >
                                Uploading...
                            </span>
                        </button>
                    </form>
                @else
                    <div class="mt-6 border border-newman-gold/25 bg-newman-gold/10 p-4 text-xs leading-6 text-white/70">
                        This gallery has reached its maximum capacity.
                        Remove an existing image before uploading another.
                    </div>
                @endif
            </section>

            <section class="min-w-0 overflow-hidden border border-newman-gold/30 bg-newman-sand p-5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-newman-gold sm:tracking-[0.3em]">
                    Gallery usage
                </p>

                <ul class="mt-4 space-y-3 text-xs leading-6 text-gray-600">
                    <li>
                        • Main Image controls the Home and Browse Tours card.
                    </li>

                    <li>
                        • Gallery Images appear inside Tour Detail.
                    </li>

                    <li>
                        • Uploading gallery images does not replace Main Image.
                    </li>
                </ul>
            </section>

            <section class="min-w-0 overflow-hidden border border-newman-gold/30 bg-newman-sand p-5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-newman-gold sm:tracking-[0.3em]">
                    Publishing safety
                </p>

                <p class="mt-3 text-xs leading-6 text-gray-600">
                    Keep the Tour Product in Draft while gallery, roadmap,
                    options, prices, schedules, and inclusions remain
                    incomplete.
                </p>

                <div class="mt-4 bg-white p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                        Current status
                    </p>

                    <p class="mt-2 font-semibold capitalize text-newman-navy">
                        {{ $tourPackage->status }}
                    </p>
                </div>
            </section>
        </aside>

        {{-- Existing images --}}
        <main class="min-w-0">
            <section class="min-w-0 overflow-hidden border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-7">
                <div class="flex min-w-0 flex-col gap-3 border-b border-gray-100 pb-5 sm:flex-row sm:items-end sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-newman-gold sm:tracking-[0.3em]">
                            Gallery order
                        </p>

                        <h2 class="mt-2 break-words text-2xl font-semibold tracking-[-0.03em] text-newman-navy">
                            Tour detail images
                        </h2>
                    </div>

                    <p class="max-w-sm text-xs leading-6 text-gray-500">
                        These images follow the order below. Use Move Earlier
                        or Move Later to change their position.
                    </p>
                </div>

                @if ($galleryImages === [])
                    <div class="mt-6 flex min-h-72 items-center justify-center border border-dashed border-newman-navy/20 bg-newman-sand/50 p-6 text-center sm:p-8">
                        <div>
                            <p class="text-lg font-semibold text-newman-navy">
                                No gallery images yet
                            </p>

                            <p class="mt-3 max-w-md text-sm leading-7 text-gray-500">
                                Upload at least one image. After a successful
                                upload, the Current counter and image list
                                should update immediately.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="mt-6 grid min-w-0 gap-5 sm:grid-cols-2 2xl:grid-cols-3">
                        @foreach ($galleryImages as $index => $imagePath)
                            @php
                                /*
                                 * Use the same URL normalizer used by
                                 * Tour Package public pages.
                                 */
                                $imageUrl = \App\Support\TourViewData::imageUrl(
                                    is_string($imagePath)
                                        ? $imagePath
                                        : null
                                );
                            @endphp

                            <article class="min-w-0 overflow-hidden border border-gray-100 bg-white shadow-sm">
                                <div class="relative overflow-hidden bg-newman-sand">
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="{{ $tourPackage->title }} gallery image {{ $index + 1 }}"
                                        class="aspect-[4/3] w-full object-cover"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='{{ $placeholderImage }}';"
                                    >

                                    <span class="absolute left-3 top-3 bg-newman-navy px-3 py-2 text-[10px] font-bold uppercase tracking-[0.14em] text-white">
                                        Position {{ $index + 1 }}
                                    </span>
                                </div>

                                <div class="min-w-0 p-4">
                                    <p
                                        class="break-words text-xs text-gray-500 [overflow-wrap:anywhere]"
                                        title="{{ is_string($imagePath) ? $imagePath : '' }}"
                                    >
                                        {{ is_string($imagePath)
                                            ? basename($imagePath)
                                            : 'Gallery image'
                                        }}
                                    </p>

                                    <a
                                        href="{{ $imageUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="mt-3 inline-flex text-[10px] font-bold uppercase tracking-[0.12em] text-newman-blue hover:text-newman-gold"
                                    >
                                        Open image
                                    </a>

                                    <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                        <form
                                            action="{{ route(
                                                'admin.tour-packages.gallery.move',
                                                [
                                                    'tourPackage' =>
                                                        $tourPackage,

                                                    'imageIndex' =>
                                                        $index,
                                                ]
                                            ) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="direction"
                                                value="up"
                                            >

                                            <button
                                                type="submit"
                                                @disabled($index === 0)
                                                class="w-full border border-newman-navy/15 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.1em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-sand disabled:cursor-not-allowed disabled:opacity-35"
                                            >
                                                Move earlier
                                            </button>
                                        </form>

                                        <form
                                            action="{{ route(
                                                'admin.tour-packages.gallery.move',
                                                [
                                                    'tourPackage' =>
                                                        $tourPackage,

                                                    'imageIndex' =>
                                                        $index,
                                                ]
                                            ) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="direction"
                                                value="down"
                                            >

                                            <button
                                                type="submit"
                                                @disabled(
                                                    $index
                                                    === count($galleryImages) - 1
                                                )
                                                class="w-full border border-newman-navy/15 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.1em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-sand disabled:cursor-not-allowed disabled:opacity-35"
                                            >
                                                Move later
                                            </button>
                                        </form>
                                    </div>

                                    <form
                                        action="{{ route(
                                            'admin.tour-packages.gallery.destroy',
                                            [
                                                'tourPackage' =>
                                                    $tourPackage,

                                                'imageIndex' =>
                                                    $index,
                                            ]
                                        ) }}"
                                        method="POST"
                                        class="mt-2"
                                        onsubmit="return confirm(
                                            'Remove this image from the tour gallery?'
                                        )"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="w-full border border-red-200 bg-red-50 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.1em] text-red-700 transition hover:bg-red-100"
                                        >
                                            Remove image
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </main>
    </div>
@endsection
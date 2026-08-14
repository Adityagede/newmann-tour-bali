@extends('layouts.app')

@section('content')
@php
    $placeholder = asset(
        'images/tour-placeholder.jpg'
    );

    /*
     * Membuat URL gambar tetap aman, baik ketika
     * controller memberikan URL penuh maupun path lokal.
     */
    $resolveGalleryImage = static function (
        mixed $image
    ): ?string {
        if (is_array($image)) {
            $image = $image['url']
                ?? $image['path']
                ?? $image['image']
                ?? null;
        }

        if (!is_string($image)) {
            return null;
        }

        $image = trim($image);

        if ($image === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $image)) {
            return $image;
        }

        if (str_starts_with($image, '/')) {
            return url($image);
        }

        return asset(ltrim($image, '/'));
    };

    $gallerySource = $tour['gallery']
        ?? $tour['gallery_images']
        ?? $tour['images']
        ?? [];

    if (
        $gallerySource instanceof
        \Illuminate\Support\Collection
    ) {
        $gallerySource = $gallerySource
            ->values()
            ->all();
    }

    if (!is_array($gallerySource)) {
        $gallerySource = [];
    }

    /*
     * Main image tetap menjadi kandidat foto pertama.
     * unique() mencegah gambar yang sama dihitung dua kali.
     */
    $gallery = collect([
        $tour['main_image']
            ?? $tour['image']
            ?? null,
    ])
        ->merge($gallerySource)
        ->map($resolveGalleryImage)
        ->filter()
        ->unique(
            fn (string $image): string =>
                strtolower($image)
        )
        ->values();

    if ($gallery->isEmpty()) {
        $gallery->push($placeholder);
    }

    $mainPhoto = $gallery->first()
        ?: $placeholder;

    /*
     * Foto utama + maksimal empat thumbnail
     * menghasilkan lima foto pada mosaic.
     */
    $sidePhotos = $gallery
        ->slice(1, 4)
        ->values();

    $totalPhotos = $gallery->count();
    $sidePhotoCount = $sidePhotos->count();

    /*
     * Layout tetap rapi ketika jumlah foto
     * samping hanya satu, dua, tiga, atau empat.
     */
    $sideGridClass = match ($sidePhotoCount) {
        1 => 'grid-cols-1 grid-rows-1',
        2 => 'grid-cols-1 grid-rows-2',
        default => 'grid-cols-2 grid-rows-2',
    };
@endphp

<main class="bg-white">
    {{-- Tour Heading --}}
    <section class="border-b border-newman-navy/10 bg-white pt-32 sm:pt-36">
        <div class="mx-auto w-[92%] max-w-7xl pb-8 sm:pb-10">
            <a
                href="{{ route('tours') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-newman-navy transition hover:text-newman-blue"
            >
                <span>←</span>
                Back to tours
            </a>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <span class="rounded-full bg-newman-sand px-4 py-2 text-[11px] font-bold uppercase tracking-[0.16em] text-newman-navy">
                    {{ $tour['badge'] }}
                </span>
        

                <span class="text-sm text-gray-500">
                    {{ $tour['area'] }}
                </span>

                <span class="h-1 w-1 rounded-full bg-gray-300"></span>

                <span class="text-sm text-gray-500">
                    {{ $tour['trip_type'] }}
                </span>
            </div>

            <h1 class="mt-5 max-w-5xl text-4xl font-semibold leading-[1.08] tracking-[-0.05em] text-newman-navy sm:text-5xl lg:text-6xl">
                {{ $tour['title'] }}
            </h1>

            <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
                @if (! empty($tour['has_rating']) && isset($tour['rating']))
                    <span class="font-semibold text-newman-navy">
                        <span class="text-newman-gold" aria-hidden="true">★</span>
                        {{ number_format($tour['rating'], 1) }}
                    </span>

                    <span class="text-gray-500">
                        {{ $tour['rating_text'] ?? '' }}
                    </span>
                @else
                    <span class="text-gray-500">New tour</span>
                @endif

                @if (! empty($tour['hosted_guest_text']))
                    <span class="h-1 w-1 rounded-full bg-gray-300" aria-hidden="true"></span>

                    <span class="text-gray-500">
                        {{ $tour['hosted_guest_text'] }}
                    </span>
                @endif
            </div>
        </div>
    </section>

    {{-- Gallery and Availability --}}
<section class="py-8 sm:py-10 lg:py-12">
    <div
        class="mx-auto grid w-[92%] max-w-7xl gap-8
            lg:grid-cols-[minmax(0,1fr)_390px]
            lg:items-start"
    >
        <div class="min-w-0">
            {{-- Interactive Tour Gallery --}}
            <x-tour-gallery-showcase
                :images="$gallery ?? ($tour['gallery'] ?? [])"
                :title="$tour['title']"
                availability-target="tour-availability"
            />

            @if ($tour['intro'])
                <p class="mt-6 max-w-4xl text-base leading-8 text-gray-600 sm:text-lg">
                    {{ $tour['intro'] }}
                </p>
            @endif

            {{-- Tour Quick Information --}}
            <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl bg-newman-sand/70 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-newman-gold">
                        Duration
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ $tour['duration'] }}
                    </p>
                </div>

                <div class="rounded-2xl bg-newman-sand/70 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-newman-gold">
                        Pickup
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ $tour['pickup_text'] }}
                    </p>
                </div>

                <div class="rounded-2xl bg-newman-sand/70 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-newman-gold">
                        Vehicle
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ $tour['vehicle'] }}
                    </p>
                </div>

                <div class="rounded-2xl bg-newman-sand/70 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-newman-gold">
                        Confirmation
                    </p>

                    <p class="mt-2 font-semibold text-newman-navy">
                        {{ $tour['confirmation_text'] }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Availability Panel --}}
        <x-tour-availability-panel
            :tour="$tour"
            :preview="$isPreview ?? false"
        />
    </div>
</section>

{{-- Available Options --}}
<x-tour-option-results
    :tour="$tour"
    :preview="$isPreview ?? false"
/>

    {{-- Tour Content --}}
    <section class="border-t border-newman-navy/10 bg-newman-sand/35 py-16 sm:py-20">
        <div class="mx-auto w-[92%] max-w-5xl">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-blue">
                    About this tour
                </p>

                <h2 class="mt-4 break-words text-3xl font-semibold leading-tight tracking-[-0.04em] text-newman-navy sm:text-5xl">
    {{ $tour['about_heading'] }}
</h2>

                @if ($tour['story'])
                    <p class="mt-6 max-w-4xl text-base leading-8 text-gray-600">
                        {{ $tour['story'] }}
                    </p>
                @endif
            </div>

            @if (! empty($tour['highlights']))
                <div class="mt-10">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                        Highlights
                    </p>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        @foreach ($tour['highlights'] as $highlight)
                            <div class="flex gap-3 rounded-2xl border border-newman-navy/8 bg-white p-4">
                                <span class="text-newman-gold">✓</span>
                                <p class="text-sm leading-6 text-newman-navy">{{ $highlight }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @php
    $roadmapItems = collect(
        $tour['itinerary'] ?? []
    )
        ->filter(function ($item): bool {
            return is_array($item)
                && (
                    ! empty($item['title'])
                    || ! empty($item['text'])
                    || ! empty($item['location'])
                );
        })
        ->values();

    $mapStops = $roadmapItems
        ->filter(function ($item): bool {
            return ! empty($item['show_on_map'])
                && is_numeric($item['latitude'] ?? null)
                && is_numeric($item['longitude'] ?? null);
        })
        ->map(function ($item, $index): array {
            return [
                'order' => $index + 1,
                'id' => $item['id'] ?? null,
                'title' => $item['title'] ?? 'Tour stop',
                'location' => $item['location'] ?? '',
                'address' => $item['address'] ?? '',
                'time' => $item['time'] ?? '',
                'duration' => $item['duration'] ?? '',
                'latitude' => (float) $item['latitude'],
                'longitude' => (float) $item['longitude'],
            ];
        })
        ->values();

    $roadmapMapId = 'newman-roadmap-map-'
        . ($tour['id'] ?? 'tour');
@endphp

<section
    id="tour-itinerary"
    class="border-y border-newman-navy/10 bg-[#faf8f3] py-14 sm:py-18 lg:py-20"
>
    <div class="mx-auto w-[calc(100%-2rem)] max-w-6xl sm:w-[92%]">
        {{-- Heading --}}
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Itinerary
                </p>

                <h2 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-4xl">
                    Follow the route, stop by stop.
                </h2>

                <p class="mt-4 max-w-2xl text-sm leading-7 text-gray-600">
                    The exact order may change depending on pickup location,
                    traffic, weather, ceremonies, and local conditions.
                </p>
            </div>

            @if ($roadmapItems->isNotEmpty())
                <div class="shrink-0 border border-newman-navy/10 bg-white px-5 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-400">
                        Planned route
                    </p>

                    <p class="mt-1 font-semibold text-newman-navy">
                        {{ $roadmapItems->count() }}
                        {{ $roadmapItems->count() === 1 ? 'stop' : 'stops' }}
                    </p>
                </div>
            @endif
        </div>

        @if ($roadmapItems->isEmpty())
            <div class="mt-8 border border-dashed border-newman-navy/20 bg-white p-8 text-center">
                <p class="font-semibold text-newman-navy">
                    The itinerary is being prepared.
                </p>

                <p class="mt-2 text-sm leading-7 text-gray-500">
                    Newman will confirm the route after reviewing the selected
                    date, option, and pickup location.
                </p>
            </div>
        @else
            <div
                data-roadmap-map="{{ $roadmapMapId }}"
                class="mt-8 grid min-w-0 gap-6 lg:grid-cols-[390px_minmax(0,1fr)] xl:grid-cols-[430px_minmax(0,1fr)]"
            >
                {{-- Compact itinerary list --}}
                <div
                    class="newman-roadmap-list min-w-0 space-y-1 lg:max-h-[520px] lg:overflow-y-auto lg:pr-2"
                >
                    @foreach ($roadmapItems as $item)
    @php
        $stopNumber = str_pad(
            (string) $loop->iteration,
            2,
            '0',
            STR_PAD_LEFT
        );

        /*
        |--------------------------------------------------------------------------
        | Matching mapped stop
        |--------------------------------------------------------------------------
        |
        | Timeline berisi seluruh stop, sedangkan $mapStops hanya berisi
        | stop yang mempunyai koordinat dan Show on map aktif.
        |
        | Karena itu jangan menggunakan $loop->index sebagai map index.
        | Cari posisi item yang benar di dalam $mapStops.
        |
        */

        $mapStopIndex = $mapStops->search(
            function ($mapStop) use ($item): bool {
                $itemId = data_get($item, 'id');
                $mapStopId = data_get(
                    $mapStop,
                    'id'
                );

                /*
                 * Gunakan database ID sebagai penghubung utama.
                 */
                if (
                    $itemId !== null
                    && $mapStopId !== null
                ) {
                    return (string) $itemId
                        === (string) $mapStopId;
                }

                /*
                 * Fallback aman apabila ID tidak ikut dimasukkan
                 * saat roadmap dinormalisasi.
                 */
                $itemTitle = mb_strtolower(
                    trim(
                        (string) data_get(
                            $item,
                            'title'
                        )
                    )
                );

                $mapStopTitle = mb_strtolower(
                    trim(
                        (string) data_get(
                            $mapStop,
                            'title'
                        )
                    )
                );

                $itemLocation = mb_strtolower(
                    trim(
                        (string) data_get(
                            $item,
                            'location'
                        )
                    )
                );

                $mapStopLocation = mb_strtolower(
                    trim(
                        (string) data_get(
                            $mapStop,
                            'location'
                        )
                    )
                );

                return $itemTitle !== ''
                    && $itemTitle === $mapStopTitle
                    && $itemLocation === $mapStopLocation;
            }
        );

        $hasMapStop = $mapStopIndex !== false;

        $stopType = strtolower(
            (string) ($item['type'] ?? '')
        );

                            $isTransport = str_contains(
                                $stopType,
                                'pickup'
                            )
                                || str_contains(
                                    $stopType,
                                    'transfer'
                                )
                                || str_contains(
                                    $stopType,
                                    'drop'
                                );
                        @endphp

                        <button
    type="button"
    @if ($hasMapStop)
        data-roadmap-stop-index="{{ $mapStopIndex }}"
    @endif
    class="newman-roadmap-row group relative flex w-full min-w-0 gap-4 border border-transparent px-3 py-4 text-left transition duration-200 hover:border-newman-gold/30 hover:bg-white"
>
                            {{-- Timeline column --}}
                            <span class="relative flex w-10 shrink-0 justify-center">
                                @unless ($loop->last)
                                    <span
                                        aria-hidden="true"
                                        class="absolute left-1/2 top-9 h-[calc(100%+20px)] w-px -translate-x-1/2 bg-newman-gold/35"
                                    ></span>
                                @endunless

                                <span
                                    class="relative z-10 flex h-9 w-9 items-center justify-center rounded-full border-2 border-[#faf8f3] bg-newman-navy text-newman-gold shadow-md"
                                >
                                    @if ($isTransport)
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            class="h-4 w-4"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M4 16V9a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v7M5 13h14M7 16v2M17 16v2"
                                            />
                                            <circle cx="7" cy="14.5" r="1" />
                                            <circle cx="17" cy="14.5" r="1" />
                                        </svg>
                                    @else
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            class="h-4 w-4"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"
                                            />
                                            <circle cx="12" cy="10" r="2.5" />
                                        </svg>
                                    @endif
                                </span>
                            </span>

                            {{-- Text --}}
                            <span class="min-w-0 flex-1">
                                <span class="flex min-w-0 flex-wrap items-center gap-x-3 gap-y-1">
                                    <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-newman-gold">
                                        Stop {{ $stopNumber }}
                                    </span>

                                    @if (! empty($item['time']))
                                        <span class="text-xs font-semibold text-newman-blue">
                                            {{ $item['time'] }}
                                        </span>
                                    @endif

                                    @if (! empty($item['duration']))
                                        <span class="text-xs text-gray-400">
                                            {{ $item['duration'] }}
                                        </span>
                                    @endif
                                </span>

                                <span class="mt-1 block break-words text-base font-semibold leading-6 text-newman-navy">
                                    {{ $item['title'] ?: 'Tour stop' }}
                                </span>

                                @if (! empty($item['location']))
                                    <span class="mt-1 block break-words text-xs font-medium text-gray-500">
                                        {{ $item['location'] }}
                                    </span>
                                @endif

                                @if (! empty($item['text']))
                                    <span class="newman-roadmap-summary mt-2 block text-sm leading-6 text-gray-500">
                                        {{ $item['text'] }}
                                    </span>
                                @endif
                            </span>

                            <span
                                aria-hidden="true"
                                class="mt-2 shrink-0 text-sm text-newman-gold transition group-hover:translate-x-1"
                            >
                                →
                            </span>
                        </button>
                    @endforeach
                </div>

                {{-- Map --}}
                <div class="min-w-0 lg:sticky lg:top-28 lg:self-start">
                    <div class="newman-roadmap-map-shell min-w-0 border border-newman-navy/10 bg-newman-sand shadow-lg shadow-newman-navy/8">
                        @if ($mapStops->isNotEmpty())
                            <div
                                id="{{ $roadmapMapId }}"
                                class="newman-roadmap-map h-[320px] w-full sm:h-[420px] lg:h-[520px]"
                                aria-label="Tour itinerary map"
                            ></div>

                            <div class="newman-roadmap-map-label pointer-events-none absolute left-15 top-3 bg-newman-navy/92 px-4 py-3 text-white shadow-lg backdrop-blur">
                                <p class="text-[9px] font-bold uppercase tracking-[0.18em] text-newman-gold">
                                    Newman route
                                </p>

                                <p class="mt-1 text-sm font-semibold">
                                    {{ $mapStops->count() }}
                                    mapped
                                    {{ $mapStops->count() === 1 ? 'stop' : 'stops' }}
                                </p>
                            </div>
                        @else
                            <div class="flex h-[320px] items-center justify-center p-8 text-center sm:h-[420px] lg:h-[520px]">
                                <div class="max-w-sm">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-newman-navy text-newman-gold">
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            class="h-5 w-5"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"
                                            />
                                            <circle cx="12" cy="10" r="2.5" />
                                        </svg>
                                    </div>

                                    <p class="mt-4 font-semibold text-newman-navy">
                                        Map points are not configured yet.
                                    </p>

                                    <p class="mt-2 text-sm leading-6 text-gray-500">
                                        Add latitude, longitude, and enable
                                        “Show on map” for each Roadmap Stop.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-newman-gold"></span>
                            Planned route
                        </span>

                        <span>
                            Click a stop to focus the map
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

            <div class="mt-16 grid gap-6 md:grid-cols-2">
                <div class="rounded-[22px] bg-newman-navy p-6 text-white sm:p-7">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                        Included
                    </p>

                    <div class="mt-6 space-y-4">
                        @forelse ($tour['included'] as $item)
                            <div class="flex gap-3">
                                <span class="text-newman-gold">✓</span>
                                <p class="text-sm leading-6 text-white/80">{{ $item }}</p>
                            </div>
                        @empty
                            <p class="text-sm leading-7 text-white/60">
                                Included details will be confirmed by Newman.
                            </p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[22px] border border-newman-navy/8 bg-white p-6 sm:p-7">
    <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-blue">
        Not included
    </p>

    <div class="mt-6 space-y-4">
        @forelse ($tour['goodToKnow'] as $item)
            <div class="flex gap-3">
                <span class="text-newman-gold">✓</span>

                <p class="text-sm leading-6 text-newman-navy">
                    {{ $item }}
                </p>
            </div>
        @empty
            <p class="text-sm leading-7 text-gray-500">
                No excluded items have been added to the default Tour Option.
            </p>
        @endforelse
    </div>
</div>
            </div>
        </div>
    </section>
    {{-- Space agar konten terakhir tidak tertutup mobile booking bar --}}
<div
    aria-hidden="true"
    class="h-24 lg:hidden"
></div>

<x-tour-mobile-booking-bar
    :tour="$tour"
/>

</main>

@endsection

@if ($mapStops->isNotEmpty())
    @push('styles')
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        >
    @endpush
@endif

@push('scripts')
    @if ($mapStops->isNotEmpty())
        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""
        ></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const mapElement = document.getElementById(
                    @json($roadmapMapId)
                );

                if (!mapElement || typeof window.L === 'undefined') {
                    return;
                }

                const stops = @json($mapStops->all());

                const map = L.map(mapElement, {
                    scrollWheelZoom: false,
                    zoomControl: true,
                    attributionControl: true,
                });

                L.tileLayer(
                    'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: 19,
                        attribution:
                            '&copy; OpenStreetMap contributors',
                    }
                ).addTo(map);

                const coordinates = [];
                const markers = [];

                const mapWrapper = document.querySelector(
                    `[data-roadmap-map="${@json($roadmapMapId)}"]`
                );

                const rows = mapWrapper
                    ? Array.from(
                        mapWrapper.querySelectorAll(
                            '[data-roadmap-stop-index]'
                        )
                    )
                    : [];

                const setActiveRow = (index) => {
                    rows.forEach((row, rowIndex) => {
                        row.classList.toggle(
                            'is-active',
                            rowIndex === index
                        );
                    });
                };

                stops.forEach((stop, index) => {
                    const coordinate = [
                        Number(stop.latitude),
                        Number(stop.longitude),
                    ];

                    coordinates.push(coordinate);

                    const number = String(
                        stop.order || index + 1
                    ).padStart(2, '0');

                    const icon = L.divIcon({
                        className: 'newman-map-marker-shell',
                        html: `
    <span class="newman-map-marker">
        <span>${number}</span>
    </span>
`,
                        iconSize: [42, 48],
                        iconAnchor: [21, 46],
                        popupAnchor: [0, -42],
                    });

                    const popupParts = [
                        stop.location,
                        stop.time,
                        stop.duration,
                    ].filter(Boolean);

                    const marker = L.marker(
                        coordinate,
                        { icon }
                    )
                        .addTo(map)
                        .bindPopup(`
                            <div class="newman-map-popup">
                                <p class="newman-map-popup-number">
                                    Stop ${number}
                                </p>

                                <strong>
                                    ${String(stop.title || 'Tour stop')}
                                </strong>

                                ${
                                    popupParts.length
                                        ? `<p>${popupParts.join(' · ')}</p>`
                                        : ''
                                }
                            </div>
                        `);

                    marker.on('click', () => {
                        setActiveRow(index);

                        rows[index]?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest',
                        });
                    });

                    markers.push(marker);
                });

                if (coordinates.length > 1) {
                    L.polyline(coordinates, {
                        color: '#d9aa55',
                        weight: 4,
                        opacity: 0.9,
                        dashArray: '2 10',
                        lineCap: 'round',
                    }).addTo(map);
                }

                if (coordinates.length === 1) {
                    map.setView(coordinates[0], 13);
                } else {
                    map.fitBounds(
                        L.latLngBounds(coordinates),
                        {
                            padding: [48, 48],
                            maxZoom: 13,
                        }
                    );
                }

                rows.forEach((row, index) => {
                    row.addEventListener('click', () => {
                        const marker = markers[index];

                        if (!marker) {
                            return;
                        }

                        const stop = stops[index];

                        map.flyTo(
                            [
                                Number(stop.latitude),
                                Number(stop.longitude),
                            ],
                            Math.max(map.getZoom(), 13),
                            {
                                duration: 0.65,
                            }
                        );

                        marker.openPopup();
                        setActiveRow(index);
                    });
                });

                setActiveRow(0);

                window.setTimeout(() => {
                    map.invalidateSize();
                }, 250);
            });
        </script>
    @endif
@endpush

@props([
    'images' => [],
    'title' => 'Tour gallery',
    'availabilityTarget' => 'tour-availability',
])

@php
    $imageSource = $images instanceof \Illuminate\Support\Collection
        ? $images->values()->all()
        : (
            is_array($images)
                ? $images
                : []
        );

    $resolveImage = static function (
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

        if (
            preg_match(
                '#^(https?:)?//#i',
                $image
            )
        ) {
            return $image;
        }

        if (str_starts_with($image, '/')) {
            return url($image);
        }

        return asset(
            ltrim($image, '/')
        );
    };

    $placeholder = asset(
        'images/tour-placeholder.jpg'
    );

    $galleryImages = collect($imageSource)
        ->map($resolveImage)
        ->filter()
        ->unique(
            fn (string $image): string =>
                strtolower($image)
        )
        ->values();

    if ($galleryImages->isEmpty()) {
        $galleryImages->push(
            $placeholder
        );
    }

    /*
     * Halaman utama menampilkan maksimal lima foto:
     * satu foto besar dan empat foto samping.
     *
     * Fullscreen viewer tetap menerima seluruh foto.
     */
    $mosaicImages = $galleryImages
        ->take(5)
        ->values();

    $mainImage = $mosaicImages->first()
        ?: $placeholder;

    $sideImages = $mosaicImages
        ->slice(1, 4)
        ->values();

    $totalPhotos = $galleryImages->count();
    $sideImageCount = $sideImages->count();

    $sideGridClass = match ($sideImageCount) {
        1 =>
            'grid-cols-1 grid-rows-1',

        2 =>
            'grid-cols-2 grid-rows-1 lg:grid-cols-1 lg:grid-rows-2',

        default =>
            'grid-cols-2 grid-rows-2',
    };
@endphp

<section
    x-data="{
        images: @js($galleryImages->all()),
        title: @js($title),

        openState: false,
        gridState: false,
        activeIndex: 0,

        previousFocus: null,
        touchStartX: null,

        open(index = 0) {
            if (!this.images.length) {
                return;
            }

            this.previousFocus =
                document.activeElement;

            this.goTo(index);
            this.gridState = false;
            this.openState = true;

            document.documentElement.style
                .overflow = 'hidden';

            this.$nextTick(() => {
                this.$refs.closeButton
                    ?.focus();
            });
        },

        close() {
            this.openState = false;
            this.gridState = false;

            document.documentElement.style
                .overflow = '';

            this.$nextTick(() => {
                this.previousFocus
                    ?.focus?.();
            });
        },

        goTo(index) {
            if (!this.images.length) {
                return;
            }

            const imageCount =
                this.images.length;

            this.activeIndex =
                (
                    (
                        Number(index)
                        % imageCount
                    )
                    + imageCount
                )
                % imageCount;

            this.gridState = false;
        },

        previous() {
            this.goTo(
                this.activeIndex - 1
            );
        },

        next() {
            this.goTo(
                this.activeIndex + 1
            );
        },

        toggleGrid() {
            this.gridState =
                !this.gridState;
        },

        handleKeydown(event) {
            if (!this.openState) {
                return;
            }

            if (event.key === 'Escape') {
                this.close();
                return;
            }

            if (
                event.key === 'ArrowLeft'
                && !this.gridState
            ) {
                event.preventDefault();
                this.previous();
                return;
            }

            if (
                event.key === 'ArrowRight'
                && !this.gridState
            ) {
                event.preventDefault();
                this.next();
                return;
            }

            if (
                event.key.toLowerCase()
                === 'g'
            ) {
                this.toggleGrid();
            }
        },

        startSwipe(event) {
            this.touchStartX =
                event.changedTouches?.[0]
                    ?.clientX
                ?? null;
        },

        finishSwipe(event) {
            if (
                this.touchStartX === null
                || this.gridState
            ) {
                return;
            }

            const touchEndX =
                event.changedTouches?.[0]
                    ?.clientX
                ?? null;

            if (touchEndX === null) {
                return;
            }

            const distance =
                touchEndX
                - this.touchStartX;

            if (Math.abs(distance) >= 50) {
                distance > 0
                    ? this.previous()
                    : this.next();
            }

            this.touchStartX = null;
        },

        scrollToAvailability() {
            this.close();

            window.setTimeout(() => {
                const target =
                    document.getElementById(
                        @js($availabilityTarget)
                    );

                if (!target) {
                    return;
                }

                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });

                target.focus({
                    preventScroll: true,
                });
            }, 160);
        },
    }"
    @keydown.window="handleKeydown($event)"
>
    {{-- Gallery mosaic --}}
    @if ($totalPhotos === 1)
        <button
            type="button"
            @click="open(0)"
            class="group relative block aspect-[16/10] w-full overflow-hidden rounded-[24px] bg-newman-sand text-left"
            aria-label="Open {{ $title }} gallery"
        >
            <img
                src="{{ $mainImage }}"
                alt="{{ $title }}"
                loading="eager"
                onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                class="absolute inset-0 block h-full w-full object-cover object-center transition duration-700 group-hover:scale-[1.02]"
            >

            <div class="absolute inset-0 bg-newman-navy/0 transition duration-500 group-hover:bg-newman-navy/10"></div>
        </button>
    @else
        <div
            class="grid gap-3 overflow-hidden rounded-[24px] bg-white lg:h-[620px] lg:grid-cols-[minmax(0,1.45fr)_minmax(300px,0.85fr)]"
        >
            {{-- Main image --}}
            <button
                type="button"
                @click="open(0)"
                class="group relative min-h-[360px] overflow-hidden bg-newman-sand text-left lg:min-h-0"
                aria-label="Open photo 1 of {{ $totalPhotos }}"
            >
                <img
                    src="{{ $mainImage }}"
                    alt="{{ $title }}"
                    loading="eager"
                    onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                    class="absolute inset-0 block h-full w-full object-cover object-center transition duration-700 group-hover:scale-[1.025]"
                >

                <div class="absolute inset-0 bg-newman-navy/0 transition duration-500 group-hover:bg-newman-navy/10"></div>
            </button>

            {{-- Side images --}}
            <div
                class="grid min-h-[440px] gap-3 lg:min-h-0 {{ $sideGridClass }}"
            >
                @foreach ($sideImages as $image)
                    @php
                        $realIndex =
                            $loop->index + 1;

                        $spanLastImage =
                            $sideImageCount === 3
                            && $loop->last;
                    @endphp

                    <button
                        type="button"
                        @click="open({{ $realIndex }})"
                        class="group relative min-h-0 overflow-hidden bg-newman-sand text-left {{ $spanLastImage ? 'col-span-2' : '' }}"
                        aria-label="Open photo {{ $realIndex + 1 }} of {{ $totalPhotos }}"
                    >
                        <img
                            src="{{ $image }}"
                            alt="{{ $title }} — photo {{ $realIndex + 1 }}"
                            loading="lazy"
                            onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                            class="absolute inset-0 block h-full w-full object-cover object-center transition duration-700 group-hover:scale-105"
                        >

                        <div class="absolute inset-0 bg-newman-navy/0 transition duration-500 group-hover:bg-newman-navy/15"></div>

                        @if ($loop->last)
                            <div class="pointer-events-none absolute inset-0 flex items-end justify-end bg-gradient-to-t from-newman-navy/60 via-transparent to-transparent p-4">
                                <span class="flex items-center gap-2 rounded-full bg-white/95 px-4 py-2 text-xs font-bold text-newman-navy shadow-lg">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        class="h-4 w-4"
                                        aria-hidden="true"
                                    >
                                        <rect
                                            x="3"
                                            y="3"
                                            width="7"
                                            height="7"
                                            rx="1"
                                        />

                                        <rect
                                            x="14"
                                            y="3"
                                            width="7"
                                            height="7"
                                            rx="1"
                                        />

                                        <rect
                                            x="3"
                                            y="14"
                                            width="7"
                                            height="7"
                                            rx="1"
                                        />

                                        <rect
                                            x="14"
                                            y="14"
                                            width="7"
                                            height="7"
                                            rx="1"
                                        />
                                    </svg>

                                    View all {{ $totalPhotos }}
                                    {{ $totalPhotos === 1
                                        ? 'photo'
                                        : 'photos' }}
                                </span>
                            </div>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Fullscreen gallery --}}
    <template x-teleport="body">
        <div
            x-cloak
            x-show="openState"
            x-transition.opacity.duration.250ms
            role="dialog"
            aria-modal="true"
            :aria-label="`${title} photo gallery`"
            class="fixed inset-0 z-[100] bg-newman-sand text-newman-navy"
        >
            <div class="flex h-full min-h-0 flex-col">
                {{-- Top navigation --}}
                <header class="relative flex h-20 shrink-0 items-center justify-center px-5 sm:h-24 sm:px-8">
                    <button
                        x-ref="closeButton"
                        type="button"
                        @click="close()"
                        class="absolute left-5 top-1/2 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-newman-navy/10 bg-white text-newman-navy shadow-[0_8px_25px_rgba(8,36,58,0.12)] transition hover:border-newman-gold hover:bg-newman-gold sm:left-8"
                        aria-label="Close gallery"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-6 w-6"
                            aria-hidden="true"
                        >
                            <path d="M5 5l14 14M19 5L5 19"/>
                        </svg>
                    </button>

                    <p class="text-sm font-bold tracking-[0.14em] text-newman-navy">
                        <span x-text="activeIndex + 1"></span>
                        <span class="text-newman-navy/35">
                            /
                        </span>
                        <span x-text="images.length"></span>
                    </p>

                    <button
                        type="button"
                        @click="toggleGrid()"
                        class="absolute right-5 top-1/2 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-newman-navy/10 bg-white text-newman-navy shadow-[0_8px_25px_rgba(8,36,58,0.12)] transition hover:border-newman-gold hover:bg-newman-gold sm:right-8"
                        :aria-label="gridState
                            ? 'Show selected photo'
                            : 'Show all photos'"
                    >
                        <svg
                            x-show="!gridState"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>

                        <svg
                            x-cloak
                            x-show="gridState"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <path d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                    </button>
                </header>

                {{-- Single image view --}}
                <div
                    x-show="!gridState"
                    class="relative flex min-h-0 flex-1 flex-col px-4 pb-4 sm:px-20 sm:pb-6 lg:px-28"
                >
                    <button
                        x-show="images.length > 1"
                        type="button"
                        @click="previous()"
                        class="absolute left-4 top-[42%] z-10 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-newman-navy/10 bg-white text-newman-navy shadow-[0_8px_25px_rgba(8,36,58,0.12)] transition hover:border-newman-gold hover:bg-newman-gold sm:left-7"
                        aria-label="Previous photo"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-6 w-6"
                            aria-hidden="true"
                        >
                            <path d="M15 5l-7 7 7 7"/>
                        </svg>
                    </button>

                    <button
                        x-show="images.length > 1"
                        type="button"
                        @click="next()"
                        class="absolute right-4 top-[42%] z-10 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border border-newman-navy/10 bg-white text-newman-navy shadow-[0_8px_25px_rgba(8,36,58,0.12)] transition hover:border-newman-gold hover:bg-newman-gold sm:right-7"
                        aria-label="Next photo"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-6 w-6"
                            aria-hidden="true"
                        >
                            <path d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    <div
                        @touchstart.passive="startSwipe($event)"
                        @touchend.passive="finishSwipe($event)"
                        class="flex min-h-0 flex-1 items-center justify-center overflow-hidden"
                    >
                        <img
                            :src="images[activeIndex]"
                            :alt="`${title} — photo ${activeIndex + 1}`"
                            class="block max-h-full max-w-full object-contain shadow-[0_24px_70px_rgba(8,36,58,0.14)]"
                        >
                    </div>

                    <div class="shrink-0 pb-1 pt-4 text-center">
                        <p
                            class="text-xs font-bold uppercase tracking-[0.18em] text-newman-gold"
                            x-text="title"
                        ></p>

                        <button
                            type="button"
                            @click="scrollToAvailability()"
                            class="mt-4 min-h-12 rounded-full border-2 border-newman-navy bg-white px-8 py-3 text-xs font-bold uppercase tracking-[0.13em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
                        >
                            Check availability
                        </button>
                    </div>
                </div>

                {{-- Grid view --}}
                <div
                    x-cloak
                    x-show="gridState"
                    class="min-h-0 flex-1 overflow-y-auto px-5 pb-10 sm:px-10 lg:px-16"
                >
                    <div class="mx-auto grid max-w-7xl grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                        <template
                            x-for="(image, index) in images"
                            :key="`${image}-${index}`"
                        >
                            <button
                                type="button"
                                @click="goTo(index)"
                                class="group relative aspect-[4/3] overflow-hidden bg-white"
                                :class="index === activeIndex
                                    ? 'ring-2 ring-newman-gold ring-offset-4 ring-offset-newman-sand'
                                    : ''"
                            >
                                <img
                                    :src="image"
                                    :alt="`${title} — photo ${index + 1}`"
                                    loading="lazy"
                                    class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                >

                                <span class="absolute bottom-3 left-3 flex h-8 min-w-8 items-center justify-center rounded-full bg-newman-navy/85 px-2 text-xs font-bold text-white">
                                    <span x-text="index + 1"></span>
                                </span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>
</section>
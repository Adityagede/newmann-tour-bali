@props([
    'tour',
])

@php
    /*
    |--------------------------------------------------------------------------
    | Mobile booking shortcut
    |--------------------------------------------------------------------------
    |
    | Harga menggunakan data publik yang sama dengan availability panel.
    | Component ini tidak menyimpan participants, tanggal, atau option.
    |
    */

    $bookingPanelId = 'tour-booking-panel-'
        . ($tour['id'] ?? 'tour');

    $priceAvailable = (bool) (
        $tour['price_available']
        ?? false
    );

    
    /*
    |--------------------------------------------------------------------------
    | Price shown in mobile booking shortcut
    |--------------------------------------------------------------------------
    */

    $priceAvailable = (bool) (
        $tour['price_available']
        ?? false
    );

    $promotion = data_get(
        $tour,
        'promotion',
        []
    );

    $promotionActive = (bool) data_get(
        $promotion,
        'active',
        false
    );

    $promotionLabel = trim(
        (string) data_get(
            $promotion,
            'label',
            ''
        )
    );

    $originalPriceText = $promotionActive
        ? data_get(
            $promotion,
            'base_adult_price_text'
        )
        : null;

    $priceLabel = $promotionActive
        ? 'Limited offer'
        : (
            $priceAvailable
                ? ($tour['price_label'] ?? 'From')
                : 'Tailored price'
        );

    $priceText = $promotionActive
        ? (
            data_get(
                $promotion,
                'adult_price_text'
            )
            ?: ($tour['price_text'] ?? 'Contact for price')
        )
        : trim(
            (string) (
                $tour['price_text']
                ?? 'Contact for price'
            )
        );

    $priceSuffix = $promotionActive
        ? 'per adult'
        : (
            $priceAvailable
                ? trim(
                    (string) (
                        $tour['price_suffix']
                        ?? ''
                    )
                )
                : ''
        );

    $activeOptionsCount = (int) (
        $tour['active_options_count']
        ?? 0
    );

    $showBookingShortcut =
        $activeOptionsCount > 0;

    $activeOptionsCount = (int) (
        $tour['active_options_count']
        ?? 0
    );

    $showBookingShortcut = $activeOptionsCount > 0;
@endphp

@if ($showBookingShortcut)
    <div
        x-data="{
            targetId: @js($bookingPanelId),
            target: null,
            observer: null,
            visible: false,

            init() {
                this.target = document.getElementById(
                    this.targetId
                );

                if (!this.target) {
                    return;
                }

                /*
                 * Tentukan kondisi awal tanpa menunggu user scroll.
                 */
                this.updateVisibilityFromPosition();

                this.observer = new IntersectionObserver(
                    (entries) => {
                        const entry = entries[0];

                        /*
                         * Bar disembunyikan ketika sebagian form
                         * sudah masuk ke area baca user.
                         */
                        this.visible = !entry.isIntersecting;
                    },
                    {
                        threshold: [
                            0,
                            0.08,
                            0.2,
                        ],

                        /*
                         * Navbar fixed tidak dihitung sebagai area
                         * form yang benar-benar terlihat.
                         */
                        rootMargin:
                            '-88px 0px -14% 0px',
                    }
                );

                this.observer.observe(this.target);

                window.addEventListener(
                    'resize',
                    () => {
                        this.updateVisibilityFromPosition();
                    },
                    {
                        passive: true,
                    }
                );
            },

            updateVisibilityFromPosition() {
                if (!this.target) {
                    this.visible = false;
                    return;
                }

                const rectangle =
                    this.target.getBoundingClientRect();

                const navbarOffset = 88;

                const targetIsVisible =
                    rectangle.bottom > navbarOffset
                    && rectangle.top
                        < window.innerHeight * 0.86;

                this.visible = !targetIsVisible;
            },

            scrollToBooking() {
                if (!this.target) {
                    return;
                }

                this.visible = false;

                const reducedMotion =
                    window.matchMedia(
                        '(prefers-reduced-motion: reduce)'
                    ).matches;

                this.target.scrollIntoView({
                    behavior:
                        reducedMotion
                            ? 'auto'
                            : 'smooth',

                    block: 'start',
                });

                /*
                 * Fokus tetap dilakukan pada form asli,
                 * bukan membuat input duplikat di bottom bar.
                 */
                window.setTimeout(() => {
                    const firstControl =
                        this.target.querySelector(
                            'button, input, select'
                        );

                    firstControl?.focus({
                        preventScroll: true,
                    });
                }, reducedMotion ? 0 : 550);
            },

            destroy() {
                this.observer?.disconnect();
            },
        }"
        class="contents"
    >
        <template x-teleport="body">
            <div
                data-tour-mobile-booking-bar
                x-cloak
                x-show="visible"
                x-transition:enter="
                    transition
                    duration-300
                    ease-out
                "
                x-transition:enter-start="
                    translate-y-full
                    opacity-0
                "
                x-transition:enter-end="
                    translate-y-0
                    opacity-100
                "
                x-transition:leave="
                    transition
                    duration-200
                    ease-in
                "
                x-transition:leave-start="
                    translate-y-0
                    opacity-100
                "
                x-transition:leave-end="
                    translate-y-full
                    opacity-0
                "
                class="
                    fixed
                    inset-x-0
                    bottom-0
                    z-[70]
                    px-2
                    lg:hidden
                "
                style="
                    padding-bottom:
                        calc(
                            0.35rem
                            + env(safe-area-inset-bottom)
                        );
                "
                role="region"
                aria-label="Tour booking shortcut"
            >
                <div
                    class="
                        mx-auto
                        flex
                        w-full
                        max-w-3xl
                        min-w-0
                        items-center
                        gap-2
                        rounded-[14px]
                        border
                        border-newman-navy/10
                        bg-white/95
                        p-2
                        shadow-[0_-8px_32px_rgba(8,36,58,0.16)]
                        backdrop-blur-xl
                        sm:gap-3
                        sm:p-2.5
                    "
                >
                    {{-- Price --}}
                    <div class="min-w-0 flex-1 pl-0.5">
    <div class="flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1">
        <p class="text-[9px] font-bold uppercase tracking-[0.16em] text-gray-400 sm:text-[10px]">
            {{ $priceLabel }}
        </p>

        @if ($promotionActive && $promotionLabel !== '')
            <span class="rounded bg-newman-gold/15 px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-[0.08em] text-newman-gold">
                {{ $promotionLabel }}
            </span>
        @endif
    </div>

    @if ($promotionActive && $originalPriceText)
        <p class="mt-1 text-[10px] leading-none text-gray-400 line-through">
            {{ $originalPriceText }}
        </p>
    @endif

    <div class="mt-1 flex min-w-0 flex-wrap items-baseline gap-x-1.5 gap-y-0.5">
        <p class="break-words text-base font-bold leading-tight tracking-[-0.03em] text-newman-navy [overflow-wrap:anywhere] sm:text-lg">
            {{ $priceText }}
        </p>

        @if ($priceSuffix !== '')
            <p class="text-[9px] leading-4 text-gray-500 sm:text-xs">
                {{ $priceSuffix }}
            </p>
        @endif
    </div>

</div>
                    {{-- CTA --}}
                    <button
                        type="button"
                        @click="scrollToBooking()"
                        class="
                            flex
                            min-h-12
                            w-[44%]
                            max-w-[250px]
                            min-w-0
                            shrink-0
                            items-center
                            justify-center
                            gap-2
                            rounded-[10px]
                            bg-newman-navy
                            px-2.5
                            py-2.5
                            text-center
                            text-[11px]
                            font-bold
                            uppercase
                            leading-4
                            tracking-[0.1em]
                            text-white
                            transition
                            duration-300
                            hover:bg-newman-blue
                            active:scale-[0.98]
                            sm:text-xs
                            sm:tracking-[0.13em]
                        "
                    >
                        <span>
                            Check availability
                        </span>

                        <svg
                            aria-hidden="true"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            class="h-4 w-4 shrink-0 text-newman-gold"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m9 6 6 6-6 6"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
@endif

@props([
    'tour' => null,
])

@php
    /*
    |--------------------------------------------------------------------------
    | Route visibility
    |--------------------------------------------------------------------------
    |
    | Floating WhatsApp hanya menjadi jalur bantuan.
    | Jangan tampilkan ketika user sudah berada di flow form,
    | review, success, contact, atau admin.
    |
    */

    $hideFloatingWhatsapp =
        request()->routeIs(
            'contact',
            'contact.*',
            'custom-trip.*',
            'booking.*',
            'booking-requests.*',
            'admin.*'
        )
        || request()->is(
            'contact',
            'contact/*',
            'custom-trip*',
            'booking*',
            'booking-requests*',
            'admin*',
            'tours/*/review*',
            'tours/*/booking*',
            '*success*'
        );

    /*
    |--------------------------------------------------------------------------
    | WhatsApp number
    |--------------------------------------------------------------------------
    |
    | Gunakan format internasional tanpa tanda +, spasi, atau strip.
    | Nomor fallback disamakan dengan nomor yang sudah dipakai project.
    |
    */

    $whatsappNumber = preg_replace(
        '/\D+/',
        '',
        (string) config(
            'newman.whatsapp_number',
            '6281246890251'
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Page-aware message
    |--------------------------------------------------------------------------
    */

    $tourTitle = is_array($tour)
        ? trim((string) data_get($tour, 'title'))
        : trim((string) data_get($tour, 'title', ''));

    $message = match (true) {
        request()->routeIs('tours.detail', 'tours.show')
            && $tourTitle !== '' =>
                implode("\n", [
                    'Hello Newman, I need help with this Tour Package:',
                    $tourTitle,
                    '',
                    'Could you help me understand the route, availability, or booking process?',
                ]),

        request()->routeIs('tours') =>
            implode("\n", [
                'Hello Newman, I am browsing your Tour Packages.',
                '',
                'Could you help me choose a suitable private Bali tour?',
            ]),

        request()->routeIs('gallery') =>
            implode("\n", [
                'Hello Newman, I saw your guest gallery.',
                '',
                'I would like help planning a private Bali journey.',
            ]),

        request()->routeIs('about') =>
            implode("\n", [
                'Hello Newman, I would like to learn more about your private Bali tours.',
            ]),

        default =>
            implode("\n", [
                'Hello Newman, I am visiting your website.',
                '',
                'Could you help me choose between a Tour Package and a Custom Trip?',
            ]),
    };

    $message .= "\n\nPage: " . url()->current();

    $whatsappUrl = 'https://wa.me/'
        . $whatsappNumber
        . '?text='
        . urlencode($message);
@endphp

@if (! $hideFloatingWhatsapp && $whatsappNumber !== '')
    <div
        x-data="{
            open: false,
            bookingBarVisible: false,
            pageOverlayOpen: false,
            bookingBarObserver: null,
            bodyObserver: null,

            init() {
                this.observeBookingBar();
                this.observeBodyState();

                window.addEventListener(
                    'resize',
                    () => this.updateBookingBarState(),
                    { passive: true }
                );
            },

            get canShow() {
                return !this.bookingBarVisible
                    && !this.pageOverlayOpen;
            },

            observeBookingBar() {
                const bookingBar = document.querySelector(
                    '[data-tour-mobile-booking-bar]'
                );

                if (!bookingBar) {
                    this.bookingBarVisible = false;
                    return;
                }

                this.updateBookingBarState();

                this.bookingBarObserver = new MutationObserver(
                    () => this.updateBookingBarState()
                );

                this.bookingBarObserver.observe(
                    bookingBar,
                    {
                        attributes: true,
                        attributeFilter: [
                            'style',
                            'class',
                            'hidden',
                        ],
                    }
                );
            },

            updateBookingBarState() {
                const bookingBar = document.querySelector(
                    '[data-tour-mobile-booking-bar]'
                );

                if (!bookingBar) {
                    this.bookingBarVisible = false;
                    return;
                }

                const styles = window.getComputedStyle(
                    bookingBar
                );

                const rectangle =
                    bookingBar.getBoundingClientRect();

                this.bookingBarVisible =
                    styles.display !== 'none'
                    && styles.visibility !== 'hidden'
                    && Number(styles.opacity || 1) > 0
                    && rectangle.height > 0
                    && rectangle.bottom > 0
                    && rectangle.top < window.innerHeight;

                if (this.bookingBarVisible) {
                    this.open = false;
                }
            },

            observeBodyState() {
                const updateBodyState = () => {
                    this.pageOverlayOpen =
                        document.body.classList.contains(
                            'overflow-hidden'
                        );

                    if (this.pageOverlayOpen) {
                        this.open = false;
                    }
                };

                updateBodyState();

                this.bodyObserver = new MutationObserver(
                    updateBodyState
                );

                this.bodyObserver.observe(
                    document.body,
                    {
                        attributes: true,
                        attributeFilter: ['class'],
                    }
                );
            },

            togglePanel() {
                if (!this.canShow) {
                    return;
                }

                this.open = !this.open;
            },

            closePanel() {
                this.open = false;
            },
        }"
        x-effect="
            if (!canShow) {
                open = false;
            }
        "
        @keydown.escape.window="closePanel()"
        class="contents"
    >
        <template x-teleport="body">
            <div
                x-cloak
                x-show="canShow"
                class="fixed right-3 z-[85] sm:right-5 lg:right-7"
                style="
                    bottom:
                        calc(
                            0.9rem
                            + env(safe-area-inset-bottom)
                        );
                "
            >
                {{-- Conversation panel --}}
                <div
                    x-show="open"
                    x-transition:enter="
                        transition
                        duration-250
                        ease-out
                    "
                    x-transition:enter-start="
                        translate-y-3
                        scale-[0.98]
                        opacity-0
                    "
                    x-transition:enter-end="
                        translate-y-0
                        scale-100
                        opacity-100
                    "
                    x-transition:leave="
                        transition
                        duration-150
                        ease-in
                    "
                    x-transition:leave-start="
                        translate-y-0
                        scale-100
                        opacity-100
                    "
                    x-transition:leave-end="
                        translate-y-2
                        scale-[0.98]
                        opacity-0
                    "
                    @click.outside="closePanel()"
                    class="
                        mb-3
                        w-[min(21rem,calc(100vw-1.5rem))]
                        min-w-0
                        overflow-hidden
                        rounded-[22px]
                        border
                        border-white/10
                        bg-newman-navy
                        text-white
                        shadow-[0_22px_70px_rgba(8,37,59,0.32)]
                    "
                    role="dialog"
                    aria-label="Chat with Newman through WhatsApp"
                >
                    {{-- Panel header --}}
                    <div
                        class="
                            flex
                            min-w-0
                            items-start
                            gap-4
                            border-b
                            border-white/10
                            p-5
                        "
                    >
                        <div
                            class="
                                flex
                                h-11
                                w-11
                                shrink-0
                                items-center
                                justify-center
                                rounded-full
                                bg-newman-gold
                                text-newman-navy
                            "
                        >
                            <svg
                                aria-hidden="true"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                class="h-5 w-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.5-4.1A8 8 0 1 1 20 11.5Z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 9.2c.4 2.1 1.7 3.4 3.8 3.8"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9.1 8.7 8.5 10c.8 2.3 2.2 3.7 4.5 4.5l1.3-.6"
                                />
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p
                                class="
                                    text-[10px]
                                    font-bold
                                    uppercase
                                    tracking-[0.2em]
                                    text-newman-gold
                                "
                            >
                                Newman Tour Bali
                            </p>

                            <h2
                                class="
                                    mt-1
                                    break-words
                                    text-lg
                                    font-semibold
                                    leading-tight
                                "
                            >
                                Need help planning your trip?
                            </h2>
                        </div>

                        <button
                            type="button"
                            @click="closePanel()"
                            class="
                                flex
                                h-9
                                w-9
                                shrink-0
                                items-center
                                justify-center
                                rounded-full
                                border
                                border-white/10
                                text-white/65
                                transition
                                hover:border-newman-gold
                                hover:text-newman-gold
                            "
                            aria-label="Close WhatsApp panel"
                        >
                            <svg
                                aria-hidden="true"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                class="h-4 w-4"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m6 6 12 12M18 6 6 18"
                                />
                            </svg>
                        </button>
                    </div>

                    {{-- Panel body --}}
                    <div class="min-w-0 p-5">
                        <p
                            class="
                                break-words
                                text-sm
                                leading-7
                                text-white/68
                            "
                        >
                            Ask about Tour Packages, a flexible Custom
                            Trip, pickup areas, group size, or the booking
                            process.
                        </p>

                        <a
                            href="{{ $whatsappUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="
                                mt-5
                                flex
                                min-h-13
                                w-full
                                min-w-0
                                items-center
                                justify-between
                                gap-3
                                rounded-[14px]
                                bg-newman-gold
                                px-5
                                py-4
                                text-xs
                                font-bold
                                uppercase
                                tracking-[0.13em]
                                text-newman-navy
                                transition
                                duration-300
                                hover:bg-white
                                active:scale-[0.985]
                            "
                        >
                            <span>
                                Continue to WhatsApp
                            </span>

                            <svg
                                aria-hidden="true"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.9"
                                class="h-4 w-4 shrink-0"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 12h14M14 7l5 5-5 5"
                                />
                            </svg>
                        </a>

                        <p
                            class="
                                mt-3
                                text-center
                                text-[10px]
                                leading-5
                                text-white/38
                            "
                        >
                            Your message will open in WhatsApp. Final
                            availability and confirmation remain manual.
                        </p>
                    </div>
                </div>

                {{-- Floating toggle --}}
                <div class="flex justify-end">
                    <button
                        type="button"
                        @click="togglePanel()"
                        :aria-expanded="open.toString()"
                        class="
                            group
                            flex
                            h-14
                            min-w-14
                            items-center
                            justify-center
                            gap-3
                            rounded-full
                            border
                            border-newman-gold/30
                            bg-newman-navy
                            px-0
                            text-white
                            shadow-[0_12px_35px_rgba(8,37,59,0.28)]
                            transition
                            duration-300
                            hover:-translate-y-0.5
                            hover:border-newman-gold
                            hover:shadow-[0_16px_40px_rgba(8,37,59,0.34)]
                            active:translate-y-0
                            sm:px-5
                        "
                        aria-label="Chat with Newman on WhatsApp"
                    >
                        <span
                            class="
                                flex
                                h-10
                                w-10
                                shrink-0
                                items-center
                                justify-center
                                rounded-full
                                bg-newman-gold
                                text-newman-navy
                            "
                        >
                            <svg
                                x-show="!open"
                                aria-hidden="true"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                class="h-5 w-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.5-4.1A8 8 0 1 1 20 11.5Z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 9.2c.4 2.1 1.7 3.4 3.8 3.8"
                                />
                            </svg>

                            <svg
                                x-cloak
                                x-show="open"
                                aria-hidden="true"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.9"
                                class="h-5 w-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m6 6 12 12M18 6 6 18"
                                />
                            </svg>
                        </span>

                        <span
                            class="
                                hidden
                                whitespace-nowrap
                                text-xs
                                font-bold
                                uppercase
                                tracking-[0.13em]
                                sm:block
                            "
                        >
                            Chat with Newman
                        </span>
                    </button>
                </div>
            </div>
        </template>
    </div>
@endif
@php
    /*
    |--------------------------------------------------------------------------
    | Newman contact details
    |--------------------------------------------------------------------------
    */

    $whatsappNumber = '6287887243495';

    $whatsappMessage = implode("\n", [
        'Hello Newman, I am visiting your website.',
        '',
        'I would like help planning a private Bali trip.',
    ]);

    $whatsappUrl = 'https://wa.me/+6287887243495'
        . $whatsappNumber
        . '?text='
        . urlencode($whatsappMessage);

    $instagramUrl = trim(
        (string) config('newman.instagram_url', 'https://www.instagram.com/newman696?igsh=YmlldjRpaWowc3pj&utm_source=qr')
    );

    $facebookUrl = trim(
        (string) config('newman.facebook_url', 'https://www.facebook.com/share/19BWUfn99G/?mibextid=wwXIfr')
    );

    $contactEmail = trim(
        (string) config(
            'newman.contact_email',
            config('mail.from.address')
        )
    );

    $contactPhone = '+6287887243495';

    $locationText = trim(
        (string) config(
            'newman.location',
            'Bali, Indonesia'
        )
    );
@endphp

<footer class="relative overflow-hidden bg-newman-navy text-white">
    {{-- Soft background decoration --}}
    <div
        aria-hidden="true"
        class="pointer-events-none absolute inset-0 opacity-[0.055]"
    >
        <div class="h-full w-full bali-pattern"></div>
    </div>

    <div
        aria-hidden="true"
        class="pointer-events-none absolute -right-40 top-12 h-96 w-96 rounded-full bg-newman-gold/10 blur-3xl"
    ></div>

    <div class="relative mx-auto w-[calc(100%-2rem)] max-w-7xl sm:w-[92%]">
        {{-- Main footer invitation --}}
        <div class="border-b border-white/10 py-14 sm:py-16 lg:py-20">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                <div class="max-w-3xl">
                    <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-newman-gold">
                        Plan your Bali journey
                    </p>

                    <h2 class="mt-4 max-w-3xl text-3xl font-semibold leading-tight tracking-[-0.04em] text-white sm:text-5xl lg:text-[56px]">
                        Planning a Bali day that feels like yours?
                    </h2>

                    <p class="mt-5 max-w-2xl text-sm leading-7 text-white/62 sm:text-base sm:leading-8">
                        Choose a prepared Tour Package or share the places,
                        date, and group size you have in mind. Newman will help
                        arrange a comfortable route and suitable transport.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row lg:justify-end">
                    <a
                        href="{{ route('tours') }}"
                        class="inline-flex min-h-13 items-center justify-center bg-newman-gold px-7 py-4 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:bg-white"
                    >
                        Explore Tour Packages
                    </a>

                    <a
                        href="{{ route('home') }}#booking"
                        class="inline-flex min-h-13 items-center justify-center border border-white/20 px-7 py-4 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition duration-300 hover:-translate-y-0.5 hover:border-newman-gold hover:text-newman-gold"
                    >
                        Plan a Custom Trip
                    </a>
                </div>
            </div>
        </div>

        {{-- Footer navigation --}}
        <div class="py-12 sm:py-14 lg:py-16">
            <div class="grid gap-x-9 gap-y-12 md:grid-cols-2 lg:grid-cols-12">
                {{-- Brand --}}
                <div class="lg:col-span-3">
                    <a
                        href="{{ route('home') }}"
                        class="inline-flex items-center gap-4"
                        aria-label="Newman Tour Bali home"
                    >
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center border border-newman-gold/35 bg-white/5">
                            <img
                                src="{{ asset('images/logo-newman.png') }}"
                                alt="Newman Tour Bali"
                                class="h-9 w-9 object-contain"
                                loading="lazy"
                                onerror="this.style.display='none'"
                            >
                        </span>

                        <span>
                            <span class="block text-lg font-semibold uppercase tracking-[0.22em] text-white">
                                Newman
                            </span>

                            <span class="mt-1 block text-[9px] font-semibold uppercase tracking-[0.3em] text-newman-gold">
                                Tour Bali
                            </span>
                        </span>
                    </a>

                    <p class="mt-6 max-w-sm text-sm leading-7 text-white/58">
                        Private Bali tours and transport, planned with local
                        knowledge and confirmed personally by Newman.
                    </p>

                    {{-- Social media --}}
                    <div class="mt-6 flex flex-wrap gap-2">
                        @if ($instagramUrl !== '')
                            <a
                                href="{{ $instagramUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-11 w-11 items-center justify-center border border-white/12 text-white/70 transition hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy"
                                aria-label="Newman on Instagram"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                >
                                    <rect
                                        x="3"
                                        y="3"
                                        width="18"
                                        height="18"
                                        rx="5"
                                    />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="4"
                                    />

                                    <circle
                                        cx="17.5"
                                        cy="6.5"
                                        r="0.8"
                                        fill="currentColor"
                                        stroke="none"
                                    />
                                </svg>
                            </a>
                        @endif

                        @if ($facebookUrl !== '')
                            <a
                                href="{{ $facebookUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-11 w-11 items-center justify-center border border-white/12 text-white/70 transition hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy"
                                aria-label="Newman on Facebook"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                >
                                    <path d="M13.7 21v-8h2.8l.4-3h-3.2V8.1c0-.9.3-1.5 1.6-1.5H17V3.9c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.4-4 4.1V10H8v3h2.6v8h3.1Z"/>
                                </svg>
                            </a>
                        @endif

                        <a
                            href="{{ $whatsappUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-11 w-11 items-center justify-center border border-white/12 text-white/70 transition hover:border-newman-gold hover:bg-newman-gold hover:text-newman-navy"
                            aria-label="Chat with Newman on WhatsApp"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                class="h-5 w-5"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.5-4.1A8 8 0 1 1 20 11.5Z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M8.7 8.5c.6 2.9 2.3 4.6 5.2 5.2"
                                />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Quick links --}}
                <div class="lg:col-span-2">
                    <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-newman-gold">
                        Quick Links
                    </p>

                    <nav
                        class="mt-6 space-y-4 text-sm"
                        aria-label="Footer quick links"
                    >
                        <a
                            href="{{ route('home') }}"
                            class="block text-white/62 transition hover:translate-x-1 hover:text-white"
                        >
                            Home
                        </a>

                        <a
                            href="{{ route('about') }}"
                            class="block text-white/62 transition hover:translate-x-1 hover:text-white"
                        >
                            About Newman
                        </a>

                        <a
                            href="{{ route('tours') }}"
                            class="block text-white/62 transition hover:translate-x-1 hover:text-white"
                        >
                            Tour Packages
                        </a>

                        <a
                            href="{{ route('gallery') }}"
                            class="block text-white/62 transition hover:translate-x-1 hover:text-white"
                        >
                            Guest Gallery
                        </a>

                        <a
                            href="{{ route('contact') }}"
                            class="block text-white/62 transition hover:translate-x-1 hover:text-white"
                        >
                            Contact
                        </a>
                    </nav>
                </div>

                {{-- Trip planning --}}
                <div class="lg:col-span-2">
                    <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-newman-gold">
                        Plan a Trip
                    </p>

                    <nav
                        class="mt-6 space-y-4 text-sm"
                        aria-label="Footer trip planning links"
                    >
                        <a
                            href="{{ route('tours') }}"
                            class="block text-white/62 transition hover:translate-x-1 hover:text-white"
                        >
                            Browse Bali Tours
                        </a>

                        <a
                            href="{{ route('home') }}#booking"
                            class="block text-white/62 transition hover:translate-x-1 hover:text-white"
                        >
                            Custom Trip Request
                        </a>

                        <a
                            href="{{ route('home') }}#vehicles"
                            class="block text-white/62 transition hover:translate-x-1 hover:text-white"
                        >
                            Private Transport
                        </a>

                        <a
                            href="{{ route('gallery') }}"
                            class="block text-white/62 transition hover:translate-x-1 hover:text-white"
                        >
                            See Guest Moments
                        </a>
                    </nav>
                </div>

                {{-- Contact --}}
                <div class="lg:col-span-2">
                    <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-newman-gold">
                        Contact Us
                    </p>

                    <div class="mt-6 space-y-5">
                        <a
                            href="{{ $whatsappUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group flex items-start gap-3"
                        >
                            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center border border-white/12 text-newman-gold transition group-hover:border-newman-gold">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    class="h-4 w-4"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.5-4.1A8 8 0 1 1 20 11.5Z"
                                    />
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-bold uppercase tracking-[0.14em] text-white/35">
                                    WhatsApp
                                </span>

                                <span class="mt-1 block break-words text-sm text-white/68 transition group-hover:text-white">
                                    {{ $contactPhone }}
                                </span>
                            </span>
                        </a>

                        @if ($contactEmail !== '')
                            <a
                                href="mailto:{{ $contactEmail }}"
                                class="group flex items-start gap-3"
                            >
                                <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center border border-white/12 text-newman-gold transition group-hover:border-newman-gold">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        class="h-4 w-4"
                                        aria-hidden="true"
                                    >
                                        <rect
                                            x="3"
                                            y="5"
                                            width="18"
                                            height="14"
                                            rx="2"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m4 7 8 6 8-6"
                                        />
                                    </svg>
                                </span>

                                <span class="min-w-0">
                                    <span class="block text-[10px] font-bold uppercase tracking-[0.14em] text-white/35">
                                        Email
                                    </span>

                                    <span class="mt-1 block break-all text-sm text-white/68 transition group-hover:text-white">
                                        {{ $contactEmail }}
                                    </span>
                                </span>
                            </a>
                        @endif

                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center border border-white/12 text-newman-gold">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    class="h-4 w-4"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"
                                    />

                                    <circle
                                        cx="12"
                                        cy="10"
                                        r="2.5"
                                    />
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-[10px] font-bold uppercase tracking-[0.14em] text-white/35">
                                    Based in
                                </span>

                                <span class="mt-1 block text-sm text-white/68">
                                    {{ $locationText }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Travel note --}}
                <div class="lg:col-span-3">
                    <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-newman-gold">
                        Travel Note
                    </p>

                    <div class="mt-6 overflow-hidden border border-white/10 bg-white/[0.055]">
                        <div class="aspect-[16/9] overflow-hidden bg-white/5">
                            <img
                                src="{{ asset('images/owner.jpg') }}"
                                alt="Newman sharing a local Bali travel moment"
                                loading="lazy"
                                decoding="async"
                                class="h-full w-full object-cover transition duration-700 hover:scale-[1.03]"
                            >
                        </div>

                        <div class="p-5">
                            <p class="text-sm leading-7 text-white/68">
                                “Tell us which places matter to you. We will
                                help shape a route that feels calm,
                                comfortable, and realistic for the day.”
                            </p>

                            <div class="mt-5 border-t border-white/10 pt-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-newman-gold">
                                    Local planning, personal confirmation
                                </p>

                                <p class="mt-2 text-xs leading-6 text-white/42">
                                    Private tours · Transport · Flexible routes
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom --}}
        <div class="border-t border-white/10 py-6">
            <div class="flex flex-col gap-4 text-xs text-white/38 sm:flex-row sm:items-center sm:justify-between">
                <p>
                    © {{ now()->year }} Newman Tour Guide.
                    Private Bali tours and transport.
                </p>

                <div class="flex flex-wrap gap-x-5 gap-y-2">
                    <a
                        href="{{ route('contact') }}"
                        class="transition hover:text-white"
                    >
                        Contact
                    </a>

                    <a
                        href="{{ route('home') }}#booking"
                        class="transition hover:text-white"
                    >
                        Booking Request
                    </a>

                    <span>
                        No online payment
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>

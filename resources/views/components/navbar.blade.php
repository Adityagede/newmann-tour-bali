@php
    /*
    |--------------------------------------------------------------------------
    | Public Navigation
    |--------------------------------------------------------------------------
    |
    | Setiap menu mempunyai nama route dan kondisi aktifnya sendiri.
    | Halaman detail, review, dan success Tour Package tetap mengaktifkan Tours.
    |
    */

    $navigationItems = [
        [
            'label' => __('navigation.home'),
            'route' => 'home',
            'active' => request()->routeIs('home'),
        ],
        [
            'label' => __('navigation.about'),
            'route' => 'about',
            'active' => request()->routeIs('about'),
        ],
        [
            'label' => __('navigation.tours'),
            'route' => 'tours',
            'active' => request()->routeIs(
                'tours',
                'tours.*',
                'booking-requests.success'
            ),
        ],
        [
            'label' => __('navigation.gallery'),
            'route' => 'gallery',
            'active' => request()->routeIs('gallery'),
        ],
        [
            'label' => __('navigation.contact'),
            'route' => 'contact',
            'active' => request()->routeIs('contact'),
        ],
    ];

    $customTripActive = request()->routeIs('custom-trip.*');

    $languageOptions = [
        'en' => [
            'code' => 'EN',
            'flag' => 'uk',
            'name' => __('navigation.english'),
        ],
        'id' => [
            'code' => 'ID',
            'flag' => 'id',
            'name' => __('navigation.indonesian'),
        ],
    ];

    $currentLocale = array_key_exists(
        app()->getLocale(),
        $languageOptions,
    )
        ? app()->getLocale()
        : 'en';

    $currentLanguage = $languageOptions[$currentLocale];
@endphp


<header
    x-data="{
        open: false,
        languageOpen: false,
        scrolled: false,
        openMenuLabel: @js(__('navigation.open_menu')),
        closeMenuLabel: @js(__('navigation.close_menu')),
        init() {
            this.scrolled = window.scrollY > 36;

            window.addEventListener('scroll', () => {
                this.scrolled = window.scrollY > 36;
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.open = false;
                } else {
                    this.languageOpen = false;
                }
            });
        }
    }"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
    @keydown.escape.window="open = false; languageOpen = false"
    class="site-header fixed left-0 top-0 w-full"
>
    <nav
        id="siteNavbar"
        class="site-navbar w-full border-b border-white/10 bg-newman-navy/25 px-5 py-4 text-white backdrop-blur-[6px] md:px-10 lg:py-5"
        :class="{ 'is-scrolled': scrolled, 'is-open': open }"
    >
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="group flex min-w-0 items-center gap-3" @click="open = false">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center border border-newman-gold/50 bg-white/10 text-base font-semibold text-newman-gold backdrop-blur-md transition duration-300 group-hover:bg-newman-gold group-hover:text-newman-navy md:h-11 md:w-11 md:text-lg">
                    <img
                        src="{{ asset('images/logo-newman.png') }}"
                        alt=""
                        width="500"
                        height="500"
                        decoding="async"
                    >
                </div>

                <div class="min-w-0">
                    <p class="truncate text-base font-semibold leading-none tracking-[0.22em] md:text-xl">
                        NEWMAN
                    </p>
                    <p class="mt-1 text-[9px] uppercase tracking-[0.35em] text-white/55 md:text-[10px]">
                        Tour Bali
                    </p>
                </div>
            </a>

            <div
    class="hidden items-center gap-9 text-[12px] font-semibold uppercase tracking-[0.18em] text-white/80 lg:flex"
    aria-label="{{ __('navigation.primary_navigation') }}"
>
    <a
        href="{{ route('home') }}"
        class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}"
        @if (request()->routeIs('home')) aria-current="page" @endif
    >
        {{ __('navigation.home') }}
    </a>

    <a
        href="{{ route('about') }}"
        class="nav-link {{ request()->routeIs('about') ? 'is-active' : '' }}"
        @if (request()->routeIs('about')) aria-current="page" @endif
    >
        {{ __('navigation.about') }}
    </a>

    <a
        href="{{ route('tours') }}"
        class="nav-link {{ request()->routeIs('tours', 'tours.*', 'booking-requests.success') ? 'is-active' : '' }}"
        @if (request()->routeIs('tours', 'tours.*', 'booking-requests.success')) aria-current="page" @endif
    >
        {{ __('navigation.tours') }}
    </a>

    <a
        href="{{ route('gallery') }}"
        class="nav-link {{ request()->routeIs('gallery') ? 'is-active' : '' }}"
        @if (request()->routeIs('gallery')) aria-current="page" @endif
    >
        {{ __('navigation.gallery') }}
    </a>

    <a
        href="{{ route('contact') }}"
        class="nav-link {{ request()->routeIs('contact') ? 'is-active' : '' }}"
        @if (request()->routeIs('contact')) aria-current="page" @endif
    >
        {{ __('navigation.contact') }}
    </a>
</div>
            <div class="hidden items-center gap-3 lg:flex">
                

                <a
                    href="{{ route('custom-trip.create') }}"
                    @class([
                        'px-8 py-5 text-[11px] font-bold uppercase tracking-[0.22em] transition duration-300',
                        'bg-newman-gold text-newman-navy hover:bg-white' => ! $customTripActive,
                        'bg-white text-newman-navy ring-2 ring-newman-gold ring-offset-2 ring-offset-newman-navy' => $customTripActive,
                    ])
                    @if ($customTripActive)
                        aria-current="page"
                    @endif
                >
                    {{ __('navigation.book_trip') }}
                </a>
            </div>

            <button
                type="button"
                class="relative flex h-11 w-11 shrink-0 items-center justify-center border border-white/20 bg-white/10 transition duration-300 hover:bg-white/15 lg:hidden"
                @click="open = !open"
                :aria-expanded="open.toString()"
                aria-controls="mobileNavigation"
                :aria-label="open ? closeMenuLabel : openMenuLabel"
            >
                <span
                    class="absolute h-[2px] w-5 bg-white transition duration-300 ease-out"
                    :class="open ? 'translate-y-0 rotate-45' : '-translate-y-2 rotate-0'"
                ></span>

                <span
                    class="absolute h-[2px] w-5 bg-white transition duration-300 ease-out"
                    :class="open ? 'opacity-0 scale-x-0' : 'opacity-100 scale-x-100'"
                ></span>

                <span
                    class="absolute h-[2px] w-5 bg-white transition duration-300 ease-out"
                    :class="open ? 'translate-y-0 -rotate-45' : 'translate-y-2 rotate-0'"
                ></span>
            </button>
        </div>
    </nav>

    <div
        x-cloak
        x-show="open"
        class="lg:hidden"
    >
        <div
            x-show="open"
            x-transition:enter="transition duration-500 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-300 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="mobile-nav-backdrop fixed inset-x-0 bottom-0 bg-newman-navy/70 backdrop-blur-md"
            @click="open = false"
        ></div>

        <div
            x-show="open"
            x-transition:enter="transition duration-500 ease-out"
            x-transition:enter-start="-translate-y-5 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition duration-300 ease-in"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="-translate-y-3 opacity-0"
            id="mobileNavigation"
            class="mobile-nav-panel absolute left-0 right-0 top-full overflow-hidden border-b border-white/10 bg-newman-navy/95 text-white shadow-2xl shadow-black/30 backdrop-blur-2xl"
        >
            <div class="mobile-nav-scroll mx-auto h-full max-w-7xl overflow-y-auto overscroll-contain px-5 py-6 md:px-10">
                <div class="grid gap-2">
    @foreach ($navigationItems as $item)
        <a
            href="{{ route($item['route']) }}"
            @click="open = false"
            @class([
                'mobile-menu-link group relative flex items-center justify-between overflow-hidden border-b px-4 py-4 text-lg font-semibold transition duration-300',
                'is-active' => $item['active'],
            ])
            @if ($item['active'])
                aria-current="page"
            @endif
        >
            @if ($item['active'])
                <span
                    aria-hidden="true"
                    class="absolute inset-y-3 left-0 w-[2px] bg-newman-gold"
                ></span>
            @endif

            <span class="relative z-10">
                {{ $item['label'] }}
            </span>

            <span
                @class([
                    'relative z-10 text-newman-gold transition duration-300 group-hover:translate-x-1',
                    'translate-x-1' => $item['active'],
                ])
            >
                →
            </span>
        </a>
    @endforeach
</div>

                

                <div
                    x-show="open"
                    x-transition:enter="transition delay-200 duration-500 ease-out"
                    x-transition:enter-start="translate-y-4 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    class="mobile-nav-utility mt-6 border border-white/10 bg-white/8 p-5"
                >
                    <p class="text-sm font-semibold text-newman-gold">
                        {{ __('navigation.private_tour') }}
                    </p>

                    <p class="mt-2 text-sm leading-6 text-white/60">
                        {{ __('navigation.private_tour_description') }}
                    </p>

                    <a
    href="{{ route('custom-trip.create') }}"
    @click="open = false"
    @class([
        'mt-5 flex items-center justify-center px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] transition duration-300',
        'bg-newman-gold text-newman-navy hover:bg-white' => ! $customTripActive,
        'bg-white text-newman-navy ring-2 ring-newman-gold' => $customTripActive,
    ])
    @if ($customTripActive)
        aria-current="page"
    @endif
>
    {{ __('navigation.book_trip') }}
</a>
                </div>
            </div>
        </div>
    </div>
</header>   

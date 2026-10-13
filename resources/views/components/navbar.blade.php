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
            'flag' => 'uk',
            'name' => 'English',
        ],
        'id' => [
            'flag' => 'id',
            'name' => 'Bahasa Indonesia',
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

            <div class="desktop-navigation hidden items-center gap-9 text-[12px] font-semibold uppercase tracking-[0.18em] text-white/80 lg:flex"
                aria-label="{{ __('navigation.primary_navigation') }}"
            >
                @foreach ($navigationItems as $item)
                    <a href="{{ route($item['route']) }}"
                        class="nav-link {{ $item['active'] ? 'is-active' : '' }}"
                        @if ($item['active']) aria-current="page" @endif
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
            <div class="navbar-actions hidden items-center gap-3 lg:flex">
                <div class="relative" @click.outside="languageOpen = false">
                    <button type="button"
                        class="inline-flex min-h-11 items-center gap-2 border border-white/20 px-3 text-white hover:bg-white/10"
                        @click="languageOpen = !languageOpen"
                        :aria-expanded="languageOpen.toString()"
                        aria-controls="desktopLanguages"
                        aria-label="{{ __('navigation.choose_language', ['language' => $currentLanguage['name']]) }}"
                    >
                        <x-language-flag :code="$currentLanguage['flag']" />
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" class="h-3 w-3"><path d="m5 7 5 5 5-5" stroke="currentColor" stroke-width="1.5" /></svg>
                    </button>
                    <div id="desktopLanguages" x-cloak x-show="languageOpen"
                        class="absolute right-0 top-full mt-2 w-48 border border-white/20 bg-newman-navy p-2 shadow-xl"
                        aria-label="{{ __('navigation.language_options') }}"
                    >
                        @foreach ($languageOptions as $locale => $language)
                            <form method="POST" action="{{ route('language.update') }}">
                                @csrf
                                <input type="hidden" name="locale" value="{{ $locale }}">
                                <button type="submit" @class(['flex min-h-11 w-full items-center gap-3 px-3 py-2 text-left text-sm hover:bg-white/10', 'text-newman-gold' => $locale === $currentLocale])
                                    @if ($locale === $currentLocale) aria-current="true" @endif
                                >
                                    <x-language-flag :code="$language['flag']" />
                                    {{ $language['name'] }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <a
                    href="{{ route('custom-trip.create') }}"
                    @class([
                        'navbar-book-cta px-8 py-5 text-[11px] font-bold uppercase tracking-[0.22em] transition duration-300',
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

                <div class="mt-5 border-t border-white/10 pt-4" aria-label="{{ __('navigation.language_options') }}">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.16em] text-white/60">{{ __('navigation.language') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($languageOptions as $locale => $language)
                            <form method="POST" action="{{ route('language.update') }}">
                                @csrf
                                <input type="hidden" name="locale" value="{{ $locale }}">
                                <button type="submit" @class(['inline-flex min-h-11 items-center gap-2 border px-3 py-2 text-sm hover:bg-white/10', 'border-newman-gold text-newman-gold' => $locale === $currentLocale, 'border-white/20 text-white' => $locale !== $currentLocale])
                                    @if ($locale === $currentLocale) aria-current="true" @endif
                                >
                                    <x-language-flag :code="$language['flag']" />
                                    {{ $language['name'] }}
                                </button>
                            </form>
                        @endforeach
                    </div>
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

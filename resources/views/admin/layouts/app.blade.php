<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Newman Admin' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#EEF4FB] text-newman-navy antialiased">
    @php
        $adminMenu = [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'active' => 'admin.dashboard',
            ],
            [
                'label' => 'Bookings',
                'route' => 'admin.tour-booking-requests.index',
                'active' => 'admin.tour-booking-requests.*',
            ],
            [
                'label' => 'Custom Trips',
                'route' => 'admin.custom-trip-requests.index',
                'active' => 'admin.custom-trip-requests.*',
            ],
            [
                'label' => 'Ratings & Reviews',
                'route' => 'admin.ratings.index',
                'active' => 'admin.ratings.*',
            ],
            [
                'label' => 'Tour Packages',
                'route' => 'admin.tour-packages.index',
                'active' => 'admin.tour-packages.*',
            ],

            [
                'label' => 'Gallery',
                'route' => 'admin.gallery.index',
                'active' => 'admin.gallery.*',
            ],
        ];
    @endphp

    <div class="min-h-screen">
        <header class="sticky top-0 z-50 border-b border-white/10 bg-newman-navy text-white shadow-lg shadow-newman-navy/10">
            <div class="mx-auto flex w-[94%] max-w-7xl items-center justify-between gap-3 py-3 sm:py-4">
                <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center border border-newman-gold/50 bg-white/10 text-sm font-semibold text-newman-gold sm:h-11 sm:w-11">
                        N
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-xs font-bold uppercase tracking-[0.24em] sm:text-sm sm:tracking-[0.3em]">
                            Newman Admin
                        </p>
                        <p class="mt-1 truncate text-[9px] uppercase tracking-[0.22em] text-white/45 sm:text-[10px]">
                            Booking Control
                        </p>
                    </div>
                </a>

                <form action="{{ route('admin.logout') }}" method="POST" class="shrink-0">
                    @csrf

                    <button
                        type="submit"
                        class="bg-newman-gold px-3 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-newman-navy transition hover:bg-white sm:px-5 sm:text-xs"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <nav class="sticky top-[67px] z-40 border-b border-newman-navy/10 bg-white/95 shadow-sm shadow-newman-navy/5 backdrop-blur lg:hidden">
            <div class="mx-auto w-[94%] overflow-x-auto py-3">
                <div class="flex min-w-max gap-2">
                    @foreach ($adminMenu as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            class="px-4 py-3 text-xs font-bold uppercase tracking-[0.14em] transition
                                {{ request()->routeIs($item['active'])
                                    ? 'bg-newman-navy text-white'
                                    : 'bg-newman-sand text-newman-navy hover:bg-newman-gold'
                                }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <a
                        href="{{ route('home') }}"
                        target="_blank"
                        class="px-4 py-3 text-xs font-bold uppercase tracking-[0.14em] text-newman-navy transition hover:bg-newman-gold"
                    >
                        Website
                    </a>
                </div>
            </div>
        </nav>

        <div class="mx-auto grid w-[94%] max-w-7xl gap-6 py-5 sm:py-6 lg:grid-cols-[260px_minmax(0,1fr)] lg:py-8">
            <aside class="hidden h-fit border border-newman-navy/10 bg-white p-4 shadow-sm shadow-newman-navy/5 lg:sticky lg:top-24 lg:block">
                <p class="px-3 text-xs font-bold uppercase tracking-[0.28em] text-newman-gold">
                    Menu
                </p>

                <nav class="mt-4 grid gap-2 text-sm font-semibold">
                    @foreach ($adminMenu as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            class="px-3 py-3 transition
                                {{ request()->routeIs($item['active'])
                                    ? 'bg-newman-navy text-white'
                                    : 'text-newman-navy hover:bg-newman-sand'
                                }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <a
                        href="{{ route('home') }}"
                        target="_blank"
                        class="px-3 py-3 text-newman-navy transition hover:bg-newman-sand"
                    >
                        View Website
                    </a>
                </nav>
            </aside>

            <main class="min-w-0">
                @if (session('success'))
                    <div class="mb-5 border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

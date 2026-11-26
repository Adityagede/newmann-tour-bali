@php
    $optionSections = [
        'edit' => ['Details', 'edit'],
        'prices.*' => ['Prices', 'prices.edit'],
        'schedules.*' => ['Schedules', 'schedules.index'],
        'items.*' => ['Included & excluded', 'items.index'],
        'discounts.*' => ['Discounts', 'discounts.index'],
        'blackouts.*' => ['Blackout dates', 'blackouts.index'],
    ];

    $editorOptions = $tourPackage->relationLoaded('options')
        ? $tourPackage->options
        : $tourPackage->options()->get(['id', 'title', 'status']);
    $selectedOption = $tourOption ?? null;
    $onlyOption = $editorOptions->count() === 1 ? $editorOptions->first() : null;
    $bookingLink = $selectedOption ?? $onlyOption;
    $optionRoute = fn ($section, $option) => route('admin.tour-packages.options.'.$section, [
        'tourPackage' => $tourPackage,
        'tourOption' => $option,
    ]);
    $optionSection = collect($optionSections)->first(
        fn ($tab, $pattern) => request()->routeIs('admin.tour-packages.options.'.$pattern)
    )[1] ?? 'edit';
    $bookingUrl = $bookingLink
        ? $optionRoute('edit', $bookingLink)
        : route('admin.tour-packages.options.'.($editorOptions->isEmpty() ? 'create' : 'index'), $tourPackage);

    $sections = [
        ['Information', route('admin.tour-packages.edit', $tourPackage), request()->routeIs('admin.tour-packages.edit')],
        ['Gallery & itinerary', route('admin.tour-packages.gallery.edit', $tourPackage), request()->routeIs('admin.tour-packages.gallery.*', 'admin.tour-packages.roadmap.*')],
        ['Booking options', $bookingUrl, request()->routeIs('admin.tour-packages.options.*')],
        ['Publishing', route('admin.tour-packages.readiness.show', $tourPackage), request()->routeIs('admin.tour-packages.readiness.*')],
    ];

    $next = null;
    if ($tourPackage->status === 'draft') {
        $next = match (true) {
            request()->routeIs('admin.tour-packages.gallery.edit')
                && count($galleryImages ?? []) >= \App\Services\TourPublishingReadinessService::MIN_GALLERY_IMAGES
                => ['Continue to itinerary', route('admin.tour-packages.roadmap.index', $tourPackage)],
            request()->routeIs('admin.tour-packages.roadmap.index')
                && ($activeCount ?? 0) >= \App\Services\TourPublishingReadinessService::MIN_SHARED_STOPS
                => ['Continue to booking options', $bookingUrl],
            $selectedOption && request()->routeIs('admin.tour-packages.options.schedules.index') && ($activeCount ?? 0) > 0
                => ['Continue to inclusions', $optionRoute('items.index', $selectedOption)],
            request()->routeIs('admin.tour-packages.options.items.index')
                && (! $selectedOption?->is_all_inclusive || ($activeIncludedCount ?? 0) > 0)
                => ['Check publishing readiness', route('admin.tour-packages.readiness.show', $tourPackage)],
            default => null,
        };
    }
@endphp

<section class="mb-6 min-w-0 border border-newman-navy/10 bg-white shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-x-5 gap-y-3 px-4 py-4 sm:px-5">
        <div class="min-w-0">
            <a href="{{ route('admin.tour-packages.index') }}" class="text-xs font-semibold text-newman-blue hover:underline">
                All tour packages
            </a>
            <div class="mt-1 flex flex-wrap items-center gap-2">
                <p class="break-words text-base font-semibold text-newman-navy">{{ $tourPackage->title }}</p>
                <span @class([
                    'rounded px-2 py-0.5 text-xs font-semibold capitalize',
                    'bg-emerald-50 text-emerald-800' => $tourPackage->status === 'active',
                    'bg-amber-50 text-amber-800' => $tourPackage->status === 'draft',
                    'bg-gray-100 text-gray-700' => $tourPackage->status === 'inactive',
                ])>{{ $tourPackage->status }}</span>
            </div>
        </div>
        <a href="{{ route('admin.tour-packages.preview', $tourPackage) }}" target="_blank" rel="noopener"
            class="text-sm font-semibold text-newman-blue hover:underline">
            Preview tour <span aria-hidden="true">↗</span><span class="sr-only"> (opens in a new tab)</span>
        </a>
    </div>

    <nav aria-label="Tour package editor" class="flex flex-wrap gap-1 border-t border-newman-navy/10 px-3 py-2 sm:px-4">
        @foreach ($sections as [$label, $url, $current])
            <a href="{{ $url }}" @if ($current) aria-current="page" @endif @class([
                'rounded px-3 py-2 text-sm font-semibold transition',
                'bg-newman-navy text-white' => $current,
                'text-newman-navy hover:bg-newman-sand' => ! $current,
            ])>{{ $label }}</a>
        @endforeach
    </nav>

    @if (request()->routeIs('admin.tour-packages.gallery.*', 'admin.tour-packages.roadmap.*'))
        <nav aria-label="Tour media and itinerary" class="flex flex-wrap gap-1 border-t border-newman-navy/10 bg-newman-sand/30 px-3 py-2 text-sm sm:px-4">
            @foreach (['Gallery' => ['gallery.edit', 'gallery.*'], 'Itinerary' => ['roadmap.index', 'roadmap.*']] as $label => [$destination, $pattern])
                <a href="{{ route('admin.tour-packages.'.$destination, $tourPackage) }}"
                    @if (request()->routeIs('admin.tour-packages.'.$pattern)) aria-current="page" @endif
                    class="rounded px-3 py-2 text-newman-navy hover:bg-white aria-[current=page]:bg-white aria-[current=page]:font-semibold">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    @endif

    @if ($selectedOption)
        <div class="border-t border-newman-navy/10 bg-newman-sand/30 px-3 py-2 sm:px-4">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 px-3 py-1.5">
                <p class="min-w-0 break-words text-sm font-semibold text-newman-navy">
                    {{ $selectedOption->title }} <span class="ml-1 text-xs font-normal capitalize text-gray-600">· {{ $selectedOption->status }}</span>
                </p>
                @if ($editorOptions->count() > 1)
                    <details class="relative">
                        <summary class="cursor-pointer text-sm font-semibold text-newman-blue">Switch option</summary>
                        <div class="z-20 mt-2 grid min-w-48 gap-1 border border-newman-navy/10 bg-white p-2 shadow-lg sm:absolute">
                            @foreach ($editorOptions as $editorOption)
                                <a href="{{ $optionRoute($optionSection, $editorOption) }}"
                                    @if ($editorOption->is($selectedOption)) aria-current="true" @endif
                                    class="break-words rounded px-3 py-2 text-sm hover:bg-newman-sand aria-[current=true]:font-bold">
                                    {{ $editorOption->title }}
                                </a>
                            @endforeach
                        </div>
                    </details>
                @endif
                <a href="{{ route('admin.tour-packages.options.index', $tourPackage) }}" class="text-sm font-semibold text-newman-blue hover:underline">
                    All options
                </a>
            </div>
            <nav aria-label="Booking option editor" class="flex flex-wrap gap-1 text-sm">
                @foreach ($optionSections as $pattern => [$label, $destination])
                    <a href="{{ $optionRoute($destination, $selectedOption) }}"
                        @if (request()->routeIs('admin.tour-packages.options.'.$pattern)) aria-current="page" @endif
                        class="rounded px-3 py-2 text-newman-navy hover:bg-white aria-[current=page]:bg-white aria-[current=page]:font-semibold">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>
    @endif

    @if ($next)
        <div class="flex justify-end border-t border-newman-navy/10 px-4 py-3 sm:px-5">
            <a href="{{ $next[1] }}" class="rounded bg-newman-gold px-4 py-2 text-sm font-bold text-newman-navy hover:bg-newman-sand">
                {{ $next[0] }} <span aria-hidden="true">→</span>
            </a>
        </div>
    @endif
</section>

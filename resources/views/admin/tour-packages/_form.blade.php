@php
    $isEdit = isset($tourPackage)
        && $tourPackage instanceof \App\Models\TourPackage
        && $tourPackage->exists;

    $formAction = $isEdit
        ? route('admin.tour-packages.update', $tourPackage)
        : route('admin.tour-packages.store');

    $pageEyebrow = $isEdit
        ? 'Edit Tour Product'
        : 'Create Tour Product';

    $pageTitle = $isEdit
        ? $tourPackage->title
        : 'Create a new Bali experience';

    $pageDescription = $isEdit
        ? 'Manage the identity, editorial content, image, and publishing status of this tour product.'
        : 'Create the main tour product first. Prices, participant types, schedules, and inclusions will be managed through Tour Options.';

    $fieldValue = static function (
        string $field,
        mixed $createDefault = null
    ) use ($isEdit, $tourPackage) {
        $databaseValue = $isEdit
            ? data_get($tourPackage, $field)
            : $createDefault;

        return old($field, $databaseValue);
    };

    $toArray = static function (mixed $value): array {
        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->values()->all();
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);

            if (
                json_last_error() === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                return $decoded;
            }

            return preg_split(
                '/\r\n|\r|\n/',
                trim($value)
            ) ?: [];
        }

        return [];
    };

    $highlightLines = collect(
        $toArray($isEdit ? $tourPackage->highlights : [])
    )
        ->map(static function (mixed $item): string {
            if (is_array($item)) {
                return trim((string) (
                    $item['title']
                    ?? $item['text']
                    ?? ''
                ));
            }

            return is_scalar($item)
                ? trim((string) $item)
                : '';
        })
        ->filter()
        ->values()
        ->all();

    $highlightsText = old(
        'highlights_text',
        implode(PHP_EOL, $highlightLines)
    );

    $itineraryLines = collect(
        $toArray($isEdit ? $tourPackage->itinerary : [])
    )
        ->map(static function (mixed $item): string {
            if (!is_array($item)) {
                return is_scalar($item)
                    ? trim((string) $item)
                    : '';
            }

            /*
             * Posisi time, title, dan text tetap dipertahankan.
             * Nilai time boleh kosong sehingga baris dapat dimulai
             * dengan karakter |.
             */
            return implode(' | ', [
                trim((string) ($item['time'] ?? '')),
                trim((string) ($item['title'] ?? '')),
                trim((string) ($item['text'] ?? '')),
            ]);
        })
        ->filter(
            static fn (string $line): bool =>
                trim(str_replace('|', '', $line)) !== ''
        )
        ->values()
        ->all();

    $itineraryText = old(
        'itinerary_text',
        implode(PHP_EOL, $itineraryLines)
    );

    $currentImagePath = $isEdit
        ? $tourPackage->main_image
        : null;

    $currentImageUrl = null;

    if ($currentImagePath) {
        $currentImageUrl = \Illuminate\Support\Str::startsWith(
            $currentImagePath,
            ['http://', 'https://']
        )
            ? $currentImagePath
            : asset(ltrim($currentImagePath, '/'));
    }

    $currentStatus = $fieldValue(
        'status',
        'draft'
    );

    $currentTourFormat = $fieldValue(
        'tour_format',
        'full_day'
    );
@endphp

<div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
            {{ $pageEyebrow }}
        </p>

        <h1 class="mt-3 max-w-4xl text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
            {{ $pageTitle }}
        </h1>

        <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
            {{ $pageDescription }}
        </p>
    </div>

    <a
        href="{{ route('admin.tour-packages.index') }}"
        class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:border-newman-gold hover:bg-newman-gold"
    >
        Back to tours
    </a>
</div>

@if (session('success'))
    <div class="mb-6 border border-emerald-200 bg-emerald-50 p-4 text-sm leading-6 text-emerald-800">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-semibold">
            Please check the highlighted tour product data.
        </p>

        <ul class="mt-3 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ $formAction }}"
    method="POST"
    enctype="multipart/form-data"
    class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_360px]"
>
    @csrf

    @if ($isEdit)
        @method('PATCH')
    @endif

    <main class="space-y-6">
        {{-- Identity --}}
        <section class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-7">
            <div class="flex flex-col gap-3 border-b border-gray-100 pb-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                        Product identity
                    </p>

                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-newman-navy">
                        Main tour information
                    </h2>
                </div>

                <p class="max-w-sm text-xs leading-6 text-gray-500">
                    This information is shared by every booking option inside the tour.
                </p>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label
                        for="title"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Tour title
                    </label>

                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ $fieldValue('title') }}"
                        maxlength="180"
                        required
                        autofocus
                        class="booking-input mt-2"
                        placeholder="Example: Bali Instagram Highlights"
                    >

                    @error('title')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        The URL slug will be generated automatically from this title.
                    </p>
                </div>

                <div>
                    <label
                        for="category"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Category
                    </label>

                    <input
                        id="category"
                        type="text"
                        name="category"
                        value="{{ $fieldValue('category') }}"
                        maxlength="80"
                        list="tour-category-suggestions"
                        class="booking-input mt-2"
                        placeholder="Example: Nature & culture"
                    >

                    <datalist id="tour-category-suggestions">
                        <option value="Nature"></option>
                        <option value="Culture"></option>
                        <option value="Photography"></option>
                        <option value="Beach"></option>
                        <option value="Family"></option>
                        <option value="Adventure"></option>
                        <option value="Custom"></option>
                    </datalist>

                    @error('category')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="area"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Area
                    </label>

                    <input
                        id="area"
                        type="text"
                        name="area"
                        value="{{ $fieldValue('area') }}"
                        maxlength="120"
                        class="booking-input mt-2"
                        placeholder="Example: East Bali"
                    >

                    @error('area')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="tour_format"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Tour format
                    </label>

                    <select
                        id="tour_format"
                        name="tour_format"
                        required
                        class="booking-input booking-select mt-2"
                    >
                        <option
                            value="full_day"
                            @selected($currentTourFormat === 'full_day')
                        >
                            Full-day tour
                        </option>

                        <option
                            value="half_day"
                            @selected($currentTourFormat === 'half_day')
                        >
                            Half-day tour
                        </option>

                        <option
                            value="activity_transfer"
                            @selected($currentTourFormat === 'activity_transfer')
                        >
                            Activity + transfer
                        </option>

                        <option
                            value="custom_trip"
                            @selected($currentTourFormat === 'custom_trip')
                        >
                            Custom trip
                        </option>
                    </select>

                    @error('tour_format')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="trip_type"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Display type
                    </label>

                    <input
                        id="trip_type"
                        type="text"
                        name="trip_type"
                        value="{{ $fieldValue('trip_type', 'Private tour') }}"
                        maxlength="80"
                        class="booking-input mt-2"
                        placeholder="Example: Private tour"
                    >

                    @error('trip_type')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="duration"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Display duration
                    </label>

                    <input
                        id="duration"
                        type="text"
                        name="duration"
                        value="{{ $fieldValue('duration') }}"
                        maxlength="80"
                        class="booking-input mt-2"
                        placeholder="Example: 10 hours"
                    >

                    @error('duration')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Option-specific duration will be managed later inside Tour Options.
                    </p>
                </div>

                <div>
                    <label
                        for="badge"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Marketing badge
                    </label>

                    <input
                        id="badge"
                        type="text"
                        name="badge"
                        value="{{ $fieldValue('badge') }}"
                        maxlength="80"
                        class="booking-input mt-2"
                        placeholder="Example: Top Pick"
                    >

                    @error('badge')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Keep it factual. Examples: Popular, Top Pick, New activity.
                    </p>
                </div>
            </div>
        </section>

        {{-- Editorial content --}}
        <section class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Editorial content
                </p>

                <h2 class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-newman-navy">
                    Tell the experience clearly
                </h2>
            </div>

            <div class="mt-6 grid gap-6">
                <div>
                    <label
                        for="description"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Card description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        maxlength="2000"
                        class="booking-input mt-2 resize-y"
                        placeholder="A concise description used on listings and tour cards."
                    >{{ old('description', $isEdit ? $tourPackage->description : '') }}</textarea>

                    @error('description')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="intro"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Detail-page introduction
                    </label>

                    <textarea
                        id="intro"
                        name="intro"
                        rows="5"
                        maxlength="2000"
                        class="booking-input mt-2 resize-y"
                        placeholder="Introduce the tour naturally and explain who it is best suited for."
                    >{{ old('intro', $isEdit ? $tourPackage->intro : '') }}</textarea>

                    @error('intro')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="story"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Experience story
                    </label>

                    <textarea
                        id="story"
                        name="story"
                        rows="8"
                        maxlength="8000"
                        class="booking-input mt-2 resize-y"
                        placeholder="Describe the pace, atmosphere, destinations, and what makes this experience distinctive."
                    >{{ old('story', $isEdit ? $tourPackage->story : '') }}</textarea>

                    @error('story')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Highlights and fallback itinerary --}}
        <section class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Highlights & route fallback
                </p>

                <h2 class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-newman-navy">
                    Structure the main experience
                </h2>
            </div>

            <div class="mt-6 grid gap-6">
                <div>
                    <label
                        for="highlights_text"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Tour highlights
                    </label>

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Write one clear highlight per line. Do not include prices, vehicles, or claims that belong to a specific option.
                    </p>

                    <textarea
                        id="highlights_text"
                        name="highlights_text"
                        rows="7"
                        maxlength="5000"
                        class="booking-input mt-2 resize-y"
                        placeholder="Iconic Bali photo locations&#10;Private and thoughtfully paced experience&#10;A balanced mix of nature and culture"
                    >{{ $highlightsText }}</textarea>

                    @error('highlights_text')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <label
                            for="itinerary_text"
                            class="text-sm font-semibold text-newman-navy"
                        >
                            Legacy itinerary fallback
                        </label>

                        <span class="w-fit bg-newman-sand px-3 py-2 text-[10px] font-bold uppercase tracking-[0.16em] text-newman-navy">
                            Temporary fallback
                        </span>
                    </div>

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Format each line as:
                        <strong>Time | Title | Description</strong>.
                        Structured roadmap stops will be managed in Step 13.
                    </p>

                    <textarea
                        id="itinerary_text"
                        name="itinerary_text"
                        rows="9"
                        maxlength="12000"
                        class="booking-input mt-2 resize-y"
                        placeholder="Early morning | Hotel pickup | Pickup timing will follow the selected option.&#10;Morning | First destination | Begin the curated route.&#10;Afternoon | Return | Travel back after the final stop."
                    >{{ $itineraryText }}</textarea>

                    @error('itinerary_text')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </section>
    </main>

    <aside class="space-y-6 lg:sticky lg:top-28">
        {{-- Publishing --}}
        <section class="border border-newman-gold/25 bg-newman-navy p-5 text-white shadow-2xl shadow-newman-navy/15 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Publishing
            </p>

            <h2 class="mt-3 text-2xl font-semibold tracking-[-0.03em]">
                Product visibility
            </h2>

            <div class="mt-6 grid gap-5">
                <div>
                    <label
                        for="status"
                        class="text-sm font-semibold"
                    >
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                        class="booking-input booking-select mt-2"
                    >
                        <option
                            value="draft"
                            @selected($currentStatus === 'draft')
                        >
                            Draft
                        </option>

                        <option
                            value="active"
                            @selected($currentStatus === 'active')
                        >
                            Active
                        </option>

                        <option
                            value="inactive"
                            @selected($currentStatus === 'inactive')
                        >
                            Inactive
                        </option>
                    </select>

                    @error('status')
                        <p class="mt-2 text-xs text-red-300">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs leading-6 text-white/55">
                        Keep new products in Draft until gallery, options, prices, schedules, and inclusions are ready.
                    </p>
                </div>

                <label class="flex items-start gap-3 border border-white/10 bg-white/5 p-4">
                    <input
                        type="checkbox"
                        name="is_popular"
                        value="1"
                        class="mt-1 h-4 w-4 accent-newman-gold"
                        @checked(
                            old(
                                'is_popular',
                                $isEdit
                                    ? (bool) $tourPackage->is_popular
                                    : false
                            )
                        )
                    >

                    <span>
                        <span class="block text-sm font-semibold">
                            Popular tour
                        </span>

                        <span class="mt-1 block text-xs leading-5 text-white/55">
                            Eligible for the six-tour Popular section on the homepage.
                        </span>
                    </span>
                </label>

                <label class="flex items-start gap-3 border border-white/10 bg-white/5 p-4">
                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        class="mt-1 h-4 w-4 accent-newman-gold"
                        @checked(
                            old(
                                'is_featured',
                                $isEdit
                                    ? (bool) $tourPackage->is_featured
                                    : false
                            )
                        )
                    >

                    <span>
                        <span class="block text-sm font-semibold">
                            Featured tour
                        </span>

                        <span class="mt-1 block text-xs leading-5 text-white/55">
                            Marks the product for editorial placement outside the Popular section.
                        </span>
                    </span>
                </label>

                <button
                    type="submit"
                    class="bg-newman-gold px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:bg-white"
                >
                    {{ $isEdit
                        ? 'Update tour product'
                        : 'Create draft product' }}
                </button>
            </div>
        </section>

        {{-- Main image --}}
        <section class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Main image
            </p>

            @if ($currentImageUrl)
                <div class="mt-5 overflow-hidden bg-newman-sand">
                    <img
                        src="{{ $currentImageUrl }}"
                        alt="{{ $isEdit ? $tourPackage->title : 'Tour image' }}"
                        class="aspect-[4/3] h-full w-full object-cover"
                    >
                </div>
            @else
                <div class="mt-5 flex aspect-[4/3] items-center justify-center border border-dashed border-newman-navy/20 bg-newman-sand/60 px-6 text-center">
                    <p class="text-xs leading-6 text-gray-500">
                        No main image selected yet.
                    </p>
                </div>
            @endif

            <label
                for="main_image"
                class="mt-5 block text-sm font-semibold text-newman-navy"
            >
                {{ $currentImageUrl
                    ? 'Replace main image'
                    : 'Upload main image' }}
            </label>

            <input
                id="main_image"
                type="file"
                name="main_image"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                class="mt-3 block w-full text-xs text-gray-600 file:mr-3 file:border-0 file:bg-newman-sand file:px-4 file:py-3 file:text-xs file:font-bold file:uppercase file:tracking-[0.12em] file:text-newman-navy"
            >

            @error('main_image')
                <p class="mt-2 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror

            <p class="mt-3 text-xs leading-6 text-gray-500">
                JPG, PNG, or WebP. Maximum 8 MB. Gallery management will be added separately.
            </p>
        </section>

        {{-- V2 architecture --}}
        <section class="border border-newman-gold/30 bg-newman-sand p-5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                V2 architecture
            </p>

            <h3 class="mt-3 text-xl font-semibold tracking-[-0.03em] text-newman-navy">
                Prices are not managed here
            </h3>

            <p class="mt-3 text-xs leading-6 text-gray-600">
                Adult, Child, Infant, inclusions, exclusions, schedules, discounts, capacity, and blackout dates belong to Tour Options.
            </p>

            @if ($isEdit)
                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="bg-white p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-400">
                            All options
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-newman-navy">
                            {{ $tourPackage->options_count ?? 0 }}
                        </p>
                    </div>

                    <div class="bg-white p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-400">
                            Active options
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-newman-navy">
                            {{ $tourPackage->active_options_count ?? 0 }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 border-t border-newman-navy/10 pt-4">
                    <p class="text-xs leading-6 text-gray-500">
                        Product slug
                    </p>

                    <code class="mt-1 block break-all text-xs font-semibold text-newman-navy">
                        {{ $tourPackage->slug }}
                    </code>
                </div>
                <a
    href="{{ route(
        'admin.tour-packages.gallery.edit',
        $tourPackage
    ) }}"
    class="mt-5 flex min-h-12 w-full items-center justify-center bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition duration-300 hover:-translate-y-0.5 hover:bg-newman-gold hover:text-newman-navy"
>
    Manage tour gallery
</a>

<a
    href="{{ route(
        'admin.tour-packages.roadmap.index',
        $tourPackage
    ) }}"
    class="mt-3 flex min-h-12 w-full items-center justify-center border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:border-newman-gold hover:bg-newman-gold"
>
    Manage roadmap
</a>

<a
    href="{{ route(
        'admin.tour-packages.options.index',
        $tourPackage
    ) }}"
    class="mt-3 flex min-h-12 w-full items-center justify-center bg-newman-gold px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:bg-newman-navy hover:text-white"
>
    Manage tour options
</a>


<a
    href="{{ route(
        'admin.tour-packages.readiness.show',
        [
            'tourPackage' => $tourPackage,
        ]
    ) }}"
    class="mt-3 flex min-h-12 w-full items-center justify-center border border-newman-gold bg-newman-sand px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:bg-newman-gold"
>
    Review publishing readiness
</a>


<a
    href="{{ route(
        'admin.tour-packages.preview',
        [
            'tourPackage' => $tourPackage,
        ]
    ) }}"
    target="_blank"
    rel="noopener"
    class="mt-3 flex min-h-12 w-full items-center justify-center bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition duration-300 hover:-translate-y-0.5 hover:bg-newman-gold hover:text-newman-navy"
>
    Preview public tour detail
</a>


            @endif
        </section>
    </aside>
</form>


@php
    $isEdit = isset($tourOption)
        && $tourOption instanceof \App\Models\TourOption
        && $tourOption->exists;

    $formAction = $isEdit
        ? route(
            'admin.tour-packages.options.update',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
            ]
        )
        : route(
            'admin.tour-packages.options.store',
            $tourPackage
        );

    $languagesText = old(
        'languages_text',
        $isEdit
            ? implode(
                PHP_EOL,
                is_array($tourOption->languages)
                    ? $tourOption->languages
                    : []
            )
            : "English\nIndonesian"
    );

    $currentPickupType = old(
        'pickup_type',
        $isEdit
            ? $tourOption->pickup_type
            : 'hotel_pickup'
    );

    $currentStatus = old(
        'status',
        $isEdit
            ? $tourOption->status
            : 'draft'
    );
@endphp

<div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
            {{ $isEdit ? 'Edit Tour Option' : 'Create Tour Option' }}
        </p>

        <h1 class="mt-3 max-w-4xl text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
            {{ $isEdit
                ? $tourOption->title
                : 'Add a bookable option' }}
        </h1>

        <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
            Product:
            <strong>{{ $tourPackage->title }}</strong>.
            Prices, participant categories, inclusions, schedules,
            discounts, and blackout dates will be configured after
            this base option is saved.
        </p>
    </div>

    <a
        href="{{ route(
            'admin.tour-packages.options.index',
            $tourPackage
        ) }}"
        class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
    >
        Back to options
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
            Please check the option data.
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
    class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_360px]"
>
    @csrf

    @if ($isEdit)
        @method('PATCH')
    @endif

    <main class="space-y-6">
        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Option identity
                </p>

                <h2 class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-newman-navy">
                    Bookable package information
                </h2>
            </div>

            <div class="mt-6 grid gap-5">
                <div>
                    <label
                        for="title"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Option title
                    </label>

                    <input
                        id="title"
                        type="text"
                        name="title"
                        required
                        maxlength="180"
                        value="{{ old(
                            'title',
                            $isEdit
                                ? $tourOption->title
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="Example: All-Inclusive Private Tour"
                    >

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Do not include a vehicle name in the option title.
                    </p>
                </div>

                <div>
                    <label
                        for="short_description"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Short description
                    </label>

                    <textarea
                        id="short_description"
                        name="short_description"
                        rows="5"
                        maxlength="1500"
                        class="booking-input mt-2 resize-y"
                        placeholder="Explain what distinguishes this option from the others."
                    >{{ old(
                        'short_description',
                        $isEdit
                            ? $tourOption->short_description
                            : ''
                    ) }}</textarea>
                </div>

                <div>
                    <label
                        for="duration_minutes"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Duration in minutes
                    </label>

                    <input
                        id="duration_minutes"
                        type="number"
                        name="duration_minutes"
                        min="1"
                        max="1440"
                        value="{{ old(
                            'duration_minutes',
                            $isEdit
                                ? $tourOption->duration_minutes
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="600"
                    >

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Example: 600 minutes equals 10 hours.
                    </p>
                </div>
            </div>
        </section>

        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Language and pickup
                </p>

                <h2 class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-newman-navy">
                    Guest-facing arrangements
                </h2>
            </div>

            <div class="mt-6 grid gap-5">
                <div>
                    <label
                        for="languages_text"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Available languages
                    </label>

                    <textarea
                        id="languages_text"
                        name="languages_text"
                        rows="5"
                        maxlength="1000"
                        class="booking-input mt-2 resize-y"
                        placeholder="English&#10;Indonesian"
                    >{{ $languagesText }}</textarea>

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Write one language per line. Comma-separated values are also accepted.
                    </p>
                </div>

                <div>
                    <label
                        for="pickup_type"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Pickup type
                    </label>

                    <select
                        id="pickup_type"
                        name="pickup_type"
                        required
                        class="booking-input booking-select mt-2"
                    >
                        @foreach ($pickupTypes as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(
                                    $currentPickupType
                                    === $value
                                )
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        for="pickup_label"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Pickup label
                    </label>

                    <input
                        id="pickup_label"
                        type="text"
                        name="pickup_label"
                        maxlength="180"
                        value="{{ old(
                            'pickup_label',
                            $isEdit
                                ? $tourOption->pickup_label
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="Example: Pickup from selected Bali areas"
                    >
                </div>

                <div>
                    <label
                        for="confirmation_note"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Confirmation note
                    </label>

                    <textarea
                        id="confirmation_note"
                        name="confirmation_note"
                        rows="4"
                        maxlength="1500"
                        class="booking-input mt-2 resize-y"
                        placeholder="Explain anything Newman must confirm after the booking request."
                    >{{ old(
                        'confirmation_note',
                        $isEdit
                            ? $tourOption->confirmation_note
                            : ''
                    ) }}</textarea>
                </div>
            </div>
        </section>

        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Participant limits
                </p>

                <h2 class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-newman-navy">
                    Group size boundaries
                </h2>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <label
                        for="min_guests"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Minimum participants
                    </label>

                    <input
                        id="min_guests"
                        type="number"
                        name="min_guests"
                        min="1"
                        max="100"
                        required
                        value="{{ old(
                            'min_guests',
                            $isEdit
                                ? $tourOption->min_guests
                                : 1
                        ) }}"
                        class="booking-input mt-2"
                    >
                </div>

                <div>
                    <label
                        for="max_guests"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Maximum participants
                    </label>

                    <input
                        id="max_guests"
                        type="number"
                        name="max_guests"
                        min="1"
                        max="100"
                        value="{{ old(
                            'max_guests',
                            $isEdit
                                ? $tourOption->max_guests
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="12"
                    >
                </div>
            </div>

            <p class="mt-3 text-xs leading-6 text-gray-500">
                Adult, Child, and Infant all count toward the group limit.
            </p>
        </section>
    </main>

    <aside class="space-y-6 lg:sticky lg:top-28">
        <section class="bg-newman-navy p-5 text-white shadow-2xl shadow-newman-navy/15 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Option status
            </p>

            <div class="mt-6 space-y-4">
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

                    <p class="mt-2 text-xs leading-6 text-white/55">
                        Activation requires participant prices and at least one active schedule.
                    </p>
                </div>

                <label class="flex items-start gap-3 border border-white/10 bg-white/5 p-4">
                    <input
                        type="checkbox"
                        name="is_all_inclusive"
                        value="1"
                        class="mt-1 h-4 w-4 accent-newman-gold"
                        @checked(
                            old(
                                'is_all_inclusive',
                                $isEdit
                                    ? (bool) $tourOption->is_all_inclusive
                                    : true
                            )
                        )
                    >

                    <span>
                        <span class="block text-sm font-semibold">
                            All-inclusive option
                        </span>

                        <span class="mt-1 block text-xs leading-5 text-white/55">
                            Requires a clear Included list before activation.
                        </span>
                    </span>
                </label>

                <label class="flex items-start gap-3 border border-white/10 bg-white/5 p-4">
                    <input
                        type="checkbox"
                        name="is_default"
                        value="1"
                        class="mt-1 h-4 w-4 accent-newman-gold"
                        @checked(
                            old(
                                'is_default',
                                $isEdit
                                    ? (bool) $tourOption->is_default
                                    : false
                            )
                        )
                    >

                    <span>
                        <span class="block text-sm font-semibold">
                            Default option
                        </span>

                        <span class="mt-1 block text-xs leading-5 text-white/55">
                            Only one option can be the default for this product.
                        </span>
                    </span>
                </label>

                <button
                    type="submit"
                    class="w-full bg-newman-gold px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-white"
                >
                    {{ $isEdit
                        ? 'Update tour option'
                        : 'Create draft option' }}
                </button>
            </div>
        </section>

        @if ($isEdit)
            <section class="border border-newman-gold/30 bg-newman-sand p-5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Configuration
                </p>

                <h3 class="mt-3 text-xl font-semibold text-newman-navy">
                    Option readiness
                </h3>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="bg-white p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                            Prices
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-newman-navy">
                            {{ $tourOption->prices_count ?? 0 }}
                        </p>
                    </div>

                    <div class="bg-white p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                            Items
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-newman-navy">
                            {{ $tourOption->items_count ?? 0 }}
                        </p>
                    </div>

                    <div class="bg-white p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                            Schedules
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-newman-navy">
                            {{ $tourOption->active_schedules_count ?? 0 }}
                        </p>
                    </div>

                    <div class="bg-white p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                            Discounts
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-newman-navy">
                            {{ $tourOption->discounts_count ?? 0 }}
                        </p>
                    </div>


                    <div class="col-span-2 bg-white p-4">
    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
        Active blackout dates
    </p>

    <p class="mt-2 text-2xl font-semibold text-newman-navy">
        {{ $tourOption->active_blackout_dates_count ?? 0 }}
    </p>
</div>
                </div>

                <a
    href="{{ route(
        'admin.tour-packages.options.prices.edit',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => $tourOption,
        ]
    ) }}"
    class="mt-5 flex min-h-12 w-full items-center justify-center bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition duration-300 hover:-translate-y-0.5 hover:bg-newman-gold hover:text-newman-navy"
>
    Manage participant prices
</a>


<a
    href="{{ route(
        'admin.tour-packages.options.items.index',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => $tourOption,
        ]
    ) }}"
    class="mt-3 flex min-h-12 w-full items-center justify-center border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:border-newman-gold hover:bg-newman-gold"
>
    Manage included & excluded
</a>


<a
    href="{{ route(
        'admin.tour-packages.options.schedules.index',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => $tourOption,
        ]
    ) }}"
    class="mt-3 flex min-h-12 w-full items-center justify-center bg-newman-gold px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:bg-newman-navy hover:text-white"
>
    Manage operating schedules
</a>


<a
    href="{{ route(
        'admin.tour-packages.options.discounts.index',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => $tourOption,
        ]
    ) }}"
    class="mt-3 flex min-h-12 w-full items-center justify-center border border-newman-gold bg-newman-sand px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition duration-300 hover:-translate-y-0.5 hover:bg-newman-gold"
>
    Manage discounts
</a>



<a
    href="{{ route(
        'admin.tour-packages.options.blackouts.index',
        [
            'tourPackage' => $tourPackage,
            'tourOption' => $tourOption,
        ]
    ) }}"
    class="mt-3 flex min-h-12 w-full items-center justify-center bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition duration-300 hover:-translate-y-0.5 hover:bg-newman-gold hover:text-newman-navy"
>
    Manage blackout dates
</a>





                <p class="mt-4 text-xs leading-6 text-gray-600">
                    Prices, items, schedules, discounts, and blackout
                    dates will be managed in Step 15.
                </p>

                <div class="mt-4 border-t border-newman-navy/10 pt-4">
                    <p class="text-xs text-gray-500">
                        Option slug
                    </p>

                    <code class="mt-1 block break-all text-xs font-semibold text-newman-navy">
                        {{ $tourOption->slug }}
                    </code>
                </div>
            </section>
        @endif
    </aside>
</form>
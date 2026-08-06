@php
    $isEdit = isset($tourOptionDiscount)
        && $tourOptionDiscount instanceof \App\Models\TourOptionDiscount
        && $tourOptionDiscount->exists;

    $formAction = $isEdit
        ? route(
            'admin.tour-packages.options.discounts.update',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionDiscount' => $tourOptionDiscount,
            ]
        )
        : route(
            'admin.tour-packages.options.discounts.store',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
            ]
        );

    $datetimeValue = static function (
        mixed $value
    ): string {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d\TH:i');
        }

        return \Illuminate\Support\Carbon::parse(
            $value
        )->format('Y-m-d\TH:i');
    };

    $currentType = old(
        'discount_type',
        $isEdit
            ? $tourOptionDiscount->discount_type
            : 'percentage'
    );

    $currentParticipantTypes = old(
        'participant_types',
        $isEdit
            ? (
                is_array($tourOptionDiscount->participant_types)
                    ? $tourOptionDiscount->participant_types
                    : []
            )
            : ['adult', 'child']
    );

    if (!is_array($currentParticipantTypes)) {
        $currentParticipantTypes = [];
    }

    $appliesToAll = old(
        'applies_to_all',
        $isEdit
            ? empty($tourOptionDiscount->participant_types)
            : false
    );

    $startsAt = old(
        'starts_at',
        $isEdit
            ? $datetimeValue(
                $tourOptionDiscount->starts_at
            )
            : ''
    );

    $endsAt = old(
        'ends_at',
        $isEdit
            ? $datetimeValue(
                $tourOptionDiscount->ends_at
            )
            : ''
    );
@endphp

<div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
            {{ $isEdit ? 'Edit Discount' : 'Add Discount' }}
        </p>

        <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
            {{ $tourOption->title }}
        </h1>

        <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
            Product:
            <strong>{{ $tourPackage->title }}</strong>.
            Discounts are applied to this option without changing
            its participant base prices.
        </p>
    </div>

    <a
        href="{{ route(
            'admin.tour-packages.options.discounts.index',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
            ]
        ) }}"
        class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
    >
        Back to discounts
    </a>
</div>

@if ($errors->any())
    <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-semibold">
            Please check the discount data.
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
    class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_360px]"
>
    @csrf

    @if ($isEdit)
        @method('PATCH')
    @endif

    <main class="space-y-6">
        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Discount information
                </p>

                <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                    Offer value
                </h2>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label
                        for="label"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Discount label
                    </label>

                    <input
                        id="label"
                        type="text"
                        name="label"
                        maxlength="180"
                        required
                        value="{{ old(
                            'label',
                            $isEdit
                                ? $tourOptionDiscount->label
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="Example: Early Booking Offer"
                    >
                </div>

                <div>
                    <label
                        for="discount_type"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Discount type
                    </label>

                    <select
                        id="discount_type"
                        name="discount_type"
                        required
                        class="booking-input booking-select mt-2"
                    >
                        @foreach ($discountTypes as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected($currentType === $value)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        for="discount_value"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Discount value
                    </label>

                    <input
                        id="discount_value"
                        type="number"
                        name="discount_value"
                        min="1"
                        max="999999999999"
                        step="1"
                        required
                        value="{{ old(
                            'discount_value',
                            $isEdit
                                ? $tourOptionDiscount->discount_value
                                : 10
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="10"
                    >

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Percentage: enter 10 for 10%.
                        Fixed: enter whole Rupiah such as 50000.
                    </p>
                </div>
            </div>
        </section>

        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Participant targets
                </p>

                <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                    Who receives the discount?
                </h2>
            </div>

            <input
                type="hidden"
                name="applies_to_all"
                value="0"
            >

            <label class="mt-6 flex items-start gap-3 border border-newman-gold/30 bg-newman-sand p-4">
                <input
                    type="checkbox"
                    name="applies_to_all"
                    value="1"
                    class="mt-1 h-4 w-4 accent-newman-gold"
                    @checked((bool) $appliesToAll)
                >

                <span>
                    <span class="block text-sm font-semibold text-newman-navy">
                        All allowed paid participants
                    </span>

                    <span class="mt-1 block text-xs leading-5 text-gray-500">
                        When enabled, the specific selections below are ignored.
                        Free participants never receive a discount amount.
                    </span>
                </span>
            </label>

            <div class="mt-5 grid gap-4 sm:grid-cols-3">
                @foreach ($participantTypes as $value => $label)
                    <label class="flex items-center gap-3 border border-newman-navy/10 bg-white p-4">
                        <input
                            type="checkbox"
                            name="participant_types[]"
                            value="{{ $value }}"
                            class="h-4 w-4 accent-newman-gold"
                            @checked(
                                in_array(
                                    $value,
                                    $currentParticipantTypes,
                                    true
                                )
                            )
                        >

                        <span class="text-sm font-semibold text-newman-navy">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>

            @error('participant_types')
                <p class="mt-3 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </section>

        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <div class="border-b border-gray-100 pb-5">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Validity period
                </p>

                <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                    Optional start and end
                </h2>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <label
                        for="starts_at"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Starts at
                    </label>

                    <input
                        id="starts_at"
                        type="datetime-local"
                        name="starts_at"
                        value="{{ $startsAt }}"
                        class="booking-input mt-2"
                    >
                </div>

                <div>
                    <label
                        for="ends_at"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Ends at
                    </label>

                    <input
                        id="ends_at"
                        type="datetime-local"
                        name="ends_at"
                        value="{{ $endsAt }}"
                        class="booking-input mt-2"
                    >
                </div>
            </div>

            <p class="mt-3 text-xs leading-6 text-gray-500">
                Leave both empty for an ongoing discount.
                An expired or future discount is automatically ignored
                by the Pricing Service until it becomes valid.
            </p>
        </section>

        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Priority
            </p>

            <div class="mt-5">
                <label
                    for="priority"
                    class="text-sm font-semibold text-newman-navy"
                >
                    Discount priority
                </label>

                <input
                    id="priority"
                    type="number"
                    name="priority"
                    min="0"
                    max="10000"
                    required
                    value="{{ old(
                        'priority',
                        $isEdit
                            ? $tourOptionDiscount->priority
                            : 10
                    ) }}"
                    class="booking-input mt-2"
                >

                <p class="mt-2 text-xs leading-6 text-gray-500">
                    When several discounts are valid, the one with the
                    highest priority is selected. Discounts are not stacked.
                </p>
            </div>
        </section>
    </main>

    <aside class="space-y-6 xl:sticky xl:top-28">
        <section class="bg-newman-navy p-5 text-white sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Discount status
            </p>

            <input
                type="hidden"
                name="is_active"
                value="0"
            >

            <label class="mt-6 flex items-start gap-3 border border-white/10 bg-white/5 p-4">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    class="mt-1 h-4 w-4 accent-newman-gold"
                    @checked(
                        old(
                            'is_active',
                            $isEdit
                                ? (bool) $tourOptionDiscount->is_active
                                : false
                        )
                    )
                >

                <span>
                    <span class="block text-sm font-semibold">
                        Active discount
                    </span>

                    <span class="mt-1 block text-xs leading-5 text-white/55">
                        Only active discounts inside their validity
                        period are used by the Pricing Service.
                    </span>
                </span>
            </label>

            <button
                type="submit"
                class="mt-5 w-full bg-newman-gold px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-white"
            >
                {{ $isEdit
                    ? 'Update discount'
                    : 'Add discount' }}
            </button>
        </section>

        <section class="border border-newman-gold/30 bg-newman-sand p-5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Pricing behavior
            </p>

            <div class="mt-4 space-y-3 text-xs leading-6 text-gray-600">
                <p>
                    Percentage and fixed discounts are calculated
                    per eligible participant.
                </p>

                <p>
                    A fixed discount cannot make a participant price negative.
                </p>

                <p>
                    Free infants remain free and receive no additional discount.
                </p>

                <p>
                    Higher priority wins; discounts are not combined.
                </p>
            </div>
        </section>
    </aside>
</form>
@php
    $isEdit = isset($tourOptionItem)
        && $tourOptionItem instanceof \App\Models\TourOptionItem
        && $tourOptionItem->exists;

    $formAction = $isEdit
        ? route(
            'admin.tour-packages.options.items.update',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
                'tourOptionItem' => $tourOptionItem,
            ]
        )
        : route(
            'admin.tour-packages.options.items.store',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
            ]
        );

    $currentType = old(
        'item_type',
        $isEdit
            ? $tourOptionItem->item_type
            : 'included'
    );

    $currentCategory = old(
        'category',
        $isEdit
            ? $tourOptionItem->category
            : 'other'
    );
@endphp

<div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
            {{ $isEdit ? 'Edit Option Item' : 'Add Option Item' }}
        </p>

        <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
            {{ $tourOption->title }}
        </h1>

        <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
            Product:
            <strong>{{ $tourPackage->title }}</strong>.
            Clearly state whether this service is included or excluded.
        </p>
    </div>

    <a
        href="{{ route(
            'admin.tour-packages.options.items.index',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
            ]
        ) }}"
        class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
    >
        Back to items
    </a>
</div>

@if ($errors->any())
    <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-semibold">
            Please check the option item.
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

    <main>
        <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Item information
            </p>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <label
                        for="item_type"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Item type
                    </label>

                    <select
                        id="item_type"
                        name="item_type"
                        required
                        class="booking-input booking-select mt-2"
                    >
                        @foreach ($itemTypes as $value => $label)
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
                        for="category"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Category
                    </label>

                    <select
                        id="category"
                        name="category"
                        required
                        class="booking-input booking-select mt-2"
                    >
                        @foreach ($categories as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected($currentCategory === $value)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label
                        for="label"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Item label
                    </label>

                    <input
                        id="label"
                        type="text"
                        name="label"
                        required
                        maxlength="180"
                        value="{{ old(
                            'label',
                            $isEdit
                                ? $tourOptionItem->label
                                : ''
                        ) }}"
                        class="booking-input mt-2"
                        placeholder="Example: Hotel pickup and drop-off"
                    >
                </div>

                <div class="sm:col-span-2">
                    <label
                        for="details"
                        class="text-sm font-semibold text-newman-navy"
                    >
                        Additional details
                    </label>

                    <textarea
                        id="details"
                        name="details"
                        rows="5"
                        maxlength="2000"
                        class="booking-input mt-2 resize-y"
                        placeholder="Add factual conditions or limitations."
                    >{{ old(
                        'details',
                        $isEdit
                            ? $tourOptionItem->details
                            : ''
                    ) }}</textarea>
                </div>
            </div>
        </section>
    </main>

    <aside class="space-y-6 lg:sticky lg:top-28">
        <section class="bg-newman-navy p-5 text-white sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Visibility
            </p>

            <div class="mt-6 space-y-4">
                <label class="flex items-start gap-3 border border-white/10 bg-white/5 p-4">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="mt-1 h-4 w-4 accent-newman-gold"
                        @checked(
                            old(
                                'is_active',
                                $isEdit
                                    ? (bool) $tourOptionItem->is_active
                                    : true
                            )
                        )
                    >

                    <span>
                        <span class="block text-sm font-semibold">
                            Active item
                        </span>

                        <span class="mt-1 block text-xs leading-5 text-white/55">
                            Inactive items remain stored but should not appear publicly.
                        </span>
                    </span>
                </label>

                <label class="flex items-start gap-3 border border-white/10 bg-white/5 p-4">
                    <input
                        type="checkbox"
                        name="is_highlighted"
                        value="1"
                        class="mt-1 h-4 w-4 accent-newman-gold"
                        @checked(
                            old(
                                'is_highlighted',
                                $isEdit
                                    ? (bool) $tourOptionItem->is_highlighted
                                    : false
                            )
                        )
                    >

                    <span>
                        <span class="block text-sm font-semibold">
                            Highlight this item
                        </span>

                        <span class="mt-1 block text-xs leading-5 text-white/55">
                            Use only for important guest-facing inclusions or exclusions.
                        </span>
                    </span>
                </label>

                <button
                    type="submit"
                    class="w-full bg-newman-gold px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-white"
                >
                    {{ $isEdit
                        ? 'Update option item'
                        : 'Add option item' }}
                </button>
            </div>
        </section>

        <section class="border border-newman-gold/30 bg-newman-sand p-5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Honest content
            </p>

            <p class="mt-3 text-xs leading-6 text-gray-600">
                Do not mark entrance tickets, meals, guides, or other
                services as included until Newman has confirmed they
                are actually covered by this option’s price.
            </p>
        </section>
    </aside>
</form>
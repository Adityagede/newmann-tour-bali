@extends('admin.layouts.app')

@section('content')
    <div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Participant Prices
            </p>

            <h1 class="mt-3 max-w-4xl text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                {{ $tourOption->title }}
            </h1>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                Product:
                <strong>{{ $tourPackage->title }}</strong>.
                Configure Adult, Child, and Infant pricing for this
                specific booking option.
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a
                href="{{ route(
                    'admin.tour-packages.options.edit',
                    [
                        'tourPackage' => $tourPackage,
                        'tourOption' => $tourOption,
                    ]
                ) }}"
                class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
            >
                Back to option
            </a>

            <a
                href="{{ route(
                    'admin.tour-packages.options.index',
                    $tourPackage
                ) }}"
                class="bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-gold hover:text-newman-navy"
            >
                All options
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 border border-emerald-200 bg-emerald-50 p-4 text-sm leading-6 text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">
                Please check the participant prices.
            </p>

            <ul class="mt-3 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route(
            'admin.tour-packages.options.prices.update',
            [
                'tourPackage' => $tourPackage,
                'tourOption' => $tourOption,
            ]
        ) }}"
        method="POST"
        class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_360px]"
    >
        @csrf
        @method('PUT')

        <main class="space-y-5">
            @foreach (
                $participantDefinitions
                as $participantType => $definition
            )
                @php
                    $price = $pricesByType->get(
                        $participantType
                    );

                    $defaultAllowed = true;

                    $defaultFree =
                        $participantType === 'infant';

                    $isAllowed = old(
                        "prices.{$participantType}.is_allowed",
                        $price
                            ? (bool) $price->is_allowed
                            : $defaultAllowed
                    );

                    $isFree = old(
                        "prices.{$participantType}.is_free",
                        $price
                            ? (bool) $price->is_free
                            : $defaultFree
                    );

                    $basePrice = old(
                        "prices.{$participantType}.base_price",
                        $price
                            ? (int) $price->base_price
                            : (
                                $defaultFree
                                    ? 0
                                    : ''
                            )
                    );
                @endphp

                <section class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-7">
                    <div class="flex flex-col gap-4 border-b border-gray-100 pb-5 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                                {{ ucfirst($participantType) }}
                            </p>

                            <h2 class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-newman-navy">
                                {{ $definition['label'] }}
                                {{ $definition['age_label'] }}
                            </h2>
                        </div>

                        <p class="max-w-sm text-xs leading-6 text-gray-500">
                            This age range is fixed across Newman Tour V2
                            and cannot be changed per option.
                        </p>
                    </div>

                  <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-semibold text-newman-navy">
                                Participant allowed
                            </p>

                            <input
                                type="hidden"
                                name="prices[{{ $participantType }}][is_allowed]"
                                value="0"
                            >

                            <label class="mt-3 flex min-h-16 items-center gap-3 border border-newman-navy/10 bg-newman-sand/50 p-4">
                                <input
                                    type="checkbox"
                                    name="prices[{{ $participantType }}][is_allowed]"
                                    value="1"
                                    class="h-4 w-4 accent-newman-gold"
                                    @checked((bool) $isAllowed)
                                >

                                <span class="text-sm font-semibold text-newman-navy">
                                    Allow {{ strtolower($definition['label']) }}
                                </span>
                            </label>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-newman-navy">
                                Free participant
                            </p>

                            <input
                                type="hidden"
                                name="prices[{{ $participantType }}][is_free]"
                                value="0"
                            >

                            <label class="mt-3 flex min-h-16 items-center gap-3 border border-newman-navy/10 bg-newman-sand/50 p-4">
                                <input
                                    type="checkbox"
                                    name="prices[{{ $participantType }}][is_free]"
                                    value="1"
                                    class="h-4 w-4 accent-newman-gold"
                                    @checked((bool) $isFree)
                                >

                                <span class="text-sm font-semibold text-newman-navy">
                                    No charge
                                </span>
                            </label>
                        </div>

                        <div class="md:col-span-2">
                                <label
                                    for="price_{{ $participantType }}"
                                    class="text-sm font-semibold text-newman-navy"
                                >
                                    Base price
                                </label>

                            <div class="mt-3 flex min-h-16 w-full overflow-hidden border border-newman-navy/10 bg-white">
                            <span class="flex shrink-0 items-center border-r border-newman-navy/10 px-4 text-sm font-bold text-newman-navy">
                                IDR
                            </span>

                            
                            <input
                                id="price_{{ $participantType }}"
                                type="number"
                                name="prices[{{ $participantType }}][base_price]"
                                min="0"
                                max="999999999999"
                                step="1"
                                inputmode="numeric"
                                value="{{ $basePrice }}"
                                class="min-h-16 min-w-0 flex-1 border-0 px-4 text-sm text-newman-navy outline-none"
                                placeholder="750000"
                            >
                        </div>

                            @error(

                                "prices.{$participantType}.base_price"
                            )
                                <p class="mt-2 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="mt-2 text-xs leading-6 text-gray-500">
                                Enter whole Rupiah without dots or commas.
                                Free participants are stored with price zero.
                            </p>
                        </div>
                    </div>
                </section>
            @endforeach
        </main>

        <aside class="space-y-6 xl:sticky xl:top-28">
            <section class="border border-newman-gold/25 bg-newman-navy p-5 text-white shadow-2xl shadow-newman-navy/15 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Save pricing
                </p>

                <h2 class="mt-3 text-2xl font-semibold tracking-[-0.03em]">
                    Participant configuration
                </h2>

                <p class="mt-4 text-xs leading-6 text-white/60">
                    Adult, Child, and Infant all count toward transport
                    capacity, including free infants.
                </p>

                <button
                    type="submit"
                    class="mt-6 w-full bg-newman-gold px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-white"
                >
                    Save participant prices
                </button>
            </section>

            <section class="border border-newman-gold/30 bg-newman-sand p-5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Pricing rules
                </p>

                <div class="mt-4 space-y-3 text-xs leading-6 text-gray-600">
                    <p>
                        A paid and allowed category must have a price
                        greater than zero.
                    </p>

                    <p>
                        A free category is automatically stored with
                        a base price of zero.
                    </p>

                    <p>
                        At least one participant category must remain
                        allowed.
                    </p>

                    <p>
                        Discounts do not replace these prices. They are
                        applied later by the Pricing Service.
                    </p>
                </div>
            </section>

            <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                    Current option
                </p>

                <dl class="mt-4 space-y-4 text-xs">
                    <div>
                        <dt class="text-gray-400">
                            Status
                        </dt>

                        <dd class="mt-1 font-semibold capitalize text-newman-navy">
                            {{ $tourOption->status }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-400">
                            Maximum participants
                        </dt>

                        <dd class="mt-1 font-semibold text-newman-navy">
                            {{ $tourOption->max_guests
                                ?? 'No configured limit' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-400">
                            Currency
                        </dt>

                        <dd class="mt-1 font-semibold text-newman-navy">
                            IDR
                        </dd>
                    </div>
                </dl>
            </section>
        </aside>
    </form>
@endsection
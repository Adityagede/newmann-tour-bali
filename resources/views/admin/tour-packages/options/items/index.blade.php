@extends('admin.layouts.app')

@section('content')
    <div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Included & Excluded
            </p>

            <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                {{ $tourOption->title }}
            </h1>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                Product:
                <strong>{{ $tourPackage->title }}</strong>.
                Maintain a clear and factual list for this specific option.
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
                    'admin.tour-packages.options.items.create',
                    [
                        'tourPackage' => $tourPackage,
                        'tourOption' => $tourOption,
                    ]
                ) }}"
                class="bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-gold hover:text-newman-navy"
            >
                Add item
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-4">
        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                Included
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $includedItems->count() }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                Active included
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $activeIncludedCount }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                Excluded
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $excludedItems->count() }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
                Active excluded
            </p>

            <p class="mt-2 text-3xl font-semibold text-newman-navy">
                {{ $activeExcludedCount }}
            </p>
        </div>
    </div>

    @php
        $groups = [
            [
                'title' => 'Included',
                'description' => 'Services covered by this option.',
                'items' => $includedItems,
            ],
            [
                'title' => 'Excluded',
                'description' => 'Services not covered by this option.',
                'items' => $excludedItems,
            ],
        ];
    @endphp

    <div class="space-y-8">
        @foreach ($groups as $group)
            <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
                <div class="border-b border-gray-100 pb-5">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                        {{ $group['title'] }}
                    </p>

                    <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                        {{ $group['items']->count() }}
                        {{ $group['items']->count() === 1 ? 'item' : 'items' }}
                    </h2>

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        {{ $group['description'] }}
                    </p>
                </div>

                @if ($group['items']->isEmpty())
                    <div class="mt-6 border border-dashed border-newman-navy/20 bg-newman-sand/40 p-8 text-center">
                        <p class="text-sm text-gray-500">
                            No {{ strtolower($group['title']) }} items yet.
                        </p>
                    </div>
                @else
                    <div class="mt-6 space-y-4">
                        @foreach ($group['items'] as $item)
                            <article class="grid gap-5 border border-newman-navy/10 p-5 lg:grid-cols-[minmax(0,1fr)_230px]">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-lg font-semibold text-newman-navy">
                                            {{ $item->label }}
                                        </h3>

                                        <span class="bg-newman-sand px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy">
                                            {{ $categories[$item->category]
                                                ?? $item->category }}
                                        </span>

                                        @if ($item->is_highlighted)
                                            <span class="bg-newman-gold px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy">
                                                Highlight
                                            </span>
                                        @endif

                                        @unless ($item->is_active)
                                            <span class="bg-gray-100 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                                Inactive
                                            </span>
                                        @endunless
                                    </div>

                                    @if ($item->details)
                                        <p class="mt-3 text-sm leading-7 text-gray-600">
                                            {{ $item->details }}
                                        </p>
                                    @endif
                                </div>

                                <div class="grid content-start grid-cols-2 gap-2">
                                    <form
                                        action="{{ route(
                                            'admin.tour-packages.options.items.move',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourOption' => $tourOption,
                                                'tourOptionItem' => $item,
                                            ]
                                        ) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="direction"
                                            value="up"
                                        >

                                        <button
                                            type="submit"
                                            @disabled($loop->first)
                                            class="w-full border border-newman-navy/15 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy disabled:opacity-30"
                                        >
                                            Earlier
                                        </button>
                                    </form>

                                    <form
                                        action="{{ route(
                                            'admin.tour-packages.options.items.move',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourOption' => $tourOption,
                                                'tourOptionItem' => $item,
                                            ]
                                        ) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="direction"
                                            value="down"
                                        >

                                        <button
                                            type="submit"
                                            @disabled($loop->last)
                                            class="w-full border border-newman-navy/15 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy disabled:opacity-30"
                                        >
                                            Later
                                        </button>
                                    </form>

                                    <a
                                        href="{{ route(
                                            'admin.tour-packages.options.items.edit',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourOption' => $tourOption,
                                                'tourOptionItem' => $item,
                                            ]
                                        ) }}"
                                        class="flex items-center justify-center border border-newman-gold bg-newman-sand px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route(
                                            'admin.tour-packages.options.items.destroy',
                                            [
                                                'tourPackage' => $tourPackage,
                                                'tourOption' => $tourOption,
                                                'tourOptionItem' => $item,
                                            ]
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm('Remove this option item?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="w-full border border-red-200 bg-red-50 px-3 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-red-700"
                                        >
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        @endforeach
    </div>
@endsection
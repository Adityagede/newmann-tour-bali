@extends('admin.layouts.app')

@section('content')
    @php
        $readyToPublish = (bool) $report['ready_to_publish'];

        $checkClasses = static function (
            array $check
        ): string {
            if ($check['passed']) {
                return 'border-emerald-200 bg-emerald-50';
            }

            if ($check['severity'] === 'warning') {
                return 'border-amber-200 bg-amber-50';
            }

            return 'border-red-200 bg-red-50';
        };

        $badgeClasses = static function (
            array $check
        ): string {
            if ($check['passed']) {
                return 'bg-emerald-100 text-emerald-700';
            }

            if ($check['severity'] === 'warning') {
                return 'bg-amber-100 text-amber-700';
            }

            return 'bg-red-100 text-red-700';
        };

        $checkLabel = static function (
            array $check
        ): string {
            if ($check['passed']) {
                return 'Passed';
            }

            return $check['severity'] === 'warning'
                ? 'Warning'
                : 'Blocking';
        };
    @endphp

    <div class="mb-7 flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
                Publishing Readiness
            </p>

            <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
                {{ $tourPackage->title }}
            </h1>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                Review all blocking requirements and warnings
                before activating this Tour Product.
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a
                href="{{ route(
                    'admin.tour-packages.edit',
                    [
                        'tourPackage' => $tourPackage,
                    ]
                ) }}"
                class="border border-newman-navy/15 bg-white px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:border-newman-gold hover:bg-newman-gold"
            >
                Back to product
            </a>

            <a
                href="{{ route(
                    'admin.tour-packages.options.index',
                    [
                        'tourPackage' => $tourPackage,
                    ]
                ) }}"
                class="bg-newman-navy px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-gold hover:text-newman-navy"
            >
                Manage options
            </a>
        </div>
    </div>

    <section
        class="mb-6 border p-6 sm:p-8
            {{ $readyToPublish
                ? 'border-emerald-200 bg-emerald-50'
                : 'border-red-200 bg-red-50' }}"
    >
        <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p
                    class="text-xs font-bold uppercase tracking-[0.3em]
                        {{ $readyToPublish
                            ? 'text-emerald-700'
                            : 'text-red-700' }}"
                >
                    Overall result
                </p>

                <h2 class="mt-3 text-3xl font-semibold text-newman-navy">
                    {{ $readyToPublish
                        ? 'Ready to publish'
                        : 'Not ready to publish' }}
                </h2>

                <p class="mt-3 max-w-2xl text-sm leading-7 text-gray-600">
                    @if ($readyToPublish)
                        The product currently passes every blocking
                        publishing requirement.
                    @else
                        Resolve all blocking checks before changing
                        the Tour Product status to Active.
                    @endif
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="min-w-32 bg-white p-4 text-center">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                        Blockers
                    </p>

                    <p class="mt-2 text-3xl font-semibold text-newman-navy">
                        {{ $report['blocking_count'] }}
                    </p>
                </div>

                <div class="min-w-32 bg-white p-4 text-center">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                        Warnings
                    </p>

                    <p class="mt-2 text-3xl font-semibold text-newman-navy">
                        {{ $report['warning_count'] }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
        <div class="bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                Gallery
            </p>

            <p class="mt-2 text-2xl font-semibold text-newman-navy">
                {{ $report['summary']['gallery_images'] }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                Highlights
            </p>

            <p class="mt-2 text-2xl font-semibold text-newman-navy">
                {{ $report['summary']['highlights'] }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                Roadmap stops
            </p>

            <p class="mt-2 text-2xl font-semibold text-newman-navy">
                {{ $report['summary']['active_shared_stops'] }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                Options
            </p>

            <p class="mt-2 text-2xl font-semibold text-newman-navy">
                {{ $report['summary']['all_options'] }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                Active options
            </p>

            <p class="mt-2 text-2xl font-semibold text-newman-navy">
                {{ $report['summary']['active_options'] }}
            </p>
        </div>

        <div class="bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">
                Default options
            </p>

            <p class="mt-2 text-2xl font-semibold text-newman-navy">
                {{ $report['summary']['default_options'] }}
            </p>
        </div>
    </div>

    <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
        <div class="border-b border-gray-100 pb-5">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Tour Product
            </p>

            <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                Product-level checks
            </h2>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            @foreach ($report['checks'] as $check)
                <article class="border p-4 {{ $checkClasses($check) }}">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-newman-navy">
                                {{ $check['label'] }}
                            </h3>

                            @unless ($check['passed'])
                                <p class="mt-2 text-xs leading-6 text-gray-600">
                                    {{ $check['message'] }}
                                </p>
                            @endunless

                            @if (
                                isset($check['context']['current_count'])
                                || isset($check['context']['minimum_count'])
                            )
                                <p class="mt-2 text-[11px] font-semibold text-gray-500">
                                    Current:
                                    {{ $check['context']['current_count'] ?? '—' }}

                                    @isset($check['context']['minimum_count'])
                                        · Minimum:
                                        {{ $check['context']['minimum_count'] }}
                                    @endisset
                                </p>
                            @endif
                        </div>

                        <span class="shrink-0 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] {{ $badgeClasses($check) }}">
                            {{ $checkLabel($check) }}
                        </span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <div class="mt-8 space-y-8">
        @forelse ($report['option_reports'] as $optionReport)
            <section class="border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex flex-col gap-5 border-b border-gray-100 pb-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                                Tour Option
                            </p>

                            @if ($optionReport['is_default'])
                                <span class="bg-newman-gold px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-newman-navy">
                                    Default
                                </span>
                            @endif

                            <span
                                class="px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em]
                                    {{ $optionReport['status'] === 'active'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-gray-100 text-gray-600' }}"
                            >
                                {{ ucfirst($optionReport['status']) }}
                            </span>
                        </div>

                        <h2 class="mt-3 text-2xl font-semibold text-newman-navy">
                            {{ $optionReport['title'] }}
                        </h2>

                        <p class="mt-2 text-xs leading-6 text-gray-500">
                            Configuration:
                            <strong>
                                {{ $optionReport['configuration_ready']
                                    ? 'Ready'
                                    : 'Not ready' }}
                            </strong>
                            · Blockers:
                            {{ $optionReport['blocking_count'] }}
                            · Warnings:
                            {{ $optionReport['warning_count'] }}
                        </p>
                    </div>

                    <a
                        href="{{ route(
                            'admin.tour-packages.options.edit',
                            [
                                'tourPackage' => $tourPackage,
                                'tourOption' => $optionReport['option_id'],
                            ]
                        ) }}"
                        class="border border-newman-gold bg-newman-sand px-5 py-3 text-center text-xs font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-newman-gold"
                    >
                        Edit option
                    </a>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
                    <div class="bg-newman-sand/60 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                            Prices
                        </p>

                        <p class="mt-2 text-xl font-semibold text-newman-navy">
                            {{ $optionReport['summary']['prices'] }}
                        </p>
                    </div>

                    <div class="bg-newman-sand/60 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                            Allowed prices
                        </p>

                        <p class="mt-2 text-xl font-semibold text-newman-navy">
                            {{ $optionReport['summary']['allowed_prices'] }}
                        </p>
                    </div>

                    <div class="bg-newman-sand/60 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                            Languages
                        </p>

                        <p class="mt-2 text-xl font-semibold text-newman-navy">
                            {{ $optionReport['summary']['languages'] }}
                        </p>
                    </div>

                    <div class="bg-newman-sand/60 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                            Included
                        </p>

                        <p class="mt-2 text-xl font-semibold text-newman-navy">
                            {{ $optionReport['summary']['active_included_items'] }}
                        </p>
                    </div>

                    <div class="bg-newman-sand/60 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                            Excluded
                        </p>

                        <p class="mt-2 text-xl font-semibold text-newman-navy">
                            {{ $optionReport['summary']['active_excluded_items'] }}
                        </p>
                    </div>

                    <div class="bg-newman-sand/60 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
                            Future schedules
                        </p>

                        <p class="mt-2 text-xl font-semibold text-newman-navy">
                            {{ $optionReport['summary']['future_schedules'] }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-2">
                    @foreach ($optionReport['checks'] as $check)
                        <article class="border p-4 {{ $checkClasses($check) }}">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-newman-navy">
                                        {{ $check['label'] }}
                                    </h3>

                                    @unless ($check['passed'])
                                        <p class="mt-2 text-xs leading-6 text-gray-600">
                                            {{ $check['message'] }}
                                        </p>
                                    @endunless
                                </div>

                                <span class="shrink-0 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] {{ $badgeClasses($check) }}">
                                    {{ $checkLabel($check) }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            <section class="border border-dashed border-newman-navy/20 bg-white p-8 text-center">
                <h2 class="text-2xl font-semibold text-newman-navy">
                    No Tour Options
                </h2>

                <p class="mt-3 text-sm leading-7 text-gray-500">
                    Create at least one Tour Option before publishing.
                </p>
            </section>
        @endforelse
    </div>

    <section class="mt-8 border border-newman-gold/30 bg-newman-sand p-5 sm:p-7">
        <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
            Publishing order
        </p>

        <div class="mt-4 grid gap-3 text-sm leading-7 text-gray-600 md:grid-cols-2">
            <p>
                1. Resolve every Tour Option blocker.
            </p>

            <p>
                2. Activate a fully configured default option.
            </p>

            <p>
                3. Return to this page and recheck the result.
            </p>

            <p>
                4. Activate the Tour Product only after it says Ready.
            </p>
        </div>
    </section>
@endsection
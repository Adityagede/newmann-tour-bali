@extends('admin.layouts.app')

@section('content')
    @php
        $isPublished = $tourPackage->status === 'active';
        $readyToPublish = $report['ready_to_publish'];
        $packageChecks = collect($report['checks'])->where('passed', false);
        $optionReports = collect($report['option_reports']);
        $statusUrl = route('admin.tour-packages.readiness.status', $tourPackage);
    @endphp

    <div class="mb-6">
        <h1 class="text-3xl font-semibold tracking-tight text-newman-navy">Publishing</h1>
        <p class="mt-2 text-sm text-gray-600">Check the package and its booking options before making it public.</p>
    </div>

    @if ($errors->any())
        <div role="alert" class="mb-5 border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section @class([
        'mb-6 flex flex-wrap items-center justify-between gap-4 border p-5 sm:p-6',
        'border-emerald-200 bg-emerald-50' => $readyToPublish,
        'border-amber-200 bg-amber-50' => ! $readyToPublish,
    ])>
        <div>
            <h2 class="text-xl font-semibold text-newman-navy">
                {{ $readyToPublish ? ($isPublished ? 'Published' : 'Ready to publish') : 'Setup needed' }}
            </h2>
            <p class="mt-1 text-sm text-gray-700">
                {{ $report['blocking_count'] }} {{ Str::plural('requirement', $report['blocking_count']) }} to complete
                &middot; {{ $report['warning_count'] }} {{ Str::plural('warning', $report['warning_count']) }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if (! $isPublished && $readyToPublish)
                <form action="{{ $statusUrl }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" name="status" value="active" class="rounded bg-newman-navy px-4 py-3 text-sm font-semibold text-white hover:bg-newman-blue">
                        Publish package
                    </button>
                </form>
            @endif
            @if ($isPublished || $tourPackage->status === 'inactive')
                <form action="{{ $statusUrl }}" method="POST" class="flex flex-wrap items-center gap-2">
                    @csrf
                    @method('PATCH')
                    @if ($isPublished)
                        <button type="submit" name="status" value="inactive"
                            class="rounded border border-newman-navy/20 bg-white px-4 py-2 text-sm font-semibold hover:bg-newman-sand">
                            Unpublish
                        </button>
                    @endif
                    <button type="submit" name="status" value="draft" class="rounded px-3 py-2 text-sm font-semibold text-newman-blue hover:underline">
                        Move to draft
                    </button>
                </form>
            @endif
        </div>
    </section>

    @if ($packageChecks->isNotEmpty())
        <section class="mb-6 border border-newman-navy/10 bg-white p-5 sm:p-6">
            <h2 class="text-lg font-semibold text-newman-navy">Package checklist</h2>
            <ul class="mt-3 divide-y divide-newman-navy/10">
                @foreach ($packageChecks as $check)
                    @include('admin.tour-packages.readiness._check', [
                        'check' => $check,
                        'url' => \App\Support\TourPackageEditor::packageCheckUrl($tourPackage, $check['key']),
                    ])
                @endforeach
            </ul>
        </section>
    @endif

    <section id="booking-options" class="scroll-mt-32 space-y-4">
        <h2 class="text-lg font-semibold text-newman-navy">Booking options</h2>
        @forelse ($optionReports as $optionReport)
            @php
                $optionParameters = ['tourPackage' => $tourPackage, 'tourOption' => $optionReport['option_id']];
                $optionChecks = collect($optionReport['checks'])->where('passed', false);
            @endphp
            <article class="border border-newman-navy/10 bg-white p-5 sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="break-words text-lg font-semibold text-newman-navy">{{ $optionReport['title'] }}</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ ucfirst($optionReport['status']) }}
                            @if ($optionReport['is_default'])
                                &middot; Default
                            @endif
                            &middot; {{ $optionReport['configuration_ready'] ? 'Configuration complete' : 'Configuration needed' }}
                        </p>
                    </div>
                    @if ($optionReport['status'] !== 'active' && $optionReport['configuration_ready'])
                        <form action="{{ route('admin.tour-packages.readiness.activate-option', $optionParameters) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded bg-newman-gold px-4 py-2 text-sm font-semibold text-newman-navy hover:bg-newman-sand">
                                Activate option
                            </button>
                        </form>
                    @else
                        <a href="{{ route('admin.tour-packages.options.edit', $optionParameters) }}" class="text-sm font-semibold text-newman-blue hover:underline">
                            Edit option
                        </a>
                    @endif
                </div>
                @if ($optionChecks->isNotEmpty())
                    <ul class="mt-4 divide-y divide-newman-navy/10 border-t border-newman-navy/10">
                        @foreach ($optionChecks as $check)
                            @include('admin.tour-packages.readiness._check', [
                                'check' => $check,
                                'url' => \App\Support\TourPackageEditor::optionCheckUrl($tourPackage, $optionReport['option_id'], $check['key']),
                            ])
                        @endforeach
                    </ul>
                @endif
            </article>
        @empty
            <p class="border border-dashed border-newman-navy/20 bg-white p-5 text-sm text-gray-600">
                Add a booking option to set up pricing and schedules.
            </p>
        @endforelse
    </section>
@endsection

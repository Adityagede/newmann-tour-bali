@extends('admin.layouts.app')

@section('content')
@php
    $statusClasses = [
        'active' => 'bg-green-100 text-green-700',
        'inactive' => 'bg-red-100 text-red-700',
        'draft' => 'bg-yellow-100 text-yellow-700',
    ];
@endphp

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
            Tour Packages
        </p>

        <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
            Manage tour packages
        </h1>

        <p class="mt-3 max-w-2xl text-sm leading-7 text-gray-600">
            Data ini nanti akan dipakai untuk homepage, halaman tours, tour detail, dan booking dropdown.
        </p>
    </div>

    <a
    href="{{ route('admin.tour-packages.create') }}"
    class="bg-newman-navy px-5 py-4 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-blue"
>
    Add Tour
</a>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-gray-400">Total Tours</p>
        <p class="mt-3 text-4xl font-semibold text-newman-navy">{{ $stats['total'] }}</p>
    </div>

    <div class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-gray-400">Active</p>
        <p class="mt-3 text-4xl font-semibold text-green-700">{{ $stats['active'] }}</p>
    </div>

    <div class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-gray-400">Popular</p>
        <p class="mt-3 text-4xl font-semibold text-newman-blue">{{ $stats['popular'] }}</p>
    </div>

    <div class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-gray-400">Featured</p>
        <p class="mt-3 text-4xl font-semibold text-newman-gold">{{ $stats['featured'] }}</p>
    </div>
</div>

<section class="mt-8 border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Tour Table
            </p>

            <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                Current tour data
            </h2>
        </div>

        <div class="-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
            <div class="flex min-w-max gap-3">
                <a
                    href="{{ route('admin.tour-packages.index') }}"
                    class="border border-newman-navy/10 px-4 py-3 text-xs font-bold uppercase tracking-[0.16em] {{ ! $status && ! $category ? 'bg-newman-navy text-white' : 'bg-white text-newman-navy' }}"
                >
                    All
                </a>

                <a
                    href="{{ route('admin.tour-packages.index', ['status' => 'active']) }}"
                    class="border border-newman-navy/10 px-4 py-3 text-xs font-bold uppercase tracking-[0.16em] {{ $status === 'active' ? 'bg-newman-navy text-white' : 'bg-white text-newman-navy' }}"
                >
                    Active
                </a>

                @foreach ($categories as $item)
                    <a
                        href="{{ route('admin.tour-packages.index', ['category' => $item]) }}"
                        class="border border-newman-navy/10 px-4 py-3 text-xs font-bold uppercase tracking-[0.16em] {{ $category === $item ? 'bg-newman-navy text-white' : 'bg-white text-newman-navy' }}"
                    >
                        {{ $item }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[1100px] text-left text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-xs uppercase tracking-[0.18em] text-gray-400">
                    <th class="py-4 pr-4">Tour</th>
                    <th class="py-4 pr-4">Category</th>
                    <th class="py-4 pr-4">Area</th>
                    <th class="py-4 pr-4">Duration</th>
                    <th class="py-4 pr-4">Vehicle</th>
                    <th class="py-4 pr-4">Rating</th>
                    <th class="py-4 pr-4">Popular</th>
                    <th class="py-4 pr-4">Status</th>
                    <th class="py-4 text-right">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($tourPackages as $tour)
                    <tr class="border-b border-gray-100 align-top">
                        <td class="py-4 pr-4">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-20 shrink-0 overflow-hidden bg-newman-sand">
                                    @if ($tour->main_image)
                                        <img
                                            src="{{ asset($tour->main_image) }}"
                                            alt="{{ $tour->title }}"
                                            class="h-full w-full object-cover"
                                        >
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xs font-bold text-newman-navy/40">
                                            No Image
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <p class="font-semibold leading-6 text-newman-navy">
                                        {{ $tour->title }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $tour->slug }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 pr-4 capitalize">
                            {{ $tour->category ?? '-' }}
                        </td>

                        <td class="py-4 pr-4">
                            {{ $tour->area ?? '-' }}
                        </td>

                        <td class="py-4 pr-4">
                            {{ $tour->duration ?? '-' }}
                        </td>

                        <td class="py-4 pr-4">
                            {{ $tour->vehicle ?? '-' }}
                        </td>

                        <td class="py-4 pr-4">
                            {{ $tour->rating }}
                        </td>

                        <td class="py-4 pr-4">
                            @if ($tour->is_popular)
                                <span class="bg-newman-gold/20 px-3 py-2 text-xs font-bold uppercase text-newman-navy">
                                    Yes
                                </span>
                            @else
                                <span class="bg-gray-100 px-3 py-2 text-xs font-bold uppercase text-gray-500">
                                    No
                                </span>
                            @endif
                        </td>

                        <td class="py-4 pr-4">
                            <span class="px-3 py-2 text-xs font-bold uppercase {{ $statusClasses[$tour->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $tour->status }}
                            </span>
                        </td>

                        <td class="py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a
                                    href="{{ url('/tours/' . $tour->slug) }}"
                                    target="_blank"
                                    class="font-bold uppercase tracking-[0.14em] text-newman-blue hover:text-newman-gold"
                                >
                                    View
                                </a>

                                <a
                                    href="{{ route('admin.tour-packages.edit', $tour) }}"
                                    class="font-bold uppercase tracking-[0.14em] text-newman-gold hover:text-newman-blue"
                                >
                                    Edit
                                </a>

                                <form
                                    action="{{ route('admin.tour-packages.destroy', $tour) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete tour package {{ $tour->title }}? This action cannot be undone.');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="font-bold uppercase tracking-[0.14em] text-red-500 hover:text-red-700"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-gray-500">
                            Belum ada tour package.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $tourPackages->links() }}
    </div>
</section>
@endsection
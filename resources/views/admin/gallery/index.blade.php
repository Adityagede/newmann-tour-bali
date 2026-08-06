@extends('admin.layouts.app')

@section('content')
@php
    $statusClasses = [
        'active' => 'bg-green-100 text-green-700',
        'draft' => 'bg-yellow-100 text-yellow-700',
        'inactive' => 'bg-red-100 text-red-700',
    ];
@endphp

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-newman-gold">
            Gallery
        </p>

        <h1 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-newman-navy sm:text-5xl">
            Guest moments
        </h1>

        <p class="mt-3 max-w-2xl text-sm leading-7 text-gray-600">
            Kelola foto perjalanan bersama tamu yang nanti tampil
            di homepage dan halaman gallery.
        </p>
    </div>

    <a
        href="{{ route('admin.gallery.create') }}"
        class="bg-newman-navy px-5 py-4 text-center text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-newman-blue"
    >
        Add Moment
    </a>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="border border-gray-100 bg-white p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-gray-400">
            Total
        </p>

        <p class="mt-3 text-4xl font-semibold text-newman-navy">
            {{ $stats['total'] }}
        </p>
    </div>

    <div class="border border-gray-100 bg-white p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-gray-400">
            Active
        </p>

        <p class="mt-3 text-4xl font-semibold text-green-700">
            {{ $stats['active'] }}
        </p>
    </div>

    <div class="border border-gray-100 bg-white p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-gray-400">
            Featured
        </p>

        <p class="mt-3 text-4xl font-semibold text-newman-gold">
            {{ $stats['featured'] }}
        </p>
    </div>

    <div class="border border-gray-100 bg-white p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-gray-400">
            Draft
        </p>

        <p class="mt-3 text-4xl font-semibold text-newman-blue">
            {{ $stats['draft'] }}
        </p>
    </div>
</div>

<section class="mt-8 border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Gallery Table
            </p>

            <h2 class="mt-2 text-2xl font-semibold text-newman-navy">
                Current gallery data
            </h2>
        </div>

        <form
            action="{{ route('admin.gallery.index') }}"
            method="GET"
            class="grid gap-3 sm:grid-cols-[1fr_180px_auto]"
        >
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search title or location"
                class="booking-input"
            >

            <select
                name="status"
                class="booking-input booking-select"
            >
                <option value="">All status</option>
                <option value="active" @selected($status === 'active')>
                    Active
                </option>
                <option value="draft" @selected($status === 'draft')>
                    Draft
                </option>
                <option value="inactive" @selected($status === 'inactive')>
                    Inactive
                </option>
            </select>

            <button
                type="submit"
                class="bg-newman-navy px-5 py-3 text-xs font-bold uppercase tracking-[0.14em] text-white"
            >
                Filter
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[1050px] text-left text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-xs uppercase tracking-[0.18em] text-gray-400">
                    <th class="py-4 pr-4">Moment</th>
                    <th class="py-4 pr-4">Category</th>
                    <th class="py-4 pr-4">Location</th>
                    <th class="py-4 pr-4">Order</th>
                    <th class="py-4 pr-4">Size</th>
                    <th class="py-4 pr-4">Featured</th>
                    <th class="py-4 pr-4">Status</th>
                    <th class="py-4 text-right">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($galleryMoments as $moment)
                    <tr class="border-b border-gray-100 align-top">
                        <td class="py-4 pr-4">
                            <div class="flex items-center gap-4">
                                <div class="h-20 w-24 shrink-0 overflow-hidden bg-newman-sand">
                                    <img
                                        src="{{ asset($moment->image_path) }}"
                                        alt="{{ $moment->alt_text ?: $moment->title }}"
                                        class="h-full w-full object-cover"
                                    >
                                </div>

                                <div class="max-w-xs">
                                    <p class="font-semibold leading-6 text-newman-navy">
                                        {{ $moment->title ?: 'Untitled moment' }}
                                    </p>

                                    <p class="mt-1 line-clamp-2 text-xs leading-5 text-gray-400">
                                        {{ $moment->caption ?: 'No caption' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 pr-4 capitalize">
                            {{ $moment->category ?: '-' }}
                        </td>

                        <td class="py-4 pr-4">
                            {{ $moment->location ?: '-' }}
                        </td>

                        <td class="py-4 pr-4">
                            {{ $moment->sort_order }}
                        </td>

                        <td class="py-4 pr-4 capitalize">
                            {{ $moment->display_size }}
                        </td>

                        <td class="py-4 pr-4">
                            @if ($moment->is_featured)
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
                            <span class="px-3 py-2 text-xs font-bold uppercase {{ $statusClasses[$moment->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $moment->status }}
                            </span>
                        </td>

                        <td class="py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a
                                    href="{{ route('admin.gallery.edit', $moment) }}"
                                    class="font-bold uppercase tracking-[0.14em] text-newman-gold hover:text-newman-blue"
                                >
                                    Edit
                                </a>

                                <form
                                    action="{{ route('admin.gallery.destroy', $moment) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this gallery moment? This action cannot be undone.');"
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
                        <td
                            colspan="8"
                            class="py-10 text-center text-gray-500"
                        >
                            Belum ada gallery moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $galleryMoments->links() }}
    </div>
</section>
@endsection
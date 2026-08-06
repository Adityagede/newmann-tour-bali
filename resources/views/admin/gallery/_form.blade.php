@php
    $isEdit = isset($galleryMoment);
    $moment = $galleryMoment ?? null;
@endphp

<form
    action="{{ $isEdit
        ? route('admin.gallery.update', $moment)
        : route('admin.gallery.store')
    }}"
    method="POST"
    enctype="multipart/form-data"
    class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]"
>
    @csrf

    @if ($isEdit)
        @method('PATCH')
    @endif

    <section class="space-y-6">
        <div class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Moment Information
            </p>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-sm font-semibold text-newman-navy">
                        Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $moment?->title) }}"
                        class="booking-input mt-2"
                        placeholder="Example: A relaxed Ubud day with our guests"
                    >
                </div>

                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        Category
                    </label>

                    <select
                        name="category"
                        class="booking-input booking-select mt-2"
                    >
                        <option value="">Select category</option>

                        @foreach ([
                            'family' => 'Family',
                            'group' => 'Group',
                            'couple' => 'Couple',
                            'ubud' => 'Ubud',
                            'south' => 'South Bali',
                            'transport' => 'Transport',
                            'other' => 'Other',
                        ] as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(
                                    old('category', $moment?->category)
                                    === $value
                                )
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold text-newman-navy">
                        Location
                    </label>

                    <input
                        type="text"
                        name="location"
                        value="{{ old('location', $moment?->location) }}"
                        class="booking-input mt-2"
                        placeholder="Example: Ubud, Bali"
                    >
                </div>

                <div class="sm:col-span-2">
                    <label class="text-sm font-semibold text-newman-navy">
                        Caption
                    </label>

                    <textarea
                        name="caption"
                        rows="5"
                        class="booking-input mt-2 resize-none"
                        placeholder="Write a short and natural story about this guest moment."
                    >{{ old('caption', $moment?->caption) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="text-sm font-semibold text-newman-navy">
                        Image Alt Text
                    </label>

                    <input
                        type="text"
                        name="alt_text"
                        value="{{ old('alt_text', $moment?->alt_text) }}"
                        class="booking-input mt-2"
                        placeholder="Example: Newman with guests at Ubud rice terrace"
                    >

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        Alt text menjelaskan isi foto untuk aksesibilitas
                        dan saat gambar tidak dapat dimuat.
                    </p>
                </div>
            </div>
        </div>

        <div class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Gallery Image
            </p>

            @if ($isEdit && $moment->image_path)
                <div class="mt-5 overflow-hidden bg-newman-sand">
                    <img
                        src="{{ asset($moment->image_path) }}"
                        alt="{{ $moment->alt_text ?: $moment->title }}"
                        class="h-72 w-full object-cover"
                    >
                </div>
            @endif

            <div class="mt-5">
                <label class="text-sm font-semibold text-newman-navy">
                    {{ $isEdit ? 'Replace Image' : 'Upload Image' }}
                </label>

                <input
                    type="file"
                    name="image"
                    accept="image/jpeg,image/png,image/webp"
                    class="mt-2 block w-full text-sm text-newman-navy file:mr-4 file:border-0 file:bg-newman-navy file:px-4 file:py-3 file:text-xs file:font-bold file:uppercase file:tracking-[0.14em] file:text-white"
                    {{ $isEdit ? '' : 'required' }}
                >

                <p class="mt-3 text-xs leading-6 text-gray-500">
                    JPG, PNG, atau WEBP. Maksimal 5MB.
                    Foto landscape atau portrait tetap bisa digunakan.
                </p>
            </div>
        </div>
    </section>

    <aside class="space-y-6">
        <div class="border border-newman-gold/25 bg-newman-navy p-5 text-white shadow-2xl shadow-newman-navy/15 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Display Setting
            </p>

            <div class="mt-6 grid gap-5">
                <div>
                    <label class="text-sm font-semibold">
                        Status
                    </label>

                    <select
                        name="status"
                        class="booking-input booking-select mt-2"
                    >
                        <option
                            value="active"
                            @selected(
                                old('status', $moment?->status ?? 'active')
                                === 'active'
                            )
                        >
                            Active
                        </option>

                        <option
                            value="draft"
                            @selected(
                                old('status', $moment?->status)
                                === 'draft'
                            )
                        >
                            Draft
                        </option>

                        <option
                            value="inactive"
                            @selected(
                                old('status', $moment?->status)
                                === 'inactive'
                            )
                        >
                            Inactive
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold">
                        Display Size
                    </label>

                    <select
                        name="display_size"
                        class="booking-input booking-select mt-2"
                    >
                        <option
                            value="regular"
                            @selected(
                                old(
                                    'display_size',
                                    $moment?->display_size ?? 'regular'
                                ) === 'regular'
                            )
                        >
                            Regular
                        </option>

                        <option
                            value="large"
                            @selected(
                                old('display_size', $moment?->display_size)
                                === 'large'
                            )
                        >
                            Large / Featured Layout
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold">
                        Sort Order
                    </label>

                    <input
                        type="number"
                        name="sort_order"
                        value="{{ old(
                            'sort_order',
                            $moment?->sort_order ?? 0
                        ) }}"
                        min="0"
                        max="9999"
                        class="booking-input mt-2"
                    >

                    <p class="mt-2 text-xs leading-6 text-white/50">
                        Angka lebih kecil akan tampil lebih awal.
                    </p>
                </div>

                <label class="flex items-start gap-3 text-sm font-semibold">
                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        class="mt-1 h-4 w-4"
                        @checked(
                            old(
                                'is_featured',
                                $moment?->is_featured ?? false
                            )
                        )
                    >

                    <span>
                        Show on homepage guest moments
                    </span>
                </label>

                <button
                    type="submit"
                    class="bg-newman-gold px-5 py-4 text-sm font-bold uppercase tracking-[0.16em] text-newman-navy transition hover:bg-white"
                >
                    {{ $isEdit ? 'Update Moment' : 'Save Moment' }}
                </button>
            </div>
        </div>

        <div class="border border-gray-100 bg-white p-5 shadow-sm shadow-newman-navy/5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-newman-gold">
                Status Guide
            </p>

            <div class="mt-4 space-y-3 text-sm leading-7 text-gray-600">
                <p>
                    <strong>Active:</strong> siap ditampilkan di website.
                </p>

                <p>
                    <strong>Draft:</strong> masih disiapkan dan tidak ditampilkan.
                </p>

                <p>
                    <strong>Inactive:</strong> disembunyikan tanpa menghapus data.
                </p>
            </div>
        </div>
    </aside>
</form>
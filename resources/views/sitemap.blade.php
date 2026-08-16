

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>{{ url('/') }}</loc>
    </url>

    <url>
        <loc>{{ url('/about') }}</loc>
    </url>

    <url>
        <loc>{{ url('/tours') }}</loc>
    </url>

    <url>
        <loc>{{ url('/gallery') }}</loc>
    </url>

    <url>
        <loc>{{ url('/contact') }}</loc>
    </url>

   @foreach ($tours as $tour)
    <url>
        <loc>{{ route('tours.detail', $tour->slug) }}</loc>

        @if ($tour->updated_at)
            <lastmod>{{ $tour->updated_at->toAtomString() }}</lastmod>
        @endif
    </url>
@endforeach

</urlset>
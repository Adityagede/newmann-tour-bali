

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
    @foreach (['/', '/about', '/tours', '/gallery', '/contact', '/custom-trip'] as $path)
        @foreach (['en', 'id'] as $locale)
            <url>
                <loc>{{ \App\Http\Middleware\SetLocale::languageUrl(url($path), $locale) }}</loc>
                @foreach (['en', 'id'] as $alternate)
                    <xhtml:link rel="alternate" hreflang="{{ $alternate }}" href="{{ \App\Http\Middleware\SetLocale::languageUrl(url($path), $alternate) }}" />
                @endforeach
            </url>
        @endforeach
    @endforeach
    @foreach ($tours as $tour)
        @foreach (['en', 'id'] as $locale)
            <url>
                <loc>{{ \App\Http\Middleware\SetLocale::languageUrl(route('tours.detail', $tour->slug), $locale) }}</loc>
                @foreach (['en', 'id'] as $alternate)
                    <xhtml:link rel="alternate" hreflang="{{ $alternate }}" href="{{ \App\Http\Middleware\SetLocale::languageUrl(route('tours.detail', $tour->slug), $alternate) }}" />
                @endforeach
                @if ($tour->updated_at)
                    <lastmod>{{ $tour->updated_at->toAtomString() }}</lastmod>
                @endif
            </url>
        @endforeach
    @endforeach
</urlset>

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * The public URL determines the language; the session remembers it for signed links.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->routeIs('localized.*') ? 'id' : 'en';
        if ($request->routeIs('tour-ratings.*')) {
            $locale = $request->session()->get('locale', 'en') === 'id' ? 'id' : 'en';
        }
        if (! $request->is('admin', 'admin/*', 'language', 'sitemap.xml')) {
            $request->session()->put('locale', $locale);
        }
        App::setLocale($locale);
        // Keep the existing route identity/handlers; language is URL context only.
        if ($request->routeIs('localized.*')) {
            $route = $request->route();
            $route->setAction(array_replace($route->getAction(), [
                'as' => substr($route->getName(), strlen('localized.')),
            ]));
        }
        URL::formatPathUsing(static function ($path, $route) use ($locale) {
            $name = $route?->getName() ?? '';
            if ($locale === 'id' && $route && $name !== 'language.update'
                && ! str_starts_with($name, 'admin.')
                && ! str_starts_with($name, 'tour-ratings.')
                && ! preg_match('#^/id(?:/|$)#', $path)) {
                return '/id'.($path === '/' ? '' : $path);
            }

            return $path;
        });

        return $next($request);
    }

    public static function languageUrl(string $url, string $locale): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $path = preg_replace('#^/id(?=/|$)#', '', $path) ?: '/';
        $path = $locale === 'id' ? '/id'.($path === '/' ? '' : $path) : $path;
        $query = parse_url($url, PHP_URL_QUERY);

        return url($path).($query ? '?'.$query : '');
    }

    /** Translate display strings without changing stored tour/form values. */
    public static function text(?string $text): string
    {
        $text ??= '';
        if (App::getLocale() !== 'id') {
            return $text;
        }
        $translated = __($text);
        if ($translated === $text) {
            $normalized = preg_replace('/\s+/u', ' ', trim($text));
            $candidate = __($normalized);
            if ($candidate !== $normalized) {
                $translated = $candidate;
            }
        }
        if ($translated !== $text) {
            return $translated;
        }

        $text = preg_replace_callback('/\b(\d+) (verified ratings?|guests hosted)\b/u',
            fn ($match) => $match[1].' '.__(str_starts_with($match[2], 'verified') ? 'verified ratings' : 'guests hosted'), $text);

        return preg_replace_callback('/\b(\d+)\s+(adults?|children|child|infants?|guests?|passengers?|vehicles?|stops?|hours?|hrs?|mins?)\b/u',
            fn ($match) => $match[1].' '.__($match[2]), $text);
    }
}

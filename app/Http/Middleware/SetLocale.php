<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Apply a supported public-site locale stored in the visitor session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config(
            'app.supported_locales',
            ['en', 'id'],
        );

        $locale = $request->session()->get(
            'locale',
            config('app.locale', 'en'),
        );

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return $next($request);
    }
}

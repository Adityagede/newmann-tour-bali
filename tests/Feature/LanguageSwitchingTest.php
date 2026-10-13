<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class LanguageSwitchingTest extends TestCase
{
    public function test_visitor_can_switch_to_indonesian(): void
    {
        $response = $this
            ->from(route('tours'))
            ->post(route('language.update'), [
                'locale' => 'id',
            ]);

        $response
            ->assertRedirect(url('/id/tours'))
            ->assertSessionHas('locale', 'id');
    }

    public function test_session_locale_is_applied_to_public_navigation(): void
    {
        $response = $this
            ->withSession(['locale' => 'id'])
            ->get('/id/about');

        $response
            ->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Beranda')
            ->assertSee('Pesan Perjalanan');
    }

    public function test_localized_page_has_flags_and_matching_language_urls(): void
    {
        $this->get('/id/about')
            ->assertOk()
            ->assertSee('Pemandu lokal Bali untuk perjalanan yang lebih personal.')
            ->assertSee('viewBox="0 0 60 30"', false)
            ->assertSee('Bahasa Indonesia')
            ->assertSee('href="'.url('/id/tours').'"', false)
            ->assertSee('rel="canonical" href="'.url('/id/about').'"', false)
            ->assertSee('hreflang="en" href="'.url('/about').'"', false)
            ->assertSee('hreflang="id" href="'.url('/id/about').'"', false);
    }

    public function test_english_url_is_english_even_after_an_indonesian_visit(): void
    {
        $this->withSession(['locale' => 'id'])->get('/about')
            ->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('A local Bali guide who helps the trip feel personal.')
            ->assertSee('href="'.url('/tours').'"', false)
            ->assertDontSee('?locale=', false);
    }

    public function test_language_switch_keeps_the_page_and_query(): void
    {
        $this->from(url('/id/tours/example?date=2026-12-10'))
            ->post('/language', ['locale' => 'en'])
            ->assertRedirect(url('/tours/example?date=2026-12-10'));
    }

    public function test_language_switch_keeps_signed_link_path_and_query(): void
    {
        $signedUrl = url('/rate-your-trip/example?expires=9999999999&signature=example');
        $this->from($signedUrl)->post('/language', ['locale' => 'id'])
            ->assertRedirect($signedUrl)
            ->assertSessionHas('locale', 'id');
    }

    public function test_localized_routes_keep_the_existing_handlers(): void
    {
        foreach (['', '/id'] as $prefix) {
            $route = \Illuminate\Support\Facades\Route::getRoutes()->match(
                \Illuminate\Http\Request::create($prefix.'/tours/example/availability', 'POST')
            );
            $this->assertSame($prefix === '' ? 'tours.availability' : 'localized.tours.availability', $route->getName());
            $this->assertSame('App\\Http\\Controllers\\TourController@availability', $route->getActionName());
            $this->assertSame('example', $route->parameter('slug'));
        }
    }

    public function test_participant_and_duration_display_is_translated(): void
    {
        app()->setLocale('id');
        $this->assertSame('1 dewasa · 2 anak · 0 bayi', \App\Http\Middleware\SetLocale::text('1 adult · 2 children · 0 infants'));
        $this->assertSame('1 jam 30 menit', \App\Http\Middleware\SetLocale::text('1 hr 30 min'));
    }

    public function test_sitemap_lists_both_languages_without_private_pages(): void
    {
        $xml = simplexml_load_string(view('sitemap', ['tours' => collect()])->render());
        $urls = array_map(fn ($entry) => (string) $entry->loc, iterator_to_array($xml->url, false));
        $this->assertCount(12, $urls);
        $this->assertContains(url('/about'), $urls);
        $this->assertContains(url('/id/about'), $urls);
        $this->assertContains(url('/id'), $urls);
        $xml->registerXPathNamespace('xhtml', 'http://www.w3.org/1999/xhtml');
        $this->assertCount(12, $xml->xpath('//xhtml:link[@hreflang="id"]'));
    }

    public function test_unsupported_locale_is_rejected(): void
    {
        $response = $this
            ->from(route('home'))
            ->post(route('language.update'), [
                'locale' => 'fr',
            ]);

        $response
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('locale');

        $this->assertNotSame(
            'fr',
            session('locale'),
        );
    }
}

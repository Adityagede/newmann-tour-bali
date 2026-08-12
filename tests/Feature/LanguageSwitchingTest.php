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
            ->assertRedirect(route('tours'))
            ->assertSessionHas('locale', 'id');
    }

    public function test_session_locale_is_applied_to_public_navigation(): void
    {
        $response = $this
            ->withSession(['locale' => 'id'])
            ->get(route('about'));

        $response
            ->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('Beranda')
            ->assertSee('Pesan Perjalanan');
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

<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\TourViewData;
use Tests\TestCase;

final class TourViewDataTest extends TestCase
{
    public function test_statistics_keys_are_always_present_in_the_public_contract(): void
    {
        $viewData = TourViewData::make([
            'title' => 'Tour without statistics',
            'slug' => 'tour-without-statistics',
        ]);

        $this->assertFalse($viewData['has_rating']);
        $this->assertNull($viewData['rating']);
        $this->assertSame(0, $viewData['rating_count']);
        $this->assertNull($viewData['rating_text']);
        $this->assertSame(0, $viewData['guest_count']);
        $this->assertSame(0, $viewData['hosted_guest_count']);
        $this->assertNull($viewData['hosted_guest_text']);
    }

    public function test_verified_statistics_have_stable_values_and_types(): void
    {
        $viewData = TourViewData::make([
            'title' => 'Tour with statistics',
            'slug' => 'tour-with-statistics',
            'verified_rating_average' => '4.5',
            'verified_rating_count' => '2',
            'verified_guest_count' => '7',
        ]);

        $this->assertTrue($viewData['has_rating']);
        $this->assertSame(4.5, $viewData['rating']);
        $this->assertSame(2, $viewData['rating_count']);
        $this->assertSame('2 verified ratings', $viewData['rating_text']);
        $this->assertSame(7, $viewData['guest_count']);
        $this->assertSame(7, $viewData['hosted_guest_count']);
        $this->assertSame('7 guests hosted', $viewData['hosted_guest_text']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\TourOption;
use App\Models\TourPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Js;
use Tests\TestCase;

class AdminTourPackageEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_creating_a_draft_leads_to_gallery_and_resuming_opens_the_missing_section(): void
    {
        $this->withSession(['admin_logged_in' => true]);
        $this->get(route('admin.tour-packages.create'))
            ->assertOk()
            ->assertSeeText('Create draft & continue')
            ->assertDontSee('name="status"', false);

        $submit = $this->post(route('admin.tour-packages.store'), [
            'title' => 'New Bali tour',
            'tour_format' => 'full_day',
        ])->assertSessionHasNoErrors();

        $tour = TourPackage::query()->sole();
        $gallery = route('admin.tour-packages.gallery.edit', $tour);
        $resume = route('admin.tour-packages.resume', $tour);

        $submit->assertRedirect($gallery);
        $this->assertSame('draft', $tour->status);
        $this->get($gallery)->assertOk()->assertSee('aria-label="Tour package editor"', false);
        $previewAvailability = route('admin.tour-packages.preview.availability', $tour);
        $this->get(route('admin.tour-packages.preview', $tour))->assertOk()
            ->assertSee(Js::from($previewAvailability)->toHtml(), false);
        $this->get($resume)->assertRedirect(route('admin.tour-packages.edit', $tour).'#category');

        $tour->update($this->packageDetails());
        $this->get($resume)->assertRedirect($gallery);
        $tour->update(['gallery_images' => ['gallery/one.webp', 'gallery/two.webp', 'gallery/three.webp']]);
        $roadmap = route('admin.tour-packages.roadmap.index', $tour);
        $this->get($gallery)->assertOk()->assertSeeText('Continue to itinerary');
        $this->get($resume)->assertRedirect($roadmap);
        $this->get($roadmap)->assertOk()->assertSeeText('Add roadmap stop');

        $this->patch(route('admin.tour-packages.update', $tour), [
            'title' => 'Edited Bali tour',
            'tour_format' => 'full_day',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.tour-packages.edit', $tour));
        $this->assertSame('draft', $tour->fresh()->status);
    }

    public function test_option_creation_opens_prices_and_editing_can_switch_sections_without_losing_the_option(): void
    {
        $this->withSession(['admin_logged_in' => true]);
        $tour = TourPackage::create($this->packageDetails());
        $this->get(route('admin.tour-packages.options.create', $tour))
            ->assertOk()->assertSeeText('Create option & set prices');

        $submit = $this->post(route('admin.tour-packages.options.store', $tour), [
            'title' => 'Private option',
            'pickup_type' => 'flexible',
            'min_guests' => 1,
            'duration_minutes' => 360,
            'languages_text' => 'English',
            'is_default' => true,
            'status' => 'draft',
        ])->assertSessionHasNoErrors();

        $option = $tour->options()->sole();
        $parameters = [$tour, $option];
        $prices = route('admin.tour-packages.options.prices.edit', $parameters);
        $schedules = route('admin.tour-packages.options.schedules.index', $parameters);

        $submit->assertRedirect($prices);
        $this->get($prices)->assertOk()
            ->assertSeeText('Save & continue to schedules')
            ->assertSee('href="'.$schedules.'"', false);
        $this->put(route('admin.tour-packages.options.prices.update', $parameters), [
            'prices' => $this->prices(),
            'continue' => 'schedules',
        ])->assertSessionHasNoErrors()->assertRedirect($schedules);
        $this->put(route('admin.tour-packages.options.prices.update', $parameters), [
            'prices' => $this->prices(),
        ])->assertSessionHasNoErrors()->assertRedirect($prices);
        $this->get($schedules)->assertOk()->assertSee('href="'.$prices.'"', false);

        $otherOption = $this->createOption($tour, ['title' => 'Small group', 'slug' => 'small-group', 'is_default' => false]);
        $otherPrices = route('admin.tour-packages.options.prices.edit', [$tour, $otherOption]);
        $this->get($prices)->assertOk()->assertSeeText('Switch option')->assertSee('href="'.$otherPrices.'"', false);
    }

    public function test_publishing_actions_require_admin_access_and_respect_readiness_and_package_ownership(): void
    {
        $tour = TourPackage::create($this->packageDetails());
        $otherTour = TourPackage::create(array_replace($this->packageDetails(), ['slug' => 'other-tour']));
        $option = $this->createOption($tour);
        $otherOption = $this->createOption($otherTour);
        $readiness = route('admin.tour-packages.readiness.show', $tour);
        $publish = route('admin.tour-packages.readiness.status', $tour);
        $activate = route('admin.tour-packages.readiness.activate-option', [$tour, $option]);

        $this->patch($publish, ['status' => 'active'])->assertRedirect(route('admin.login'));
        $this->withSession(['admin_logged_in' => true]);
        $this->get($readiness)->assertOk()
            ->assertDontSeeText('Publish package')
            ->assertSee('href="'.route('admin.tour-packages.options.prices.edit', [$tour, $option]).'"', false);
        $this->patch(route('admin.tour-packages.readiness.activate-option', [$tour, $otherOption]))->assertNotFound();
        $this->patch($activate)->assertRedirect($readiness)->assertSessionHasErrors('status');
        $this->patch($publish, ['status' => 'active'])->assertRedirect($readiness)->assertSessionHasErrors('status');
        $this->assertSame('draft', $tour->fresh()->status);
        $this->assertSame('draft', $option->fresh()->status);

        $tour->update(['gallery_images' => ['gallery/one.webp', 'gallery/two.webp', 'gallery/three.webp']]);
        foreach (['Pickup', 'Temple visit'] as $index => $title) {
            $tour->stops()->create([
                'day_number' => 1,
                'stop_type' => 'other',
                'title' => $title,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
        $option->schedules()->create([
            'day_of_week' => 1,
            'start_time' => '09:00:00',
            'is_active' => true,
        ]);
        $this->put(route('admin.tour-packages.options.prices.update', [$tour, $option]), [
            'prices' => $this->prices(),
        ])->assertSessionHasNoErrors();

        $this->get($readiness)->assertOk()->assertSeeText('Activate option');
        $this->patch($activate)->assertSessionHasNoErrors()->assertRedirect($readiness);
        $this->get($readiness)->assertOk()->assertSeeText('Publish package');
        $this->patch($publish, ['status' => 'active'])->assertSessionHasNoErrors()->assertRedirect($readiness);
        $this->assertSame('active', $tour->fresh()->status);

        $this->patch($publish, ['status' => 'inactive'])->assertSessionHasNoErrors()->assertRedirect($readiness);
        $this->assertSame('inactive', $tour->fresh()->status);
    }

    private function packageDetails(): array
    {
        return [
            'title' => 'New Bali tour',
            'slug' => 'new-bali-tour',
            'tour_format' => 'full_day',
            'category' => 'Culture',
            'area' => 'Ubud',
            'duration' => 'Full day',
            'description' => 'Tour card copy',
            'intro' => 'Tour introduction',
            'story' => 'Tour narrative',
            'main_image' => 'images/tour.webp',
            'highlights' => ['Temple', 'Rice field', 'Local food'],
            'status' => 'draft',
        ];
    }

    private function createOption(TourPackage $tour, array $attributes = []): TourOption
    {
        return $tour->options()->create(array_replace([
            'title' => 'Private option',
            'slug' => 'private-option',
            'duration_minutes' => 360,
            'languages' => ['English'],
            'pickup_type' => 'flexible',
            'min_guests' => 1,
            'is_all_inclusive' => false,
            'is_default' => true,
            'status' => 'draft',
        ], $attributes));
    }

    private function prices(): array
    {
        return [
            'adult' => ['is_allowed' => true, 'is_free' => false, 'base_price' => 450000],
            'child' => ['is_allowed' => false, 'is_free' => false],
            'infant' => ['is_allowed' => false, 'is_free' => false],
        ];
    }
}

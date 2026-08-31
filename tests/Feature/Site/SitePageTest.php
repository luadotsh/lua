<?php

declare(strict_types=1);

use App\Models\Plan;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the home page', function (): void {
    $this->get(route('site.home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/Home')
            ->has('seo.title')
            ->has('seo.description')
        );
});

it('pairs each tier with its monthly and yearly price', function (): void {
    $this->get(route('site.pricing'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/Pricing')
            ->has('tiers')
            ->has('tiers.0.name')
            ->has('tiers.0.monthly_price')
            ->has('tiers.0.yearly_price')
        );
});

// A tier is one row on the page, not two. The yearly plan is a price on that
// row, so it must never appear as a tier of its own.
it('does not render a yearly plan as a separate tier', function (): void {
    $this->get(route('site.pricing'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('tiers', function ($tiers) {
                $names = collect($tiers)->pluck('name');

                return $names->count() === $names->unique()->count();
            })
        );
});

it('quotes the price the plans table holds', function (): void {
    $pro = Plan::where('internal_id', 'pro-monthly')->firstOrFail();
    $proYearly = Plan::where('internal_id', 'pro-yearly')->firstOrFail();

    $this->get(route('site.pricing'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('tiers', fn ($tiers) => collect($tiers)
                ->firstWhere('name', 'Pro') === [
                    'name' => 'Pro',
                    'internal_id' => 'pro-monthly',
                    'monthly_price' => (int) $pro->price,
                    'yearly_price' => (int) $proYearly->price,
                    'max_links' => (int) $pro->max_links,
                    'max_events' => (int) $pro->max_events,
                    'max_users' => (int) $pro->max_users,
                    'max_tags' => (int) $pro->max_tags,
                    'max_domains' => (int) $pro->max_domains,
                ]
            )
        );
});

it('keeps private plans off the pricing page', function (): void {
    Plan::factory()->create([
        'name' => 'Secret',
        'is_private' => true,
        'is_monthly' => true,
        'access_level' => 99,
    ]);

    $this->get(route('site.pricing'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('tiers', fn ($tiers) => ! collect($tiers)->pluck('name')->contains('Secret'))
        );
});

it('renders the legal pages', function (string $route, string $component): void {
    $this->get(route($route))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component)->has('seo.title'));
})->with([
    ['site.terms', 'Site/Terms'],
    ['site.privacy', 'Site/Privacy'],
]);

it('renders the marketing pages to a guest', function (string $route): void {
    $this->assertGuest();

    $this->get(route($route))->assertOk();
})->with(['site.home', 'site.pricing', 'site.terms', 'site.privacy', 'site.alternatives.index']);

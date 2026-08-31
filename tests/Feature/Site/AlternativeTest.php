<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;

it('lists every configured alternative on the index', function (): void {
    $this->get(route('site.alternatives.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/Alternatives/Index')
            ->has('alternatives', count(config('alternatives')))
            ->has('alternatives.0.slug')
            ->has('alternatives.0.name')
            ->has('alternatives.0.intro')
        );
});

it('renders the bitly comparison', function (): void {
    $this->get(route('site.alternatives.show', 'bitly'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/Alternatives/Show')
            ->where('slug', 'bitly')
            ->where('alternative.name', 'Bitly')
            ->has('alternative.reasons')
            ->has('alternative.comparison')
            ->has('alternative.pricing')
            ->has('alternative.fit.good.items')
            ->has('alternative.fit.bad.items')
            ->has('seo.title')
        );
});

// The template reads these keys unconditionally, so a competitor added with a
// section missing would render a broken page rather than fail here.
it('gives every alternative the shape the template renders', function (): void {
    foreach (config('alternatives') as $slug => $entry) {
        expect($entry)
            ->toHaveKeys(['name', 'seo', 'intro', 'reasons', 'comparison', 'pricing', 'fit'])
            ->and($entry['seo'])->toHaveKeys(['title', 'description'])
            ->and($entry['fit'])->toHaveKeys(['good', 'bad']);

        foreach (['good', 'bad'] as $side) {
            expect($entry['fit'][$side])->toHaveKeys(['title', 'items'])
                ->and($entry['fit'][$side]['items'])->not->toBeEmpty();
        }

        foreach ($entry['reasons'] as $reason) {
            expect($reason)->toHaveKeys(['title', 'description']);
        }

        foreach ($entry['comparison'] as $row) {
            expect($row)->toHaveKeys(['feature', 'lua', 'competitor']);
        }

        foreach ($entry['pricing'] as $row) {
            expect($row)->toHaveKeys(['tier', 'lua', 'competitor']);
        }

        expect($slug)->toMatch('/^[a-z0-9-]+$/');
    }
});

it('404s an unknown alternative', function (): void {
    $this->get(route('site.alternatives.show', 'not-a-real-one'))->assertNotFound();
});

// `config("alternatives.{$slug}")` reads with dot notation, so a slug carrying
// a dot would reach into a nested key instead of missing.
it('404s a slug that tries to traverse the config', function (): void {
    $this->get(route('site.alternatives.show', 'bitly.name'))->assertNotFound();
});

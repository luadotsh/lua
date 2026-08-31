<?php

declare(strict_types=1);

use App\Actions\Link\CreateLink;
use Inertia\Testing\AssertableInertia as Assert;

it('lists every configured use case', function (): void {
    $this->get(route('site.use-cases.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/UseCases/Index')
            ->has('useCases', count(config('use_cases')))
            ->has('useCases.0.slug')
        );
});

it('renders a use case with its steps and its caveat', function (): void {
    $this->get(route('site.use-cases.show', 'agencies'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/UseCases/Show')
            ->where('useCase.name', 'Agencies')
            ->has('useCase.steps')
            ->has('useCase.features')
            ->has('useCase.caveat')
            ->has('others', 3)
        );
});

// The template reads every key unconditionally, so a page added with a section
// missing would render broken rather than fail here.
it('gives every use case the shape the template renders', function (): void {
    foreach (config('use_cases') as $slug => $entry) {
        expect($slug)->toMatch('/^[a-z0-9-]+$/')
            ->and($entry)->toHaveKeys(['name', 'seo', 'intro', 'problem', 'steps', 'features', 'caveat'])
            ->and($entry['seo'])->toHaveKeys(['title', 'description'])
            ->and($entry['steps'])->not->toBeEmpty()
            ->and($entry['features'])->not->toBeEmpty();

        foreach ($entry['steps'] as $step) {
            expect($step)->toHaveKeys(['title', 'description']);
        }

        // The caveat is the part that makes the rest credible, so an empty one
        // is a page that only sells.
        expect(strlen($entry['caveat']))->toBeGreaterThan(80);
    }
});

it('never links a use case to itself', function (): void {
    foreach (array_keys(config('use_cases')) as $slug) {
        $this->get(route('site.use-cases.show', $slug))
            ->assertInertia(fn (Assert $page) => $page
                ->where('others', fn ($others) => ! collect($others)->pluck('slug')->contains($slug))
            );
    }
});

it('404s an unknown use case', function (): void {
    $this->get(route('site.use-cases.show', 'nobody'))->assertNotFound();
});

it('reserves the new paths as short-link back-halves', function (): void {
    expect(CreateLink::reservedKeys())->toContain('use-cases', 'tools', 'faq');
});

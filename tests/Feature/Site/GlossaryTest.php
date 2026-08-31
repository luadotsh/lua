<?php

declare(strict_types=1);

use App\Actions\Link\CreateLink;
use Inertia\Testing\AssertableInertia as Assert;

it('groups every term by first letter', function (): void {
    $this->get(route('site.glossary.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/Glossary/Index')
            ->has('letters')
            ->where('letters', fn ($letters) => collect($letters)->pluck('terms')->flatten(1)->count()
                === count(config('glossary')))
        );
});

it('renders a term with its definition and its related terms', function (): void {
    $this->get(route('site.glossary.show', 'utm-parameters'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/Glossary/Show')
            ->where('entry.term', 'UTM parameters')
            ->has('entry.body')
            ->has('related')
        );
});

// A term pointing at one that does not exist renders a dead end, silently.
it('resolves every related term', function (): void {
    $terms = config('glossary');

    foreach ($terms as $slug => $entry) {
        foreach ($entry['related'] ?? [] as $related) {
            // Named in the message rather than by toHaveKey's second argument,
            // which is an expected value and not a description.
            expect(array_key_exists($related, $terms))
                ->toBeTrue("{$slug} points at a term that does not exist: {$related}");
        }
    }
});

it('never lists a term as related to itself', function (): void {
    foreach (config('glossary') as $slug => $entry) {
        expect($entry['related'] ?? [])->not->toContain($slug);
    }
});

// The one-sentence definition is what the index, the meta description and the
// structured data all use, so it has to stand on its own.
it('gives every term a definition that stands alone', function (): void {
    foreach (config('glossary') as $slug => $entry) {
        expect($slug)->toMatch('/^[a-z0-9-]+$/')
            ->and($entry)->toHaveKeys(['term', 'short', 'body', 'related'])
            ->and($entry['body'])->not->toBeEmpty()
            ->and(strlen($entry['short']))->toBeGreaterThan(40)
            // Under 155 so it can be the meta description unaltered.
            ->and(strlen($entry['short']))->toBeLessThan(200);
    }
});

it('404s an unknown term', function (): void {
    $this->get(route('site.glossary.show', 'not-a-term'))->assertNotFound();
});

it('reserves the glossary path', function (): void {
    expect(CreateLink::reservedKeys())->toContain('glossary');
});

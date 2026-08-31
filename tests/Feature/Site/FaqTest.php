<?php

declare(strict_types=1);

use App\Actions\Link\CreateLink;
use App\Http\Controllers\Site\PageController;
use Inertia\Testing\AssertableInertia as Assert;

it('renders every group and question', function (): void {
    $questions = collect(config('faq'))->flatten(1)->count();

    $this->get(route('site.faq'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/Faq')
            ->has('groups', count(config('faq')))
            ->where('groups', fn ($groups) => collect($groups)->pluck('items')->flatten(1)->count() === $questions)
        );
});

// One source for the page, the home page and the structured data. A second
// list would drift, and the drift would be two different answers to the same
// question on two pages of the same site.
it('leads the home page with a subset of the same answers', function (): void {
    $home = collect(PageController::faqGroups(homeOnly: true))->pluck('items')->flatten(1);
    $all = collect(PageController::faqGroups())->pluck('items')->flatten(1);

    expect($home)->not->toBeEmpty()
        ->and($home->count())->toBeLessThan($all->count());

    foreach ($home as $item) {
        expect($all->contains($item))->toBeTrue();
    }
});

it('shares those answers with the home page', function (): void {
    $this->get(route('site.home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->has('faq')->has('faq.0.items.0.question'));
});

it('gives every question an answer that is not a placeholder', function (): void {
    foreach (collect(config('faq'))->flatten(1) as $item) {
        expect($item)->toHaveKeys(['question', 'answer'])
            ->and($item['question'])->toEndWith('?')
            ->and(strlen($item['answer']))->toBeGreaterThan(60);
    }
});

it('reserves the faq path as a short-link back-half', function (): void {
    expect(CreateLink::reservedKeys())->toContain('faq');
});

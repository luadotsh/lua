<?php

declare(strict_types=1);

use App\Models\User;

test('the dashboard renders the filters carried by the url', function () {
    $this->actingAs(User::factory()->withWorkspace()->create());

    $page = visit(route('analytics.index', ['country' => 'BR', 'browser' => 'Chrome']));

    // The pill reads the country back in words; the URL keeps the raw code.
    $page->assertSee('Brazil')
        ->assertSee('Chrome')
        ->assertSee('Clear filters')
        ->assertNoJavaScriptErrors();
});

test('removing a filter drops only that dimension from the url', function () {
    $this->actingAs(User::factory()->withWorkspace()->create());

    $page = visit(route('analytics.index', ['country' => 'BR', 'browser' => 'Chrome']));

    $page->click('@analytics-filter-remove-country');

    $page->assertQueryStringMissing('country')
        ->assertQueryStringHas('browser')
        // One filter left, so the shortcut has nothing left to shorten.
        ->assertDontSee('Clear filters')
        ->assertNoJavaScriptErrors();
});

test('clearing the filters keeps the date window', function () {
    $this->actingAs(User::factory()->withWorkspace()->create());

    $page = visit(route('analytics.index', [
        'start' => '2026-08-01',
        'end' => '2026-08-30',
        'country' => 'BR',
        'browser' => 'Chrome',
    ]));

    $page->click('@analytics-filters-clear');

    $page->assertQueryStringMissing('country')
        ->assertQueryStringMissing('browser')
        ->assertQueryStringHas('start', '2026-08-01')
        ->assertQueryStringHas('end', '2026-08-30')
        ->assertNoJavaScriptErrors();
});

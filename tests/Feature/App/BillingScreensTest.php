<?php

declare(strict_types=1);

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

it('shows the billing screen', function () {
    $this->actingAs($this->user)
        ->get(route('setting.billing.index'))
        ->assertOk();
});

it('offers the paid plans on the upgrade screen', function () {
    Plan::factory()->create(['name' => 'Private beta', 'is_private' => true]);

    $this->actingAs($this->user)
        ->get(route('setting.billing.upgrade'))
        ->assertOk()
        ->assertInertia(function (Assert $page) {
            $names = collect($page->toArray()['props']['plans'])->pluck('internal_id');

            // Free is what you are already on, and a private plan is one
            // somebody is put on rather than picks.
            return expect($names)->not->toContain('free')
                && expect($names)->not->toContain('private-beta');
        });
});

it('404s a checkout for a plan that does not exist', function () {
    $this->actingAs($this->user)
        ->get(route('setting.billing.checkout', '00000000-0000-0000-0000-000000000000'))
        ->assertNotFound();
});

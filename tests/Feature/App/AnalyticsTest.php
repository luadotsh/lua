<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

it('returns a successful response', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('analytics.index'));

    $response->assertStatus(200);
});

/**
 * The timezone arrives from the browser and is handed to the database as the
 * argument to `at time zone` / `CONVERT_TZ`. Two things follow: a zone the
 * database does not know is an error on PostgreSQL and a silently null bucket
 * on MySQL, so it has to be validated; and browsers still report deprecated
 * IANA aliases, so the validation cannot be the plain `timezone` rule.
 */
it('accepts the deprecated timezone aliases browsers still send', function (string $timezone) {
    $this->actingAs($this->user)
        ->getJson(route('analytics.statistics', [
            'start' => '2026-06-01',
            'end' => '2026-06-15',
            'group' => 'day',
            'timezone' => $timezone,
        ]))
        ->assertSuccessful();
})->with([
    'Asia/Calcutta (Indian clients report this, not Asia/Kolkata)' => 'Asia/Calcutta',
    'Brazil/East' => 'Brazil/East',
    'Asia/Kolkata' => 'Asia/Kolkata',
    'UTC' => 'UTC',
]);

it('rejects a timezone the database would choke on', function () {
    $this->actingAs($this->user)
        ->getJson(route('analytics.statistics', [
            'start' => '2026-06-01',
            'end' => '2026-06-15',
            'group' => 'day',
            'timezone' => 'Not/A_Timezone',
        ]))
        ->assertJsonValidationErrors('timezone');
});

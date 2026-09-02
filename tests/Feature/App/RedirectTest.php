<?php

declare(strict_types=1);

use App\Enums\Domain\Status;
use App\Models\Domain;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('the link will be redirect successfully', function () {

    $link = Link::factory()->create();

    $response = $this
        ->get(route('links.redirect', $link->key));

    $response->assertStatus(302);
    $response->assertRedirect($link->url);
});

it('invalid link will return 404', function () {

    $link = Link::factory()->create();

    $response = $this
        ->get(route('links.redirect', 'abc'));

    $response->assertNotFound();
});

it('sends the main domain root to the marketing site', function () {
    // The site is a separate deployment now. A request to the bare domain
    // carries no key, so it falls through to the middleware, which forwards
    // it to config('app.website').
    $this->get(route('links.redirect'))
        ->assertRedirect(config('app.website'));
});

it('sends a secondary lua domain without a key to the site', function () {
    // A domain we own but do not serve the site from: the site routes are
    // scoped to the main domain, so this one does fall through to the
    // middleware.
    config(['domains.available' => ['go.lua.test']]);

    $this->get('https://go.lua.test')
        ->assertRedirect(config('app.website'));
});

it('an unknown domain without key is redirected to the site', function () {
    // Host is neither a default domain nor a registered custom domain.
    $this->get('https://not-ours.example.com')
        ->assertRedirect(config('app.website'));
});

it('redirects to the iOS URL if the user is on iOS', function () {
    $link = Link::factory()->create([
        'ios' => 'https://example.com/ios',
    ]);

    $response = $this
        ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        ])
        ->get(route('links.redirect', $link->key));

    $response->assertStatus(302);
    $response->assertRedirect($link->ios);
});

it('redirects to the default URL if the user not on iOS', function () {
    $link = Link::factory()->create([
        'ios' => 'https://example.com/ios',
    ]);

    $response = $this->get(route('links.redirect', $link->key));

    $response->assertStatus(302);
    $response->assertRedirect($link->url);
});

it('redirects to the default URL if user its on iOS but no iOS URL is set', function () {
    $link = Link::factory()->create([
        'ios' => null,
    ]);

    $response = $this
        ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        ])
        ->get(route('links.redirect', $link->key));

    $response->assertStatus(302);
    $response->assertRedirect($link->url);
});

it('redirects to the default URL if the user not on Android', function () {
    $link = Link::factory()->create([
        'android' => 'https://example.com/android',
    ]);

    $response = $this->get(route('links.redirect', $link->key));

    $response->assertStatus(302);
    $response->assertRedirect($link->url);
});

it('redirects to the default URL if user its on Android but no Android URL is set', function () {
    $link = Link::factory()->create([
        'android' => null,
    ]);

    $response = $this
        ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 14; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Mobile Safari/537.36',
        ])
        ->get(route('links.redirect', $link->key));

    $response->assertStatus(302);
    $response->assertRedirect($link->url);
});

it('redirects to the Android URL if the user is on Android', function () {
    $link = Link::factory()->create([
        'android' => 'https://example.com/android',
    ]);

    $response = $this
        ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 14; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Mobile Safari/537.36',
        ])
        ->get(route('links.redirect', $link->key));

    $response->assertStatus(302);
    $response->assertRedirect($link->android);
});

it('expired links without url will return 404', function () {
    $link = Link::factory()->create([
        'expires_at' => now()->subDay(),
        'expired_redirect_url' => null,
    ]);

    $response = $this->get(route('links.redirect', $link->key));

    $response->assertNotFound();
});

it('redirects to the expired redirect URL if the link is expired', function () {
    $link = Link::factory()->create([
        'expires_at' => now()->subDay(),
        'expired_redirect_url' => 'https://example.com',
    ]);

    $response = $this->get(route('links.redirect', $link->key));

    $response->assertStatus(302);
    $response->assertRedirect($link->expired_redirect_url);
});

it('resolves the link by host as well as key', function () {
    // The same key on two domains. Matching on the key alone would resolve to
    // whichever row came back first, sending the visitor to the wrong place.
    $host = parse_url((string) config('app.url'), PHP_URL_HOST);

    $mine = Link::factory()->create([
        'domain' => $host,
        'key' => 'shared-key',
        'link' => "https://{$host}/shared-key",
        'url' => 'https://example.com/mine',
    ]);

    Link::factory()->create([
        'domain' => 'someone-else.test',
        'key' => 'shared-key',
        'link' => 'https://someone-else.test/shared-key',
        'url' => 'https://example.com/theirs',
    ]);

    $this->get(route('links.redirect', 'shared-key'))
        ->assertRedirect($mine->url);
});

it('does not leak a password gate across domains', function () {
    $host = parse_url((string) config('app.url'), PHP_URL_HOST);

    Link::factory()->create([
        'domain' => 'someone-else.test',
        'key' => 'gated',
        'link' => 'https://someone-else.test/gated',
        'password' => 'secret',
    ]);

    // The key exists, but not on this host.
    $this->get(route('links.password', 'gated'))->assertNotFound();
    $this->post(route('links.password.validate', 'gated'), ['password' => 'secret'])
        ->assertNotFound();
});

it('stores the link password encrypted but reads it back', function () {
    $host = parse_url((string) config('app.url'), PHP_URL_HOST);

    $link = Link::factory()->create([
        'domain' => $host,
        'key' => 'locked',
        'link' => "https://{$host}/locked",
        'password' => 'correct-horse',
    ]);

    // Ciphertext on disk, plaintext through the model.
    $stored = DB::table('links')->where('id', $link->id)->value('password');

    expect($stored)->not->toBe('correct-horse')
        ->and($link->fresh()->password)->toBe('correct-horse');
});

it('gates a protected link and accepts only the right password', function () {
    $host = parse_url((string) config('app.url'), PHP_URL_HOST);

    $link = Link::factory()->create([
        'domain' => $host,
        'key' => 'locked-2',
        'link' => "https://{$host}/locked-2",
        'password' => 'correct-horse',
    ]);

    $this->get(route('links.redirect', 'locked-2'))
        ->assertRedirect(route('links.password', 'locked-2'));

    $this->post(route('links.password.validate', 'locked-2'), ['password' => 'wrong'])
        ->assertSessionHasErrors('password');

    $this->post(route('links.password.validate', 'locked-2'), ['password' => 'correct-horse'])
        ->assertSessionHasNoErrors();
});

it('sends the link utms on to the destination', function () {
    // The factory fills every UTM; this test is about two of them.
    $link = Link::factory()->create([
        'url' => 'https://example.com/pricing',
        'utm_source' => 'newsletter',
        'utm_medium' => null,
        'utm_campaign' => 'launch',
        'utm_term' => null,
        'utm_content' => null,
    ]);

    $this->get(route('links.redirect', $link->key))
        ->assertRedirect('https://example.com/pricing?utm_source=newsletter&utm_campaign=launch');
});

it('prefers the utm the visitor arrived with', function () {
    $link = Link::factory()->create([
        'url' => 'https://example.com/pricing',
        'utm_source' => 'newsletter',
        'utm_medium' => null,
        'utm_campaign' => null,
        'utm_term' => null,
        'utm_content' => null,
    ]);

    $this->get(route('links.redirect', $link->key).'?utm_source=twitter')
        ->assertRedirect('https://example.com/pricing?utm_source=twitter');
});

it('sends the root of a custom domain to its not found url', function () {
    $workspace = User::factory()->withWorkspace()->create()->currentWorkspace;

    Domain::factory()->create([
        'workspace_id' => $workspace->id,
        'domain' => 'links.example.com',
        'status' => Status::ACTIVE,
        'not_found_url' => 'https://example.com/lost',
    ]);

    // The middleware only steps in for the bare host: with a key present the
    // request belongs to the redirect controller.
    $this->get('https://links.example.com')
        ->assertRedirect('https://example.com/lost');
});

it('sends the root of a custom domain with no fallback to the website', function () {
    $workspace = User::factory()->withWorkspace()->create()->currentWorkspace;

    Domain::factory()->create([
        'workspace_id' => $workspace->id,
        'domain' => 'links.example.com',
        'status' => Status::ACTIVE,
        'not_found_url' => null,
    ]);

    $this->get('https://links.example.com')
        ->assertRedirect(config('app.website'));
});

it('shows the password gate before a protected link resolves', function () {
    $workspace = User::factory()->withWorkspace()->create()->currentWorkspace;

    $link = Link::factory()->create([
        'workspace_id' => $workspace->id,
        'domain' => 'lua.test',
        'key' => 'secret',
        'password' => 'sesame',
    ]);

    $this->get(route('links.password', $link->key))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Link/Password'));
});

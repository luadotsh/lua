<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'lua.auth.google' => true,
        'services.google.client_id' => 'id',
        'services.google.client_secret' => 'secret',
        'services.google.redirect' => 'https://lua.test/auth/google/callback',
    ]);
});

/** Stands in for the round trip out to the provider and back. */
function fakeSocialUser(string $id, string $email, ?string $name = 'Ada Lovelace'): SocialiteUser
{
    $user = new SocialiteUser;
    $user->map(['id' => $id, 'name' => $name, 'nickname' => 'ada', 'email' => $email]);

    return $user;
}

function fakeSocialite(?SocialiteUser $user): void
{
    Socialite::shouldReceive('driver')->with('google')->andReturn($driver = Mockery::mock());
    $driver->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

    if ($user === null) {
        $driver->shouldReceive('user')->andThrow(new Exception('denied'));

        return;
    }

    $driver->shouldReceive('user')->andReturn($user);
}

it('bounces you out to the provider', function () {
    fakeSocialite(fakeSocialUser('g-1', 'ada@example.com'));

    $this->get(route('auth.social', 'google'))->assertRedirect();
});

it('remembers you were linking when you were already signed in', function () {
    fakeSocialite(fakeSocialUser('g-1', 'ada@example.com'));

    // The Connect button on the authentication screen used to bounce off the
    // guest middleware and never reach this at all.
    $this->actingAs(User::factory()->withWorkspace()->create())
        ->get(route('auth.social', 'google'))
        ->assertRedirect()
        ->assertSessionHas('social_connect', 'google');
});

it('refuses a provider we do not offer', function () {
    $this->get(route('auth.social', 'myspace'))->assertNotFound();
});

it('creates an account on first sign in', function () {
    fakeSocialite(fakeSocialUser('g-1', 'ada@example.com'));

    $this->get(route('auth.social.callback', 'google'))
        ->assertRedirect(route('links.index'));

    $user = User::where('email', 'ada@example.com')->firstOrFail();

    expect($user->google_id)->toBe('g-1')
        ->and(auth()->id())->toBe($user->id);
});

it('signs in an account that already used this provider', function () {
    $existing = User::factory()->withWorkspace()->create([
        'email' => 'ada@example.com',
        'google_id' => 'g-1',
    ]);

    fakeSocialite(fakeSocialUser('g-1', 'ada@example.com'));

    $this->get(route('auth.social.callback', 'google'))
        ->assertRedirect(route('links.index'));

    expect(auth()->id())->toBe($existing->id)
        ->and(User::count())->toBe(1);
});

it('records the link when an existing account signs in this way first time', function () {
    $existing = User::factory()->withWorkspace()->create([
        'email' => 'ada@example.com',
        'google_id' => null,
    ]);

    fakeSocialite(fakeSocialUser('g-1', 'ada@example.com'));

    $this->get(route('auth.social.callback', 'google'));

    expect($existing->fresh()->google_id)->toBe('g-1');
});

it('sends you back to login when the provider refuses', function () {
    fakeSocialite(null);

    $this->get(route('auth.social.callback', 'google'))
        ->assertRedirect(route('login'));

    expect(auth()->check())->toBeFalse();
});

it('links a provider to the account already signed in', function () {
    $user = User::factory()->withWorkspace()->create(['google_id' => null]);

    fakeSocialite(fakeSocialUser('g-1', 'someone@example.com'));

    $this->actingAs($user)
        ->withSession(['social_connect' => 'google'])
        ->get(route('auth.social.callback', 'google'))
        ->assertRedirect(route('setting.authentication.edit'));

    expect($user->fresh()->google_id)->toBe('g-1');
});

it('refuses to link a provider account someone else already has', function () {
    User::factory()->withWorkspace()->create(['google_id' => 'g-1']);
    $user = User::factory()->withWorkspace()->create(['google_id' => null]);

    fakeSocialite(fakeSocialUser('g-1', 'someone@example.com'));

    $this->actingAs($user)
        ->withSession(['social_connect' => 'google'])
        ->get(route('auth.social.callback', 'google'))
        ->assertRedirect(route('setting.authentication.edit'));

    expect($user->fresh()->google_id)->toBeNull();
});

it('falls back to the nickname when the provider has no name', function () {
    fakeSocialite(fakeSocialUser('g-1', 'ada@example.com', name: null));

    $this->get(route('auth.social.callback', 'google'));

    expect(User::where('email', 'ada@example.com')->firstOrFail()->name)->toBe('ada');
});

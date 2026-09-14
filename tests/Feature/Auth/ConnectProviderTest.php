<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'lua.auth.google' => true,
        'lua.auth.github' => true,
        'services.google.client_id' => 'id',
        'services.google.client_secret' => 'secret',
        'services.google.redirect' => 'https://lua.test/auth/google/callback',
        'services.github.client_id' => 'id',
        'services.github.client_secret' => 'secret',
        'services.github.redirect' => 'https://lua.test/auth/github/callback',
    ]);
});

it('sends a signed-in user to google from the settings connect route', function () {
    $driver = Mockery::mock(AbstractProvider::class);
    $driver->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));
    Socialite::shouldReceive('driver')->with('google')->andReturn($driver);

    $this->actingAs(User::factory()->withWorkspace()->create())
        ->get(route('setting.authentication.providers.connect', 'google'))
        ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
});

it('sends a signed-in user to github from the settings connect route', function () {
    $driver = Mockery::mock(AbstractProvider::class);
    $driver->shouldReceive('redirect')->andReturn(redirect('https://github.com/login/oauth/authorize'));
    Socialite::shouldReceive('driver')->with('github')->andReturn($driver);

    $this->actingAs(User::factory()->withWorkspace()->create())
        ->get(route('setting.authentication.providers.connect', 'github'))
        ->assertRedirect('https://github.com/login/oauth/authorize');
});

it('refuses an unknown provider on the settings connect route', function () {
    $this->actingAs(User::factory()->withWorkspace()->create())
        ->get(route('setting.authentication.providers.connect', 'twitter'))
        ->assertNotFound();
});

it('404s the settings connect route when the provider is off', function () {
    config(['lua.auth.github' => false]);

    $this->actingAs(User::factory()->withWorkspace()->create())
        ->get(route('setting.authentication.providers.connect', 'github'))
        ->assertNotFound();
});

it('asks a guest to sign in before connecting a provider', function () {
    $this->get(route('setting.authentication.providers.connect', 'google'))
        ->assertRedirect(route('login'));
});

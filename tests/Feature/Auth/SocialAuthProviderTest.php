<?php

declare(strict_types=1);

use App\Enums\Auth\SocialAuthProvider;

/**
 * A provider needs both the switch in config/lua.php and its credentials.
 * Either half missing hides the button, which is what keeps a self-hosted
 * install from offering a login that leads straight to an OAuth error.
 */
function configureProvider(string $provider, ?bool $enabled = true, bool $credentials = true): void
{
    config([
        "lua.auth.{$provider}" => $enabled,
        "services.{$provider}.client_id" => $credentials ? 'id' : null,
        "services.{$provider}.client_secret" => $credentials ? 'secret' : null,
    ]);
}

it('offers a provider that is switched on and configured', function () {
    configureProvider('google');
    configureProvider('github', enabled: false);

    expect(SocialAuthProvider::Google->isEnabled())->toBeTrue()
        ->and(SocialAuthProvider::GitHub->isEnabled())->toBeFalse()
        ->and(SocialAuthProvider::enabled())->toBe([SocialAuthProvider::Google]);
});

it('hides a provider switched on but missing its credentials', function () {
    configureProvider('google', credentials: false);

    expect(SocialAuthProvider::Google->isEnabled())->toBeFalse();
});

it('keeps the login page free of buttons when nothing is configured', function () {
    configureProvider('google', enabled: false, credentials: false);
    configureProvider('github', enabled: false, credentials: false);

    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('socialProviders', []));
});

it('ships a configured provider to the login and register pages', function () {
    configureProvider('google');
    configureProvider('github', enabled: false);

    foreach (['login', 'register'] as $route) {
        $this->get(route($route))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('socialProviders', [
                    ['provider' => 'google', 'label' => 'Google'],
                ])
                ->etc()
            );
    }
});

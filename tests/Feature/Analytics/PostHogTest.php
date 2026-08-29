<?php

declare(strict_types=1);

use App\Enums\PostHog\UserEvent;
use App\Enums\User\Role;
use App\Jobs\PostHog\SendEvent;
use App\Jobs\PostHog\SyncUser;
use App\Models\Invite;
use App\Models\User;
use App\Models\Workspace;
use App\Services\PostHogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

/**
 * Lua is open source and self-hosted installs must be able to run with no
 * analytics at all, so "disabled" has to mean nothing is dispatched — not
 * merely that the events get dropped somewhere further down.
 */
function enablePostHog(): void
{
    config()->set('services.posthog.enabled', true);
    config()->set('services.posthog.api_key', 'phc_test_key');
}

function disablePostHog(): void
{
    config()->set('services.posthog.enabled', false);
    config()->set('services.posthog.api_key', null);
}

test('posthog is disabled unless both the flag and a key are configured', function () {
    disablePostHog();
    expect(PostHogService::isEnabled())->toBeFalse();

    config()->set('services.posthog.enabled', true);
    expect(PostHogService::isEnabled())->toBeFalse('the flag alone must not enable it');

    config()->set('services.posthog.enabled', false);
    config()->set('services.posthog.api_key', 'phc_test_key');
    expect(PostHogService::isEnabled())->toBeFalse('a key alone must not enable it');

    enablePostHog();
    expect(PostHogService::isEnabled())->toBeTrue();
});

test('registering dispatches the sync and signup event when posthog is enabled', function () {
    Queue::fake();
    enablePostHog();

    $this->post(route('register'), [
        'name' => 'Tracked User',
        'email' => 'tracked@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    Queue::assertPushed(SyncUser::class);
    Queue::assertPushed(SendEvent::class, function (SendEvent $job) {
        return $job->method === 'capture'
            && $job->payload['event'] === UserEvent::SignedUp->value
            && $job->payload['properties']['auth_provider'] === 'email';
    });
});

test('the signup event carries the workspace group', function () {
    Queue::fake();
    enablePostHog();

    $this->post(route('register'), [
        'name' => 'Grouped User',
        'email' => 'grouped@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $workspace = User::where('email', 'grouped@example.com')->firstOrFail()->currentWorkspace;

    Queue::assertPushed(SendEvent::class, function (SendEvent $job) use ($workspace) {
        return ($job->payload['properties']['$groups']['workspace'] ?? null) === (string) $workspace->id;
    });
});

test('nothing is dispatched to posthog when it is disabled', function () {
    Queue::fake();
    disablePostHog();

    $this->post(route('register'), [
        'name' => 'Untracked User',
        'email' => 'untracked@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    Queue::assertNotPushed(SendEvent::class);

    // Registration itself must be unaffected.
    $user = User::where('email', 'untracked@example.com')->firstOrFail();
    expect($user->currentWorkspace->name)->toBe("Untracked's Workspace");
});

test('the send event job is inert when posthog is disabled', function () {
    disablePostHog();

    // No API key is configured, so reaching the SDK would blow up. Returning
    // cleanly is the assertion.
    (new SendEvent('capture', ['distinctId' => 'x', 'event' => 'y', 'properties' => []]))->handle();
})->throwsNoExceptions();

test('posthog jobs run on their own queue so analytics never blocks real work', function () {
    expect((new SyncUser('user-id'))->queue)->toBe('posthog')
        ->and((new SendEvent('capture', []))->queue)->toBe('posthog');
});

test('an invited user is synced but does not emit the signup event', function () {
    Queue::fake();
    enablePostHog();

    $workspace = Workspace::factory()->create();
    $invite = Invite::factory()->create([
        'workspace_id' => $workspace->id,
        'email' => 'invited@example.com',
        'role' => Role::ROLE_USER->value,
    ]);

    $this->post(route('auth.invites.accept', $invite->id), [
        'name' => 'Invited User',
        'email' => 'invited@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    Queue::assertPushed(SyncUser::class);
    Queue::assertNotPushed(SendEvent::class);
});

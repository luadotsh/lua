<?php

declare(strict_types=1);

use App\Listeners\StripeEventListener;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\Events\WebhookReceived;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Workspace::updated queues a Stripe customer sync for any workspace with a
    // stripe_id, and the suite has no API key. The plan change is what is under
    // test, not the sync it triggers.
    Queue::fake();

    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
    // stripe_id is not fillable; Cashier writes it directly.
    $this->workspace->forceFill(['stripe_id' => 'cus_123'])->saveQuietly();

    $this->paid = Plan::factory()->create(['stripe_id' => 'price_paid']);
});

/** The shape Stripe sends for a subscription event. */
function subscriptionEvent(string $type, string $customer, ?string $plan = null): WebhookReceived
{
    return new WebhookReceived([
        'type' => $type,
        'data' => ['object' => [
            'customer' => $customer,
            'items' => ['data' => [['plan' => ['id' => $plan]]]],
        ]],
    ]);
}

it('moves a workspace onto the plan it just subscribed to', function () {
    (new StripeEventListener)->handle(
        subscriptionEvent('customer.subscription.created', 'cus_123', 'price_paid'),
    );

    expect($this->workspace->fresh()->plan_id)->toBe($this->paid->id);
});

it('moves a workspace when its subscription changes plan', function () {
    (new StripeEventListener)->handle(
        subscriptionEvent('customer.subscription.updated', 'cus_123', 'price_paid'),
    );

    expect($this->workspace->fresh()->plan_id)->toBe($this->paid->id);
});

it('drops a workspace back to free when the subscription ends', function () {
    $this->workspace->update(['plan_id' => $this->paid->id]);

    (new StripeEventListener)->handle(
        subscriptionEvent('customer.subscription.deleted', 'cus_123'),
    );

    $free = Plan::where('internal_id', 'free')->firstOrFail();

    expect($this->workspace->fresh()->plan_id)->toBe($free->id);
});

it('ignores an event it does not handle', function () {
    $before = $this->workspace->plan_id;

    (new StripeEventListener)->handle(
        subscriptionEvent('invoice.paid', 'cus_123', 'price_paid'),
    );

    expect($this->workspace->fresh()->plan_id)->toBe($before);
});

it('refuses an event for a customer we do not have', function () {
    expect(fn () => (new StripeEventListener)->handle(
        subscriptionEvent('customer.subscription.created', 'cus_unknown', 'price_paid'),
    ))->toThrow(ModelNotFoundException::class);
});

it('refuses an event for a plan we do not sell', function () {
    expect(fn () => (new StripeEventListener)->handle(
        subscriptionEvent('customer.subscription.created', 'cus_123', 'price_unknown'),
    ))->toThrow(ModelNotFoundException::class);
});

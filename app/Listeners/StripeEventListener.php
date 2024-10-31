<?php

declare(strict_types=1);

namespace App\Listeners;

use Laravel\Cashier\Events\WebhookReceived;

use App\Models\Workspace;
use App\Models\Plan;

class StripeEventListener
{
    /**
     * Handle received Stripe webhooks.
     */
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;

        // Handle subscription created
        if ($payload['type'] === 'customer.subscription.created') {
            $this->handleSubscriptionEvent($payload);
        }

        // Handle subscription updated
        if ($payload['type'] === 'customer.subscription.updated') {
            $this->handleSubscriptionEvent($payload);
        }

        // Handle subscription deleted
        if ($payload['type'] === 'customer.subscription.deleted') {
            $this->handleSubscriptionDeleted($payload);
        }
    }

    /**
     * Handle subscription creation or update.
     */
    private function handleSubscriptionEvent($payload): void
    {
        $subscription = $payload['data']['object']; // The subscription object from Stripe
        $planId = $subscription['items']['data'][0]['plan']['id']; // Get the plan ID
        $customerId = $subscription['customer']; // Get the Stripe customer ID

        // Find the workspace associated with the Stripe customer ID
        $workspace = Workspace::where('stripe_id', $customerId)->firstOrFail();

        // get plan
        $plan = Plan::where('stripe_id', $planId)->firstOrFail();

        // update plan
        $workspace->update([
            'plan_id' => $plan->id,
        ]);
    }

    /**
     * Handle subscription deletion.
     */
    private function handleSubscriptionDeleted($payload): void
    {
        $subscription = $payload['data']['object']; // The subscription object from Stripe
        $customerId = $subscription['customer']; // Get the Stripe customer ID

        // Find the workspace associated with the Stripe customer ID
        $workspace = Workspace::where('stripe_id', $customerId)->firstOrFail();

        // get plan
        $plan = Plan::where('internal_id', 'free')->firstOrFail();

        // update to free plan
        $workspace->update([
            'plan_id' => $plan->id,
        ]);
    }
}

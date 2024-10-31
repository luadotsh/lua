<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Plan;
use App\Models\Domain;
use App\Models\Tag;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Setting/Billing/Index');
    }

    public function upgrade(Request $request)
    {
        $plans = Plan::where('internal_id', '!=', 'free')->where('is_private', false)->get();
        return Inertia::render('Setting/Billing/Upgrade', [
            'plans' => $plans,
        ]);
    }

    public function checkout($planId, Request $request)
    {
        $workspace = $request->user()->currentWorkspace;

        // get the plan
        $plan = Plan::where('id', $planId)->firstOrFail();

        // create a stripe customer
        $workspace->createOrGetStripeCustomer();

        return $workspace
            ->newSubscription('default', $plan->stripe_id)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('setting.billing.checkout-success'),
                'cancel_url' => route('setting.billing.upgrade'),
            ]);
    }

    public function billingPortal(Request $request)
    {
        return Inertia::location($request->user()->currentWorkspace->redirectToBillingPortal(route('setting.billing.index')));
    }
}

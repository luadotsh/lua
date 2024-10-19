<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Plan;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $plans = Plan::
            where('internal_id', '!=', 'free')
            ->where('is_private', false)
            ->get();

        return Inertia::render('Setting/Billing/Index', [
            'plans' => $plans,
        ]);
    }

    public function checkout($stripeId, Request $request)
    {
        return $request->user()->currentWorkspace
            ->newSubscription('default', $stripeId)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('setting.billing.checkout-success'),
                'cancel_url' => route('setting.billing.index'),
            ]);
    }

    public function billingPortal(Request $request)
    {
        return Inertia::location($request->user()->currentWorkspace->redirectToBillingPortal(route('setting.billing.index')));
    }

    public function swapFreePlan(Request $request)
    {
        $project = $request->user()->currentWorkspace;

        if ($project->subscribed('default')) {
            $project->subscription('default')->cancelNowAndInvoice();
        }

        $project->plan_id = Plan::where('internal_id', 'free')->first()->id;
        $project->trial_ends_at = null;
        $project->save();

        $project->updateFeatureFlags();

        session()->flash('flash.banner', 'You are now on free plan!');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }
}

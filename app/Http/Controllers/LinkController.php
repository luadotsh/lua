<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Jobs\ProcessLinkStat;

use App\Models\Link;
use App\Models\Domain;
use App\Models\Tag;

use Inertia\Inertia;
use Inertia\Response;

class LinkController extends Controller
{
    public function index(Request $request): Response
    {
        $workspace = $request->user()->currentWorkspace;

        $query = Link::where('workspace_id', $workspace->id)
            ->with('tags')
            ->latest();

        // search
        if ($request->q) {
            $query->where(function ($query) use ($request) {
                // $query->where('name', 'LIKE', '%' . $request->q . '%');
                // $query->orWhere('code', 'LIKE', '%' . $request->q . '%');
                // $query->orWhereHas('products', function ($query) use ($request) {
                //     $query->where('name', 'LIKE', '%' . $request->q . '%');
                // });
            });
        }

        $links = $query->paginate(config('app.pagination.default'));

        // the workspace domains
        $domains = Domain::where('workspace_id', $workspace->id)->get()->pluck('domain')->toArray();

        return Inertia::render('Link/Index', [
            'table' => $links,
            'hasData' => Link::where('workspace_id', $workspace->id)->exists(),
            'domains' => array_merge($domains, config('domains.all')),
            'tags' => Tag::where('workspace_id', $workspace->id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        // Create the discount
        $link = Link::create([
            'workspace_id' => $request->user()->currentWorkspace->id,
            'domain' => $request->domain,
            'key' => $request->key,
            'url' => $request->url,
            'link' => "https://{$request->domain}/{$request->key}",
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_campaign' => $request->utm_campaign,
            'utm_term' => $request->utm_term,
            'utm_content' => $request->utm_content,
        ]);

        // update tags
        $link->tags()->sync(collect($request->tags)->pluck('id'));

        return redirect(route('links.index'));
    }

    public function edit($id, Request $request)
    {
        $store = $request->user()->currentStore;

        $discount = Link::where('id', $id)
            ->where('store_id', $store->id)
            ->with('products')
            ->firstOrFail();

        return Inertia::render('App/Discount/Edit', [
            'discount' => $discount,
            'products' => $store->products()->get(),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $store = $request->user()->currentStore;

        $discount = Discount::where('store_id', $store->id)->where('id', $id)->firstOrFail();

        $discount->update([
            'name' => $request->name,
            'code' => $discount->created_at_stripe ? $discount->code : $request->code,
            'published' => $request->published,

            'type' => $discount->created_at_stripe ? $discount->type : $request->type,
            'amount' => $discount->created_at_stripe ? $discount->amount : $request->amount,

            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'max_redemptions' => $request->max_redemptions,

            'duration' => $discount->created_at_stripe ? $discount->duration : $request->duration,
            'duration_in_months' => $discount->created_at_stripe ? $discount->duration_in_months : ($request->duration === DiscountDuration::DURATION_REPEATING->value ? $request->duration_in_months : null),
        ]);

        $discount->products()->sync($request->products);

        DiscountCreated::dispatchIf(!$discount->created_at_stripe, $discount);

        session()->flash('flash.banner', 'Discount updated.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroy($id, Request $request)
    {
        $product = Discount::where('id', $id)->where('store_id', auth()->user()->currentStore->id)->firstOrFail();
        $product->delete();

        session()->flash('flash.banner', 'Discount deleted.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('discounts.index'));
    }

    public function show($key, Request $request)
    {
        // procura o link
        $link = Link::where('key', $key)->firstOrFail();
        ProcessLinkStat::dispatch($link, $request->userAgent(), $request->getLanguages(), $request->ip(), $request->header('Referer'));
        return redirect($link->url, 302);
    }
}

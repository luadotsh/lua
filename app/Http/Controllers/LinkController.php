<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Link\CreateRequest;
use App\Http\Requests\Link\UpdateRequest;

use App\Models\Link;
use App\Models\Domain;
use App\Models\Tag;

use Inertia\Inertia;
use Inertia\Response;

class LinkController extends Controller
{
    public function index(Request $request, $id = null): Response
    {
        $workspace = Auth::user()->currentWorkspace;

        $query = Link::where('workspace_id', $workspace->id)
            ->with('tags')
            ->latest();

        // search
        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('link', 'LIKE', '%' . $request->q . '%');
                $query->orWhere('url', 'LIKE', '%' . $request->q . '%');
                $query->orWhere('utm_source', 'LIKE', '%' . $request->q . '%');
                $query->orWhere('utm_medium', 'LIKE', '%' . $request->q . '%');
                $query->orWhere('utm_campaign', 'LIKE', '%' . $request->q . '%');
                $query->orWhere('utm_term', 'LIKE', '%' . $request->q . '%');
                $query->orWhere('utm_content', 'LIKE', '%' . $request->q . '%');
            });
        }

        $links = $query->paginate(config('app.pagination.default'));

        // the workspace domains
        $domains = Domain::where('workspace_id', $workspace->id)->get()->pluck('domain')->toArray();

        return Inertia::render('Link/Index', [
            'table' => $links,
            'hasData' => Link::where('workspace_id', $workspace->id)->exists(),
            'domains' => array_merge($domains, config('domains.available')),
            'tags' => Tag::where('workspace_id', $workspace->id)->get(),
            'link' => $id ? Link::where('workspace_id', $workspace->id)
                ->where('id', $id)
                ->with('tags')
                ->firstOrFail()
                ->makeVisible('password')
                : null,
        ]);
    }

    public function store(CreateRequest $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $response = Gate::inspect('reached-link-limit', $workspace);
        if (!$response->allowed()) {
            session()->flash('flash.banner', 'You have reached the limit of links, please upgrade your plan.');
            session()->flash('flash.bannerStyle', 'danger');
            return back();
        }

        $key = $request->key ? $request->key : Str::random(6);

        $link = Link::create([
            'workspace_id' => $workspace->id,
            'domain' => $request->domain,
            'key' => $key,
            'url' => $request->url,
            'ios' => $request->ios,
            'android' => $request->android,
            'link' => "https://{$request->domain}/{$key}",
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_campaign' => $request->utm_campaign,
            'utm_term' => $request->utm_term,
            'utm_content' => $request->utm_content,
            'password' => $request->password,
            'expires_at' => $request->expires_at,
            'expired_redirect_url' => $request->expired_redirect_url,
        ]);

        // update tags
        $link->tags()->sync($request->tags);

        session()->flash('flash.banner', 'Link created successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('links.index'));
    }

    public function update($id, UpdateRequest $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $link = Link::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();

        $key = $request->key ? $request->key : Str::random(6);

        $link->update([
            'domain' => $request->domain,
            'key' => $key,
            'url' => $request->url,
            'link' => "https://{$request->domain}/{$key}",
            'ios' => $request->ios,
            'android' => $request->android,
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_campaign' => $request->utm_campaign,
            'utm_term' => $request->utm_term,
            'utm_content' => $request->utm_content,
            'password' => $request->password,
            'expires_at' => $request->expires_at,
            'expired_redirect_url' => $request->expired_redirect_url,
        ]);

        // update tags
        $link->tags()->sync($request->tags);

        session()->flash('flash.banner', 'Link updated successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('links.index'));
    }

    public function destroy($id, Request $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $link = Link::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();
        $link->delete();

        session()->flash('flash.banner', 'Link deleted successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('links.index'));
    }
}

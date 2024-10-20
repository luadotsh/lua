<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\Link\CreateRequest;
use App\Http\Requests\Link\UpdateRequest;

use App\Jobs\ProcessLinkStat;

use App\Models\Link;
use App\Models\Domain;
use App\Models\Tag;

use Inertia\Inertia;
use Inertia\Response;

class LinkController extends Controller
{
    public function index(Request $request, $id = null): Response
    {
        $workspace = $request->user()->currentWorkspace;

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
            'domains' => array_merge($domains, config('domains.all')),
            'tags' => Tag::where('workspace_id', $workspace->id)->get(),

            'link' => $id ? Link::where('workspace_id', $workspace->id)->where('id', $id)->with('tags')->firstOrFail() : null,
        ]);
    }

    public function store(CreateRequest $request)
    {
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
        $link->tags()->sync($request->tags);

        session()->flash('flash.banner', 'Link created successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('links.index'));
    }

    public function update($id, UpdateRequest $request)
    {
        $workspace = $request->user()->currentWorkspace;

        $link = Link::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();

        $link->update([
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
        $link->tags()->sync($request->tags);

        session()->flash('flash.banner', 'Link updated successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('links.index'));
    }

    public function destroy($id, Request $request)
    {
        $workspace = $request->user()->currentWorkspace;

        $link = Link::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();
        $link->delete();

        session()->flash('flash.banner', 'Link deleted successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('links.index'));
    }

    public function show($key, Request $request)
    {
        // procura o link
        $link = Link::where('key', $key)->firstOrFail();
        ProcessLinkStat::dispatch($link, $request->userAgent(), $request->getLanguages(), $request->ip(), $request->header('Referer'));
        return redirect($link->url, 302);
    }
}

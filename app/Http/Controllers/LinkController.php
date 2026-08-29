<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Link\UpdateLink;
use App\Actions\Link\DeleteLink;
use App\Actions\Link\CreateLink;
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

        $link = CreateLink::execute($workspace, $request->validated());

        session()->flash('flash.banner', 'Link created successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('links.index'));
    }

    public function update($id, UpdateRequest $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $link = Link::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();

        UpdateLink::execute($link, $request->validated());

        session()->flash('flash.banner', 'Link updated successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('links.index'));
    }

    public function destroy($id, Request $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $link = Link::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();
        DeleteLink::execute($link);

        session()->flash('flash.banner', 'Link deleted successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('links.index'));
    }
}

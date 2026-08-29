<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Tag\ListTags;
use App\Actions\Link\ListLinks;
use App\Actions\Link\GetLink;
use App\Actions\Domain\ListDomains;
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

        // Keeps the 404 the old firstOrFail() produced for an unknown id.
        $link = $id ? GetLink::execute($workspace, $id) : null;
        abort_if($id && ! $link, 404);

        $links = ListLinks::execute($workspace, ['search' => $request->q]);

        $domains = ListDomains::execute($workspace)->pluck('domain')->toArray();

        return Inertia::render('Link/Index', [
            'table' => $links,
            'hasData' => ListLinks::hasAny($workspace),
            'domains' => array_merge($domains, config('domains.available')),
            'tags' => ListTags::execute($workspace),
            'link' => $link?->makeVisible('password'),
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

        $link = GetLink::execute($workspace, $id);
        abort_unless($link, 404);

        UpdateLink::execute($link, $request->validated());

        session()->flash('flash.banner', 'Link updated successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('links.index'));
    }

    public function destroy($id, Request $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $link = GetLink::execute($workspace, $id);
        abort_unless($link, 404);
        DeleteLink::execute($link);

        session()->flash('flash.banner', 'Link deleted successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('links.index'));
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Domain\ListDomains;
use App\Actions\Link\CreateLink;
use App\Actions\Link\DeleteLink;
use App\Actions\Link\GetLink;
use App\Actions\Link\ListLinks;
use App\Actions\Link\UpdateLink;
use App\Actions\Tag\ListTags;
use App\Http\Requests\Link\CreateRequest;
use App\Http\Requests\Link\UpdateRequest;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class LinkController extends Controller
{
    public function index(Request $request): Response
    {
        $workspace = Auth::user()->currentWorkspace;

        return Inertia::render('Link/Index', [
            'table' => ListLinks::execute($workspace, ['search' => $request->q]),
            'hasData' => ListLinks::hasAny($workspace),
        ]);
    }

    public function edit($id): Response
    {
        $workspace = Auth::user()->currentWorkspace;

        $link = GetLink::execute($workspace, $id);
        abort_unless($link, 404);

        return Inertia::render('Link/Edit', [
            // The owner set this password and may need to read it back.
            'link' => $link->makeVisible('password'),
            ...$this->formData(),
        ]);
    }

    /**
     * What the edit form needs to offer beyond the link itself.
     *
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        $workspace = Auth::user()->currentWorkspace;

        $domains = ListDomains::execute($workspace)->pluck('domain')->toArray();

        return [
            'domains' => array_merge($domains, config('domains.available')),
            'tags' => ListTags::execute($workspace),
        ];
    }

    public function store(CreateRequest $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $response = Gate::inspect('reached-link-limit', $workspace);
        if (! $response->allowed()) {
            session()->flash('flash.banner', 'You have reached the limit of links, please upgrade your plan.');
            session()->flash('flash.bannerStyle', 'danger');

            return back();
        }

        $link = CreateLink::execute($workspace, $request->validated());

        session()->flash('flash.banner', 'Link created. Add the rest below.');
        session()->flash('flash.bannerStyle', 'success');

        // Creating asks for the minimum; the edit screen is where the link gets
        // its tags, campaign, targeting and the rest.
        return redirect(route('links.edit', $link->id));
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

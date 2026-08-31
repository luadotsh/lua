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
use App\Actions\TeamMember\ListMembers;
use App\Http\Requests\Link\CreateRequest;
use App\Http\Requests\Link\UpdateRequest;
use App\Models\Link;
use App\Models\LinkStat;
use Carbon\CarbonImmutable;
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

        $domains = ListDomains::execute($workspace)->pluck('domain')->toArray();

        return Inertia::render('Link/Index', [
            // What the filters can offer, so the controls are built from what
            // the workspace actually has rather than from free text.
            'filters' => $request->only(['q', 'tag', 'domain', 'user']),
            'tags' => ListTags::execute($workspace),
            'domains' => array_merge($domains, config('domains.available')),
            'members' => ListMembers::execute($workspace),
            // `Inertia::scroll()` marks the prop as one `<InfiniteScroll>` may
            // extend; the pagination underneath is unchanged.
            'table' => Inertia::scroll(fn () => ListLinks::execute($workspace, [
                'search' => $request->q,
                'tag' => $request->array('tag'),
                'domain' => $request->array('domain'),
                'user' => $request->array('user'),
            ])),
            'hasData' => ListLinks::hasAny($workspace),
        ]);
    }

    /**
     * A link's own dashboard: what it received, where from, and every event in
     * the period. The statistics come from the same endpoint the workspace
     * dashboard reads, narrowed to this link, so the two can never disagree
     * about what a click is.
     */
    public function show(Request $request, $id): Response
    {
        $workspace = Auth::user()->currentWorkspace;

        $link = GetLink::execute($workspace, $id);
        abort_unless($link, 404);

        $start = CarbonImmutable::parse($request->start ?: now()->subDays(29))->startOfDay();
        $end = CarbonImmutable::parse($request->end ?: now())->endOfDay();

        $events = LinkStat::where('workspace_id', $workspace->id)
            ->where('link_id', $link->id)
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->paginate((int) config('lua.pagination.default'))
            ->withQueryString();

        return Inertia::render('Link/Show', [
            'link' => $link,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'table' => Inertia::scroll(fn () => $events),
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

        $link = CreateLink::execute($workspace, $request->validated(), Auth::user());

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

        // Stay on the link being edited. Saving one field is not a reason to
        // lose the screen and scroll back to find it again.
        return back();
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

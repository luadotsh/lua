<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Actions\Tag\SortTags;
use App\Actions\Tag\UpdateTag;
use App\Actions\Tag\DeleteTag;
use App\Actions\Tag\CreateTag;
use App\Http\Requests\Tag\UpdateRequest;
use App\Http\Requests\Tag\CreateRequest;
use App\Http\Requests\Tag\SortRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::where('workspace_id', auth()->user()->currentWorkspace->id)->get();

        return Inertia::render('Setting/Tag/Index', [
            'tags' => $tags,
        ]);
    }

    public function store(CreateRequest $request)
    {

        $workspace = auth()->user()->currentWorkspace;

        $response = Gate::inspect('reached-tag-limit', $workspace);
        if (!$response->allowed()) {
            session()->flash('flash.banner', 'You have reached the limit of tags, please upgrade your plan.');
            session()->flash('flash.bannerStyle', 'danger');
            return back();
        }

        CreateTag::execute($workspace, $request->validated());

        session()->flash('flash.banner', 'Tag created successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function update(UpdateRequest $request, $id)
    {
        $tag = Tag::where('id', $id)->where('workspace_id', auth()->user()->currentWorkspace->id)->firstOrFail();

        UpdateTag::execute($tag, $request->validated());

        session()->flash('flash.banner', 'Tag updated successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroy($id)
    {
        $tag = Tag::where('workspace_id', auth()->user()->currentWorkspace->id)->where('id', $id)->firstOrFail();

        DeleteTag::execute($tag);

        session()->flash('flash.banner', 'Tag deleted successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function sort(SortRequest $request)
    {
        SortTags::execute(
            auth()->user()->currentWorkspace,
            $request->validated('tags'),
        );

        return back();
    }
}

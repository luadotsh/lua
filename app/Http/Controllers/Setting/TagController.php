<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

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

    public function store(Request $request)
    {

        $workspace = auth()->user()->currentWorkspace;

        $response = Gate::inspect('reached-tag-limit', $workspace);
        if (!$response->allowed()) {
            session()->flash('flash.banner', 'You have reached the limit of tags, please upgrade your plan.');
            session()->flash('flash.bannerStyle', 'danger');
            return back();
        }

        $request->validate([
            'name' => ['required', 'max:255'],
            'color' => ['required', 'max:255'],
        ]);

        $label = new Tag;
        $label->workspace_id = $workspace->id;
        $label->name = $request->name;
        $label->color = $request->color;
        $label->sort = Tag::where('workspace_id', $workspace->id)->count() + 1;
        $label->save();

        session()->flash('flash.banner', 'Tag created successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'color' => ['required', 'max:255'],
        ]);

        $tag = Tag::where('id', $id)->where('workspace_id', auth()->user()->currentWorkspace->id)->firstOrFail();
        $tag->name = $request->name;
        $tag->color = $request->color;
        $tag->save();

        session()->flash('flash.banner', 'Tag updated successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroy($id)
    {
        $tag = Tag::where('workspace_id', auth()->user()->currentWorkspace->id)->where('id', $id)->firstOrFail();
        $tag->delete();

        session()->flash('flash.banner', 'Tag deleted successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function sort(Request $request)
    {
        $request->validate([
            'tags' => ['required', 'array']
        ]);

        $workspace = auth()->user()->currentWorkspace;

        DB::beginTransaction();

        try {
            foreach ($request->tags as $sort => $tag) {
                $tag = Tag::where('id', $tag['id'])->where('workspace_id', $workspace->id)->firstOrFail();
                $tag->sort = $sort + 1;
                $tag->save();
            }

            DB::commit();
            return response()->json();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['status' => 'error', 'message' => 'Could not sort update, please try again.'], 500);
        }
    }
}

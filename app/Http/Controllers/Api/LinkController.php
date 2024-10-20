<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests\Link\CreateRequest;
use App\Http\Requests\Link\UpdateRequest;

use App\Models\Link;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $links = Link::where('workspace_id', $request->workspace->id)
            ->with('tags')
            ->latest()
            ->paginate(config('app.pagination.default'));

        return response()->json($links);
    }

    public function store(CreateRequest $request)
    {
        $link = Link::create([
            'workspace_id' => $request->workspace->id,
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

        return response()->json($link);
    }

    public function update($id, UpdateRequest $request)
    {
        $link = Link::where('workspace_id', $request->workspace->id)->where('id', $id)->first();
        if (!$link) {
            return response()->json(['message' => 'Link not found'], 404);
        }

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

        return response()->json($link);
    }

    public function destroy($id, Request $request)
    {
        $link = Link::where('workspace_id', $request->workspace->id)->where('id', $id)->first();
        if(!$link) {
            return response()->json(['message' => 'Link not found'], 404);
        }

        $link->delete();

        return response()->json(['message' => 'Link deleted']);
    }
}

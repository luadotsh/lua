<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\LinkResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

        return LinkResource::collection($links);
    }

    public function show($id, Request $request)
    {
        $link = Link::where('workspace_id', $request->workspace->id)->where('id', $id)->with('tags')->first();
        if (!$link) {
            return response()->json(['message' => 'Link not found'], 404);
        }

        return response()->json(new LinkResource($link), 200);
    }

    public function store(CreateRequest $request)
    {
        $response = Gate::inspect('reached-link-limit', $request->workspace);
        if (!$response->allowed()) {
            return response()->json(['message' => 'You have reached the link limit'], 403);
        }

        $link = Link::create([
            'workspace_id' => $request->workspace->id,
            'domain' => $request->domain,
            'key' => $request->key,
            'url' => $request->url,
            'link' => "https://{$request->domain}/{$request->key}",
            'ios' => $request->ios,
            'android' => $request->android,
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_campaign' => $request->utm_campaign,
            'utm_term' => $request->utm_term,
            'utm_content' => $request->utm_content,
            'password' => $request->password,
            'external_id' => $request->external_id,
            'expires_at' => $request->expires_at,
            'expired_redirect_url' => $request->expired_redirect_url,
        ]);

        // update tags
        $link->tags()->sync($request->tags);

        return response()->json(new LinkResource($link), 201);
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
            'ios' => $request->ios,
            'android' => $request->android,
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_campaign' => $request->utm_campaign,
            'utm_term' => $request->utm_term,
            'utm_content' => $request->utm_content,
            'password' => $request->password,
            'external_id' => $request->external_id,
            'expires_at' => $request->expires_at,
            'expired_redirect_url' => $request->expired_redirect_url,
        ]);

        // update tags
        $link->tags()->sync($request->tags);

        return response()->json(new LinkResource($link), 200);
    }

    public function destroy($id, Request $request)
    {
        $link = Link::where('workspace_id', $request->workspace->id)->where('id', $id)->first();
        if(!$link) {
            return response()->json(['message' => 'Link not found'], 404);
        }

        $link->delete();

        return response()->json(['message' => 'Link deleted'], 200);
    }
}

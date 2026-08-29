<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Tag\ListTags;
use App\Actions\Tag\GetTag;
use App\Actions\Tag\UpdateTag;
use App\Actions\Tag\DeleteTag;
use App\Actions\Tag\CreateTag;
use App\Http\Resources\Api\TagResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Tag\CreateRequest;
use App\Http\Requests\Tag\UpdateRequest;

use App\Models\Tag;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tags = ListTags::paginate($request->workspace, $request->query('per_page'));

        return TagResource::collection($tags);
    }

    public function store(CreateRequest $request)
    {
        $response = Gate::inspect('reached-tag-limit', $request->workspace);
        if (!$response->allowed()) {
            return response()->json(['message' => 'You have reached the tag limit'], 403);
        }

        $tag = CreateTag::execute($request->workspace, $request->validated());

        return response()->json(new TagResource($tag), 201);
    }

    public function update($id, UpdateRequest $request)
    {
        $tag = GetTag::execute($request->workspace, $id);
        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        UpdateTag::execute($tag, $request->validated());


        return response()->json(new TagResource($tag), 200);
    }

    public function destroy($id, Request $request)
    {
        $tag = GetTag::execute($request->workspace, $id);
        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        DeleteTag::execute($tag);

        return response()->json(['message' => 'Tag deleted']);
    }
}

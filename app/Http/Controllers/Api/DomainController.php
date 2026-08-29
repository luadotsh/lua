<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Domain\UpdateDomain;
use App\Actions\Domain\DeleteDomain;
use App\Actions\Domain\CreateDomain;
use App\Http\Requests\Domain\ValidateRequest;
use App\Http\Resources\Api\DomainResource;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

use App\Http\Requests\Domain\CreateRequest;
use App\Http\Requests\Domain\UpdateRequest;

use App\Models\Domain;
use App\Enums\Domain\Status;

class DomainController extends Controller
{
    public function validate(ValidateRequest $request)
    {
        $defaults = in_array($request->input('domain'), config('domains.available'));
        $db = Domain::where('domain', $request->input('domain'))
            ->where('status', Status::ACTIVE)
            ->exists();

        if ($defaults || $db) {
            return response()->json(['valid' => true]);
        }

        return response()->json(['valid' => false], 404);
    }

    public function index(Request $request)
    {
        $domains = Domain::where('workspace_id', $request->workspace->id)
            ->latest()
            ->paginate(config('app.pagination.default'));

        return DomainResource::collection($domains);
    }

    public function store(CreateRequest $request)
    {
        $response = Gate::inspect('reached-domain-limit', $request->workspace);
        if (!$response->allowed()) {
            return response()->json(['message' => 'You have reached the domain limit'], 403);
        }

        $domain = CreateDomain::execute($request->workspace, $request->validated());

        return response()->json(new DomainResource($domain), 201);
    }

    public function update($id, UpdateRequest $request)
    {
        $domain = Domain::where('workspace_id', $request->workspace->id)->where('id', $id)->first();
        if (!$domain) {
            return response()->json(['message' => 'Domain not found'], 404);
        }

        UpdateDomain::execute($domain, $request->validated());

        return response()->json(new DomainResource($domain), 200);
    }

    public function destroy($id, Request $request)
    {
        $domain = Domain::where('workspace_id', $request->workspace->id)->where('id', $id)->first();
        if (!$domain) {
            return response()->json(['message' => 'Domain not found'], 404);
        }

        DeleteDomain::execute($domain);

        return response()->json(['message' => 'Domain deleted'], 200);
    }
}

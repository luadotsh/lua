<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\DomainResource;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

use App\Http\Requests\Domain\CreateRequest;
use App\Http\Requests\Domain\UpdateRequest;

use App\Models\Domain;
use App\Enums\Domain\Status;

class DomainController extends Controller
{
    public function validate(Request $request)
    {
        $request->validate([
            'domain' => ['required', 'max:255'],
        ], $request->query());

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

        $domain = Domain::create([
            'workspace_id' => $request->workspace->id,
            'domain' => $request->domain,
            'status' => Status::PENDING,
            'not_found_url' => $request->not_found_url,
            'expired_url' => $request->expired_url,
        ]);

        return response()->json(new DomainResource($domain), 201);
    }

    public function update($id, UpdateRequest $request)
    {
        $domain = Domain::where('workspace_id', $request->workspace->id)->where('id', $id)->first();
        if (!$domain) {
            return response()->json(['message' => 'Domain not found'], 404);
        }

        $domain->update([
            'domain' => $request->domain,
            'not_found_url' => $request->not_found_url,
            'expired_url' => $request->expired_url,
        ]);

        if ($domain->wasChanged('domain')) {
            $domain->status = Status::PENDING;
            $domain->save();
        }

        return response()->json(new DomainResource($domain), 200);
    }

    public function destroy($id, Request $request)
    {
        $domain = Domain::where('workspace_id', $request->workspace->id)->where('id', $id)->first();
        if (!$domain) {
            return response()->json(['message' => 'Domain not found'], 404);
        }

        $domain->delete();

        return response()->json(['message' => 'Domain deleted'], 200);
    }
}

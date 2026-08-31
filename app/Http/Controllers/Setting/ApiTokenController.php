<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Actions\AccessToken\ListWorkspaceApiKeys;
use App\Actions\AccessToken\RevokeAccessToken;
use App\Actions\ApiKey\CreateApiKey;
use App\Http\Requests\ApiToken\CreateRequest;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ApiTokenController extends Controller
{
    public function index()
    {
        $workspace = auth()->user()->currentWorkspace;

        $tokens = ListWorkspaceApiKeys::execute($workspace);

        return Inertia::render('Setting/ApiToken/Index', [
            'tokens' => $tokens->map(fn ($token) => [
                'id' => $token->id,
                'name' => $token->name,
                'last_used_at' => $token->last_used_at?->toIso8601String(),
                'expires_at' => $token->expires_at?->toIso8601String(),
                'created_at' => $token->created_at?->toIso8601String(),
            ])->values(),
            'hasData' => $tokens->isNotEmpty(),
        ]);
    }

    public function store(CreateRequest $request)
    {
        // A key acts for the whole workspace, so minting one is running it.
        Gate::authorize('administer', auth()->user()->currentWorkspace);

        $result = CreateApiKey::execute(
            auth()->user(),
            auth()->user()->currentWorkspace,
            $request->only('name', 'expires_at'),
        );

        // The plain token is shown once and never stored in readable form.
        return back()->with('flash', [
            'token' => $result['plain_token'],
        ]);
    }

    public function destroy($id)
    {
        Gate::authorize('administer', auth()->user()->currentWorkspace);

        $revoked = RevokeAccessToken::execute(
            auth()->user(),
            auth()->user()->currentWorkspace,
            $id,
        );

        session()->flash('flash.banner', $revoked ? 'API token revoked.' : 'Token not found.');
        session()->flash('flash.bannerStyle', $revoked ? 'success' : 'danger');

        return back();
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Actions\AccessToken\ListConnectedMcpClients;
use App\Actions\AccessToken\RevokeAccessToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class McpController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Setting/Mcp/Index', [
            'mcpUrl' => route('mcp'),
            'docsUrl' => 'https://github.com/luadotsh/lua#mcp-server',
            'connectedClients' => ListConnectedMcpClients::execute($user, $user->currentWorkspace)
                ->map(fn ($token) => [
                    'id' => $token->id,
                    'name' => $token->client?->name ?? 'Unnamed client',
                    'last_used_at' => $token->last_used_at?->toIso8601String(),
                ])->values(),
        ]);
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        $revoked = RevokeAccessToken::execute(
            $request->user(),
            $request->user()->currentWorkspace,
            $id,
        );

        session()->flash('flash.banner', $revoked ? 'App disconnected.' : 'Connection not found.');
        session()->flash('flash.bannerStyle', $revoked ? 'success' : 'danger');

        return back();
    }
}

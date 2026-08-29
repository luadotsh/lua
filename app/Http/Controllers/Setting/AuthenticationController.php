<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Actions\Auth\UpdatePassword;
use App\Actions\Auth\UnlinkSocialAccount;
use App\Actions\Auth\DestroyOtherSessions;
use App\Http\Requests\Authentication\UpdatePasswordRequest;
use App\Http\Requests\Authentication\DestroySessionsRequest;
use App\Actions\AccessToken\ListConnectedMcpClients;
use App\Actions\AccessToken\RevokeAccessToken;
use App\Enums\Auth\SocialAuthProvider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticationController extends Controller
{
    public function edit(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Setting/Authentication/Index', [
            'hasPassword' => (bool) $user->password,
            'connectedAccounts' => $this->connectedAccounts($user),
            'sessions' => $this->sessions($request),
            'mcpClients' => ListConnectedMcpClients::execute($user, $user->currentWorkspace)
                ->map(fn ($token) => [
                    'id' => $token->id,
                    'name' => $token->client?->name,
                    'last_used_at' => $token->last_used_at?->toIso8601String(),
                    'created_at' => $token->created_at?->toIso8601String(),
                ])->values(),
            'mcpUrl' => route('mcp'),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        UpdatePassword::execute($request->user(), $request->validated('password'));

        session()->flash('flash.banner', 'Password updated.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroyOtherSessions(DestroySessionsRequest $request): RedirectResponse
    {
        $user = $request->user();

        DestroyOtherSessions::execute($user, $request->session()->getId());

        session()->flash('flash.banner', 'Signed out of your other sessions.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function disconnectProvider(Request $request, string $provider): RedirectResponse
    {
        $socialProvider = SocialAuthProvider::tryFrom($provider);

        abort_unless($socialProvider !== null, 404);

        $user = $request->user();

        if (! UnlinkSocialAccount::execute($user, $socialProvider)) {
            session()->flash('flash.banner', 'Set a password before disconnecting your only sign-in method.');
            session()->flash('flash.bannerStyle', 'danger');

            return back();
        }

        session()->flash('flash.banner', "{$socialProvider->label()} disconnected.");
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function revokeMcpClient(Request $request, string $id): RedirectResponse
    {
        $revoked = RevokeAccessToken::execute(
            $request->user(),
            $request->user()->currentWorkspace,
            $id,
        );

        session()->flash('flash.banner', $revoked ? 'Connection revoked.' : 'Connection not found.');
        session()->flash('flash.bannerStyle', $revoked ? 'success' : 'danger');

        return back();
    }

    /**
     * @return array<int, array{provider: string, label: string, connected: bool}>
     */
    private function connectedAccounts(User $user): array
    {
        return collect(SocialAuthProvider::cases())
            ->map(fn (SocialAuthProvider $provider) => [
                'provider' => $provider->value,
                'label' => $provider->label(),
                'connected' => filled($user->{"{$provider->value}_id"}),
            ])->values()->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sessions(Request $request): array
    {
        if (config('session.driver') !== 'database') {
            return [];
        }

        return collect(
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->id)
                ->orderByDesc('last_activity')
                ->get(),
        )->map(fn ($session) => [
            'id' => $session->id,
            'ip_address' => $session->ip_address,
            'user_agent' => $session->user_agent,
            'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            'is_current' => $session->id === $request->session()->getId(),
        ])->values()->all();
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LinkSocialAccount;
use App\Actions\Auth\LoginUser;
use App\Actions\User\CreateUser;
use App\Enums\Auth\SocialAuthProvider;
use App\Http\Controllers\Auth\Concerns\PreservesAttributionParameters;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

/**
 * One entry point for every social provider, so adding another is a case in
 * SocialAuthProvider plus credentials rather than a new controller.
 */
class SocialAuthController extends Controller
{
    use PreservesAttributionParameters;

    public function redirectToProvider(Request $request, string $provider)
    {
        $socialProvider = $this->provider($provider);

        // Carry campaign parameters across the bounce out to the provider.
        $this->storeAttributionParameters($request);

        return Inertia::location(
            Socialite::driver($socialProvider->value)->redirect()->getTargetUrl(),
        );
    }

    public function handleProviderCallback(Request $request, string $provider)
    {
        $socialProvider = $this->provider($provider);

        try {
            $socialUser = Socialite::driver($socialProvider->value)->user();
        } catch (\Exception $e) {
            return redirect(route('login'));
        }

        $column = $socialProvider->column();

        // `guest` middleware gates login/signup; `auth` gates the settings
        // connect flow. The callback is reachable by both and branches here.
        if ($request->user()) {
            return $this->connectToCurrentUser($request->user(), $socialProvider, $socialUser->getId());
        }

        $existingUser = User::where($column, $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if ($existingUser) {
            // Record the link on first sign-in through this provider.
            if (! $existingUser->{$column}) {
                LinkSocialAccount::execute($existingUser, $socialProvider, $socialUser->getId());
            }

            LoginUser::forUser($request, $existingUser, remember: true);

            return redirect()->to(route('links.index'));
        }

        $user = CreateUser::execute([
            'name' => $socialUser->getName() ?: $socialUser->getNickname(),
            'email' => $socialUser->getEmail(),
            $column => $socialUser->getId(),
            'email_verified_at' => now(),
            'auth_provider' => $socialProvider->value,
        ], $this->retrieveAttributionParameters());

        event(new Registered($user));

        LoginUser::forUser($request, $user);

        return redirect(route('links.index'));
    }

    private function connectToCurrentUser(User $user, SocialAuthProvider $socialProvider, string $providerId): RedirectResponse
    {
        if (LinkSocialAccount::isTaken($socialProvider, $providerId, $user)) {
            session()->flash('flash.banner', "That {$socialProvider->label()} account is already linked to another user.");
            session()->flash('flash.bannerStyle', 'danger');

            return redirect(route('setting.authentication.edit'));
        }

        if ($user->{$socialProvider->column()} !== $providerId) {
            LinkSocialAccount::execute($user, $socialProvider, $providerId);
        }

        session()->flash('flash.banner', "{$socialProvider->label()} connected.");
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('setting.authentication.edit'));
    }

    private function provider(string $provider): SocialAuthProvider
    {
        $socialProvider = SocialAuthProvider::tryFrom($provider);

        abort_unless($socialProvider?->isEnabled(), 404);

        return $socialProvider;
    }
}

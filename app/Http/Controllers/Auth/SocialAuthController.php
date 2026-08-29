<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateUser;
use App\Enums\Auth\SocialAuthProvider;
use App\Http\Controllers\Auth\Concerns\PreservesAttributionParameters;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // An authenticated user is linking, not signing in.
        if ($request->user()) {
            $request->session()->put('social_connect', $socialProvider->value);
        }

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

        // Linking an extra provider to the account already signed in.
        if ($request->session()->pull('social_connect') === $socialProvider->value && $request->user()) {
            $taken = User::where($column, $socialUser->getId())
                ->where('id', '!=', $request->user()->id)
                ->exists();

            if ($taken) {
                session()->flash('flash.banner', "That {$socialProvider->label()} account is already linked to another user.");
                session()->flash('flash.bannerStyle', 'danger');

                return redirect(route('setting.authentication.edit'));
            }

            $request->user()->forceFill([$column => $socialUser->getId()])->save();

            session()->flash('flash.banner', "{$socialProvider->label()} connected.");
            session()->flash('flash.bannerStyle', 'success');

            return redirect(route('setting.authentication.edit'));
        }

        $existingUser = User::where($column, $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if ($existingUser) {
            // Record the link on first sign-in through this provider.
            if (! $existingUser->{$column}) {
                $existingUser->forceFill([$column => $socialUser->getId()])->save();
            }

            Auth::login($existingUser, true);

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

        Auth::login($user);

        return redirect(route('links.index'));
    }

    private function provider(string $provider): SocialAuthProvider
    {
        $socialProvider = SocialAuthProvider::tryFrom($provider);

        abort_unless($socialProvider?->isEnabled(), 404);

        return $socialProvider;
    }
}

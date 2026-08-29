<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateUser;
use App\Http\Controllers\Auth\Concerns\PreservesAttributionParameters;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    use PreservesAttributionParameters;

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider(Request $request)
    {
        $this->storeAttributionParameters($request);

        return Inertia::location(Socialite::driver('google')->redirect());
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect(route('login'));
        }

        // check if they're an existing user
        $existingUser = User::where('email', $googleUser->email)->first();
        if ($existingUser) {

            // log them in
            auth()->login($existingUser, true);

            return redirect()->to(route('links.index'));
        }

        // create user
        $user = CreateUser::execute([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'email_verified_at' => now(),
            'auth_provider' => 'google',
        ], $this->retrieveAttributionParameters());

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('links.index'));
    }
}

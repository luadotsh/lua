<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginUser
{
    /**
     * How many failed attempts a single email and IP may make before the pair
     * is locked out.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Signs a person in from their credentials. Throws a validation error
     * rather than returning false, so a wrong password and a locked-out
     * attempt reach the form the same way.
     *
     * @param  array{email: string, password: string}  $credentials
     *
     * @throws ValidationException
     */
    public static function execute(Request $request, array $credentials, bool $remember = false): void
    {
        $key = self::throttleKey($credentials['email'], $request->ip());

        self::ensureIsNotRateLimited($request, $key);

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($key);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($key);

        // A fresh session id after sign-in, so a fixated one cannot be reused.
        $request->session()->regenerate();
    }

    /**
     * Signs in a user who has already been identified some other way: they
     * just registered, accepted an invite, or came back from a provider.
     */
    public static function forUser(Request $request, User $user, bool $remember = false): void
    {
        Auth::login($user, $remember);

        $request->session()->regenerate();
    }

    /**
     * @throws ValidationException
     */
    private static function ensureIsNotRateLimited(Request $request, string $key): void
    {
        if (! RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Per email and IP, so one person's failures cannot lock out another's
     * account, and one address cannot walk through a list of emails.
     */
    private static function throttleKey(string $email, ?string $ip): string
    {
        return Str::transliterate(Str::lower($email).'|'.$ip);
    }
}

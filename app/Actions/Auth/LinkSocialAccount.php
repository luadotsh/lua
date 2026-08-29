<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Enums\Auth\SocialAuthProvider;
use App\Models\User;

class LinkSocialAccount
{
    public static function execute(User $user, SocialAuthProvider $provider, string $providerId): void
    {
        $user->forceFill([$provider->column() => $providerId])->save();
    }

    /**
     * Whether another account already claims this provider identity.
     */
    public static function isTaken(SocialAuthProvider $provider, string $providerId, User $except): bool
    {
        return User::where($provider->column(), $providerId)
            ->where('id', '!=', $except->id)
            ->exists();
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Enums\Auth\SocialAuthProvider;
use App\Models\User;

class UnlinkSocialAccount
{
    /**
     * Refuses when the provider is the only way into the account, which would
     * otherwise lock the person out.
     */
    public static function execute(User $user, SocialAuthProvider $provider): bool
    {
        if (! $user->password) {
            return false;
        }

        $user->forceFill([$provider->column() => null])->save();

        return true;
    }
}

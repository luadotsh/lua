<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Models\User;

class DeleteAvatar
{
    public static function execute(User $user): void
    {
        $user->forceFill(['photo' => null])->save();
    }
}

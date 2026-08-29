<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UpdatePassword
{
    /**
     * Hashing lives here so no caller can store a plain password by mistake.
     * $rotateRememberToken invalidates "remember me" cookies, which is what a
     * password reset wants and an in-app change does not.
     */
    public static function execute(User $user, string $password, bool $rotateRememberToken = false): void
    {
        $attributes = ['password' => Hash::make($password)];

        if ($rotateRememberToken) {
            $attributes['remember_token'] = Str::random(60);
        }

        $user->forceFill($attributes)->save();
    }
}

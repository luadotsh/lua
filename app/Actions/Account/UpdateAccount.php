<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Models\User;

class UpdateAccount
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function execute(User $user, array $data): User
    {
        foreach (['name', 'email'] as $field) {
            if (array_key_exists($field, $data)) {
                $user->{$field} = $data[$field];
            }
        }

        // Changing the address means it has to be proved again.
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DestroyOtherSessions
{
    /**
     * Only meaningful on the database session driver; other drivers have no
     * table to clear and the call is a no-op.
     */
    public static function execute(User $user, string $keepSessionId): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->where('id', '!=', $keepSessionId)
            ->delete();
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Link;

use App\Models\Link;

class ResolveLinkByKey
{
    /**
     * How a visitor's request becomes a link: the host they arrived on plus
     * the key in the path. The host matters — two workspaces may hold the same
     * key on different domains, and matching on the key alone would resolve to
     * whichever row the database returned first.
     *
     * Null rather than an exception, so each caller decides its own 404.
     */
    public static function execute(string $host, ?string $key): ?Link
    {
        if (blank($key)) {
            return null;
        }

        return Link::where('domain', $host)
            ->where('key', $key)
            ->with('workspace')
            ->first();
    }
}

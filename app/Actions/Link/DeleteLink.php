<?php

declare(strict_types=1);

namespace App\Actions\Link;

use App\Models\Link;

class DeleteLink
{
    public static function execute(Link $link): void
    {
        $link->delete();
    }
}

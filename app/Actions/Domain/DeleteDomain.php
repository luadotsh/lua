<?php

declare(strict_types=1);

namespace App\Actions\Domain;

use App\Models\Domain;

class DeleteDomain
{
    public static function execute(Domain $domain): void
    {
        $domain->delete();
    }
}

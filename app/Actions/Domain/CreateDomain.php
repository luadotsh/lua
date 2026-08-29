<?php

declare(strict_types=1);

namespace App\Actions\Domain;

use App\Enums\Domain\Status;
use App\Models\Domain;
use App\Models\Workspace;

class CreateDomain
{
    /**
     * @param  array{domain: string, not_found_url?: string|null, expired_url?: string|null}  $data
     */
    public static function execute(Workspace $workspace, array $data): Domain
    {
        return Domain::create([
            'workspace_id' => $workspace->id,
            'domain' => data_get($data, 'domain'),
            // A new domain has not proved its DNS yet.
            'status' => Status::PENDING,
            'not_found_url' => data_get($data, 'not_found_url'),
            'expired_url' => data_get($data, 'expired_url'),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Domain;

use App\Enums\Domain\Status;
use App\Models\Domain;
use App\Models\Workspace;
use Illuminate\Validation\ValidationException;

class CreateDomain
{
    /**
     * The plan's custom-domain allowance.
     *
     * Enforced here rather than in the controllers because the web form, the
     * REST API and the MCP tool all come through this action — a check in one
     * of them would be a limit the other two ignore.
     *
     * A plan with no allowance at all gets its own message: "0 of 0 used" is
     * not what went wrong, the plan simply does not include custom domains.
     */
    private static function assertWithinPlanLimit(Workspace $workspace): void
    {
        $domains = $workspace->usage()['domains'];

        if (! $domains['reached_limit']) {
            return;
        }

        throw ValidationException::withMessages([
            'domain' => $domains['limit'] === 0
                ? 'Custom domains are not included in your plan. Upgrade to connect one.'
                : "Your plan covers {$domains['limit']} custom domains and you have connected them all. Upgrade to add another.",
        ]);
    }

    /**
     * @param  array{domain: string, not_found_url?: string|null, expired_url?: string|null}  $data
     */
    public static function execute(Workspace $workspace, array $data): Domain
    {
        self::assertWithinPlanLimit($workspace);

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

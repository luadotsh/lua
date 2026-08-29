<?php

declare(strict_types=1);

namespace App\Actions\Domain;

use App\Enums\Domain\Status;
use App\Models\Domain;

class VerifyDomainDns
{
    /**
     * Marks the domain active once a CNAME points at the configured target.
     * Returns whether the record was found; the caller decides what to say.
     */
    public static function execute(Domain $domain): bool
    {
        $records = dns_get_record($domain->domain, DNS_CNAME) ?: [];

        foreach ($records as $record) {
            if (
                isset($record['host'], $record['target'])
                && $record['host'] === $domain->domain
                && $record['target'] === config('domains.cname')
            ) {
                $domain->forceFill(['status' => Status::ACTIVE])->save();

                return true;
            }
        }

        return false;
    }
}

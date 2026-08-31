<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * The records the shadowed dns_get_record() below hands back.
 *
 * VerifyDomainDns calls dns_get_record() unqualified inside its own namespace,
 * so PHP looks for App\Actions\Domain\dns_get_record first and finds the stub.
 * That is what makes the action testable — it takes no resolver and there is
 * nothing to inject — and it also keeps the suite off the network entirely.
 */
final class DnsRecords
{
    /** @var list<array<string, string>> */
    public static array $records = [];

    /**
     * @param  list<array<string, string>>  $records
     */
    public static function fake(array $records = []): void
    {
        self::$records = $records;
    }

    public static function cname(string $host, string $target): array
    {
        return ['host' => $host, 'type' => 'CNAME', 'target' => $target];
    }
}

namespace App\Actions\Domain;

use Tests\Support\DnsRecords;

if (! function_exists(__NAMESPACE__.'\dns_get_record')) {
    function dns_get_record(string $hostname, int $type = DNS_ANY): array
    {
        return DnsRecords::$records;
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Domain;

use App\Enums\Domain\Status;
use App\Models\Domain;

class UpdateDomain
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function execute(Domain $domain, array $data): Domain
    {
        foreach (['domain', 'not_found_url', 'expired_url'] as $field) {
            if (array_key_exists($field, $data)) {
                $domain->{$field} = $data[$field];
            }
        }

        $domain->save();

        // Pointing at a different host invalidates the previous DNS check.
        if ($domain->wasChanged('domain')) {
            $domain->status = Status::PENDING;
            $domain->save();
        }

        return $domain;
    }
}

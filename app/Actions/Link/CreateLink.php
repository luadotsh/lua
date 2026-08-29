<?php

declare(strict_types=1);

namespace App\Actions\Link;

use App\Models\Link;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateLink
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function execute(Workspace $workspace, array $data): Link
    {
        $domain = data_get($data, 'domain') ?: config('domains.main');
        $key = data_get($data, 'key') ?: Str::lower(Str::random(7));

        return DB::transaction(function () use ($workspace, $data, $domain, $key): Link {
            $link = Link::create([
                'workspace_id' => $workspace->id,
                'domain' => $domain,
                'key' => $key,
                'url' => data_get($data, 'url'),
                'link' => "https://{$domain}/{$key}",
                'ios' => data_get($data, 'ios'),
                'android' => data_get($data, 'android'),
                'utm_source' => data_get($data, 'utm_source'),
                'utm_medium' => data_get($data, 'utm_medium'),
                'utm_campaign' => data_get($data, 'utm_campaign'),
                'utm_term' => data_get($data, 'utm_term'),
                'utm_content' => data_get($data, 'utm_content'),
                'password' => data_get($data, 'password'),
                'external_id' => data_get($data, 'external_id'),
                'expires_at' => data_get($data, 'expires_at'),
                'expired_redirect_url' => data_get($data, 'expired_redirect_url'),
            ]);

            $link->tags()->sync(data_get($data, 'tags') ?? []);

            return $link->load('tags');
        });
    }
}

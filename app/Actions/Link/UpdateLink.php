<?php

declare(strict_types=1);

namespace App\Actions\Link;

use App\Models\Link;
use Illuminate\Support\Facades\DB;

class UpdateLink
{
    /**
     * Only keys present in $data are written, so a partial update from an MCP
     * tool does not blank out fields the caller never mentioned.
     *
     * @param  array<string, mixed>  $data
     */
    public static function execute(Link $link, array $data): Link
    {
        $fields = [
            'domain', 'key', 'url', 'ios', 'android',
            'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
            'password', 'external_id', 'expires_at', 'expired_redirect_url',
        ];

        $attributes = [];

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $attributes[$field] = $data[$field];
            }
        }

        return DB::transaction(function () use ($link, $data, $attributes): Link {
            $link->fill($attributes);

            // The short link is derived, so it has to follow domain/key.
            $link->link = "https://{$link->domain}/{$link->key}";
            $link->save();

            if (array_key_exists('tags', $data)) {
                $link->tags()->sync($data['tags'] ?? []);
            }

            return $link->load('tags');
        });
    }
}

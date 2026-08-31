<?php

declare(strict_types=1);

namespace App\Actions\Link;

use App\Models\Link;
use App\Models\Tag;
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

        // An empty key would derive the short link down to the bare domain,
        // which is the redirect root rather than a link. The rules only apply
        // to a key that is filled, so this is the field's floor.
        if (array_key_exists('key', $attributes) && blank($attributes['key'])) {
            unset($attributes['key']);
        }

        return DB::transaction(function () use ($link, $data, $attributes): Link {
            $link->fill($attributes);

            // The short link is derived, so it has to follow domain/key.
            $link->link = "https://{$link->domain}/{$link->key}";
            $link->save();

            if (array_key_exists('tags', $data)) {
                $link->tags()->sync(self::ownTags($link, $data['tags'] ?? []));
            }

            return $link->load('tags');
        });
    }

    /**
     * Only the workspace's own tags.
     *
     * sync() attaches whatever ids it is given, and the rules validate that
     * `tags` is an array and nothing more — so a caller could otherwise pin
     * another workspace's tag onto its link and read the name off the list.
     *
     * @param  list<string>  $ids
     * @return list<string>
     */
    private static function ownTags(Link $link, array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        return Tag::where('workspace_id', $link->workspace_id)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();
    }
}

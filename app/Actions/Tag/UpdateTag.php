<?php

declare(strict_types=1);

namespace App\Actions\Tag;

use App\Models\Tag;

class UpdateTag
{
    /**
     * Only keys present in $data are written, so a partial update never blanks
     * a field the caller did not mention.
     *
     * @param  array<string, mixed>  $data
     */
    public static function execute(Tag $tag, array $data): Tag
    {
        foreach (['name', 'color'] as $field) {
            if (array_key_exists($field, $data)) {
                $tag->{$field} = $data[$field];
            }
        }

        $tag->save();

        return $tag;
    }
}

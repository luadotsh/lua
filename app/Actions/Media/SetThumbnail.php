<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Support\Facades\DB;

class SetThumbnail
{
    /**
     * Exactly one media per model carries the thumbnail flag, so the others
     * are cleared in the same transaction.
     */
    public static function execute(string $modelId, string $mediaId): bool
    {
        return DB::transaction(function () use ($modelId, $mediaId): bool {
            $target = Media::where('id', $mediaId)->where('model_id', $modelId)->first();

            if (! $target) {
                return false;
            }

            foreach (Media::where('model_id', $modelId)->get() as $media) {
                $media->setCustomProperty('thumbnail', false);
                $media->save();
            }

            $target->setCustomProperty('thumbnail', true);
            $target->save();

            return true;
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Support\Facades\DB;

class SortMedia
{
    /**
     * @param  array<int, array{id: string|int}>  $medias
     */
    public static function execute(string $modelType, string $collection, array $medias): void
    {
        DB::transaction(function () use ($modelType, $collection, $medias): void {
            foreach ($medias as $position => $media) {
                Media::where('id', data_get($media, 'id'))
                    ->where('model_type', $modelType)
                    ->where('collection_name', $collection)
                    ->update(['order_column' => $position + 1]);
            }
        });
    }
}

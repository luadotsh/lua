<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class StoreMedia
{
    /**
     * @param  array{model: string, model_id: string, collection: string, visibility: string}  $data
     */
    public static function execute(Request $request, array $data): SpatieMedia
    {
        $class = 'App\\Models\\'.data_get($data, 'model');
        $modelId = data_get($data, 'model_id');

        $model = $class::where('id', $modelId)->firstOrFail();

        // The first upload for a model becomes its thumbnail.
        $isFirst = ! Media::where('model_id', $modelId)->exists();

        return $model->addMediaFromRequest('media')
            ->withCustomProperties(['thumbnail' => $isFirst])
            ->addCustomHeaders([
                'ACL' => data_get($data, 'visibility') === 'public' ? 'public-read' : 'private',
            ])
            ->toMediaCollection(data_get($data, 'collection'));
    }
}

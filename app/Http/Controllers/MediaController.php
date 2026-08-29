<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Media\StoreMedia;
use App\Actions\Media\SortMedia;
use App\Actions\Media\SetThumbnail;
use App\Http\Requests\Media\StoreRequest;
use App\Http\Requests\Media\SortRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Media;

class MediaController extends Controller
{
    public function store(StoreRequest $request)
    {
        return response()->json(
            StoreMedia::execute($request, $request->validated()),
        );
    }

    public function sort(SortRequest $request)
    {
        SortMedia::execute(
            $request->validated('model'),
            $request->validated('collection'),
            $request->validated('medias'),
        );

        return response()->json();
    }

    public function thumbnail($modelId, $id)
    {
        abort_unless(SetThumbnail::execute($modelId, $id), 404);

        return back();
    }

    public function download($id, Request $request)
    {
        $media = Media::where('id', $id)->firstOrFail();
        return Storage::download($media->getPath());
    }

    public function destroy($modelId, $id)
    {
        $media = Media::where('id', $id)
            ->where('model_id', $modelId)
            ->firstOrFail();

        $media->delete();

        return back();
    }
}

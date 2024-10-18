<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Media;

class MediaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'media' => ['required', 'file'],
            'collection' => 'required',
            'model' => 'required',
            'model_id' => 'required',
            'visibility' => 'required|in:public,private',
        ]);

        $model = 'App?Models?'.$request->model;
        $model = str_replace('?', '\\', $model);

        // check if it's the first media to this model, if so, set it as thumbnail
        $media = Media::where('model_id', $request->model_id)->first();
        $thumbnail = $media ? false : true;

        $model = $model::where('id', $request->model_id)->firstOrFail();
        $upload = $model->addMediaFromRequest('media')
            ->withCustomProperties(['thumbnail' => $thumbnail])
            ->addCustomHeaders([
                'ACL' => $request->visibility === 'public' ? 'public-read' : 'private',
            ])
            ->toMediaCollection($request->collection);

        return response()->json($upload);
    }

    public function sort(Request $request)
    {
        $request->validate([
            'medias' => ['required', 'array'],
            'model' => 'required',
            'collection' => 'required',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->medias as $sort => $media) {

                $media = Media::where('id', $media['id'])
                ->where('model_type', $request->model)
                ->where('collection_name', $request->collection)
                ->firstOrFail();

                $media->order_column = $sort + 1;
                $media->save();
            }

            DB::commit();
            return response()->json();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['status' => 'error', 'message' => 'Could not sort update, please try again.'], 500);
        }
    }

    public function thumbnail($modelId, $id)
    {
        // set all media to not thumbnail
        $media = Media::where('model_id', $modelId)->get();
        foreach ($media as $m) {
            $m->setCustomProperty('thumbnail', false);
            $m->save();
        }

        // set media to thumbnail
        $media = Media::where('id', $id)
            ->where('model_id', $modelId)
            ->firstOrFail();

        $media->setCustomProperty('thumbnail', true);
        $media->save();

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

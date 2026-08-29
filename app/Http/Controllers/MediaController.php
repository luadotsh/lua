<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Media\DeleteMedia;
use App\Actions\Media\StoreMedia;
use App\Http\Requests\Media\StoreRequest;
use App\Models\Media;

class MediaController extends Controller
{
    public function store(StoreRequest $request)
    {
        return response()->json(
            StoreMedia::execute(
                $request->user(),
                $request->validated('collection'),
                $request->file('media'),
            ),
        );
    }

    public function destroy(Media $media)
    {
        abort_unless(DeleteMedia::execute(request()->user(), $media), 403);

        return back();
    }
}

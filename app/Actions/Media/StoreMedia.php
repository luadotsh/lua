<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\Media;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\UploadedFile;

class StoreMedia
{
    /**
     * A person may only upload to their own avatar, or to the logo of a
     * workspace they belong to.
     */
    public static function execute(User $user, string $collection, UploadedFile $file): Media
    {
        $owner = match ($collection) {
            'avatar' => $user,
            'logo' => $user->currentWorkspace,
            default => null,
        };

        abort_unless($owner !== null, 404);
        abort_unless(
            $owner instanceof Workspace ? $user->belongsToWorkspace($owner) : true,
            403,
        );

        return $owner->addMedia($file, $collection);
    }
}

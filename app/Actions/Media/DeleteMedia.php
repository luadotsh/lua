<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\Media;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Storage;

class DeleteMedia
{
    /**
     * Ownership is resolved from the media itself: a person may delete their
     * own avatar, or the logo of a workspace they belong to. Nothing else.
     */
    public static function execute(User $user, Media $media): bool
    {
        $owner = $media->mediable;

        $allowed = match (true) {
            $owner instanceof User => $owner->is($user),
            $owner instanceof Workspace => $user->belongsToWorkspace($owner),
            default => false,
        };

        if (! $allowed) {
            return false;
        }

        Storage::delete($media->path);
        $media->delete();

        return true;
    }
}

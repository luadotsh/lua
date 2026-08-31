<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class DeleteAvatar
{
    /**
     * Avatars live in the media collection; this used to null a legacy `photo`
     * column nothing reads, so "remove photo" left the avatar exactly where it
     * was and the file on disk.
     */
    public static function execute(User $user): void
    {
        $media = $user->getFirstMedia('avatar');

        if ($media === null) {
            return;
        }

        Storage::delete($media->path);
        $media->delete();

        $user->unsetRelation('media');
    }
}

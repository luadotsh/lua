<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Media\Visibility;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class MediaUrlGeneratorService extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        if($this->media->visibility == Visibility::PRIVATE) {
            return $this->getTemporaryUrl(
                now()->addMinutes(30),
                ['ResponseContentDisposition' => 'attachment']
            );
        }

        $url = $this->getDisk()->url($this->getPathRelativeToRoot());
        return $this->versionUrl($url);
    }
}

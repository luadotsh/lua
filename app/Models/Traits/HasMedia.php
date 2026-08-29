<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Media;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

trait HasMedia
{
    /**
     * Every collection here holds exactly one file, so uploading replaces what
     * was there. Add an entry to give a model a new bucket.
     *
     * @var array<class-string, list<string>>
     */
    private static array $singleFileCollections = [
        User::class => ['avatar'],
        Workspace::class => ['logo'],
    ];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function getMedia(string $collection): MorphMany
    {
        return $this->media()->where('collection', $collection);
    }

    public function getFirstMedia(string $collection): ?Media
    {
        return $this->getMedia($collection)->latest()->first();
    }

    public function getFirstMediaUrl(string $collection, ?string $default = null): ?string
    {
        return $this->getFirstMedia($collection)?->url ?? $default;
    }

    public function hasMedia(string $collection): bool
    {
        return $this->getMedia($collection)->exists();
    }

    public function addMedia(UploadedFile $file, string $collection): Media
    {
        if ($this->isSingleFileCollection($collection)) {
            $this->clearMediaCollection($collection);
        }

        [$bytes, $mimeType, $extension] = $this->normalizeImage(
            $file->getPathname(),
            (string) $file->getMimeType(),
            $file->getClientOriginalExtension(),
        );

        $path = 'medias/'.Str::uuid().'.'.$extension;

        Storage::put($path, $bytes);

        return $this->media()->create([
            'collection' => $collection,
            'path' => $path,
            // Client filenames can carry bytes that are not valid UTF-8, which
            // Postgres rejects outright.
            'original_filename' => mb_scrub($file->getClientOriginalName(), 'UTF-8'),
            'mime_type' => $mimeType,
            'size' => strlen($bytes),
            'meta' => $this->imageDimensions($bytes),
        ]);
    }

    public function clearMediaCollection(string $collection): void
    {
        $this->getMedia($collection)->get()->each(function (Media $media): void {
            Storage::delete($media->path);
            $media->delete();
        });
    }

    public function isSingleFileCollection(string $collection): bool
    {
        return in_array($collection, self::$singleFileCollections[static::class] ?? [], true);
    }

    /**
     * PNG, WebP, HEIC and AVIF become JPEG so every browser and every mail
     * client can render an avatar or a logo. JPEG and GIF pass through, GIF
     * because re-encoding would drop its animation.
     *
     * @return array{0: string, 1: string, 2: string} bytes, mime type, extension
     */
    private function normalizeImage(string $path, string $mimeType, string $extension): array
    {
        if (in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/gif'], true)) {
            return [(string) file_get_contents($path), $mimeType, $extension];
        }

        try {
            $bytes = (string) (new ImageManager(new Driver))
                ->decodePath($path)
                ->encode(new JpegEncoder(quality: 90));

            return [$bytes, 'image/jpeg', 'jpg'];
        } catch (\Throwable $e) {
            Log::warning('HasMedia: could not normalize image, storing the original', [
                'mime' => $mimeType,
                'error' => $e->getMessage(),
            ]);

            return [(string) file_get_contents($path), $mimeType, $extension];
        }
    }

    /**
     * @return array{width?: int, height?: int}
     */
    private function imageDimensions(string $bytes): array
    {
        $info = @getimagesizefromstring($bytes);

        return $info ? ['width' => $info[0], 'height' => $info[1]] : [];
    }
}

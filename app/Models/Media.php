<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\MediaScope;

use App\Enums\Media\Visibility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $table = 'medias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'model_id',
        'model_type',
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'visibility',
        'conversions_disk',
        'size',
        'manipulations',
        'custom_properties',
        'generated_conversions',
        'responsive_images',
        'order_column'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'size_formatted',
        'url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'manipulations' => 'array',
            'custom_properties' => 'array',
            'generated_conversions' => 'array',
            'responsive_images' => 'array',
            'visibility' => Visibility::class
        ];
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    public static function boot(): void
    {
        static::creating(function (Media $media) {
            $media->visibility = $media->custom_properties['custom_headers']['ACL'] === 'public-read' ? Visibility::PUBLIC : Visibility::PRIVATE;
        });
        parent::boot();
    }

    /**
     * The "booted" method of the model.x5m
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new MediaScope());
    }

    /**
     * Get the size of the media file in a human readable format.
     *
     * @return string
     */
    public function getSizeFormattedAttribute()
    {
        return $this->human_readable_size;
    }

    /**
     * Get the URL of the media file.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->getUrl();
    }
}

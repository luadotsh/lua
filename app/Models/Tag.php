<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\TagScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * A CSS hex colour: 3, 6 or 8 digits. Kept here so the web request, the API
     * request and the MCP tool all validate the same thing.
     */
    public const COLOR_PATTERN = '/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'workspace_id',
        'name',
        'sort',
        'color',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TagScope);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function links(): BelongsToMany
    {
        return $this->belongsToMany(Link::class)->withTimestamps();
    }
}

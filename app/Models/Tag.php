<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use App\Models\Scopes\TagScope;

class Tag extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'workspace_id',
        'name',
        'sort',
        'color'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TagScope);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function links()
    {
        return $this->belongsToMany(Link::class)->withTimestamps();
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'internal_id',
        'price',
        'is_monthly',
        'stripe_id',
        'access_level',
        'is_private',
        'max_links',
        'max_events',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}

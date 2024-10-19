<?php

declare(strict_types=1);

namespace App\Models;

use Laravel\Pennant\Feature;
use Laravel\Pennant\Concerns\HasFeatures;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Billable;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Workspace extends Model implements HasMedia
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use Billable;
    use HasFeatures;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'plan_id',
        'logo',
        'billing_cycle_start'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'pm_last_four',
        'pm_type',
        'stripe_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [

        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'features',
        'logo_url'
    ];

    public function getFeaturesAttribute()
    {
        return Feature::all();
    }

    public function updateFeatureFlags()
    {
        foreach ($this->features as $key => $feature) {
            Feature::for($this)->forget($key);
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logos');
    }

    public function getLogoUrlAttribute()
    {
        return $this->hasMedia('logos')
        ? $this->getFirstMediaUrl('logos')
        : "https://api.dicebear.com/7.x/initials/svg?backgroundType=gradientLinear&fontFamily=Helvetica&fontSize=40&seed=url". urlencode($this->name);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->as('membership')
            ->withTimestamps();
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }

    public function linkStats(): HasMany
    {
        return $this->hasMany(LinkStat::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }
}

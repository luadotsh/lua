<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\User\Role;

use function Illuminate\Events\queueable;

use App\Models\Traits\WorkspaceUsage;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use App\Observers\WorkspaceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(WorkspaceObserver::class)]
class Workspace extends Model implements HasMedia
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use Billable;
    use InteractsWithMedia;
    use WorkspaceUsage;

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
        'logo_url'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::updated(queueable(function (Workspace $workspace) {
            if ($workspace->hasStripeId()) {
                $workspace->syncStripeCustomerDetails();
            }
        }));
    }

    /**
     * Get the customer name that should be synced to Stripe.
     */
    public function stripeEmail(): string|null
    {
        return $this->users()->where('role', Role::ROLE_OWNER)->first()->email;
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

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
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

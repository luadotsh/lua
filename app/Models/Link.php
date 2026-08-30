<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Link\Os;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Link extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'workspace_id',
        'user_id',
        'domain',
        'key',
        'url',
        'link',
        'ios',
        'android',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'clicks',
        'last_click',
        'external_id',
        'password',
        'expires_at',
        'expired_redirect_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The password itself stays hidden; whether there is one is not a secret,
     * and the list needs it to show the lock.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'has_password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Encrypted, not hashed: the owner has to be able to read back the
            // password they set in order to share it.
            'password' => 'encrypted',
            'last_click' => 'datetime',
            'expires_at' => 'datetime',
            'os' => Os::class,
        ];
    }

    public function hasPassword(): Attribute
    {
        return Attribute::get(fn (): bool => filled($this->password));
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isBefore(now());
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /** Whoever created the link. Null for links made before it was recorded. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function linkStats(): HasMany
    {
        return $this->hasMany(LinkStat::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}

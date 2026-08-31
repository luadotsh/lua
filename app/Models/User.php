<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasMedia;
use App\Models\Traits\HasWorkspaces;
use App\Support\AttributionKeys;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasMedia, HasUuids, HasWorkspaces, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_workspace_id',
        'photo',
        'google_id',
        'github_id',
        'email_verified_at',
        ...AttributionKeys::UTM,
        ...AttributionKeys::CLICK_ID,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'has_photo',
        'photo_url',
    ];

    public function getHasPhotoAttribute(): bool
    {
        return $this->getFirstMedia('avatar') !== null;
    }

    /**
     * Null when nothing has been uploaded. The avatar component draws initials
     * from the name in that case, which keeps the name off a third party: this
     * used to hand every viewer's browser a dicebear URL with the person's real
     * name in the query string.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('avatar') ?: null;
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invite extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Without this, CreateInvite's `Invite::create()` threw on every call and
     * inviting anyone without an account was a 500. Factories never caught it:
     * they write unguarded.
     *
     * @var list<string>
     */
    protected $fillable = [
        'workspace_id',
        'email',
        'role',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}

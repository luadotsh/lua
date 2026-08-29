<?php

declare(strict_types=1);

namespace App\Actions\Invite;

use App\Mail\Team\SendUserInvite;
use App\Models\Invite;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Mail;

class CreateInvite
{
    /**
     * Someone who already has an account joins the workspace directly; anyone
     * else gets an invite email. Returns the invite, or null when the person
     * was added straight away.
     *
     * @param  array{email: string, role: string}  $data
     */
    public static function execute(Workspace $workspace, array $data): ?Invite
    {
        $email = data_get($data, 'email');
        $role = data_get($data, 'role');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->workspaces()->syncWithPivotValues(
                [$workspace->id],
                ['role' => $role],
                false,
            );

            return null;
        }

        $invite = Invite::create([
            'workspace_id' => $workspace->id,
            'email' => $email,
            'role' => $role,
        ]);

        Mail::to($invite->email)->send(new SendUserInvite($workspace, $invite));

        return $invite;
    }
}

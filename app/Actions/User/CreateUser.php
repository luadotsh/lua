<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Actions\Workspace\CreateWorkspace;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUser
{
    /**
     * The single entry point for creating a user, whatever the surface: the
     * registration form, an OAuth callback, or an invite acceptance. Everyone
     * except an invited user gets a personal workspace here, so no signup flow
     * has to stop at a create-workspace step.
     *
     * @param  array{name: string, email: string, password?: string|null, email_verified_at?: \DateTimeInterface|null, current_workspace_id?: string|null, is_invite?: bool}  $data
     * @param  array<string, string>  $attributionParameters  UTM parameters and ad click IDs captured before signup
     */
    public static function execute(array $data, array $attributionParameters = []): User
    {
        $isInviteRegistration = (bool) data_get($data, 'is_invite', false);

        return DB::transaction(function () use ($data, $attributionParameters, $isInviteRegistration): User {
            $user = User::create([
                ...$attributionParameters,
                'name' => data_get($data, 'name'),
                'email' => data_get($data, 'email'),
                'password' => data_get($data, 'password'),
                'current_workspace_id' => data_get($data, 'current_workspace_id'),
                'email_verified_at' => data_get($data, 'email_verified_at', $isInviteRegistration ? now() : null),
            ]);

            // An invited user joins the workspace that invited them; giving them
            // a personal one too would leave them with a stray empty workspace.
            if (! $isInviteRegistration) {
                CreateWorkspace::execute($user, [
                    'name' => CreateWorkspace::defaultNameFor((string) $user->name),
                ]);
            }

            return $user;
        });
    }
}

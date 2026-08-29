<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Actions\Workspace\CreateWorkspace;
use App\Enums\PostHog\UserEvent;
use App\Jobs\PostHog\SyncUser;
use App\Models\User;
use App\Services\PostHogService;
use Illuminate\Support\Facades\DB;

class CreateUser
{
    /**
     * The single entry point for creating a user, whatever the surface: the
     * registration form, an OAuth callback, or an invite acceptance. Everyone
     * except an invited user gets a personal workspace here, so no signup flow
     * has to stop at a create-workspace step.
     *
     * @param  array{name: string, email: string, password?: string|null, google_id?: string|null, github_id?: string|null, email_verified_at?: \DateTimeInterface|null, current_workspace_id?: string|null, is_invite?: bool, invite_role?: string, auth_provider?: string}  $data
     * @param  array<string, string>  $attributionParameters  UTM parameters and ad click IDs captured before signup
     */
    public static function execute(array $data, array $attributionParameters = []): User
    {
        $isInviteRegistration = (bool) data_get($data, 'is_invite', false);

        $user = DB::transaction(function () use ($data, $attributionParameters, $isInviteRegistration): User {
            $user = User::create([
                ...$attributionParameters,
                'name' => data_get($data, 'name'),
                'email' => data_get($data, 'email'),
                'password' => data_get($data, 'password'),
                'google_id' => data_get($data, 'google_id'),
                'github_id' => data_get($data, 'github_id'),
                'current_workspace_id' => data_get($data, 'current_workspace_id'),
                'email_verified_at' => data_get($data, 'email_verified_at', $isInviteRegistration ? now() : null),
            ]);

            // An invited user joins the workspace that invited them, with the
            // role the invite carried; giving them a personal workspace too
            // would leave a stray empty one behind.
            if ($isInviteRegistration) {
                $workspaceId = data_get($data, 'current_workspace_id');
                $role = data_get($data, 'invite_role');

                if ($workspaceId && $role) {
                    $user->workspaces()->attach($workspaceId, ['role' => $role]);
                }
            }

            if (! $isInviteRegistration) {
                CreateWorkspace::execute($user, [
                    'name' => CreateWorkspace::defaultNameFor((string) $user->name),
                ]);
            }

            return $user;
        });

        if (PostHogService::shouldTrack()) {
            SyncUser::dispatch((string) $user->id);

            // Joining a workspace by invite is not a signup, so it does not
            // emit the acquisition event.
            if (! $isInviteRegistration) {
                app(PostHogService::class)->capture(
                    (string) $user->id,
                    UserEvent::SignedUp->value,
                    ['auth_provider' => data_get($data, 'auth_provider', 'email')],
                    $user->currentWorkspace,
                );
            }
        }

        return $user;
    }
}

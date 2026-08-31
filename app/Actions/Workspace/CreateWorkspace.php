<?php

declare(strict_types=1);

namespace App\Actions\Workspace;

use App\Enums\User\Role;
use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateWorkspace
{
    /**
     * @param  array{name: string}  $data
     */
    public static function execute(User $user, array $data): Workspace
    {
        $plan = Plan::where('internal_id', 'free')->first();

        if (! $plan) {
            throw new RuntimeException('The free plan is missing; run the PlanSeeder.');
        }

        return DB::transaction(function () use ($user, $data, $plan): Workspace {
            $workspace = Workspace::create([
                'name' => data_get($data, 'name'),
                'owner_id' => $user->id,
                'plan_id' => $plan->id,
                'billing_cycle_start' => now()->day,
            ]);

            // The owner is also a member, at the highest role there is. What
            // ownership adds on top is what only one person may do: transfer
            // it, and never be removed or demoted out of the workspace.
            $user->workspaces()->attach($workspace->id, [
                'role' => Role::ROLE_ADMIN,
            ]);

            $user->forceFill([
                'current_workspace_id' => $workspace->id,
            ])->save();

            return $workspace;
        });
    }

    /**
     * The workspace every user gets on signup, so they land straight in the app
     * instead of on a create-workspace form.
     */
    public static function defaultNameFor(string $userName): string
    {
        $firstName = trim(strtok(trim($userName), ' ') ?: '');

        return $firstName === '' ? 'My Workspace' : "{$firstName}'s Workspace";
    }
}

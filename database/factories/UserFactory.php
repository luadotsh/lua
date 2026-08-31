<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\User\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withWorkspace()
    {
        return $this->afterCreating(function (User $user) {

            $workspace = Workspace::factory()->create();
            $workspace->forceFill(['owner_id' => $user->id])->save();
            $user->workspaces()->attach($workspace->id, ['role' => Role::ROLE_ADMIN]);

            // set the current team
            $user->current_workspace_id = $workspace->id;
            $user->save();
        });
    }
}

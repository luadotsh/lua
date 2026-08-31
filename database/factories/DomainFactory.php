<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Domain\Status;
use App\Models\Domain;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Domain>
 */
class DomainFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'domain' => $this->faker->domainName,
            'status' => Status::ACTIVE,
        ];
    }
}

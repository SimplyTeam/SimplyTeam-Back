<?php

namespace Database\Factories;

use App\Models\WorkspaceInvitation;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace>
 */
class WorkspaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->realText
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Workspace $workspace) {
            WorkspaceInvitation::factory()
                ->count(5)
                ->create([
                    'workspace_id' => $workspace->id
                ]);
        });
    }
}

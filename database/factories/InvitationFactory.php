<?php

namespace Database\Factories;

use App\Models\WorkspaceInvitation;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkspaceInvitation>
 */
class InvitationFactory extends Factory
{
    protected $model = WorkspaceInvitation::class;

    public function definition()
    {
        return [
            'email' => $this->faker->email,
            'workspace_id' => Workspace::factory(),
            'token' => $this->faker->uuid,
            'accepted_at' => null
        ];
    }
}

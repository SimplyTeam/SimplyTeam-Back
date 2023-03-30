<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

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

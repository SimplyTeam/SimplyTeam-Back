<?php
namespace Database\Factories;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'workspace_id' => Workspace::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
